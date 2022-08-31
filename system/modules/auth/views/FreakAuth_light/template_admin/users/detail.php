<h2><?=$action?></h2>

<p>&nbsp;</p>

<!--USERPROFILE DATA-->

<!-- START DETAILS DATA-->
<div class="details">
<fieldset>
<legend>User profile details</legend>

<?php if ($this->config->item('FAL_create_user_profile') AND !empty($user_profile))
{?>
	<ul>
	<?php 
		foreach ($user_profile as $field=>$profile)
		{?>
			<li><?=$label[$field]?>: <?=$profile?></li>
		
		<?php 
		}?>
	</ul>
<?php 
}
elseif($this->config->item('FAL_create_user_profile') AND empty($user_profile)) 
{?>
	 <p class="error">no data in DB: please add them</p>
<?php 
} else {?><p class="error">userprofile disabled in config</p><?php }?>

</fieldset>
</div>

<!-- END USERPROFILE DATA-->

<?php if (isset($user))
{?>
	<table>
	  <tr>
	    <th scope="col">id</th>
	    <th scope="col">user name</th>
	    <th scope="col">e-mail</th>
	    <th scope="col">role</th>
	    <th scope="col">banned</th>
	    <?php 
	    if ($this->config->item('FAL_use_country') && isset($user['country']))
	    {?>
		    <th scope="col">country</th>
		    <?php 
	    }?>
	    <th scope="col">&nbsp;</th>
	  </tr>
	  <tr class="center">
	    <td><?=$user['id'];?></td>
	    <td><?=$user['user_name'];?></td>
	    <td><?=$user['email'];?></td>
	    <td ><?=$user['role'];?></td>
	    <td ><?=($user['banned']) ? "Y" :  "N";?></td>
	    <?php if ($this->config->item('FAL_use_country') && isset($user['country']))
	        {?>
	    		<td><?=$user['country'];?></td>
	    	<?php 
	        }?>
	    <td>
	    		<?=anchor('admin/'.$controller.'/edit/'.$user['id'], '<img src="'.base_url().'public/css/images_adminconsole/pencil.png" alt="edit" title="edit">', array('title' => 'edit'));?>
	    		<?=anchor('admin/'.$controller.'/del/'.$user['id'], '<img src="'.base_url().'public/css/images_adminconsole/cross.png" alt="delete" title="delete">', array('onCLick' => "return confirm('Are you SURE you want to delete this record?')", 'title'=>'delete'));?>
	    </td>
	  </tr>
	</table>
<?php
}
else 
{
	echo $error_message;
}
?>
<?=form_open('admin/'.$controller)?>
<?=form_submit(array('class'=>'submit',
					 'name'=>'back', 
					 'id'=>'submit',
	                 'value'=>'Back'))?>

<?=form_close()?>