
<form action="/contact-us/webmaster.php" method="post" name="webmaster" id="webmaster">

<div>

<?=form_hidden('siteid', SITE_ID);?>


<table border="0">

<tr>
<td style="text-align:right; vertical-align:top"><label for="FName">First Name:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->FName));?>
<?=$this->validation->FName_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="LName">Last Name:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LName));?>
<?=$this->validation->LName_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Email">Email:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
<?=$this->validation->Email_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Email2">Please Confirm Your Email:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'Email2', 'id'=>'Email2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email2));?>
<?=$this->validation->Email2_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Comment">Message:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_textarea(array('name'=>'Comment', 'id'=>'Comment', 'cols' => 40, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->Comment));?>
<?=$this->validation->Comment_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top">&nbsp;</td>
<td style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Send Message'))?></td>
</tr>

<tr>
<td width="120"><img src="/images/spacer.gif" width="120" height="1" alt=""></td>
<td width="100%"><img src="/images/spacer.gif" width="100" height="1" alt=""></td>
</tr>

<tr>
<td></td>
<td style="text-align:left; vertical-align:top"><span style="font-size:80%; color:#F00;">*</span><span style="font-size:80%;"> denotes required field</span></td>
</tr>

</table>

</div>

</form>

