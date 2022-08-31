<?php  $this->load->view('header');  ?>

<?php echo form_open($action); ?>

<p class="blockintro">Fill out the fields below to create a new record.</p>
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
      <dd><?=form_input(array('name'=>$field->name, 'id'=>$field->name, 'maxlength'=>$field->max_length, 'size'=>'60', 'value'=>$this->validation->{$field->name}));?>
      <?=$this->validation->{$error_string};?></dd>
      <?php endif; ?>

<?php endforeach; ?>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Insert'))?> or <?=anchor(array($base_uri, 'view'), 'Cancel');?>
</div>

</form>

<?php $this->load->view('footer'); ?>
