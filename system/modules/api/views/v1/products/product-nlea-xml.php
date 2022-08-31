<?='<?xml version="1.0" encoding="UTF-8"?>';?>

<productNLEA generator="hcg" version="1.0">
<source><?=$source;?></source>
<?php if ( ! empty($nlea)): ?>
<response>
   <?php if ( ! empty($nlea)): ?>
   <NLEA>
   <?php endif; ?>
   <?php foreach ($nlea AS $key => $value): ?>
      <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
      <?php else: ?>
   <<?=$key;?>/>
      <?php endif; ?>
   <?php endforeach; ?>
   <?php if ( ! empty($nlea)): ?>
   </NLEA>
   <?php endif; ?>
</response>
<?php endif; ?>
<status><?=$status;?></status>
</productNLEA>
