{ "categoryList": {
   "source": "<?=$source;?>",
<?php if ( ! empty($categories)): ?>
   "response": {
      "ProductCategories": [
 <?php $clast = count($categories); $ccnt = 1; ?>
 <?php foreach ($categories AS $category): ?>
     {
  <?php $last = count($category); $cnt = 1; ?>
  <?php foreach ($category AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
   <?php $cnt++; ?>
  <?php endforeach; ?>
  }<?php if ($ccnt != $clast): ?>,<?php endif; ?> <?php // end ProductCategory object ?> 
  <?php $ccnt++; ?>
  <?php endforeach; ?>
  ] <?php // end ProductCategories array ?> 
   }, <?php // end response object ?> 
<?php endif; ?>
   "status": "<?=$status;?>"
   } <?php // end categoryDetail object ?> 
} <?php // end JSON code ?> 
