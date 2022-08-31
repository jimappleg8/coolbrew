{ "categoryDetail": {
   "source": "<?=$source;?>",
<?php if ( ! empty($category)): ?>
   "response": {
     "ProductCategory": {
 <?php $last = count($category); $cnt = 1; ?>
 <?php foreach ($category AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  } <?php // end ProductCategory object ?> 
   }, <?php // end response object ?> 
<?php endif; ?>
   "status": "<?=$status;?>"
   } <?php // end categoryDetail object ?> 
} <?php // end JSON code ?> 
