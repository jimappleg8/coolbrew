<body>

<?=$this->load->view('sites/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('sites/people/index/'.$site_id.'/');?>">Done</a>

               </div>

   <h1 id="top">Site Permissions</h1>

            </div>

            <div class="innercol">

<?php if ($admin['user_exists'] == true): ?>

   <form method="post" action="<?=site_url('sites/people/assign/'.$site_id.'/'.$last_action);?>">
   
   <?php foreach ($user_list AS $company): ?>
   
   <h2><?=$company['CompanyName'];?></h2>
   
   <div class="listing">
   
      <?php foreach ($company['people'] AS $user): ?>

      <?php $fieldname = 'user'.$user['UserID']; ?>

         <?php if ($user['GroupName'] == 'admin'): ?>
      <div style="border-top:1px solid #666; clear:both;">
      <p style="margin:0 0 0 2em; padding:4px 0;"><input type="hidden" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="0" /> <label for="<?=$fieldname;?>" style="color:red;"><?=$user['FirstName'];?> <?=$user['LastName'];?> (<?=$user['Username'];?>) has access as an administrator</label></p>
      </div>
         <?php else: ?>
      <div style="border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$user['FirstName'];?> <?=$user['LastName'];?> (<?=$user['Username'];?>)</label></p>
      </div>
         <?php endif; ?>

      <?php endforeach; ?>

   </div> <?php /* listing */ ?>

   <?php endforeach; ?>

   <div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('sites/people/index/'.$site_id.'/');?>">Done</a>
   </div>

   </form>

<?php else: ?>

   <p>There are no users to display.</p>
   
   <p><a class="admin" href="<?=site_url('cp/people/add/'.$last_action);?>">Create the first user.</a></p>

<?php endif; ?>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         </div>   <?php /* col */ ?>
         
      </div>   <?php /* Right */ ?>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>
