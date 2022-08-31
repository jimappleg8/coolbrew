<div class="block" style="margin-bottom:0;">

   <div class="listing">
   <dl>
   <?php foreach($hours AS $hour): ?>
      <?php if ($hour['ID'] == $hour_id): ?>
<form id="edit_hours_item_<?=$sprint_id;?>" onsubmit="return false;">

<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="DateSpent">Date Spent:</label></dt>
      <dd><?=form_input(array('name'=>'DateSpent', 'id'=>'DateSpent', 'maxlength'=>'20', 'size'=>'45', 'value'=>$this->validation->DateSpent));?>
      <?=$this->validation->DateSpent_error;?></dd>

      <dt><label for="HoursSpent">Hours Spent:</label></dt>
      <dd><?=form_input(array('name'=>'HoursSpent', 'id'=>'HoursSpent', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->HoursSpent));?>
      <?=$this->validation->HoursSpent_error;?></dd>

      <dt><label for="Username">User:</label></dt>
      <dd><?=form_input(array('name'=>'Username', 'id'=>'Username', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Username));?>
      <?=$this->validation->Username_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="IsCapitalExpense" id="IsCapitalExpense" value="1" <?=$this->validation->set_checkbox('IsCapitalExpense', '1');?> \>  These hours can be capitalized.
      <?=$this->validation->IsCapitalExpense_error;?></dd>
   </dl>
</div>

    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes', 'onclick'=>'editHours_'.$sprint_id.'('.$hour_id.', '.$last_action.');'))?> or <a class="admin" href="#" onclick="listHours_<?=$sprint_id;?>(); return false;">Cancel</a>

</form>
      
      <?php else: ?>
   <dt style="margin:0 14px 5px 0; font-size:11px;"><a href="<?=site_url('cp/hours/edit/'.$hour['ID'].'/'.$last_action);?>" class="admin" onclick="showEditHours_<?=$sprint_id;?>(this.href); return false;">edit</a> | <a href="<?=site_url('cp/hours/delete/'.$hour['ID']);?>" class="admin" onclick="deleteHours_<?=$sprint_id;?>(this.href); return false;">delete</a></dt>
   <dd style="font-size:11px;"><?=$hour['DateSpent'];?> &mdash; <?=$hour['HoursSpent'];?> hrs. by <?=$hour['Username'];?><?php if ($hour['IsCapitalExpense'] == 1):?> (capital)<?php endif; ?>
   </dd>
      <?php endif; ?>
   <?php endforeach; ?>
   </dl>
   </div> <?php // listing ?>
   
</div>
