<?=$this->load->view('tabs');?>

<div id="job-dataarea">

<div class="page-header">
   <div class="page-header-links">
   <a class="admin" href="<?=site_url('jobs/index');?>">Cancel</a>
   </div>
   <h2>Add a Job Listing</h2>
</div>

<p>You may add your job listing below. Please note that you are able to include HTML in your summary and description if you would like to.</p>

<form method="post" action="<?=site_url('jobs/add/'.$last_action);?>">

<div align="center">

<table cellpadding="6" cellspacing="0" border="0">

<tr>
<td colspan="2" style="text-align:left; vertical-align:top"><span style="font-size:90%; color:#F00;">*</span><span style="font-size:90%;"> denotes required field</span></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Title">Job Title:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Title));?>
<?=$this->validation->Title_error;?></td>
</tr>

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
<td style="text-align:right; vertical-align:top"><label for="CompanyID">Company:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_dropdown('CompanyID', $companies, $this->validation->CompanyID);?>
<?=$this->validation->CompanyID_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Manager">Manager:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'Manager', 'id'=>'Manager', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Manager));?>
<?=$this->validation->Manager_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Summary">Summary:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_textarea(array('name'=>'Summary', 'id'=>'Summary', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Summary, 'class'=>'box'));?>
<?=$this->validation->Summary_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Description">Job Description:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 60, 'rows' => 20, 'value'=>$this->validation->Description, 'class'=>'box'));?>
<?=$this->validation->Description_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="Status">Status:</label></td>
<td style="text-align:left; vertical-align:top">
<input type="checkbox" name="Status" id="Status" value="1" <?=$this->validation->set_checkbox('Status', '1');?> />  Publish this job
<?=$this->validation->Status_error;?></td>
</tr>

<tr>
<td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td>
</tr>

<tr>
<td colspan="2" style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this job listing'))?> or <a class="admin" href="<?=site_url('jobs/index');?>">Cancel</a></td>
</tr>

</table>

</div>
      
</form>
   
</div>