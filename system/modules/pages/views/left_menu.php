<div style="margin-top:12px;">
<?php foreach($menu AS $item): ?>
   <?php if ($item['display'] == TRUE): ?>
      <div style="margin-left:<?=($item['level']-1)*2;?>em;">
      <p style="margin:0; padding:0;<?php if ($item['hilite'] == TRUE): ?> font-weight:bold;<?php endif; ?>"><a href="<?=$item['URL'];?>" style="text-decoration:none;"><?=$item['MenuText'];?></a></p>
      </div>
   <?php endif; ?>
<?php endforeach; ?>
</div>