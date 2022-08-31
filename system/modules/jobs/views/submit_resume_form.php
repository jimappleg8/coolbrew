<h2>Submit your resume</h2>

<div id="job-form">

<form action="/careers/submit_resume.php/<?=$job_id;?>/<?=$loc_id;?>/<?=$cat_id;?>/" method="post" name="submit_resume" id="submit_resume">

<?=form_hidden('JobID', $JobID);?>
<?=form_hidden('Subject', $Subject);?>

<p>For: <span style="font-size:12px; line-height:1.6em; background:#FF9;"><?php if ($Title != "None"): ?><?=$Title?> (<?=$JobNum?>)<?php else: ?>any positions that become available at the following location and job category...<?php endif; ?></span></p>

<table width="450" border="0">

<tr>
<td colspan="2"><span class="required">*</span><span style="font-size:90%;"> denotes required field</span></td>
</tr>

<tr>
<td colspan="2"><h3>Location and Category</h3></td>
</tr>

<?php if ($Title != "None"): ?>

   <tr>
   <td style="text-align:right; vertical-align:top"><label for="LocationID">Location:</label></td>
   <td style="text-align:left; vertical-align:top">
   <span style="font-size:12px; line-height:1.6em; background:#FF9;"><?=$all_locations[$LocationID]?></span>
   <?=form_hidden('LocationID', $LocationID);?>
</td>
   </tr>

   <tr>
   <td style="text-align:right; vertical-align:top"><label for="CategoryID">Category:</label></td>
   <td style="text-align:left; vertical-align:top">
   <span style="font-size:12px; line-height:1.6em; background:#FF9;"><?=$categories[$CategoryID]?></span>
   <?=form_hidden('CategoryID', $CategoryID);?>
   </td>
   </tr>

<?php else: ?>

   <?php if ($LocationID == 3): ?>

   <tr>
   <td style="text-align:right; vertical-align:top"><label for="LocationID">Location:</label></td>
   <td style="text-align:left; vertical-align:top">
   <span style="font-size:12px; line-height:1.6em; background:#FF9;">Canada</span>
   <?=form_hidden('LocationID', $LocationID);?>
   </td>
   </tr>

   <?php elseif ($LocationID == 5): ?>

   <tr>
   <td style="text-align:right; vertical-align:top"><label for="LocationID">Location:</label></td>
   <td style="text-align:left; vertical-align:top">
   <span style="font-size:12px; line-height:1.6em; background:#FF9;">Europe</span></td>
   <?=form_hidden('LocationID', $LocationID);?>
   </tr>

   <?php else: ?>

   <tr>
   <td style="text-align:right; vertical-align:top"><label for="LocationID">Location:</label><span style="font-size:80%; color:#F00;">*</span></td>
   <td style="text-align:left; vertical-align:top">
   <?=form_dropdown('LocationID', $locations, $this->validation->LocationID);?>
   <?=$this->validation->LocationID_error;?></td>
   </tr>

   <?php endif; ?>

   <tr>
   <td style="text-align:right; vertical-align:top"><label for="CategoryID">Category:</label><span style="font-size:80%; color:#F00;">*</span></td>
   <td style="text-align:left; vertical-align:top">
   <?=form_dropdown('CategoryID', $categories, $this->validation->CategoryID);?>
   <?=$this->validation->CategoryID_error;?></td>
   </tr>

<?php endif; ?>

<tr>
<td colspan="2"><h3>Contact Information</h3></td>
</tr>

<tr>
<td class="label"><label for="FName">First Name:</label><span class="required">*</span></td>
<td>
<?=form_input(array('name'=>'FName', 'id'=>'FName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->FName));?>
<?=$this->validation->FName_error;?></td>
</tr>

<tr>
<td class="label"><label for="MName">Middle Name or Initial:</label></td>
<td>
<?=form_input(array('name'=>'MName', 'id'=>'MName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->MName));?>
<?=$this->validation->MName_error;?></td>
</tr>

<tr>
<td class="label"><label for="LName">Last Name:</label><span class="required">*</span></td>
<td>
<?=form_input(array('name'=>'LName', 'id'=>'LName', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->LName));?>
<?=$this->validation->LName_error;?></td>
</tr>

<tr>
<td class="label"><label for="Address">Address:</label></td>
<td>
<?=form_textarea(array('name'=>'Address', 'id'=>'Address', 'cols' => 40, 'rows' => 4, 'value'=>$this->validation->Address));?>
<?=$this->validation->Address_error;?></td>
</tr>

<tr>
<td class="label"><label for="HomePhone">Home Phone:</label><span class="required">*</span></td>
<td>
<?=form_input(array('name'=>'HomePhone', 'id'=>'HomePhone', 'maxlength'=>'14', 'size'=>'14', 'value'=>$this->validation->HomePhone));?>
<?=$this->validation->HomePhone_error;?></td>
</tr>

<tr>
<td class="label"><label for="WorkPhone">Work Phone:</label></td>
<td>
<?=form_input(array('name'=>'WorkPhone', 'id'=>'WorkPhone', 'maxlength'=>'14', 'size'=>'14', 'value'=>$this->validation->WorkPhone));?>
<?=$this->validation->WorkPhone_error;?></td>
</tr>

<tr>
<td class="label"><label for="Email">Email:</label><span class="required">*</span></td>
<td style="text-align:left; vertical-align:top">
<?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?>
<?=$this->validation->Email_error;?></td>
</tr>

<tr>
<td colspan="2"><h3>Resume:<span class="required">*</span></h3></td>
</tr>

<tr>
<td colspan="2">Plain text only.</td>
</tr>

<tr>
<td colspan="2">
<?=form_textarea(array('name'=>'Resume', 'id'=>'Resume', 'cols' => 62, 'rows' => 20, 'wrap' => "virtual", 'value'=>$this->validation->Resume));?>
<?=$this->validation->Resume_error;?></td>
</tr>

<tr>
<td colspan="2"><h3>Cover Letter:</h3></td>
</tr>

<tr>
<td colspan="2">
<?=form_textarea(array('name'=>'CoverLtr', 'id'=>'CoverLtr', 'cols' => 62, 'rows' => 10, 'wrap' => "virtual", 'value'=>$this->validation->CoverLtr));?>
<?=$this->validation->CoverLtr_error;?></td>
</tr>

<tr>
<td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td>
</tr>

<tr>
<td colspan="2"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Submit your resume'))?> or <?php if ($Title != "None"): ?><a href="/careers/detail.php/<?=$JobID;?>/">Cancel</a><?php elseif ($LocationID == 3 || $LocationID == 5): ?><a href="/careers/index.php">Cancel</a><?php else: ?><a href="/careers/positions.php">Cancel</a><?php endif; ?></td>
</tr>

</table>

</form>

</div>
