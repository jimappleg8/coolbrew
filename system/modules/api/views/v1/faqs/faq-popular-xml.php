<?='<?xml version="1.0" encoding="UTF-8"?>';?>
<faqPopularSearches generator="hcg" version="1.0">
<source><?=$source;?></source>
<response>
<?php if ( ! empty($searches)): ?>
   <Searches>
   <?php foreach ($searches AS $search): ?>
      <Search>
      <?php foreach ($search AS $key => $value): ?>
      <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php endforeach; ?>
      </Search>
   <?php endforeach; ?>
   </Searches>
<?php endif; ?>
</response>
<status><?=$status;?></status>
</faqPopularSearches>
