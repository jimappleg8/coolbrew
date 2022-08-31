<?=$this->load->view('tabs');?><div id="read-only"><p>This job is <b>ON HOLD</b> and cannot be edited. To edit it, either copy it to a new job or re-activate it. <a class="admin" href="<?=site_url('jobs/index');?>">Return to open jobs</a></p></div><div id="job-dataarea"><div class="page-header">   <div class="page-header-links">      <a class="admin" href="<?=site_url('jobs/onhold');?>">Close Window</a> <span class="pipe">|</span>       <a class="admin" href="<?=site_url('jobs/copy/'.$job_id);?>">Copy to new job</a> <span class="pipe">|</span>       <a class="admin" href="<?=site_url('jobs/reactivate/'.$job_id.'/'.$last_action);?>">Re-activate this job listing</a>   </div>   <h2>View On-Hold Job Listing</h2></div><form method="post" action="<?=site_url('jobs/view_onhold/'.$job_id.'/'.$last_action);?>"><div align="center"><table cellpadding="6" cellspacing="0" border="0"><tr><td style="text-align:right; vertical-align:top"><label for="Title">Job No.:</label></td><td style="text-align:left; vertical-align:top"><span style="font-size:12px; color:#000;"><?=$job_num;?></span></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">Job Title:</label></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Title));?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="OnHoldDate"><span style="color:#F00;">On-Hold Date:</span></label></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'OnHoldDate', 'id'=>'OnHoldDate', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->OnHoldDate));?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="OnHoldByName"><span style="color:#F00;">Put On Hold By:</span></label></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'OnHoldByName', 'id'=>'OnHoldByName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->OnHoldByName));?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="OnHoldNotes"><span style="color:#F00;">On-Hold Notes:</span></label></td><td style="text-align:left; vertical-align:top"><?=form_textarea(array('name'=>'OnHoldNotes', 'id'=>'OnHoldNotes', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->OnHoldNotes, 'class'=>'box'));?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="LocationID">Location:</label></td><td style="text-align:left; vertical-align:top"><?=form_dropdown('LocationID', $locations, $this->validation->LocationID);?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="CategoryID">Category:</label></td><td style="text-align:left; vertical-align:top"><?=form_dropdown('CategoryID', $categories, $this->validation->CategoryID);?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="CompanyID">Company:</label></td><td style="text-align:left; vertical-align:top"><?=form_dropdown('CompanyID', $companies, $this->validation->CompanyID);?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Manager">Manager:</label></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'Manager', 'id'=>'Manager', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Manager));?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Summary">Summary:</label></td><td style="text-align:left; vertical-align:top"><?=form_textarea(array('name'=>'Summary', 'id'=>'Summary', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Summary, 'class'=>'box'));?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Description">Job Description:</label></td><td style="text-align:left; vertical-align:top"><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 60, 'rows' => 20, 'value'=>$this->validation->Description, 'class'=>'box'));?></td></tr><tr><td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td></tr><tr><td colspan="2" style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Close window'))?></td></tr></table></div>      </form>   </div>