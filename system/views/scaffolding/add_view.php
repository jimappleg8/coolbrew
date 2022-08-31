&lt;?php  $this->load->view('header'); ?&gt;

&lt;?php echo form_open($action); ?&gt;

<p class="blockintro">Fill out the fields below to create a new record.</p>
<div class="block">
   <dl>
<?php foreach($fields as $field): ?>
<?php if ($field->primary_key == 1) continue; ?>
      <dt><label for="<?=$field->name;?>"><?=$field->name;?></label></dt>
<?php if ($field->type == 'blob'): ?>
      <dd>&lt;?=form_textarea(array('name'=>'<?=$field->name?>', 'id'=>'<?=$field->name?>', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation-><?=$field->name?>, 'class'=>'box'));?&gt;
      &lt;?=$this->validation-><?=$field->name?>_error;?&gt;</dd>
<?php else: ?>
      <dd>&lt;?=form_input(array('name'=>'<?=$field->name?>', 'id'=>'<?=$field->name?>', 'maxlength'=>'<?=$field->max_length;?>', 'size'=>'60', 'value'=>$this->validation-><?=$field->name?>));?&gt;
      &lt;?=$this->validation-><?=$field->name?>_error;?&gt;</dd>
<?php endif; ?>
<?php endforeach; ?>
   </dl>
</div>

<div class="action">
   &lt;?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Insert'))?&gt; or &lt;?=anchor(array($base_uri, 'view'), 'Cancel');?&gt;
</div>

</form>

&lt;?php $this->load->view('footer'); ?&gt;
