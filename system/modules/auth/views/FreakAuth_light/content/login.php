<fieldset><legend accesskey="D" tabindex="1">login</legend>
<?=form_open('auth/')?>
<!--USERNAME-->
	<p><label for="user_name"><?=$this->lang->line('FAL_user_name_label')?>:</label></span>
	<?=form_input(array('name'=>'user_name', 
	                       'id'=>'user_name',
	                       'maxlength'=>'30', 
	                       'size'=>'30',
	                       'value'=>(isset($this->validation) ? $this->validation->{'user_name'} : '')))?>
    <span><?=(isset($this->validation) ? $this->validation->{'user_name'.'_error'} : '')?></span>
   </p>
    <!--PASSWORD-->
	<p><label for="password"><?=$this->lang->line('FAL_user_password_label')?>:</label>
	<?=form_password(array('name'=>'password', 
	                       'id'=>'password',
	                       'maxlength'=>'30', 
	                       'size'=>'30',
	                       'value'=>(isset($this->validation) ? $this->validation->{'password'} : '')))?>
    
    <span><?=(isset($this->validation) ? $this->validation->{'password'.'_error'} : '')?></span>
    <span class=note><?=anchor('auth/forgotten_password', $this->lang->line('FAL_forgotten_password_label'))?></span></p>	
    <!--CAPTCHA (security image)-->
	<?php
	if ($this->config->item('FAL_use_security_code_login'))
	{?>
	<p><label for="security"><?=$this->lang->line('FAL_captcha_label')?>:</label>
	<?=form_input(array('name'=>'security', 
	                       'id'=>'security',
	                       'maxlength'=>'45', 
	                       'size'=>'45',
	                       'value'=>''))?>
    <span><?=(isset($this->validation) ? $this->validation->{'security'.'_error'} : '')?></span>
    <span class=note><img src="<?=base_url()?><?=$this->config->item('FAL_security_code_image_path').$this->config->item('FAL_security_code_image') ?>"></span></p>
    <?php }?>
    <!-- END CAPTCHA (security image)-->
    
	
	<?=form_submit(array('name'=>'login', 
	                     'id'=>'login', 
	                     'value'=>$this->lang->line('FAL_login_label')))?>
    <?php
    if ($this->config->item('FAL_allow_user_registration'))
	{?>
	<p><?=anchor('auth/register', $this->lang->line('FAL_register_label'))?></p>
	<?php }?>
<?=form_close()?>
</fieldset>