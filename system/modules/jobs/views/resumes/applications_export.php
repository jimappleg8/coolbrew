<?=$this->load->view('tabs');?>

<div id="job-dataarea">

<div class="page-header">
   <div class="page-header-links">
   </div>
   <h2>Export Job Applications</h2>
</div>

<p>Enter the date range you want for the export.</p>

<form method="post" action="<?=site_url('resumes/export_applications');?>">

<div align="center">

<table cellpadding="6" cellspacing="0" border="0">

<tr>
<td colspan="2" style="text-align:left; vertical-align:top"><span style="font-size:90%; color:#F00;">*</span><span style="font-size:90%;"> denotes required field</span></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="StartDate">Start Date:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'StartDate', 'id'=>'StartDate', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->StartDate));?> <span style="font-size:0.8em;">format: YYYY-MM-DD</span>
<?=$this->validation->StartDate_error;?></td>
</tr>

<tr>
<td style="text-align:right; vertical-align:top"><label for="EndDate">End Date:</label><span style="font-size:80%; color:#F00;">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'EndDate', 'id'=>'EndDate', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->EndDate));?> <span style="font-size:0.8em;">format: YYYY-MM-DD</span>
<?=$this->validation->EndDate_error;?></td>
</tr>

<tr>
<td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td>
</tr>

<tr>
<td colspan="2" style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Export Data'))?></td>
</tr>

</table>

</div>
      
</form>
   
</div>