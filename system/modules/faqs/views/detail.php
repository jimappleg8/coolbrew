<?php if ($faq['Status'] == 'active'): ?>

   <p>
   <?php if ($faq['FlagAsNew'] == 1): ?>
      <span style="color:red;">NEW!</span>&nbsp;&nbsp;
   <?php endif; ?>
   <b><?=nl2br($faq['Question']);?></b></p>
   <?=$faq['Answer'];?>

<?php else: ?>

   <p>This FAQ is no longer available.</p>

<?php endif; ?>