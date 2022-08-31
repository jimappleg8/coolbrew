<h2>Email job to a friend</h2>

<p><strong>Title:</strong> <span style="font-size:12px; line-height:1.6em; background:#FF9;"><?=$Title?> (<?=$JobNum?>)</span>
<br><strong>Location:</strong> <span style="font-size:12px; line-height:1.6em; background:#FF9;"><?=$LocationName?></span></p>

<p>Fill out this form to send an email message to your friend with a link to the job referenced above. The information you submit will not be stored or used for any other purpose.</p>

<div id="job-form">

<form action="/careers/email.php/<?=$JobID?>/" method="post" name="email" id="email">

<?=form_hidden('JobID', $JobID);?>

<table width="450" border="0">

<tr>
<td colspan="2"><span class="required">*</span><span style="font-size:90%;"> denotes required field</span></td>
</tr>

<tr>
<td colspan="2"><h3>Contact Information</h3></td>
</tr>

<tr>
<td class="label"><label for="FriendEmail">Your Friend's Email:</label><span class="required">*</span></td>
<td>
<?=form_input(array('name'=>'FriendEmail', 'id'=>'FriendEmail', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->FriendEmail));?>
<?=$this->validation->FriendEmail_error;?></td>
</tr>

<tr>
<td class="label"><label for="YourName">Your Name:</label><span class="required">*</span></td>
<td>
<?=form_input(array('name'=>'YourName', 'id'=>'YourName', 'maxlength'=>'100', 'size'=>'40', 'value'=>$this->validation->YourName));?>
<?=$this->validation->YourName_error;?></td>
</tr>

<tr>
<td class="label"><label for="YourEmail">Your Email:</label><span class="required">*</span></td>
<td>
<?=form_input(array('name'=>'YourEmail', 'id'=>'YourEmail', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->YourEmail));?>
<?=$this->validation->YourEmail_error;?></td>
</tr>

<tr>
<td class="label"><label for="Subject">Subject:</label></td>
<td>
<?=form_input(array('name'=>'Subject', 'id'=>'Subject', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Subject));?>
<?=$this->validation->Subject_error;?></td>
</tr>

<tr>
<td colspan="2"><h3>Personal Message:<span class="required">*</span></h3></td>
</tr>

<tr>
<td colspan="2">
<?=form_textarea(array('name'=>'Message', 'id'=>'Message', 'cols' => 62, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->Message));?>
<?=$this->validation->Message_error;?></td>
</tr>

<tr>
<td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td>
</tr>

<tr>
<td colspan="2"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Send email'))?> or <a href="/careers/detail.php/<?=$JobID?>/">Cancel</a></td>
</tr>

</table>

</form>

</div>
