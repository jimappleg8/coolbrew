<div id="register">
<fieldset>
<legend><?=$heading?></legend>
<?=form_open('auth/changepassword')?>
      <p><label for="user_name">user name</label>

      <?=form_input(array('name'=>'user_name', 
	                       'id'=>'user_name',
	                       'maxlength'=>'30', 
	                       'size'=>'30',
	                       'value'=>(isset($this->validation) ? $this->validation->{'user_name'} : '')))?>
     </label><span><?=(isset($this->validation) ? $this->validation->{'user_name'.'_error'} : '')?></span>
	</p>

      <p><label for="password">old password</label>
      <?=form_password(array('name'=>'password', 
	                       'id'=>'password',
	                       'maxlength'=>'16', 
	                       'size'=>'16',
	                       'value'=>(isset($this->validation) ? $this->validation->{'password'} : '')))?>
    	<span><?=(isset($this->validation) ? $this->validation->{'password'.'_error'} : '')?></span>
      </p>
    <p><label for="new_password">new password</label>
    <?=form_password(array('name'=>'new_password', 
	                       'id'=>'new_password',
	                       'maxlength'=>'16', 
	                       'size'=>'16',
	                       'value'=>(isset($this->validation) ? $this->validation->{'new_password'} : '')))?>
    	<span><?=(isset($this->validation) ? $this->validation->{'new_password'.'_error'} : '')?></span>
    </p>
      <p><label for="password_confirm">retype new password</label>
      <?=form_password(array('name'=>'password_confirm', 
	                       'id'=>'password_confirm',
	                       'maxlength'=>'16', 
	                       'size'=>'16',
	                       'value'=>(isset($this->validation) ? $this->validation->{'password_confirm'} : '')))?>
    <span><?=(isset($this->validation) ? $this->validation->{'password_confirm'.'_error'} : '')?></span>
      </p>
	    	<input type="submit" name="Submit" value="Submit" class="submit"/>
	        <input type="reset" name="Reset" value="reset" />
</fieldset>
</form>
</div>