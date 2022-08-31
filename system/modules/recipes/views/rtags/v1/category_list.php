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
<div id="recipe-list" class="clearfix">

   <h2><?=$category['CategoryName']; ?></h2>

   <?php if ($category['ImageFile'] != ''): ?><img src="<?=$resource_url;?>/<?=$category['ImageFile'];?>" alt="<?=$category['CategoryName'];?>" /><?php endif; ?>

<?php $count = 0; ?>

<?php foreach ($recipes AS $recipe): ?>

   <?php $count++; ?>
   
   <?php $my_detail_url = str_replace('{RecipeCode}', $recipe['RecipeCode'], $detail_url); ?>
   <?php $my_detail_url = str_replace('{CategoryCode}', $category_code, $my_detail_url); ?>
   
   <?php if ($count == 1): ?>
   <ul>
   <?php endif; ?>
   
   <li><a href="<?=$my_detail_url;?>"><?=$recipe['Title'];?></a><?php if ($recipe['Featured'] == 1): ?> <span class="featured">Featured</span><?php endif; ?><?php if ($recipe['FlagAsNew'] == 1): ?> <span class="new">New!</span><?php endif; ?></li>
   
   <?php if ($count == count($recipes)): ?>
   </ul>
   <?php endif; ?>

<?php endforeach; ?>
	
</div>