<?php
if (SERVER_LEVEL == 'live')
{
   $resource_url = 'http://resources.hcgweb.net';
}
elseif (SERVER_LEVEL == 'stage')
{
   $resource_url = 'http://stage.resources.hcgweb.net';
}
elseif (SERVER_LEVEL == 'dev')
{
   $resource_url = 'http://resources.hcgweb.hcgweb.net';
}
else // server level is local
{
   $resource_url = 'http://resources-hcgweb:8888';
}
?>
<div id="recipe-detail" class="clearfix" itemscope itemtype="http://schema.org/Recipe>

   <h2 itemprop="name"><?=$recipe['Title'];?></h2>
   
   <?php if ($recipe['ImageFile'] != ''): ?><img src="<?=$resource_url;?>/<?=$recipe['ImageFile'];?>" alt="<?=$recipe['Title'];?> itemprop="image" /><?php endif; ?>

   <?php if ($recipe['Yield'] != ''): ?><div class="recipe-yield" itemprop="recipeYield">Yield: <?=$recipe['Yield'];?></div><?php endif; ?>

   <?php if ($recipe['Description'] != ''): ?><div class="recipe-description" itemprop="description"><?=$recipe['Description'];?></div><?php endif; ?>

<h3>Ingredients</h3>
         <?php foreach ($recipe['Ingredients'] AS $ingred): ?>
   <?php if ($ingred['IsHeading'] == 1): ?><div class="ingredient-heading"><?php else: ?><div class="ingredient" itemprop="ingredients"><?php endif; ?><?php if($ingred['Quantity'] != ''): ?><?=$ingred['Quantity'];?>&nbsp;<?php endif; ?><?=$ingred['Name'];?>
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
   
   <div class="recipe-directions" itemprop="recipeInstructions"><h3>Directions</h3><?=$recipe['Directions'];?></div>

</div>