<?='<?xml version="1.0" encoding="UTF-8"?>';?>

<categoryDetail generator="hcg" version="1.0">
<source><?=$source;?></source>
<?php if ( ! empty($category)): ?>
<response>
   <ProductCategory>
   <?php foreach ($category AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   </ProductCategory>
</response>
<?php endif; ?>
<status><?=$status;?></status>
</categoryDetail>
