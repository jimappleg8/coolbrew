<?php $count = 0; ?>

<?php foreach ($recipes AS $recipe): ?>

   <?php $count++; ?>

   <?php if ($recipe['Featured'] == 1): ?>
<div class="featured">

<h3>Featured Recipe</h3>
   <?php endif; ?>

<div class="recipe"<?php if ($count == 1): ?> style="border:0;"<?php endif; ?>>

   <h2><?=$recipe['Title'];?></h2>
   
   <?php if ($recipe['Yield'] != ''): ?>
   <p class="makes"><?=$recipe['Yield'];?></p>
   <?php endif; ?>

   <div class="ingredients">
   <?php foreach ($recipe['Ingredients'] AS $ingred): ?>
   <?php if ($ingred['IsHeading'] == 1): ?><div style="font-size:14px; margin:11px 0 0 12px; font-weight:bold;"><?php else: ?><div style="font-size:12px; margin:0 0 0 12px;"><?php endif; ?><?php if($ingred['Quantity'] != ''): ?><?=$ingred['Quantity'];?>&nbsp;<?php endif; ?><?=$ingred['Name'];?>
      </div>
   <?php endforeach; ?>
   </div>

<?php

   if (isset($recipe['RecipeLinks']))
   {
      foreach ($recipe['RecipeLinks'] AS $key => $values)
      {
         $recipe['Directions'] = str_replace('[~'.$key.'~]', '<a href="/recipes/detail.php/'.$values['RecipeCode'].'">'.$values['Title'].'</a>', $recipe['Directions']);
      }
   }

?>

   <div class="instructions">
   <?=$recipe['Directions'];?>
   </div>
   
   <?php if ($recipe['Citation'] != '<p>&#160;</p>'): ?>
   <div class="citation"><?=$recipe['Citation'];?></div>
   <?php endif; ?>

</div>

   <?php if ($recipe['Featured'] == 1): ?>
</div> 
   <?php endif; ?>

<?php endforeach; ?>