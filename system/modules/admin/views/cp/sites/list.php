<body id="sites">

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('cp/sites/report');?>">View report</a>

               </div>
<?php
   $active_sites = 0;
   $boulder_sites = 0;
   foreach($site_list as $site)
   {
      $active_sites++;
      if ($site['HostingVendor'] == 'hcgWeb')
         $boulder_sites++;
   }
?>


   <h1>All active sites <span> &mdash; <?=$boulder_sites;?> of <?=$active_sites;?> hosted in Boulder</span></h1>

            </div>

            <div class="innercol">

<?php if ($admin['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$admin['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($admin['site_exists'] == true): ?>

<?php  // build the link menu
   for ($i=0; $i<count($site_list); $i++)
   {
      $site_list[$i]['LinkMenu'] = '';
      $first = TRUE;
      $bgcolor = ($site_list[$i]['FullAccess'] == TRUE || $admin['group'] == 'admin') ? '#FF9' : '#900';
      if ($site_list[$i]['DevVendorURL'] != '')
      {
         $vendor = ($site_list[$i]['DevVendorName'] != '') ? $site_list[$i]['DevVendorName'] : 'vendor';
         $site_list[$i]['LinkMenu'] .= '<a href="'.$site_list[$i]['DevVendorURL'].'" target="_blank" style="background-color:transparent; font-size:inherit;">Dev at '.$vendor.'</a>';
         $first = FALSE;
      }
      if ($site_list[$i]['DevURL'] != '')
      {
         $site_list[$i]['LinkMenu'] .= ($first == FALSE) ? ' | ' : '';
         $site_list[$i]['LinkMenu'] .= '<a href="'.$site_list[$i]['DevURL'].'" target="_blank" style="background-color:transparent; font-size:inherit;">Dev</a>';
         $first = FALSE;
      }
      if ($site_list[$i]['StageURL'] != '')
      {
         $site_list[$i]['LinkMenu'] .= ($first == FALSE) ? ' | ' : '';
         $site_list[$i]['LinkMenu'] .= '<a href="'.$site_list[$i]['StageURL'].'" target="_blank" style="background-color:transparent; font-size:inherit;">Stage</a>';
         $first = FALSE;
      }
      if ($site_list[$i]['LiveURL'] != '')
      {
         $site_list[$i]['LinkMenu'] .= ($first == FALSE) ? ' | ' : '';
         $site_list[$i]['LinkMenu'] .= '<a href="'.$site_list[$i]['LiveURL'].'" target="_blank" style="background-color:transparent; font-size:inherit;">Live</a>';
         $first = FALSE;
      }
      if ($site_list[$i]['LinkMenu'] != '')
      {
         $site_list[$i]['LinkMenu'] = ' &mdash; <div style="display:inline; font-size:0.9em; color:#666;">View: '.$site_list[$i]['LinkMenu'].'</div>';
      }
   }
?>

   <div class="listing">
   
   <?php if ($admin['group'] == 'admin'): ?>
   
      <?php foreach($site_list as $site): ?>
   
      <div class="site clearfix" style="background-color:#FF9;">

         <div class="notes" style="background-color:#FF9;"><?php if ($site['HostingVendor'] != 'hcgWeb' && $site['HostingVendor'] != 'Unknown Vendor'): ?> <span style="background-color:red; color:#FFF; margin-left:6px; padding:0 6px; font-size:10px;">hosted off-site</span><?php elseif ($site['HostingVendor'] == 'Unknown Vendor'): ?> <span style="background-color:#900; color:#FFF; margin-left:6px; padding:0 6px; font-size:10px;">unknown off-site host</span><?php endif; ?></div>
         <div class="domain"><a href="<?=site_url('sites/dashboards/index/'.$site['SiteID']);?>" style="background-color:#FF9;"><?=$site['Domain'];?></a><?=$site['LinkMenu'];?>
         <br /><span class="description"><?=$site['Description'];?></span></div>         
      
      </div> <?php // site ?>
   
      <?php endforeach; ?>

   <?php else: ?>
   
   <h2>Your full-access sites</h2>

      <?php foreach($site_list as $site): ?>
   
         <?php if ($site['FullAccess'] == TRUE): ?>
   
      <div class="site clearfix" style="background-color:#FF9;">

         <div class="notes" style="background-color:#FF9;"><?php if ($site['HostingVendor'] != 'hcgWeb' && $site['HostingVendor'] != 'Unknown Vendor'): ?> <span style="background-color:red; color:#FFF; margin-left:6px; padding:0 6px; font-size:10px;">hosted off-site</span><?php elseif ($site['HostingVendor'] == 'Unknown Vendor'): ?> <span style="background-color:#900; color:#FFF; margin-left:6px; padding:0 6px; font-size:10px;">unknown off-site host</span><?php endif; ?></div>
         <div class="domain"><a href="<?=site_url('sites/dashboards/index/'.$site['SiteID']);?>" style="background-color:#FF9;"><?=$site['Domain'];?></a><?=$site['LinkMenu'];?>
         <br /><span class="description"><?=$site['Description'];?></span></div>         
      
      </div> <?php // site ?>
   
         <?php endif; ?>

      <?php endforeach; ?>
   
   <h2>Your limited-access sites</h2>

      <?php foreach($site_list as $site): ?>
   
         <?php if ($site['FullAccess'] == FALSE): ?>
   
      <div class="site clearfix">

         <div class="notes"><?php if ($site['HostingVendor'] != 'hcgWeb' && $site['HostingVendor'] != 'Unknown Vendor'): ?> <span style="background-color:red; color:#FFF; margin-left:6px; padding:0 6px; font-size:10px;">hosted off-site</span><?php elseif ($site['HostingVendor'] == 'Unknown Vendor'): ?> <span style="background-color:#900; color:#FFF; margin-left:6px; padding:0 6px; font-size:10px;">unknown off-site host</span><?php endif; ?></div>
         <div class="domain"><a href="<?=site_url('sites/dashboards/index/'.$site['SiteID']);?>"><?=$site['Domain'];?></a><?=$site['LinkMenu'];?>
         <br /><span class="description"><?=$site['Description'];?></span></div>         
      
      </div> <?php // site ?>
      
         <?php endif; ?>
   
      <?php endforeach; ?>

   <?php endif; ?>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no sites to display.</p>

<?php endif; ?>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
<?php if ($admin['group'] == 'admin'): ?>
            <a href="<?=site_url('cp/sites/add/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_site.gif" width="138" height="31" alt="Add a new site" style="border:0px; margin:4px 0 24px 0;" /></a>
<?php endif; ?>

<script type="text/javascript">

$( document ).ready(function() {

   // activate the accordion.
   $( "#accordion" ).accordion({
      active: false,
      collapsible: true,
      heightStyle: "content"
   });

});

</script>

            <h1>All sites by brand</h1>

<?php $tmp_brandname = ''; ?>
<?php $lastbrand = count($brands) - 1; ?>
           <div id="accordion">
<?php for ($i=0; $i<=$lastbrand; $i++): ?>
   <?php if ($brands[$i]['BrandID'] != $tmp_brandname): ?>
      <?php $tmp_brandname = $brands[$i]['BrandID']; ?>
      <?php if ($i != 0): ?>
              </div>
      <?php endif; ?>
           <h3><?=ascii_to_entities($brands[$i]['Name']);?></h3>
              <div>
   <?php endif; ?>
              <p><a href="<?=site_url('sites/dashboards/index/'.$brands[$i]['SiteID']);?>"><?=$brands[$i]['Domain'];?></a></p>
<?php endfor; ?>
              </div>
           </div>

<?php if ( ! empty($inactive_list)): ?>

         <h1 style="margin-top:36px;">Inactive Sites</h1>
         <div class="brand">

   <?php foreach($inactive_list as $site): ?>
      <?php if ($site['FullAccess'] == TRUE): ?>
   
            <div class="site"<?php if ($site['FullAccess'] == TRUE): ?> style="background-color:#FF9;"<?php endif; ?>>
            <a href="<?=site_url('sites/dashboards/index/'.$site['SiteID']);?>"><?=$site['Domain'];?></a>
            </div>

   
      <?php endif; ?>
   <?php endforeach; ?>

      </div>
<?php endif; ?>


         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>

   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>
