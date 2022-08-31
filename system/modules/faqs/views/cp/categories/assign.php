<body>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

<?php if ($admin['error_msg'] != ''): ?>
<div id="flash_error"><?=$admin['error_msg'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('cp/faqs/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><span><a href="<?=site_url('cp/faqs/edit/'.$site_id.'/'.$faq_id.'/'.$last_action);?>">Edit FAQ Info</a> | </span>Assign Categories</h1>

            </div>

            <div class="innercol">

<h1 style="font-weight:normal; margin-bottom:1.5em;"><span style="color:#999; font-size:120%;">Q.</span> <?=($faq['ShortQuestion'] != '') ? $faq['ShortQuestion'] : $faq['Question'];?></h1>

<?php if ($admin['category_exists'] == true): ?>

   <form method="post" action="<?=site_url('cp/categories/assign/'.$site_id.'/'.$faq_id.'/'.$last_action);?>">
   
   <div class="listing">
   
   <?php foreach($category_list AS $cat): ?>

      <?php $fieldname = 'cat'.$cat['ID']; ?>

      <?php if ($cat['level'] > 0): ?>
      <div style="margin-left:<?=($cat['level']-1)*2;?>em; border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$cat['Name'];?></label></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

   </div> <?php // listing ?>
   
   <div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('cp/faqs/index/'.$site_id);?>">Cancel</a>
   </div>

   </form>

<?php else: ?>

   <p>There are no categories to display.</p>
   
   <p><a class="admin" href="<?=site_url('cp/categories/add/'.$site_id.'/'.$last_action);?>">Create the first category.</a></p>

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
