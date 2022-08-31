<div class="block" style="margin-bottom:0;">
<?php if ($admin['hours_exist'] == TRUE): ?>

   <div class="listing">
   <dl>
   <?php foreach($hours AS $hour): ?>
   <dt style="margin:0 14px 5px 0; font-size:11px;"><a href="<?=site_url('cp/hours/edit/'.$hour['ID'].'/'.$last_action);?>" class="admin" onclick="showEditHours_<?=$sprint_id;?>(this.href); return false;">edit</a> | <a href="<?=site_url('cp/hours/delete/'.$hour['ID']);?>" class="admin" onclick="deleteHours_<?=$sprint_id;?>(this.href); return false;">delete</a></dt>
   <dd style="font-size:11px;"><?=$hour['DateSpent'];?> &mdash; <?=$hour['HoursSpent'];?> hrs. by <?=$hour['Username'];?><?php if ($hour['IsCapitalExpense'] == 1):?> (capital)<?php endif; ?>
   </dd>
   <?php endforeach; ?>
   </dl>
   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no hours to display.</p>
   
<?php endif; ?>
   
</div>
