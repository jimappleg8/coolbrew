<div id="basic-form">

<?=form_open($action);?>

<table style="border:0;" cellpadding="0" cellspacing="0">

<tr>
<td class="fieldname"><label for="SenderFirstName">Your First Name:</label></td>
<td><?=form_input(array('name'=>'SenderFirstName', 'id'=>'SenderFirstName', 'maxlength'=>'255', 'size'=>'25', 'value'=>$this->validation->SenderFirstName));?>
<?php if ($this->validation->SenderFirstName_error != ''): ?>
<br /><?=$this->validation->SenderFirstName_error;?><?php endif; ?></td>
</tr>

<tr>
<td class="fieldname"><label for="SenderLastName">Your Last Name:</label></td>
<td><?=form_input(array('name'=>'SenderLastName', 'id'=>'SenderLastName', 'maxlength'=>'255', 'size'=>'25', 'value'=>$this->validation->SenderLastName));?>
<?php if ($this->validation->SenderLastName_error != ''): ?>
<br /><?=$this->validation->SenderLastName_error;?><?php endif; ?></td>
</tr>

<tr>
<td class="fieldname"><label for="SenderEmail">Your Email:</label></td>
<td><?=form_input(array('name'=>'SenderEmail', 'id'=>'SenderEmail', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->SenderEmail));?>
<?php if ($this->validation->SenderEmail_error != ''): ?>
<br /><?=$this->validation->SenderEmail_error;?><?php endif; ?></td>
</tr>

<tr>
<td class="fieldname"><label for="Message">Message to friends:<br><span style="font-weight:normal;">(optional)</span></label></td>
<td><?=form_textarea(array('name'=>'Message', 'id'=>'Message', 'cols' => 40, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->Message));?></td>
</tr>

</table>

<table style="border:0;" cellpadding="0" cellspacing="0">

<tr>
<td class="fieldname" style="text-align:left;">Friend's Email Address:</td>
<td class="fieldname" style="text-align:left; width:120px;">First Name:</td>
<td class="fieldname" style="text-align:left;">Last Name:</td>
</tr>

<?php for ($i=1; $i<=$num_friends; $i++): ?>
   <?php $email = 'Friend'.$i.'Email'; ?>
   <?php $email_error = 'Friend'.$i.'Email_error'; ?>
   <?php $fname = 'Friend'.$i.'FirstName'; ?>
   <?php $lname = 'Friend'.$i.'LastName'; ?>
<tr>
<td><?=form_input(array('name'=>$email, 'id'=>$email, 'maxlength'=>'255', 'size'=>'25', 'style'=>'width:200px', 'value'=>$this->validation->$email));?></td>
<td><?=form_input(array('name'=>$fname, 'id'=>$fname, 'maxlength'=>'255', 'size'=>'25', 'style'=>'width:120px', 'value'=>$this->validation->$fname));?></td>
<td><?=form_input(array('name'=>$lname, 'id'=>$lname, 'maxlength'=>'255', 'size'=>'25', 'style'=>'width:120px', 'value'=>$this->validation->$lname));?></td>
</tr>
   <?php if ($this->validation->$email_error != ''): ?>
<tr>
<td colspan="3"><?=$this->validation->$email_error;?></td>
</tr>
   <?php endif; ?>
<?php endfor; ?>

<?php if ($offer_sender_copy == 1): ?>
<tr>
<td colspan="3"><input type="checkbox" name="SenderCopy" id="SenderCopy" value="1" <?=$this->validation->set_checkbox('SenderCopy', '1');?> \><label for="SenderCopy"> Send me a copy of the email.</label>
<?=$this->validation->SenderCopy_error;?></td>
</tr>
<?php endif; ?>

<tr>
<td colspan="3">&nbsp;</td>
</tr>

<tr>
<td colspan="3"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Send Messages'))?></td>
</tr>

</table>

<?=form_hidden('URL', $this->validation->URL);?>

</form>

</div>