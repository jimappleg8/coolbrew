<body>

<?=$this->load->view('sites/tabs');?>

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

   <a class="admin" href="<?=site_url('sites/faqs/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><span><a href="<?=site_url('sites/faqs/edit/'.$site_id.'/'.$faq_id.'/'.$answer_id.'/'.$last_action);?>">Edit FAQ Info</a> | </span>Assign Categories</h1>

            </div>

            <div class="innercol">

<h1 style="font-weight:normal; margin-bottom:1.5em;"><span style="color:#999; font-size:120%;">Q.</span> <?=($faq['ShortQuestion'] != '') ? $faq['ShortQuestion'] : $faq['Question'];?></h1>

<?php if ($admin['category_exists'] == true): ?>

   <form method="post" action="<?=site_url('sites/categories/assign/'.$site_id.'/'.$faq_id.'/'.$answer_id.'/'.$last_action);?>">
   
   <div class="listing">
   
   <h2>Categories</h2>
   
   <?php foreach($category_list AS $cat): ?>

      <?php $fieldname = 'cat'.$cat['ID']; ?>

      <?php if ($cat['level'] > 0): ?>
      <div style="margin-left:<?=($cat['level']-1)*2;?>em; border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$cat['Name'];?></label></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

   </div> <?php // listing ?>
   
   <div class="listing">
   
   <h2>Products</h2>
   
   <?php if ($admin['product_exists'] == true): ?>

      <?php foreach($product_list AS $category): ?>

         <?php $fieldname = 'prodcat'.$category['CategoryID']; ?>

      <div style="border-top:1px solid #666; clear:both; margin-left:<?=($category['level']-1)*2;?>em;">
      <p style="margin:0; padding:4px 0; font-weight:bold;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$category['CategoryName'];?></label></p>
      <?php foreach($category['Products'] AS $product): ?>
         <?php $prodname = 'prod'.$product['ProductID']; ?>
         <div style="margin-left:2em; border-top:1px dotted #666;"><p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$prodname;?>" id="<?=$prodname;?>" value="1" <?=$this->validation->set_checkbox($prodname, '1');?> /> <label for="<?=$prodname;?>"><?=$product['ProductName'];?></label></p></div>
      <?php endforeach; ?>
      </div>

      <?php endforeach; ?>
   
   <?php else: ?>
   
      <div style="border-top:1px solid #666; margin-left:0; padding:4px 0;">There are no products to display.</div>

   <?php endif; ?>

   </div> <?php // listing ?>
   
   <div style="height:18px;"></div>

   <div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('sites/faqs/index/'.$site_id);?>">Cancel</a>
   </div>

   </form>

<?php else: ?>

   <p>There are no categories to display.</p>
   
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

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
