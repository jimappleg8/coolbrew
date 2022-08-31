<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($recipes['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$recipes['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('recipes/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><span><a href="<?=site_url('recipes/edit/'.$site_id.'/'.$recipe_id.'/'.$last_action);?>">Recipe Info</a> | <a href="<?=site_url('nleas/edit/'.$site_id.'/'.$recipe_id.'/'.$last_action);?>">Nutrition Facts</a> | </span><strong>Categories</strong>

            </div>

            <div class="innercol">

<?php if ($recipes['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$recipes['error_msg'];?>
</div>
<?php endif; ?>

<h1 style="margin-bottom:12px;"><?=$recipe['Title'];?></h1>

<?php if ($recipes['category_exists'] == true): ?>

   <form method="post" action="<?=site_url('categories/assign/'.$site_id.'/'.$recipe_id.'/'.$last_action);?>">
   
   <div class="listing">
   
   <?php foreach($category_list AS $cat): ?>

      <?php $fieldname = 'cat'.$cat['ID']; ?>

      <?php if ($cat['level'] == 1): ?>
      <div style="margin-left:0; border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"> <label><strong><?=$cat['CategoryName'];?></strong></label></p>
      </div>
       <?php elseif ($cat['level'] == 2): ?>
      <div style="margin-left:2em; border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$cat['CategoryName'];?></label></p>
      </div>
     <?php endif; ?>

   <?php endforeach; ?>

   </div> <?php // listing ?>
   
   <div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('recipes/index/'.$site_id);?>">Cancel</a>
   </div>

   </form>

<?php else: ?>

   <p>There are no categories to display.</p>
   
   <p><a class="admin" href="<?=site_url('categories/add/'.$site_id.'/'.$last_action);?>">Create the first category.</a></p>

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
