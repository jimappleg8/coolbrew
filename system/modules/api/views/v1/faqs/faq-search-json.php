{ "faqSearch": {
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
<?php if ( ! empty($faqs)): ?>
     "Faqs": [
 <?php $flast = count($faqs); $fcnt = 1; ?>
 <?php foreach ($faqs AS $faq): ?>
     {
  <?php $last = count($faq); $cnt = 1; ?>
  <?php foreach ($faq AS $key => $value): ?>
     "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
  <?php endforeach; ?>
        }<?php if ($fcnt != $flast): ?>,<?php endif; ?> <?php // end faq object ?> 
        <?php $fcnt++; ?>
 <?php endforeach; ?>
      ] <?php // end Faqs array ?> 
<?php endif; ?>
   }, <?php // end response object ?> 
   "status": "<?=$status;?>"
   } <?php // end faqSearch object ?> 
} <?php // end JSON code ?> 
