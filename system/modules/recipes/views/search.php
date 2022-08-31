<div class="recipe-search">

<form method="post" action="/modules/recipes/search.php" name="recipe" id="recipe">

<div style="width:891px; margin:0 auto; background-color:#D5C26E;">

   <table border="0" cellpadding="3" cellspacing="0">

   <tr>
   <td colspan="5">&nbsp;</td>
   </tr>

   <tr>
   <td width="2%" rowspan="10">&nbsp;</td>
   <td id="tdLog" width="64%" rowspan="10">
   <div id="dLog" style="width:566px; color:#000; height:398px; overflow:auto; border:1px solid #000; background-color:#FFF; padding:0 9px;">
<?php if (isset($recipes)): ?>
   <?php if ( ! empty($recipes)): ?>
      <?php foreach ($recipes AS $recipe): ?>
   <h2 style="margin-bottom:11px;"><?=$recipe['Title'];?></h2>
   
   <?php foreach ($recipe['Ingredients'] AS $ingred): ?>
   <?php if ($ingred['IsHeading'] == 1): ?><div style="font-size:14px; margin:11px 0 0 12px; font-weight:bold;"><?php else: ?><div style="font-size:12px; margin:0 0 0 12px;"><?php endif; ?><?php if($ingred['Quantity'] != ''): ?><?=$ingred['Quantity'];?>&nbsp;<?php endif; ?><?=$ingred['Name'];?>
   </div>
      <?php endforeach; ?></p>

<?php

   if (isset($recipe['RecipeLinks']))
   {
      foreach ($recipe['RecipeLinks'] AS $key => $values)
      {
         $recipe['Directions'] = str_replace('[~'.$key.'~]', '<a href="/recipes/detail.php/'.$values['RecipeCode'].'">'.$values['Title'].'</a>', $recipe['Directions']);
      }
   }

?>

   <p><?=nl2br_except_pre($recipe['Directions']);?></p>
      <?php endforeach; ?>
   <?php else: ?>
   <p>No results found.</p>
   <p>Try widening your search by entering fewer search words or setting the product and category pulldowns to "All".</p>
   <?php endif; ?>
<?php else: ?>
<p>This is the default recipe home page before a search is made.</p>
<p>Enter a search term in the field on the right, select a product or category, or do both to narrow your search as much as you want.</p>
<?php endif; ?>
   </div>
   </td>
   <td width="2%" rowspan="11">&nbsp;</td>
   <td width="32%" align="left">Search Recipes:
   </td>
   <td width="2%" rowspan="11">&nbsp;</td>
   </tr>

   <tr>
   <td>

<?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->Words));?>
<?=$this->validation->Words_error;?>

   </td>
   </tr>

   <tr>
   <td align="left">Narrow your search even more by selecting categories below.</td>
   </tr>

   <tr>
   <td align="left">Products:</td>
   </tr>

   <tr>
   <td>

<?=form_dropdown('Product', $products, $this->validation->Product);?>
<?=$this->validation->Product_error;?>

   </td>
   </tr>

<?php foreach ($lists AS $item): ?>
   <?php $error_name = $item['Code'].'_error'; ?>

   <tr>
   <td><?=$item['Name'];?>:</td>
   </tr>

   <tr>
   <td align="left" id="">

<?=form_dropdown($item['Code'], $item['List'], $this->validation->$item['Code']);?>
<?=$this->validation->$error_name;?>

   </td>
   </tr>
   
<?php endforeach; ?>
   
   <tr>
   <td align="right"><input type="submit" name="rcpSearch" id="rcpSearch" value="Search"></td>
   </tr>

   <tr>
   <td colspan="5">&nbsp;</td>
   </tr>

   </table>

</div>

</form>

</div>