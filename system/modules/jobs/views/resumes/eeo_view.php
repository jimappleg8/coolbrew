<div id="job-dataarea">

<div class="page-header">
   <div class="page-header-links">&nbsp;</div>
   <h2>Vountary applicant affirmative action information</h2>
</div>

<?php if ($this->validation->EEODate == '0000-00-00'): ?>

<table width="450" border="0">

<tr>
<td>This applicant did not fill out the EEO form.</td>
</tr>

</table>

<?php else: ?>

<div id="job-form">

<form action="<?=site_url('resumes/view_eeo/'.$resume_id);?>" method="post">

<table width="450" border="0">

<tr>
<td colspan="3">In an effort to implement our government affirmative action program record keeping and reporting requirements, we ask that you complete this data survey.  Your cooperation is appreciated.  Providing this information is STRICTLY VOLUNTARY.  Failure to provide it will not affect the decision regarding your potential employment.  This form is not to be considered a part of the application for employment, is not used for interview purposes and is filed separately with the EEO records.  All information will be considered strictly private and confidential and will be used for EEO purposes only.</td>
</tr>

<tr>
<td colspan="3"><img src="/images/dot_clear.gif" width="10" height="10" alt=""></td>
</tr>

<tr>
<td colspan="3"><h2>Gender</h2></td>
</tr>

<tr>
<td colspan="3"><label for="EEOGender">Choose one:</label></td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEOGender" id="EEOGender" value="male" <?=$this->validation->set_radio('EEOGender', 'male');?> \></td>
<td colspan="2">Male</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEOGender" id="EEOGender" value="female" <?=$this->validation->set_radio('EEOGender', 'female');?> \></td>
<td colspan="2">Female</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEOGender" id="EEOGender" value="no_response" <?=$this->validation->set_radio('EEOGender', 'no_response');?> \></td>
<td colspan="2">I choose not to respond</td>
</tr>

<tr>
<td colspan="3"><img src="/images/dot_clear.gif" width="10" height="10" alt=""></td>
</tr>

<tr>
<td colspan="3"><h2>Ethnicity</h2></td>
</tr>

<tr>
<td colspan="3"><label for="EEOEthnicity">1. Are you Hispanic or Latino?  <b>HISPANIC</b> - A person of Cuban, Mexican, Puerto Rican, South or Central American, or other Spanish culture or origin, regardless of race.</label></td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEOEthnicity" id="EEOEthnicity" value="yes" <?=$this->validation->set_radio('EEOEthnicity', 'yes');?> \></td>
<td colspan="2">Yes</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEOEthnicity" id="EEOEthnicity" value="no" <?=$this->validation->set_radio('EEOEthnicity', 'no');?> \></td>
<td colspan="2">No</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEOEthnicity" id="EEOEthnicity" value="no_response" <?=$this->validation->set_radio('EEOEthnicity', 'no_response');?> \></td>
<td colspan="2">I choose not to respond</td>
</tr>

<tr>
<td colspan="3"><img src="/images/dot_clear.gif" width="10" height="10" alt=""></td>
</tr>

<tr>
<td colspan="3"><h2>Race</h2></td>
</tr>

<tr>
<td colspan="3"><p>To assist in appropriate identification, an applicant may be included in the group to which he or she appears to belong, identifies with, or is regarded in the community as belonging in accordance with definitions below.</p></td>
</tr>

