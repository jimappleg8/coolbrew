<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<productList generator="hcg" version="1.0">
<source><?=$source;?></source>
<response>
<?php if ( ! empty($category)): ?>
   <ProductCategory>
   <?php foreach ($category AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   </ProductCategory>
<?php endif; ?>
<?php if ( ! empty($products)): ?>
   <Products>
   <?php foreach ($products AS $product): ?>
      <Product>
      <?php foreach ($product AS $key => $value): ?>
      <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php endforeach; ?>
      </Product>
   <?php endforeach; ?>
   </Products>
<?php endif; ?>
</response>
<status><?=$status;?></status>
</productList>
