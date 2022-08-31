<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<productSimpleList generator="hcg" version="1.0">
<source><?=$source;?></source>
<response>
<?php if ( ! empty($categories)): ?>
   <ProductCategories>
   <?php for ($i=0, $cnt=count($categories); $i<$cnt; $i++): ?>
      <ProductCategory>
      <?php foreach ($categories[$i] AS $key => $value): ?>
         <?php if ($key == 'Products' && ! empty($value)): ?>
         <Products>
            <?php foreach ($value AS $product): ?>
            <Product>
               <?php foreach ($product AS $pkey => $pvalue): ?>
                  <?php if ($pkey != 'CategoryName'): ?>
                     <?php if ($pvalue != ''): ?>
      <<?=$pkey;?>><?=$pvalue;?></<?=$pkey;?>>
                     <?php else: ?>
      <<?=$pkey;?>/>
                     <?php endif; ?>
                  <?php endif; ?>
               <?php endforeach; ?>
            </Product>
            <?php endforeach; ?>
         </Products>
         <?php elseif ($value != ''): ?>
      <<?=$key;?>><?=$value;?></<?=$key;?>>
         <?php else: ?>
      <<?=$key;?>/>
         <?php endif; ?>
      <?php endforeach; ?>
      </ProductCategory>
   <?php endfor; ?>
   </ProductCategories>
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
</productSimpleList>
