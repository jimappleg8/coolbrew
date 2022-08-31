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

   <?php if ($recipes['limited']): ?><a class="admin" href="<?=site_url('recipes/index/'.$site_id.'/all');?>">Show all</a><?php endif; ?>

               </div>

   <h1><?php if ($recipes['limited']): ?>Recipes in <?=$category['CategoryName'];?><?php else: ?>All active recipes<?php endif; ?> <span>(<?=count($recipe_list);?> recipes)</span></h1>

            </div>

            <div class="innercol">

<?php if ($recipes['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$recipes['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($recipes['recipe_exists'] == true): ?>

   <div class="listing">
   
   <?php foreach($recipe_list AS $recipe): ?>
      
      <div style="border-top:1px solid #666;">
      <div style="float:right; font-size:10px; padding-top:6px;"><?=$recipe['ID'];?></div>
      <p style="margin:0; padding:4px 0;"><a style="text-decoration:none;" href="<?=site_url('recipes/edit/'.$site_id.'/'.$recipe['ID'].'/'.$last_action);?>"><?=$recipe['Title'];?></a><?php if ($recipe['Status'] == 'pending'): ?> <span style="color:red;">(pending)</span><?php endif; ?><?php if ($recipe['FlagAsNew'] == 1): ?> <span style="color:red;">NEW!</span><?php endif; ?>
      <br /><span style="font-size:90%;"><?php if ( ! empty($recipe['Categories'])): ?><b>Categories</b><?php foreach($recipe['Categories'] AS $cat): ?> &bull; <?=$category_lookup[$cat]['CategoryName'];?><?php endforeach; ?><?php else: ?>No categories<?php endif; ?></span></p>
      </div>
      
   <?php endforeach; ?>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no recipes to display.</p>
   
   <p><a class="admin" href="<?=site_url('recipes/add/'.$site_id.'/'.$last_action);?>">Create the first recipe.</a></p>
   
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
            
            <a href="<?=site_url('recipes/add/'.$site_id.'/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_recipe.gif" width="138" height="31" alt="Add a new recipe" style="border:0px; margin:4px 0 24px 0;" /></a>

            <h1>Limit to...</h1>

   <?php foreach($category_list AS $cat): ?>

      <?php if ($cat['level'] == 1): ?>
      <div style="margin-left:<?=($cat['level']-1)*12+12;?>px;">
      <p style="margin:0; padding:4px 0;"><?=$cat['CategoryName'];?></p>
      </div>
      <?php elseif ($cat['level'] == 2): ?>
      <div style="margin-left:<?=($cat['level']-1)*12+12;?>px;">
      <p style="margin:0; padding:4px 0;"><a href="<?=site_url('recipes/index/'.$site_id.'/'.$cat['CategoryCode']);?>" style="text-decoration:none;"><?=$cat['CategoryName'];?></a></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
