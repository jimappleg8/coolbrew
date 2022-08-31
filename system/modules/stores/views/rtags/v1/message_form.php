<body onunload="GUnload()" id="message-page">

	<div class="popup">
	<a href="javascript:window.close();">Close Window</a>
	</div>

<?php if ($display_response == FALSE): ?>

<form action="<?=$action;?>" method="post" name="message" id="message">

<div id="message">

<h1>Tell Us About This Store</h1>

<div class="store">
   <?=stripslashes($this->validation->StoreName);?>
   <br /><?=stripslashes($this->validation->Address1);?>
   <?php if ($this->validation->Address2 != ''): ?><br /><?=stripslashes($this->validation->Address2);?><?php endif; ?>
   <br /><?=stripslashes($this->validation->City);?>, <?=$this->validation->State;?> <?=$this->validation->Zip;?> 
   <br /><?=$this->validation->Phone;?>
</div>

<p><strong>Thank you</strong> for helping us keep our store locator current. Please tell us what you know about the store below and we will make sure our data is updated.</p>

<p style="font-size:90%; color:#666;">All fields are required.</p>

<?=form_hidden('StoreID', $this->validation->StoreID);?>
<?=form_hidden('StoreName', $this->validation->StoreName);?>
<?=form_hidden('Address1', $this->validation->Address1);?>
<?=form_hidden('Address2', $this->validation->Address2);?>
<?=form_hidden('City', $this->validation->City);?>
<?=form_hidden('State', $this->validation->State);?>
<?=form_hidden('Zip', $this->validation->Zip);?>
<?=form_hidden('Phone', $this->validation->Phone);?>
<?=form_hidden('ProductID', $this->validation->ProductID);?>
<?=form_hidden('ProductName', $this->validation->ProductName);?>
<?=form_hidden('redirect', 'message');?>

<table border="0">

<tr>
<td style="text-align:right; vertical-align:top"><label for="FirstName">Your Name:</label></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'FirstName', 'id'=>'FirstName', 'maxlength'=>'25', 'size'=>'15', 'value'=>$this->validation->FirstName));?>
<?=$this->validation->FirstName_error;?>&nbsp; &nbsp;<?=form_input(array('name'=>'LastName', 'id'=>'LastName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LastName));?>
<?=$this->validation->LastName_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Email">Your Email:</label></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
<?=$this->validation->Email_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top">&nbsp;</td>
<td style="text-align:left; vertical-align:top">
<input type="checkbox" name="Affiliated" id="Affiliated" value="1" <?=$this->validation->set_checkbox('Affiliated', '1');?> \>  I am the owner or an employee of this store.
<?=$this->validation->Affiliated_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Message">Message:</label></td>
<td style="text-align:left; vertical-align:top">
<?=form_textarea(array('name'=>'Message', 'id'=>'Message', 'cols' => 40, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->Message));?>
<?=$this->validation->Message_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top">&nbsp;</td>
<td style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Send Message'))?></td>
</tr>

<tr>
<td width="120"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="120" height="1" alt=""></td>
<td width="100%"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="100" height="1" alt=""></td>
</tr>

</table>

</div>

</form>

<?php else: ?>

<div id="message">

<h1>Tell Us About This Store</h1>

<div class="success">
<p style="font-size:110%;"><strong>Your message has been sent!</strong> Thank you for taking the time to help us keep our store locator current.</p>

<p>The <?=$brand_name;?> Store Locator Team</p>
</div>

</div>

<?php endif; ?>

</body>