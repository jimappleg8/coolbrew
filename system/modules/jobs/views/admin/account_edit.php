<?=$this->load->view('tabs');?><div id="job-dataarea"><div class="page-header">   <div class="page-header-links">      <a class="admin" href="<?=site_url($return_url);?>">Cancel</a>   </div>   <h2>Edit Your Account</h2></div><p>Please edit the information for your account below. To change your password, just enter the new password in the password field.</p><form method="post" action="<?=site_url('admin/edit_account/'.$last_action.'/'.$return_url);?>"><div align="center"><table cellpadding="6" cellspacing="0" border="0"><tr><td colspan="2" style="text-align:left; vertical-align:top"><span style="font-size:90%; color:#F00;">*</span><span style="font-size:90%;"> denotes required field</span></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">First Name:</label><span style="font-size:80%; color:#F00;">*</span></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'FirstName', 'id'=>'FirstName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->FirstName));?><?=$this->validation->FirstName_error;?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">Last Name:</label><span style="font-size:80%; color:#F00;">*</span></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'LastName', 'id'=>'LastName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->LastName));?><?=$this->validation->LastName_error;?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">Email:</label><span style="font-size:80%; color:#F00;">*</span></td><td style="text-align:left; vertical-align:top"><?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Email));?><?=$this->validation->Email_error;?></td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">Username:</label></td><td style="text-align:left; vertical-align:top"><span style="color:#000;"><?=$this->validation->Username;?></span> (this field cannot be changed)</td></tr><tr><td style="text-align:right; vertical-align:top"><label for="Title">Password:</label></td><td style="text-align:left; vertical-align:top"><?=form_password(array('name'=>'Password', 'id'=>'Password', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Password));?><?=$this->validation->Password_error;?></td></tr><tr><td width="240"><img src="/images/dot_clear.gif" width="120" height="10" alt=""></td><td width="100%"><img src="/images/dot_clear.gif" width="100" height="10" alt=""></td></tr><tr><td colspan="2" style="text-align:left; vertical-align:top"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url($return_url);?>">Cancel</a></td></tr></table></div>      </form>   </div>