<tr>
<td colspan="3"><label for="EEORace">2. If you answered no to Question #1, please choose one of the following:</label></td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="white" <?=$this->validation->set_radio('EEORace', 'white');?> \></td>
<td colspan="2"><b>WHITE</b> - A person having origins in any of the original peoples of Europe, North Africa, or the Middle East.</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="black" <?=$this->validation->set_radio('EEORace', 'black');?> \></td>
<td colspan="2"><b>BLACK OR AFRICAN AMERICAN</b> - A person having origins in any of the Black racial groups of Africa.</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="islander" <?=$this->validation->set_radio('EEORace', 'islander');?> \></td>
<td colspan="2"><b>NATIVE HAWAIIAN OR OTHER PACIFIC ISLANDER</b> - A person having origins in any of the original peoples of Hawaii, Guam, Samoa, or other Pacific Islands.</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="asian" <?=$this->validation->set_radio('EEORace', 'asian');?> \></td>
<td colspan="2"><b>ASIAN</b> - A person having origins in any of the original peoples of the Far East, Southeast Asia, or the Indian subcontinent including, for example, Cambodia, China, India, Japan, Korea, Malaysia, Pakistan, the Philippine Islands, Thailand and Vietnam.</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="native" <?=$this->validation->set_radio('EEORace', 'native');?> \></td>
<td colspan="2"><b>AMERICAN INDIAN OR ALASKAN NATIVE</b> - A person having origins in any of the original peoples of North America and South America (including Central America), and who maintains tribal affiliation or community recognition.</td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="multi" <?=$this->validation->set_radio('EEORace', 'multi');?> \></td>
<td colspan="2"><b>TWO OR MORE RACES (Not Hispanic or Latino)</b> - A person who identifies with more than one of the above five races.
<br />Please list the one race above with which you most strongly identify: <?=form_dropdown('EEOMultiPrime', $multi, $this->validation->EEOMultiPrime);?></td>
</tr>

<tr>
<td class="label"><input type="radio" name="EEORace" id="EEORace" value="no_response" <?=$this->validation->set_radio('EEORace', 'no_response');?> \></td>
<td colspan="2">I choose not to respond</td>
</tr>

<tr>
<td colspan="3"><img src="/images/dot_clear.gif" width="10" height="10" alt=""></td>
</tr>

<tr>
<td colspan="3"><h2>Signature</h2></td>
</tr>

<tr>
<td colspan="2" class="label"><label for="Name">Name:</label></td>
<td><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->Name));?>
<?=$this->validation->Name_error;?></td>
</tr>

<tr>
<td colspan="2" class="label"><label for="EEOSignature">E-Signature:</label></td>
<td><?=form_input(array('name'=>'EEOSignature', 'id'=>'EEOSignature', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->EEOSignature));?>
<?=$this->validation->EEOSignature_error;?> (if available)</td>
</tr>

<tr>
<td colspan="2" class="label"><label for="EEODate">Date:</label></td>
<td><?=form_input(array('name'=>'EEODate', 'id'=>'EEODate', 'maxlength'=>'25', 'size'=>'25', 'value'=>$this->validation->EEODate));?>
<?=$this->validation->EEODate_error;?></td>
</tr>

<tr>
<td colspan="3"><img src="/images/dot_clear.gif" width="10" height="10" alt=""></td>
</tr>

<tr>
<td colspan="3">TO BE FILED SEPARATELY FROM THE APPLICATION FORM</td>
</tr>

<tr>
<td colspan="3"><img src="/images/dot_clear.gif" width="10" height="10" alt=""></td>
</tr>

<tr>
<td colspan="3">Hain Celestial is fully committed to equal employment opportunity as a matter of company policy.  Our policy provides all employees and applicants equal opportunity for employment without regard to race, color, religion, gender, national origin, age, veteran status, physical or mental handicap or disability, sexual orientation, marital status, or other non job related characteristics.  This policy covers employment practices such as recruitment, hiring, placement, training, upgrading, promotion, transfer and rates of pay.  Applicants and employees should expect a workplace environment free from discrimination or harassment in any form; employees are expected to conduct themselves with consideration and respect for the dignity of their co-workers.</td>
<tr>

<tr>
<td width="40"><img src="/images/dot_clear.gif" width="40" height="20" alt=""></td>
<td width="60"><img src="/images/dot_clear.gif" width="60" height="20" alt=""></td>
<td width="100%"><img src="/images/dot_clear.gif" width="100" height="20" alt=""></td>
</tr>

<tr>
<td colspan="3"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Close the window'))?></td>
</tr>

</table>

</form>

</div>

<?php endif; ?>

</div>
