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
   $resource_url = 'http://resources.hcgweb.hcgweb.net';
}
?>
<div id="recipe-search" class="clearfix">
   <div id="recipe-search-inner" class="clearfix">

   <div id="recipe-search-results">
      <div id="recipe-search-results-inner">

         <div id="recipe-detail" class="clearfix">

   <h2><?=$recipe['Title'];?></h2>
   
   <?php if ($recipe['ImageFile'] != ''): ?><img src="<?=$resource_url;?>/<?=$recipe['ImageFile'];?>" alt="<?=$recipe['Title'];?>" /><?php endif; ?>
   
   <?php if ($recipe['Yield'] != ''): ?><div class="recipe-yield">Yield: <?=$recipe['Yield'];?></div><?php endif; ?>
   
   <?php if ($recipe['Description'] != ''): ?><div class="recipe-description"><?=$recipe['Description'];?></div><?php endif; ?>

<!-- This is where the ingredients would be listed -->
         <?php foreach ($recipe['Ingredients'] AS $ingred): ?>
   <?php if ($ingred['IsHeading'] == 1): ?><div class="ingredient-heading"><?php else: ?><div class="ingredient"><?php endif; ?><?php if($ingred['Quantity'] != ''): ?><?=$ingred['Quantity'];?>&nbsp;<?php endif; ?><?=$ingred['Name'];?>
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

   <div class="recipe-directions"><?=$recipe['Directions'];?></div>

         </div> <?php /* recipe-detail */ ?>

      </div>  <?php /* recipe-search-results-inner */ ?>
   </div>  <?php /* recipe-search-results */ ?>

   <div id="recipe-search-form">
      <div id="recipe-search-form-inner">
      
<form method="post" action="<?=$action;?>" name="recipe" id="recipe">

   <h2>Search Recipes:</h2>
   
<?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->Words));?>
<?=$this->validation->Words_error;?>

   <p>Narrow your results by selecting categories below.</p>

   <label for="Product">Products:</label>
<?=form_dropdown('Product', $products, $this->validation->Product);?>
<?=$this->validation->Product_error;?>

   <?php foreach ($lists AS $item): ?>
      <?php $error_name = $item['Code'].'_error'; ?>

   <label for="<?=$item['Code'];?>"><?=$item['Name'];?>:</label>
<?=form_dropdown($item['Code'], $item['List'], $this->validation->$item['Code']);?>
<?=$this->validation->$error_name;?>

   <?php endforeach; ?>
   
   <input type="submit" name="rcpSearch" id="rcpSearch" value="Search" style="display:inline;" /> <span class="return-link">or <a href="<?=$action;?>">Go to Recipe Home</a></span>

</form>

      </div>  <?php /* recipe-search-form-inner */ ?>
   </div>  <?php /* recipe-search-form */ ?>

   </div>  <?php /* recipe-search-inner */ ?>
</div>  <?php /* recipe-search */ ?>