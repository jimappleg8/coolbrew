<?php /* list the domains for this site */ ?>
   
      <h1 style="margin-top:18px;">Domains pointing to this site</h1>
   <?php foreach ($domains AS $key => $site): ?>
      <div class="indent">
      <ul>
      <?php for ($j=0; $j<count($site['domains']); $j++): ?>
         <li><?php if ($site['domains'][$j]['PrimaryDomain'] == 1): ?><span style="color:#960;"><?php endif; ?><?=$site['domains'][$j]['Domain'];?><?php if ($site['domains'][$j]['PrimaryDomain'] == 1): ?> (primary)</span><?php endif; ?></li>
      <?php endfor; ?>
      </ul>
      </div>
   <?php endforeach; ?>