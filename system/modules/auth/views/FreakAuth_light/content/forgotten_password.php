<h2>Forgotten Password</h2>
<?=form_open('auth/forgotten_password')?>
	<p><label for="email"><?=$this->lang->line('FAL_user_email_label')?>:</label>
	<?=form_input(array('name'=>'email', 
	                       'id'=>'email',
	                       'maxlength'=>'100', 
	                       'size'=>'60',
	                       'value'=>(isset($this->validation) ? $this->validation->{'email'} : '')))?>
    <span><?=(isset($this->validation) ? $this->validation->{'email'.'_error'} : '')?></span></p>
    <!--CAPTCHA (security image)-->
	<?php
	if ($this->config->item('FAL_use_security_code_forgot_password'))
	{?>
	<p><label for="security"><?=$this->lang->line('FAL_captcha_label')?>:</label>
	<?=form_input(array('name'=>'security', 
	                       'id'=>'security',
	                       'maxlength'=>'45', 
	                       'size'=>'45',
	                       'value'=>''))?>
    <span><?=(isset($this->validation) ? $this->validation->{'security'.'_error'} : '')?></span>
    <span class="note"><img src="<?=base_url().$this->config->item('FAL_security_code_image_path').$this->config->item('FAL_security_code_image') ?>"></span></p>
    <?php }?>
    <!-- END CAPTCHA (security image)-->
	<P><?=form_submit(array('name'=>'submit', 
 
	                     'value'=>$this->lang->line('FAL_submit_label')))?>
 </p>
<?=form_close()?>