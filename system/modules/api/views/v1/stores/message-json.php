{ "storeMessage": {
   "response": {
<?php if ( ! empty($submitted)): ?>
     "Submitted": {
 <?php $last = count($submitted); $cnt = 1; ?>
 <?php foreach ($submitted AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  } <?php // end Submitted object ?> 
<?php endif; ?>
   }, <?php // end response object ?> 
   "status": "<?=$status;?>"
   } <?php // end storeMessage object ?> 
} <?php // end JSON code ?> 
