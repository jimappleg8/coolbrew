<body id="stores">

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['error'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertbad_icon.gif) #C00 left no-repeat; clear:both; color:#FFF; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #C99;"><?=$admin['error'];?></div>
<?php endif; ?>

<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
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

   <h1>Store Utilities</h1>

            </div>

            <div class="innercol">

<p>These are the tools that are available:</p>

   <div class="listing">

      <div class="utility">
         <h2 style="margin-top:0;"><a href="<?=site_url('utilities/find_duplicate');?>">Find duplicate records</a></h2>
         <p>Looks for records with the same address and allows you delete one of them.</p>
      </div>    

      <div class="utility">
         <h2><a href="<?=site_url('imports/update_coordinates');?>">Import latitude and longitude coordinates</a></h2>
         <p>Finds up to 100 stores that do not currently have coordinates for their location and looks up the coordinates up using Google Maps. There are often issues that need to be resolved as you run this script, so you need to pay attention to any errors you receive and spend the time to fix them.</p>
      </div>    

      <div class="utility">
         <h2><a href="<?=site_url('utilities/close_nielsen');?>">Close Nielsen messages</a></h2>
         <p>Closes all messages about Nielsen-supplied stores since we cannot make changes to that data directly.</p>
      </div>    

   </div>
            </div>   <?php // innercol ?>

         </div>   <?php // col ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2011 The Hain Celestial Group, Inc.

        </div>   <?php // Footer ?>

      </div>   <?php // Left ?>

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
&nbsp;
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>

   </div>   <?php // class="container" ?>

</div>   <?php // Wrapper ?>

</body>
