<div id="basic-form">

<?php echo form_open($action); ?>

<table style="border:0;" cellpadding="0" cellspacing="0">

<?php $isQuiz = FALSE; ?>

<?php foreach($fields as $field): ?>

   <?php if ($field['primary_key'] == 1) continue; ?>
   <?php if ($field['type'] == 'submit')
      {
      $submit = $field;
      continue;
      }
   ?>
   <?php if ($field['question_type'] != 'none' && $isQuiz == FALSE): ?>
      <?php $isQuiz = TRUE; ?>
   <tr>
   <td colspan="2" class="field-name" style="text-align:left; padding:18px 0 9px 20px; font-size:12px; font-weight:bold;"><?=$quiz_head;?>
   <input type="hidden" name="quiz_group" value="<?=$quiz_group;?>" />
   </td>
   </tr>
   <?php endif; ?>
   <?php $error_string = $field['name'].'_error'; ?>

   <?php if ($isQuiz == FALSE): ?>
   <tr>
   <td class="field-name"><label for="<?=$field['name'];?>"><?=$field['label'];?>:</label></td>
   <td class="field">
   <?php else: ?>
   <tr>
   <td colspan="2" class="field-name" style="text-align:left; padding-left:20px; border-top:1px solid #666;"><label for="<?=$field['name'];?>"><?=$field['question'];?></label></td>
   </tr>
   <tr>
   <td class="field-name">&nbsp;</td>
   <td class="field" style="padding-bottom:12px;">
   <?php endif; ?>

      <?php if ($field['input_type'] == 'textarea'): ?>

   <?=form_textarea(array('name'=>$field['name'], 'id'=>$field['name'], 'cols' => $field['cols'], 'rows' => $field['rows'], 'value'=>$this->validation->{$field['name']}, 'class'=>'box'));?>
   <?=$this->validation->{$error_string};?></td>

      <?php elseif ($field['input_type'] == 'dropdown'): ?>

   <?=form_dropdown($field['name'], $field['data'], $this->validation->{$field['name']});?>
   <?=$this->validation->{$error_string};?></td>

      <?php elseif ($field['input_type'] == 'checkbox'): ?>

   <input type="checkbox" name="<?=$field['name'];?>" id="<?=$field['name'];?>" value="<?=$field['value'];?>" <?=$this->validation->set_checkbox($field['name'], $field['value']);?> \><label for="<?=$field['name'];?>"> <?=$field['post_label'];?></label>
   <?=$this->validation->{$error_string};?></td>

      <?php elseif ($field['input_type'] == 'radio'): ?>

   <input type="hidden" name="<?=$field['name'];?>" value="" />
         <?php $first = TRUE; $count = 0; ?>
         <?php foreach ($field['data'] AS $key => $value): ?>
            <?php $count++; ?>
   <?php if ($first == TRUE) { $first = FALSE; } else { echo '<br />'; } ?>
   <label for="<?=$field['name'];?><?=$count;?>"><input type="radio" name="<?=$field['name'];?>" id="<?=$field['name'];?><?=$count;?>" value="<?=$key;?>" <?=$this->validation->set_radio($field['name'], $key);?> \> <?php if ($isQuiz == TRUE && $field['question_type'] == 'multiple') { echo $key.'. '; } ?><?=$value;?></label>
         <?php endforeach; ?>
   <?=$this->validation->{$error_string};?>
   </td>

      <?php elseif ($field['input_type'] == 'date'): ?>

   <?php $field['data']['time'] = $this->validation->{$field['name']}; ?>
   <?php $field['data']['prefix'] = ''; ?>
   <?php $field['data']['field_array'] = $field['name']; ?>
   <?=form_select_date($field['data']);?>
   <?=$this->validation->{$error_string};?></td>

      <?php else: ?>

   <?=form_input(array('name'=>$field['name'], 'id'=>$field['name'], 'maxlength'=>$field['limit'], 'size'=>$field['size'], 'value'=>$this->validation->{$field['name']}));?>
   <?=$this->validation->{$error_string};?></td>

      <?php endif; ?>
   </tr>
   
<?php endforeach; ?>

   <tr>
   <td class="field-name">&nbsp;</td>
   <td class="field" style="padding-top:10px;"><?=form_submit(array('name'=>$submit['name'], 'class'=>$submit['name'], 'value'=>$submit['label']))?></td>
   </tr>

</table>

<div class="action">
</div>

</form>

</div>