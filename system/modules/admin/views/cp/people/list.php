<body class="people">

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
<?php if ($people['message'] != ''): ?>
<div id="flash_alert"><?=$people['message'];?></div>
<?php endif; ?>

<?php if ($people['error_msg'] != ''): ?>
<div id="flash_error"><?=$people['error_msg'];?></div>
<?php endif; ?>

   <div class="container">

      <div class="Full">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

<?php if ($people['group'] == 'admin'): ?>
   <a class="admin" href="<?=site_url('cp/companies/add/'.$last_action);?>">Add a new company</a>
<?php endif; ?>

               </div>

   <h1>People who have access to the Admin module</h1>

            </div>

            <div class="innercol">

<div class="listing">

<?php if ($people['people_exist'] == true): ?>

   <?php foreach ($people_list AS $company): ?>
   
<div class="company" id="client_<?=$company['ID'];?>">
<a name="client_<?=$company['ID'];?>"></a>
<h1><?=$company['CompanyName'];?></h1>

      <?php $num_cols = 3; ?>
      <?php array_unshift($company['people'], array()); ?>
      <?php $columns = partition($company['people'], $num_cols); ?>
      
      <?php foreach ($columns AS $column): ?>
   
   <div class="people_column">
         <?php foreach ($column AS $person): ?>
            <?php if (empty($person)): ?>
      <div class="contact" id="company_<?=$company['ID'];?>">
         <div class="avatar">
         <img alt="Company_avatar" src="/images/admin/companies/company_general.jpg" />
         </div>  <?php // avatar ?>
         <div class="body">
            <?php $address_br = FALSE; ?>
            <h3><?=$company['CompanyName'];?></h3>
            <?php if ($company['Address1'] != ''): ?><?=$company['Address1'];?><br /><?php endif; ?>
            <?php if ($company['Address2'] != ''): ?><?=$company['Address2'];?><br /><?php endif; ?>
            <?php if ($company['City'] != ''): ?><?=$company['City'];?>,<?php $address_br = TRUE; ?><?php endif; ?>
            <?php if ($company['State'] != ''): ?> <?=$company['State'];?><?php $address_br = TRUE; ?><?php endif; ?>
            <?php if ($company['Zip'] != ''): ?> <?=$company['Zip'];?><?php $address_br = TRUE; ?><?php endif; ?>
            <?php if ($address_br == TRUE): ?><br /><?php endif; ?>
            <?php if ($company['OfficePhone'] != ''): ?><span class="item"><span class="label">O:</span> <?=$company['OfficePhone'];?></span><br /><?php endif; ?>
            <?php if ($company['FaxPhone'] != ''): ?><span class="item"><span class="label">F:</span> <?=$company['FaxPhone'];?></span><br /><?php endif; ?>
            <?php if ($company['WebAddress'] != ''): ?><span class="item"><a href="<?=$company['WebAddress'];?>"><?=$company['WebAddress'];?></a></span><br /><?php endif; ?>
            <?php if ($people['group'] == 'admin'): ?><div class="edit">
            <a href="<?=site_url('cp/companies/edit/'.$company['ID'].'/'.$last_action);?>" class="admin">Edit</a> this company<br />
            <a href="<?=site_url('cp/people/add/'.$company['ID'].'/'.$last_action);?>" class="admin">Add person</a> 
            to <?=$company['CompanyName'];?>
            <br />
            </div>  <?php /* edit */ ?><?php endif; ?>
         </div>  <?php /* body */ ?>
      </div>  <?php /* contact */ ?>
               <?php continue; ?>
            <?php endif; ?>
            <?php if (file_exists(DOCPATH.'/images/admin/people/'.$person['Username'].'.jpg')): ?>
               <?php $avatar = $person['Username'].'.jpg'; ?>
            <?php elseif ($person['Gender'] == 'M'): ?>
               <?php $avatar = 'user_male.jpg'; ?>
            <?php else: ?>
               <?php $avatar = 'user_female.jpg'; ?>
            <?php endif; ?>
      <div class="contact" id="client_employee_<?=$person['Username'];?>">
         <div class="avatar">
         <img alt="Person_avatar" src="/images/admin/people/<?=$avatar;?>" />
         </div>  <?php /* avatar */ ?>
         <div class="body">
         <h3><?=$person['FirstName'];?> <?=$person['LastName'];?></h3>
         <?php if ($person['Title'] != ''): ?><?=$person['Title'];?><br /><?php endif; ?>
         <a href="mailto:<?=$person['Email'];?>"><?=$person['Email'];?></a><br />
         <?php if ($person['IMName'] != ''): ?><span class="label"><?=$person['IMService'];?> IM:</span> <?=$person['IMName'];?><br /><?php endif; ?>    
         <?php if ($person['OfficePhone'] != ''): ?><span class="label">O:</span> <?=$person['OfficePhone'];?><?php if ($person['OfficePhoneExt'] != ''): ?> x<?=$person['OfficePhoneExt'];?><?php endif; ?><br /><?php endif; ?>
         <?php if ($person['MobilePhone'] != ''): ?><span class="label">M:</span> <?=$person['MobilePhone'];?><br /><?php endif; ?>
         <?php if ($person['HomePhone'] != ''): ?><span class="label">H:</span> <?=$person['HomePhone'];?><br /><?php endif; ?>
         <?php if ($person['FaxPhone'] != ''): ?><span class="label">F:</span> <?=$person['FaxPhone'];?><br /><?php endif; ?>
         <?php if ($person['GroupName'] == 'admin'): ?>Administrator<br /><?php endif; ?>
         
            <?php if ($people['group'] == 'admin' || $person['Username'] == $user['Username']): ?><div class="edit">
            <a href="<?=site_url('cp/people/edit/'.$person['Username'].'/'.$last_action);?>" class="admin">Edit</a>
            </div>  <?php /* edit */ ?><?php endif; ?>
         </div>  <?php /* body */ ?>
      </div>  <?php /* contact */ ?>
         <?php endforeach; ?>
   </div>  <?php /* people_column */ ?>
      <?php endforeach; ?>
   <div style="clear: both;">&nbsp;</div>
</div>  <?php /* company */ ?>
   <?php endforeach; ?>

<?php else: ?>

   <p>There are no people to display.</p>

<?php endif; ?>

</div>   <?php // listing ?>

            </div>   <?php /* innercol */ ?>

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2008-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Full -->

   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>