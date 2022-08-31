<?php /* list the other sites for this brand */ ?>

      <h1>All sites for this brand</h1>
      <div class="indent">
      <ul>
   <?php foreach ($brand_sites AS $bsite): ?>
      <?php if ($bsite['SiteID'] == $site_id): ?>
      <li><strong><?=$bsite['Domain'];?></strong></li>
      <?php elseif (($admin['group'] == 'admin') || ($this->administrator->check_acl($bsite['SiteID'].'-site', 'view') == TRUE)): ?>
      <li><a href="<?=site_url('sites/dashboards/index/'.$bsite['SiteID']);?>"><?=$bsite['Domain'];?></a></li>
      <?php else: ?>
      <li><?=$bsite['Domain'];?></li>
      <?php endif; ?>
   <?php endforeach; ?>
      </ul>
      </div>