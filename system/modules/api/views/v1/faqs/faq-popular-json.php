{ "faqPopularSearches": {
   "source": "<?=$source;?>",
   "response": {
<?php if ( ! empty($searches)): ?>
     "Searches": [
 <?php $slast = count($searches); $scnt = 1; ?>
 <?php foreach ($searches AS $search): ?>
     {
  <?php $last = count($search); $cnt = 1; ?>
  <?php foreach ($search AS $key => $value): ?>
     "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
  <?php endforeach; ?>
        }<?php if ($scnt != $slast): ?>,<?php endif; ?> <?php // end search object ?> 
        <?php $scnt++; ?>
 <?php endforeach; ?>
      ] <?php // end Searches array ?> 
<?php endif; ?>
   }, <?php // end response object ?> 
   "status": "<?=$status;?>"
   } <?php // end faqPopularSearches object ?> 
} <?php // end JSON code ?> 
