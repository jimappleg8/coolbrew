<body><?=$this->load->view('sites/tabs');?><div id="Wrapper">     <div class="container">   <table class="layout">   <tr>   <td class="left">      <div class="Left">         <div class="col">                            <div class="page-header">               <div class="page-header-links">      <a class="admin" href="<?=site_url('sites/people/index/'.$site_id);?>">Cancel</a> |       <a class="admin" href="<?=site_url('sites/people/remove/'.$site_id.'/'.$username);?>">Remove from this site</a>               </div>   <!-- page_header_links -->   <h1>Edit this Person</h1>            </div>   <!-- page_header -->            <div class="innercol">                           <div id="basic-form"><form method="post" action="<?=site_url('sites/people/edit/'.$username.'/'.$last_action);?>"><p class="blockintro">This person's name will be used whenever we need to identify who is responsible for a project, schedule, etc.</p><div class="block">   <dl>      <dt class="required"><label for="FirstName">First Name:</label></dt>      <dd><?=form_input(array('name'=>'FirstName', 'id'=>'FirstName', 'maxlength'=>'100', 'size'=>'30', 'value'=>$this->validation->FirstName));?>      <?=$this->validation->FirstName_error;?></dd>      <dt class="required"><label for="LastName">Last Name:</label></dt>      <dd><?=form_input(array('name'=>'LastName', 'id'=>'LastName', 'maxlength'=>'100', 'size'=>'30', 'value'=>$this->validation->LastName));?>      <?=$this->validation->LastName_error;?></dd>      <dt class="required"><label for="Email">Email:</label></dt>      <dd><?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'200', 'size'=>'30', 'value'=>$this->validation->Email));?>      <?=$this->validation->Email_error;?></dd>      <dt class="required"><label for="CompanyID">Company:</label></dt>      <dd><?=form_dropdown('CompanyID', $companies, $this->validation->CompanyID);?>      <?=$this->validation->CompanyID_error;?></dd>      <dt class="required"><label for="GroupName">Group:</label></dt>      <dd><?=form_dropdown('GroupName', $groups, $this->validation->GroupName);?>      <?=$this->validation->GroupName_error;?></dd>   </dl></div><p class="blockintro">Choose a user name and password so that this person can log in (they can change this later).</p><div class="block">   <dl>      <dt class="required"><label for="Username">Username:</label></dt>      <dd style="padding:3px 0 6px 0;"><span style="color:#000; font-size:12px;"><?=$this->validation->Username;?></span> (this field cannot be changed)</dd>      <dt class="required"><label for="Password">Password:</label></dt>      <dd><?=form_password(array('name'=>'Password', 'id'=>'Password', 'maxlength'=>'40', 'size'=>'30', 'value'=>$this->validation->Password));?>      <?=$this->validation->Password_error;?></dd>      <dt>&nbsp;</dt>      <dd><input type="checkbox" name="ResendEmail" id="ResendEmail" value="1" <?=$this->validation->set_checkbox('ResendEmail', '1');?> \>  Resend email to user with login information      <?=$this->validation->ResendEmail_error;?></dd>   </dl></div><p class="blockintro">The rest is optional, but some contact info will come in handy when you want to take your communication offline.</p><div class="block">   <dl>      <dt><label for="Title">Title:</label></dt>      <dd><?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'128', 'size'=>'30', 'value'=>$this->validation->Title));?>      <?=$this->validation->Title_error;?></dd>      <dt><label for="OfficePhone">Office #:</label></dt>      <dd><?=form_input(array('name'=>'OfficePhone', 'id'=>'OfficePhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->OfficePhone));?>      <?=$this->validation->OfficePhone_error;?> Ext: <?=form_input(array('name'=>'OfficePhoneExt', 'id'=>'OfficePhoneExt', 'maxlength'=>'20', 'size'=>'6', 'value'=>$this->validation->OfficePhoneExt));?>      <?=$this->validation->OfficePhoneExt_error;?></dd>            <dt><label for="MobilePhone">Mobile #:</label></dt>      <dd><?=form_input(array('name'=>'MobilePhone', 'id'=>'MobilePhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->MobilePhone));?>      <?=$this->validation->MobilePhone_error;?></dd>      <dt><label for="FaxPhone">Fax #:</label></dt>      <dd><?=form_input(array('name'=>'FaxPhone', 'id'=>'FaxPhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->FaxPhone));?>      <?=$this->validation->FaxPhone_error;?></dd>      <dt><label for="HomePhone">Home #:</label></dt>      <dd><?=form_input(array('name'=>'HomePhone', 'id'=>'HomePhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->HomePhone));?>      <?=$this->validation->HomePhone_error;?></dd>      <dt><label for="IMName">IM Name:</label></dt>      <dd><?=form_input(array('name'=>'IMName', 'id'=>'IMName', 'maxlength'=>'20', 'size'=>'30', 'value'=>$this->validation->IMName));?>      <?=$this->validation->IMName_error;?> IM Service: <?=form_dropdown('IMService', $im_services, $this->validation->IMService);?>      <?=$this->validation->IMService_error;?></dd>      <dt><label for="Gender">Gender:</label></dt>      <dd><?=form_dropdown('Gender', $genders, $this->validation->Gender);?>      <?=$this->validation->Gender_error;?></dd>   </dl></div><div class="action">   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('sites/people/index/'.$site_id);?>">Cancel</a></div>      </form>               </div>   <!-- basic-form -->               </div>   <!-- innercol -->         </div>   <!-- col -->         <div class="bottom">&nbsp;</div>         <div id="Footer">   &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.        </div>   <!-- Footer -->      </div>   <!-- Left -->   </td>   <td class="right">      <div class="Right">         <div class="col">            <?php if (file_exists(DOCPATH.'/images/admin/people/'.$user['Username'].'.jpg')): ?>   <?php $avatar = $user['Username'].'.jpg'; ?><?php elseif ($user['Gender'] == 'M'): ?>   <?php $avatar = 'user_male.jpg'; ?><?php else: ?>   <?php $avatar = 'user_female.jpg'; ?><?php endif; ?><div class="contact" style="background-color:#FFF; margin:36px 0 24px 0; padding:12px 9px;">   <div class="avatar" style="float:left;">      <img alt="Person_avatar" src="/images/admin/people/<?=$avatar;?>" />   </div>  <?php /* avatar */ ?>   <div class="body" style="font-size:85%; padding-left:59px;">      <h3 style="margin-top:0;"><?=$user['FirstName'];?> <?=$user['LastName'];?></h3>      <?php if ($user['Title'] != ''): ?><?=$user['Title'];?><br /><?php endif; ?>      <a href="mailto:<?=$user['Email'];?>"><?=$user['Email'];?></a><br />      <?php if ($user['IMName'] != ''): ?><span class="label"><?=$user['IMService'];?> IM:</span> <?=$user['IMName'];?><br /><?php endif; ?>          <?php if ($user['OfficePhone'] != ''): ?><span class="label">O:</span> <?=$user['OfficePhone'];?><?php if ($user['OfficePhoneExt'] != ''): ?> x<?=$user['OfficePhoneExt'];?><?php endif; ?><br /><?php endif; ?>      <?php if ($user['MobilePhone'] != ''): ?><span class="label">M:</span> <?=$user['MobilePhone'];?><br /><?php endif; ?>      <?php if ($user['HomePhone'] != ''): ?><span class="label">H:</span> <?=$user['HomePhone'];?><br /><?php endif; ?>      <?php if ($user['FaxPhone'] != ''): ?><span class="label">F:</span> <?=$user['FaxPhone'];?><br /><?php endif; ?>   </div></div>         </div>   <!-- col -->      </div>   <!-- Right -->   </td>   </tr>   </table>         </div>   <!-- class="container" --></div>   <!-- Wrapper --></body>