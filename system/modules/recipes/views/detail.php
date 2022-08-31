<div class="recipe-search">

<form method="post" action="/recipes/index.php" name="recipe" id="recipe">

<div style="width:645px;">

   <table border="0" cellpadding="3" cellspacing="0">

   <tr>
   <td colspan="5">&nbsp;</td>
   </tr>

   <tr>
   <td id="tdLog" valign="top">
   <div id="dLog" style="color:#000; background-color:#FFF; padding:0 9px; min-height:450px;">

   <h2 style="margin-bottom:11px;"><?=$recipe['Title'];?></h2>
   
   <?php if ($recipe['Description'] != ''): ?><?=$recipe['Description'];?><?php endif; ?>

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

   <p><?=$recipe['Directions'];?></p>

   </div>
   </td>
   <td style="background:url(/images/dot_black.gif) repeat-y top center;"><img src="/images/dot_clear.gif" width="4" height="18" alt=""></td>
   <td align="left" valign="top">

<p><strong>Search Recipes</strong></p>

<?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->Words));?>
<?=$this->validation->Words_error;?>

<p>Narrow your search even more by selecting categories below.</p>

<div style="margin:1em 0 1.5em 0;"><p style="margin:0 0 6px 3px;">Products</p>
<?=form_dropdown('Product', $products, $this->validation->Product, 'style="width:194px;"');?>
<?=$this->validation->Product_error;?>
</div>

<?php foreach ($lists AS $item): ?>
   <?php $error_name = $item['Code'].'_error'; ?>

<div style="margin:1em 0 1.5em 0;"><p style="margin:0 0 6px 3px;"><?=$item['Name'];?></p>
<?=form_dropdown($item['Code'], $item['List'], $this->validation->$item['Code'], 'style="width:194px;"');?>
<?=$this->validation->$error_name;?>
</div>

<?php endforeach; ?>

<input type="submit" name="rcpSearch" id="rcpSearch" value="Search">

   </td>
   <td><img src="/images/dot_clear.gif" width="4" height="18" alt=""></td>
   </tr>

   <tr>
   <td width="425"><img src="/images/dot_clear.gif" width="419" height="18" alt=""></td>
   <td width="10"><img src="/images/dot_clear.gif" width="4" height="18" alt=""></td>
   <td width="200"><img src="/images/dot_clear.gif" width="194" height="18" alt=""></td>
   <td width="10"><img src="/images/dot_clear.gif" width="4" height="18" alt=""></td>
   </tr>

   </table>

</div>

</form>

</div>