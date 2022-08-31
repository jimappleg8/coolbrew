<h2><?=$action?></h2>

<p>&nbsp;</p>
<?=$pagination_links;?>
<? 
//if no records in DB don't display the result table
if (isset($user)) 
  {?>
<?=form_open('admin/'.$controller.'/add')?>
<?=form_submit(array('class'=>'submit',
					 'name'=>'Add', 
					 'id'=>'submit',
	                 'value'=> $controller=='admins' ? 'Add admin' : 'Add user'))?>

<?=form_close()?>

<table>
  <tr>
    <th scope="col">id</th>
    <th scope="col">user name</th>
    <th scope="col">role</th>
    <th scope="col">&nbsp;</th>
  </tr>

  <?php foreach($user as $key=>$value):?>
  <tr class="center">
    <td><?=$user[$key]['id'];?></td>
    <td><?=$user[$key]['user_name'];?></td>
    <td><?=$user[$key]['role']?></td>
    <td>  
		    <?=anchor('admin/'.$controller.'/show/'.$user[$key]['id'], '<img src="'.base_url().'public/css/images_adminconsole/zoom.png" alt="view" title="view">', array('title' => 'view'));?>
		    <?=anchor('admin/'.$controller.'/edit/'.$user[$key]['id'], '<img src="'.base_url().'public/css/images_adminconsole/pencil.png" alt="edit" title="edit">', array('title' => 'edit'));?>
    		<?php     		
    		if ($user[$key]['role']!='superadmin')
    		{?>
		    <?=anchor('admin/'.$controller.'/del/'.$user[$key]['id'], '<img src="'.base_url().'public/css/images_adminconsole/cross.png" alt="delete" title="delete">', array('onCLick' => "return confirm('Are you SURE you want to delete this record?')", 'title' => 'delete'));?>
		    <?php
    		}
    		?>
    </td>
  </tr>
  <?php endforeach;?>
</table>
<?php }?>
<?=form_open('admin/'.$controller.'/add')?>
<?=form_submit(array('class'=>'submit',
					 'name'=>'Add', 
					 'id'=>'submit',
	                 'value'=> $controller=='admins' ? 'Add admin' : 'Add user'))?>

<?=form_close()?>
<?=$pagination_links;?>