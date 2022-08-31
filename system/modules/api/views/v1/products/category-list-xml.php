<?='<?xml version="1.0" encoding="UTF-8"?>';?>

<categoryList generator="hcg" version="1.0">
<source><?=$source;?></source>
<?php if ( ! empty($categories)): ?>
<response>
   <ProductCategories>
   <?php foreach ($categories AS $category): ?>
      <ProductCategory>
      <?php foreach ($category AS $key => $value): ?>
         <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
         <?php else: ?>
   <<?=$key;?>/>
         <?php endif; ?>
      <?php endforeach; ?>
      </ProductCategory>
   <?php endforeach; ?>
   </ProductCategories>
</response>
<?php endif; ?>
<status><?=$status;?></status>
</categoryList>
