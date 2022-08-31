{ "productDetail": {
   "source": "<?=$source;?>",
<?php if ( ! empty($product) && ! empty($categories)): ?>
   "response": {
 <?php if ( ! empty($product)): ?>
     "Product": {
 <?php endif; ?>
 <?php $last = count($product); $cnt = 1; ?>
 <?php foreach ($product AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
 <?php if ( ! empty($product)): ?>
  }, <?php // end product object ?> 
 <?php endif; ?>
 <?php if ( ! empty($categories)): ?>
     "ProductCategories": [
 <?php endif; ?>
 <?php $clast = count($categories); $ccnt = 1; ?>
 <?php foreach ($categories AS $category): ?>
     {
 <?php $last = count($category); $cnt = 1; ?>
 <?php foreach ($category AS $key => $value): ?>
     "<?=$key;?>": "<?=$value;?>"<?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
     }<?php if ($ccnt != $clast): ?>,<?php endif; ?> <?php // end category object ?>
  <?php $ccnt++; ?>  
   <?php endforeach; ?>
   <?php if ( ! empty($categories)): ?>
      ] <?php // end categories array ?> 
   <?php endif; ?>
   }, <?php // end response object ?> 
<?php endif; ?>
   "status": "<?=$status;?>"
   } <?php // end productDetail object ?> 
} <?php // end JSON code ?> 
