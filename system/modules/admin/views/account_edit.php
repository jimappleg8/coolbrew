<div id="basic-form"><form method="post" action="<?=site_url($last_action);?>"><div class="block" style="background-color:transparent;">   <dl>      <dt class="required"><label for="Title">First Name:</label></dt>      <dd><?=form_input(array('name'=>'FirstName', 'id'=>'FirstName', 'maxlength'=>'100', 'size'=>'30', 'value'=>$this->validation->FirstName));?>      <?=$this->validation->FirstName_error;?></dd>      <dt class="required"><label for="LastName">Last Name:</label></dt>      <dd><?=form_input(array('name'=>'LastName', 'id'=>'LastName', 'maxlength'=>'100', 'size'=>'30', 'value'=>$this->validation->LastName));?>      <?=$this->validation->LastName_error;?></dd>      <dt class="required"><label for="Email">Email:</label></dt>      <dd><?=form_input(array('name'=>'Email', 'id'=>'Email', 'maxlength'=>'200', 'size'=>'30', 'value'=>$this->validation->Email));?>      <?=$this->validation->Email_error;?></dd>   </dl></div><p class="blockintro">Change your password by entering it below.</p><div class="block" style="background-color:transparent;">   <dl>      <dt class="required"><label for="Username">Username:</label></dt>      <dd style="padding:3px 0 6px 0;"><span style="color:#000; font-size:12px;"><?=$this->validation->Username;?></span> (this field cannot be changed)</dd>      <dt class="required"><label for="Password">Password:</label></dt>      <dd><?=form_password(array('name'=>'Password', 'id'=>'Password', 'maxlength'=>'40', 'size'=>'30', 'value'=>$this->validation->Password));?>      <?=$this->validation->Password_error;?></dd>   </dl></div><p class="blockintro">The rest is optional, but some contact info will come in handy when you want to take your communication offline.</p><div class="block" style="background-color:transparent;">   <dl>      <dt><label for="Title">Title:</label></dt>      <dd><?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'128', 'size'=>'30', 'value'=>$this->validation->Title));?>      <?=$this->validation->Title_error;?></dd>      <dt><label for="OfficePhone">Office #:</label></dt>      <dd><?=form_input(array('name'=>'OfficePhone', 'id'=>'OfficePhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->OfficePhone));?>      <?=$this->validation->OfficePhone_error;?> Ext: <?=form_input(array('name'=>'OfficePhoneExt', 'id'=>'OfficePhoneExt', 'maxlength'=>'20', 'size'=>'6', 'value'=>$this->validation->OfficePhoneExt));?>      <?=$this->validation->OfficePhoneExt_error;?></dd>            <dt><label for="MobilePhone">Mobile #:</label></dt>      <dd><?=form_input(array('name'=>'MobilePhone', 'id'=>'MobilePhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->MobilePhone));?>      <?=$this->validation->MobilePhone_error;?></dd>      <dt><label for="FaxPhone">Fax #:</label></dt>      <dd><?=form_input(array('name'=>'FaxPhone', 'id'=>'FaxPhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->FaxPhone));?>      <?=$this->validation->FaxPhone_error;?></dd>      <dt><label for="HomePhone">Home #:</label></dt>      <dd><?=form_input(array('name'=>'HomePhone', 'id'=>'HomePhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->HomePhone));?>      <?=$this->validation->HomePhone_error;?></dd>      <dt><label for="IMName">IM Name:</label></dt>      <dd><?=form_input(array('name'=>'IMName', 'id'=>'IMName', 'maxlength'=>'20', 'size'=>'30', 'value'=>$this->validation->IMName));?>      <?=$this->validation->IMName_error;?> IM Service: <?=form_dropdown('IMService', $im_services, $this->validation->IMService);?>      <?=$this->validation->IMService_error;?></dd>   </dl></div><div class="action" style="margin-bottom:30px;">   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?></div>      </form></div>