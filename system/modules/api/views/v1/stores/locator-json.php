{ "storeLocator": {
   "source": "<?=$source;?>",
   "response": {
<?php if ( ! empty($search)): ?>
     "Search": {
 <?php $last = count($search); $cnt = 1; ?>
 <?php foreach ($search AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  }, <?php // end Search object ?> 
<?php endif; ?>
<?php if ( ! empty($stores)): ?>
     "Stores": [
 <?php $slast = count($stores); $scnt = 1; ?>
 <?php foreach ($stores AS $store): ?>
     {
  <?php $last = count($store); $cnt = 1; ?>
  <?php foreach ($store AS $key => $value): ?>
     "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
  <?php endforeach; ?>
        }<?php if ($scnt != $slast): ?>,<?php endif; ?> <?php // end store object ?> 
        <?php $scnt++; ?>
 <?php endforeach; ?>
      ] <?php // end stores array ?> 
<?php endif; ?>
   }, <?php // end response object ?> 
   "status": "<?=$status;?>"
   } <?php // end storeLocator object ?> 
} <?php // end JSON code ?> 
