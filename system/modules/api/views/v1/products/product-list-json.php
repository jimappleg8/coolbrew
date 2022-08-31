{ "productList": {
   "source": "<?=$source;?>",
   "response": {
<?php if ( ! empty($category)): ?>
     "ProductCategory": {
 <?php $last = count($category); $cnt = 1; ?>
 <?php foreach ($category AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  }, <?php // end ProductCategory object ?> 
<?php endif; ?>
<?php if ( ! empty($products)): ?>
     "Products": [
 <?php $plast = count($products); $pcnt = 1; ?>
 <?php foreach ($products AS $product): ?>
     {
  <?php $last = count($product); $cnt = 1; ?>
  <?php foreach ($product AS $key => $value): ?>
     "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
  <?php endforeach; ?>
        }<?php if ($pcnt != $plast): ?>,<?php endif; ?> <?php // end product object ?> 
        <?php $pcnt++; ?>
 <?php endforeach; ?>
      ] <?php // end products array ?> 
<?php endif; ?>
   }, <?php // end response object ?> 
   "status": "<?=$status;?>"
   } <?php // end productList object ?> 
} <?php // end JSON code ?> 
