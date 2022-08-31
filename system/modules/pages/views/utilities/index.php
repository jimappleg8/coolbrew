<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($pages['message'] != ''): ?>
<div id="flash_alert"><?=$pages['message'];?></div>
<?php endif; ?>

<?php if ($pages['error_msg'] != ''): ?>
<div id="flash_error"><?=$pages['error_msg'];?></div>
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

   <h1>Utilities</h1>

            </div>

            <div class="innercol">

<h2>Import the <?=$site_id;?>_menu.txt file</h2>

<p><?php if ($menu_file_exists && ! isset($utility['ImportedMenu'])): ?><span style="color:green;">Menu file found.</span> <a href="<?=site_url('utilities/import_menu/'.$site_id.'/1/');?>" class="admin">Make a dry run</a> | <a href="<?=site_url('utilities/import_menu/'.$site_id.'/0/');?>" class="admin">Import menu file</a><?php elseif (isset($utility['ImportedMenu'])): ?><span style="color:red;">Menu file has already been imported.</span><?php else: ?><span style="color:red;">No menu file found.</span><?php endif; ?></p>

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
