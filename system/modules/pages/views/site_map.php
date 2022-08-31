
<?php foreach ($site_data AS $page): ?>

   <?php if ($page['level'] > 0): ?>
   <div style="margin-left:<?=($page['level']-1)*2;?>em;<?php if ($page['level'] == 1): ?> margin-top:1em; border-top:1px solid #999;<?php endif; ?>">
   <p style="margin:0; padding:4px 0;"><a href="<?=$page['URL'];?>"style="text-decoration:none;"><?=$page['MenuText'];?></a></p>
   </div>
   <?php endif; ?>

<?php endforeach; ?>
   
