<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

               </div>

   <h1>Categories</h1>

            </div>

            <div class="innercol">

<?php if ($products['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$products['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($products['category_exists'] == true): ?>

   <div class="listing">
   
   <?php foreach($category_list AS $cat): ?>

      <?php if ($cat['level'] > 0): ?>
         <?php $category_plus = $cat['CategoryOrder'] + 1; ?>
      <div style="margin-left:<?=($cat['level']-1)*2;?>em; border-top:1px solid #666; clear:both;">
      <p style="float:right; text-align:right; margin:0; padding:4px 0;">
      <a class="admin" href="<?=site_url('categories/move/'.$site_id.'/'.$cat['CategoryID'].'/dn/'.$last_action);?>">v</a>
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('categories/move/'.$site_id.'/'.$cat['CategoryID'].'/up/'.$last_action);?>">^</a>
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('categories/add/'.$site_id.'/'.$cat['CategoryParentID'].'/'.$category_plus.'/'.$last_action);?>">insert peer</a>
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('categories/add/'.$site_id.'/'.$cat['CategoryID'].'/'.$cat['next_child'].'/'.$last_action);?>">add child</a>
      <span class="pipe">|</span>
      <a class="admin" href="<?=site_url('categories/delete/'.$site_id.'/'.$cat['CategoryID'].'/'.$last_action);?>">delete</a></p>
      <p style="margin:0; padding:4px 0;"><a style="text-decoration:none;" href="<?=site_url('categories/edit/'.$site_id.'/'.$cat['CategoryID'].'/'.$last_action);?>"><?=$cat['CategoryName'];?></a><?php if ($cat['Status'] == 'discontinued'): ?> <span style="color:red;">(discontinued)</span><?php endif; ?></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no categories to display.</p>
   
   <p><a class="admin" href="<?=site_url('categories/add/'.$site_id.'/'.$root_id.'/1/'.$last_action);?>">Create the first category.</a></p>

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
