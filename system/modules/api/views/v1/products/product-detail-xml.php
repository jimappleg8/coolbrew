<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<productDetail generator="hcg" version="1.0">
<source><?=$source;?></source>
<?php if ( ! empty($product) && ! empty($categories)): ?>
<response>
   <?php if ( ! empty($product)): ?>
   <Product>
   <?php endif; ?>
   <?php foreach ($product AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   <?php if ( ! empty($product)): ?>
   </Product>
   <?php endif; ?>

   <?php if ( ! empty($categories)): ?>
   <ProductCategories>
   <?php endif; ?>
   <?php foreach ($categories AS $category): ?>
      <ProductCategory>
      <?php foreach ($category AS $key => $value): ?>
      <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php endforeach; ?>
      </ProductCategory>
   <?php endforeach; ?>
   <?php if ( ! empty($categories)): ?>
   </ProductCategories>
   <?php endif; ?>
</response>
<?php endif; ?>
<status><?=$status;?></status>
</productDetail>
