
<form method="post" action="/careers/positions.php">

<div align="center">

<table cellpadding="6" cellspacing="0" border="0">

<tr>
<td style="text-align:right; vertical-align:top"><label for="LocationID">Location:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_dropdown('LocationID', $locations, $this->validation->LocationID);?>
<?=$this->validation->LocationID_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="CategoryID">Category:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_dropdown('CategoryID', $categories, $this->validation->CategoryID);?>
<?=$this->validation->CategoryID_error;?></td>
</tr>

<tr>
<td>&nbsp;</td>
<td style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Continue'))?></td>
</tr>

<tr>
<td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td>
</tr>

</table>

</div>
      
</form>
