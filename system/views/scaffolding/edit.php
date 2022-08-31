<?php $this->load->view('header');  ?>

<?php echo form_open($action); ?>

<p class="blockintro">Edit the fields below to make changes to this record.</p>
<div class="block">
   <dl>

<?php foreach($fields as $field): ?>

   <?php if ($field->primary_key == 1) continue; ?>
   <?php $error_string = $field->name.'_error'; ?>

      <dt><label for="<?=$field->name;?>"><?=$field->name.' '.$field->default;?></label></dt>
      <?php if ($field->type == 'blob'): ?>
      <dd><?=form_textarea(array('name'=>$field->name, 'id'=>$field->name, 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->{$field->name}, 'class'=>'box'));?>
      <?=$this->validation->{$error_string};?></dd>
      <?php else: ?>
      <dd><?=form_input(array('name'=>$field->name, 'id'=>$field->name, 'size'=>'60', 'value'=>$this->validation->{$field->name}));?>
      <?=$this->validation->{$error_string};?></dd>
      <?php endif; ?>

<?php endforeach; ?>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <?=anchor(array($base_uri, 'view'), 'Cancel');?>
</div>

</form>

<?php $this->load->view('footer'); ?>