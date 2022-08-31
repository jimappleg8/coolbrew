{ "productNLEA": {
   "source": "<?=$source;?>",
<?php if ( ! empty($nlea)): ?>
   "response": {
     "nlea": {
 <?php $last = count($nlea); $cnt = 1; ?>
 <?php foreach ($nlea AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  } <?php // end nlea object ?> 
   }, <?php // end response object ?> 
<?php endif; ?>
   "status": "<?=$status;?>"
   } <?php // end productNLEA object ?> 
} <?php // end JSON code ?> 
