<div class="block" style="margin-bottom:0;">
<?php if ($recipes['site_exists'] == true): ?>

   <div class="listing">
   <dl>
   <dt style="font-size:11px;"><label>Sites:</label></dt>
   <?php $cnt = 1; ?>
   <?php foreach($sites AS $site): ?>
   <?php if ($cnt > 1): ?><dt style="font-size:11px;"></dt><?php endif; ?>
   <?php $cnt++; ?>
   <dd style="font-size:11px;<?php if ($site['SiteID'] == $site_id): ?> font-weight:bold;<?php endif; ?>"><?=$site['Domain'];?> (<?=$site['SiteID'];?>) <?php if (count($sites) > 1): ?><a href="<?=site_url('sites/delete/'.$site_id.'/'.$recipe_id.'/'.$site['SiteID']);?>" class="admin" onclick="deleteSite(this.href); return false;">remove</a><?php endif; ?></dd>
   <?php endforeach; ?>
   </dl>
   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no sites to display.</p>
   
<?php endif; ?>
   
</div>
