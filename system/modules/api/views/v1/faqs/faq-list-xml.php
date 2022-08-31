<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<faqList generator="hcg" version="1.0">
<source><?=$source;?></source>
<response>
<?php if ( ! empty($category)): ?>
   <FaqCategory>
   <?php foreach ($category AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   </FaqCategory>
<?php endif; ?>
<?php if ( ! empty($faqs)): ?>
   <Faqs>
   <?php foreach ($faqs AS $faq): ?>
      <Faq>
      <?php foreach ($faq AS $key => $value): ?>
      <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php endforeach; ?>
      </Faq>
   <?php endforeach; ?>
   </Faqs>
<?php endif; ?>
</response>
<status><?=$status;?></status>
</faqList>
