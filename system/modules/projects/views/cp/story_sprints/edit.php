<form id="edit_sprint_item_<?=$sprint_id;?>" onsubmit="return false;">

<div class="block" style="margin-bottom:0;<?php if ($current_sprint['ID'] == $sprint_id):?> background-color:#FC0;<?php else: ?> background-color:#CCC;<?php endif; ?>">
   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>

      <dt><label for="EstimatedHours">Estimated Hours:</label></dt>
      <dd><?=form_input(array('name'=>'EstimatedHours', 'id'=>'EstimatedHours', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->EstimatedHours));?>
      <?=$this->validation->EstimatedHours_error;?></dd>
   </dl>
</div>

    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes', 'onclick'=>'editSprint_'.$sprint_id.'('.$story_id.', '.$sprint_id.', '.$last_action.');'))?> or <a class="admin" href="#" onclick="listSprint_<?=$sprint_id;?>(); return false;">Cancel</a>

</form>
