<?='<?xml version="1.0" encoding="UTF-8"?>';?>

<faqDetail generator="hcg" version="1.0">
<source><?=$source;?></source>
<?php if ( ! empty($faq)): ?>
<response>
   <Faq>
   <?php foreach ($faq AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   </Faq>
</response>
<?php endif; ?>
<status><?=$status;?></status>
</faqDetail>
