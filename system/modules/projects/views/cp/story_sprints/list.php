<div class="block" style="margin-bottom:0;<?php if ($current_sprint['ID'] == $sprint_id):?> background-color:#FC0;<?php else: ?> background-color:#CCC;<?php endif; ?>">
   <dl>
      <dt style="margin:0 14px 5px 0; font-size:11px;"><a href="<?=site_url('cp/story_sprints/edit/'.$story_id.'/'.$sprint_id.'/'.$last_action);?>" class="admin" onclick="showEditSprint_<?=$sprint_id;?>(this.href); return false;">edit</a> | <a href="<?=site_url('cp/story_sprints/delete/'.$story_id.'/'.$sprint_id.'/'.$last_action);?>" class="admin" onclick="deleteSprint_<?=$sprint_id;?>(this.href); return false;">delete</a></dt>
      <dd><span style="font-size:1.2em;"><strong><?=$sprint['Name'];?></strong> (<?=date('M. j, Y', strtotime($sprint['StartDate']));?> - <?=date('M. j, Y', strtotime($sprint['EndDate']));?>) <a href="<?=site_url('cp/sprints/index/'.$sprint_id);?>" class="admin">view sprint</a>
      <br /><div class="<?=strtolower(str_replace(' ', '-', $sprint['Status']));?>" style="display:inline; padding:1px 6px 2px 6px; margin-right:1em;"><?=$sprint['Status'];?></div></span>
      Estimated Hours: <?=$sprint['EstimatedHours'];?></dd>
   </dl>
</div>
