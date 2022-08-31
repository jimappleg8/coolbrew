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

   <h1>All active ingredients</h1>

            </div>

            <div class="innercol">

<?php if (isset ($ingredients ['error_msg']) && $ingredients['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$ingredients['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($ingredients['ingredient_exists'] == true): ?>

      <?php foreach($ingredient_list AS $ingredient): ?>
      
      <div style="border-top:1px solid #666;">
      <p style="margin:0; padding:4px 0;">
		<a style="text-decoration:none;" href="<?=site_url('ingredients/edit/'.$site_id.'/'.$ingredient['ID'].'/'.$last_action);?>"><?=$ingredient['Ingredient'];?></a><?php if ($ingredient['Status'] == 'pending'): ?> <span style="color:red;">(pending)</span><?php endif; ?></p>
      </div>
      
      <?php endforeach; ?>

<?php else: ?>

   <p>There are no ingredients to display.</p>
   
   <p><a class="admin" href="<?=site_url('ingredients/add/'.$site_id.'/'.$last_action);?>">Create the first ingredient.</a></p>
   
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
            
            <a href="<?=site_url('ingredients/add/'.$site_id.'/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_ingredient.gif" width="138" height="31" alt="Add a new ingredient" style="border:0px; margin:4px 0 24px 0;" /></a>

         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
