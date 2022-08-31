<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<storeMessage generator="hcg" version="1.0">
<response>
<?php if ( ! empty($submitted)): ?>
   <Submitted>
   <?php foreach ($submitted AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   </Submitted>
<?php endif; ?>
</response>
<status><?=$status;?></status>
</storeMessage>
