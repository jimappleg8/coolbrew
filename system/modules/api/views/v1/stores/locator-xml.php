<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<storeLocator generator="hcg" version="1.0">
<source><?=$source;?></source>
<response>
<?php if ( ! empty($search)): ?>
   <Search>
   <?php foreach ($search AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   </Search>
<?php endif; ?>
<?php if ( ! empty($stores)): ?>
   <Stores>
   <?php foreach ($stores AS $store): ?>
      <Store>
      <?php foreach ($store AS $key => $value): ?>
      <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php endforeach; ?>
      </Store>
   <?php endforeach; ?>
   </Stores>
<?php endif; ?>
</response>
<status><?=$status;?></status>
</storeLocator>
