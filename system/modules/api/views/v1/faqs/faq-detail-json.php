{ "faqDetail": {
   "source": "<?=$source;?>",
<?php if ( ! empty($faq)): ?>
   "response": {
     "Faq": {
 <?php $last = count($faq); $cnt = 1; ?>
 <?php foreach ($faq AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  } <?php // end Faq object ?> 
   }, <?php // end response object ?> 
<?php endif; ?>
   "status": "<?=$status;?>"
   } <?php // end faqDetail object ?> 
} <?php // end JSON code ?> 
