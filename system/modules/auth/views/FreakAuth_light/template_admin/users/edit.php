<h2><?=$action?></h2>

<p>&nbsp;</p>
<?=form_open('admin/users/edit')?>
<!--USERPROFILE DATA-->
<div class="userprofile">
<fieldset>
<legend>User profile</legend>
<?php if ($this->config->item('FAL_create_user_profile') AND !empty($fields))
{
	foreach ($fields as $field=>$label)
	{?>
	<p><label for="<?=$field?>"><?=$label?>:</label>
    <?=form_input(array('name'=>$field, 
	                    'id'=>$field,
	                    'maxlength'=>'45', 
	                    'size'=>'25',
	                    'value'=>(isset($user_prof[$field]) ? $user_prof[$field] : $this->validation->{$field})))?>
	  <span><?=(isset($this->validation) ? $this->validation->{$field.'_error'} : '')?></span></p>

<?php }
}
elseif($this->config->item('FAL_create_user_profile') AND empty($user_profile)) {?> <p class="error">no data in DB: please add them</p>
<?php } else {?><p class="error">userprofile disabled in config</p><?php }?>
</fieldset>
</div>
<!-- END USERPROFILE DATA-->
<fieldset>
<legend>User main</legend>
       <?=form_hidden('id', (isset($user['id']) ? $user['id'] : $this->validation->{'id'}));?>
       
       <p><label for="user_name">username:</label>
       <?=form_input(array('name'=>'user_name', 
	                       'id'=>'user_name',
	                       'maxlength'=>'45', 
	                       'size'=>'35',
	                       'value'=>(isset($user['user_name']) ? $user['user_name'] : $this->validation->{'user_name'})))?>
	  <span><?=(isset($this->validation) ? $this->validation->{'user_name'.'_error'} : '')?></span>
	  <p/>

      <p><label for="email">e-mail:</label>
      <?=form_input(array('name'=>'email', 
	                       'id'=>'email',
	                       'maxlength'=>'120', 
	                       'size'=>'35',
	                       'value'=>(isset($user['email']) ? $user['email'] : $this->validation->{'email'})))?>
    	<span><?=(isset($this->validation) ? $this->validation->{'email'.'_error'} : '')?></span>
      </p>
      <p><label for="password">password:</label>
      <?=form_password(array('name'=>'password', 
	                       'id'=>'password',
	                       'maxlength'=>'16', 
	                       'size'=>'16',
	                       'value'=>(isset($this->validation->{'password'}) ? $this->validation->{'password'} : '')))?>
    	<span><?=(isset($this->validation) ? $this->validation->{'password'.'_error'} : '')?></span>
      </p>

      <p><label for="password_confirm">retype password:</label>
      <?=form_password(array('name'=>'password_confirm', 
	                       'id'=>'password_confirm',
	                       'maxlength'=>'16', 
	                       'size'=>'16',
	                       'value'=>(isset($this->validation) ? $this->validation->{'password_confirm'} : '')))?>
    <span><?=(isset($this->validation) ? $this->validation->{'password_confirm'.'_error'} : '')?></span>
     </p>
     
    <?php if ($this->config->item('FAL_use_country'))
        {?>

      <p><label for="country_id">country:</label>
      <?=form_dropdown('country_id',
	                 $countries,
	                 (isset($user['country_id']) ? $user['country_id'] :  $this->validation->country_id))?>
	<span><?=(isset($this->validation) ? $this->validation->{'country_id'.'_error'} : '')?></span>
    </p>
    <?php } ?>
	
    <p><label for="role">role:</label>
         <select name="role" id="role">
         <option value="">-------------</option>
         <?php foreach ($role_options as $value)
         {?>
         	<option value="<?=$value?>" <?php if(isset($user['role']) AND $user['role'] == $value){echo 'selected';} else   echo $this->validation->set_select('role', $value); ?> ><?=$value?></option>
         <?php 
         }
         ?>
         </select>
         <span><?=(isset($this->validation) ? $this->validation->{'role'.'_error'} : '')?></span>
	</p>
    <p><label for="country_id">is banned?</label>
         <?=form_checkbox(array( 'name'      => 'banned',
					              'id'       => 'banned',
					              'value'    => 1,
					              'checked'  => ($this->validation->{'banned'}==1 OR (isset($user['banned']) AND $user['banned']==1) ? TRUE : FALSE),
					            )
					        )?>
	</p>

</fieldset>
     <input type="submit" name="Submit" value="save" />
     <input type="button" name="back" class="submit" value="back" onclick="location.href = '<?=base_url();?>index.php/admin/<?=$controller;?>'"/>

</form>