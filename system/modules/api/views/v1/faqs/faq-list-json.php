{ "faqList": {
   "source": "<?=$source;?>",
   "response": {
<?php if ( ! empty($category)): ?>
     "FaqCategory": {
 <?php $last = count($category); $cnt = 1; ?>
 <?php foreach ($category AS $key => $value): ?>
      "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
 <?php endforeach; ?>
  }, <?php // end FaqCategory object ?> 
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
   } <?php // end faqList object ?> 
} <?php // end JSON code ?> 
