<?=$this->load->view('tabs');?><div id="job-dataarea"><div class="page-header">   <div class="page-header-links">      <a class="admin" href="<?=site_url('companies/index');?>">Cancel</a> <span class="pipe">|</span>      <a class="admin" href="<?=site_url('companies/delete/'.$cmpny_id.'/'.$last_action);?>">Delete this company</a>   </div>   <h2>Edit Company</h2></div><p>Please update the information for this company below.</p><form method="post" action="<?=site_url('companies/edit/'.$cmpny_id.'/'.$last_action);?>"><div align="center"><table cellpadding="6" cellspacing="0" border="0"><tr><td colspan="2" style="text-align:left; vertical-align:top"><span style="font-size:90%; color:#F00;">*</span><span style="font-size:90%;"> denotes required field</span></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">Company Name:</label><span style="font-size:80%; color:#F00;">*</span></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'CompanyName', 'id'=>'CompanyName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->CompanyName));?><?=$this->validation->CompanyName_error;?></td></tr><tr><td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td></tr><tr><td colspan="2" style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('companies/index');?>">Cancel</a></td></tr></table></div>      </form>   </div>