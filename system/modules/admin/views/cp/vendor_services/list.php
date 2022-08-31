<body>

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

               </div>

   <h1>Vendor services</h1>

            </div>

            <div class="innercol">

<?php if ($admin['service_exists'] == TRUE): ?>

   <div class="listing">
   
   <?php foreach($service_list AS $svc): ?>

         <?php $service_plus = $svc['SortOrder'] + 1; ?>
      <?php if ($svc['level'] > 0): ?>
      <div style="margin-left:<?=($svc['level']-1)*2;?>em; border-top:1px solid #666; clear:both;">
      <p style="float:right; text-align:right; margin:0; padding:4px 0;">
      <a class="admin" href="<?=site_url('cp/vendor_services/move/'.$svc['ID'].'/dn/'.$last_action);?>">v</a>
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('cp/vendor_services/move/'.$svc['ID'].'/up/'.$last_action);?>">^</a>
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('cp/vendor_services/add/'.$svc['ParentID'].'/'.$service_plus.'/'.$last_action);?>">insert peer</a>
<!--      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('cp/vendor_services/add/'.$svc['ID'].'/'.$svc['next_child'].'/'.$last_action);?>">add child</a> -->
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('cp/vendor_services/delete/'.$svc['ID'].'/'.$last_action);?>">delete</a></p>
      <p style="margin:0; padding:4px 0;"><a style="text-decoration:none;" href="<?=site_url('cp/vendor_services/edit/'.$svc['ID'].'/'.$last_action);?>"><?=$svc['Name'];?></a></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no services to display.</p>
   
   <p><a class="admin" href="<?=site_url('cp/vendor_services/add/0/1/'.$last_action);?>">Create the first service.</a></p>

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
            
         </div>   <?php // col ?>
         
      </div>   <?php // Right ?>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
