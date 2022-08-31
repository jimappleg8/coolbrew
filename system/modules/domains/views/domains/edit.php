<body>

<script type="text/javascript">
<!--
<?php if ($domain['PrimaryDomain'] == 1): ?>
function dodelete()
{
   alert(" You cannot delete this domain because it is a Primary Domain.\n\nYou must choose a different Primary Domain for this site before you can delete it. ");
}
<?php else: ?>
function dodelete()
{
   if (confirm(" Are you sure you want to permanently delete this domain?\n\nThis cannot be undone. "))
   {
      document.location = "<?=site_url('domains/delete/'.$domain['ID']);?>";
   }
}
<?php endif; ?>
//-->
</script>


<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <?php if ($admin['group'] == 'admin'): ?><a class="admin" href="#" onclick="dodelete()">Delete this domain</a> | <?php endif; ?>
   <a class="admin" href="<?=site_url('domains/index');?>">Cancel</a>

               </div>
               
   <h1>Edit a Domain</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('domains/edit/'.$domain['ID'].'/'.$last_action);?>">

<p class="blockintro">Enter the domain name without the "http://" or the "www."</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Domain">Domain Name:</label></dt>
      <dd><?=form_input(array('name'=>'Domain', 'id'=>'Domain', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Domain));?>
      <?=$this->validation->Domain_error;?></dd>
   </dl>
</div>

<p class="blockintro">Tell us what site this domain points to. If the site is discontinued or temporarily disabled, indicate what site the domain redirects to. If the site does not exist, you must create a new site before you add the domain.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="SiteID">Site:</label></dt>
      <dd><?=form_dropdown('SiteID', $sites, $this->validation->SiteID);?>
      <?=$this->validation->SiteID_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, tell us the Registrar and DNS vendors.</p>
<div class="block">
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NotRegistered" id="NotRegistered" value="1" <?=$this->validation->set_checkbox('NotRegistered', '1');?> \><label for="NotRegistered">  This domain is NOT registered by the Hain Celestial Group</label>
      <?=$this->validation->NotRegistered_error;?></dd>

      <dt>Registrar:</dt>
      <dd><table>
      <tr>
      <td><label for="RegistrarVendor">Choose an existing REGISTRAR</label></td>
      <td><label for="RegistrarName"><strong>OR</strong> Enter a new REGISTRAR name</label></td>
      </tr>
      <tr>
      <td><?=form_dropdown('RegistrarVendor', $vendors, $this->validation->RegistrarVendor);?>
      <?=$this->validation->RegistrarVendor_error;?></td>
      <td><?=form_input(array('name'=>'RegistrarName', 'id'=>'RegistrarName', 'maxlength'=>'64', 'size'=>'30', 'value'=>$this->validation->RegistrarName));?>
      <?=$this->validation->RegistrarName_error;?></td>
      </tr>
      </table></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="RegistrarShouldBePrimary" id="RegistrarShouldBePrimary" value="1" <?=$this->validation->set_checkbox('RegistrarShouldBePrimary', '1');?> \><label for="RegistrarShouldBePrimary">  The registrar for this domain should be the primary (<?=$primary_registrar['VendorName'];?>)</label>
      <?=$this->validation->RegistrarShouldBePrimary_error;?></dd>

      <dt>DNS Vendor:</dt>
      <dd><table>
      <tr>
      <td><label for="DNSVendor">Choose an existing DNS vendor</label></td>
      <td><label for="DNSName"><strong>OR</strong> Enter a new DNS vendor name</label></td>
      </tr>
      <tr>
      <td><?=form_dropdown('DNSVendor', $vendors, $this->validation->DNSVendor);?>
      <?=$this->validation->DNSVendor_error;?></td>
      <td><?=form_input(array('name'=>'DNSName', 'id'=>'DNSName', 'maxlength'=>'64', 'size'=>'30', 'value'=>$this->validation->DNSName));?>
      <?=$this->validation->DNSName_error;?></td>
      </tr>
      </table></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="DNSShouldBePrimary" id="DNSShouldBePrimary" value="1" <?=$this->validation->set_checkbox('DNSShouldBePrimary', '1');?> \><label for="DNSShouldBePrimary">  The DNS vendor for this domain should be the primary (<?=$primary_dns_vendor['VendorName'];?>)</label>
      <?=$this->validation->DNSShouldBePrimary_error;?></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="PrimaryDNSIsSetUp" id="PrimaryDNSIsSetUp" value="1" <?=$this->validation->set_checkbox('PrimaryDNSIsSetUp', '1');?> \><label for="PrimaryDNSIsSetUp">  This domain is set up in the primary DNS (<?=$primary_dns_vendor['VendorName'];?>)</label>
      <?=$this->validation->PrimaryDNSIsSetUp_error;?></dd>
   </dl>
</div>

<p class="blockintro">CSC Corporate Domains info:</p>
<div class="block">
   <dl>
      <dt><label for="Brand">Brand:</label></dt>
      <dd><?=form_input(array('name'=>'Brand', 'id'=>'Brand', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Brand));?>
      <?=$this->validation->Brand_error;?></dd>
   </dl>

   <dl>
      <dt><label for="Extension">Extension:</label></dt>
      <dd><?=form_input(array('name'=>'Extension', 'id'=>'Extension', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Extension));?>
      <?=$this->validation->Extension_error;?></dd>
   </dl>

   <dl>
      <dt><label for="Country">Country:</label></dt>
      <dd><?=form_input(array('name'=>'Country', 'id'=>'Country', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Country));?>
      <?=$this->validation->Country_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegistrationDate">Registration Date:</label></dt>
      <dd><?=form_input(array('name'=>'RegistrationDate', 'id'=>'RegistrationDate', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegistrationDate));?>
      <?=$this->validation->RegistrationDate_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegistryExpiryDate">Registry Expiry Date:</label></dt>
      <dd><?=form_input(array('name'=>'RegistryExpiryDate', 'id'=>'RegistryExpiryDate', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegistryExpiryDate));?>
      <?=$this->validation->RegistryExpiryDate_error;?></dd>
   </dl>

   <dl>
      <dt><label for="PaidUntilDate">Paid Until Date:</label></dt>
      <dd><?=form_input(array('name'=>'PaidUntilDate', 'id'=>'PaidUntilDate', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->PaidUntilDate));?>
      <?=$this->validation->PaidUntilDate_error;?></dd>
   </dl>

   <dl>
      <dt><label for="BusinessUnit">Business Unit:</label></dt>
      <dd><?=form_input(array('name'=>'BusinessUnit', 'id'=>'BusinessUnit', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->BusinessUnit));?>
      <?=$this->validation->BusinessUnit_error;?></dd>
   </dl>

   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_input(array('name'=>'Status', 'id'=>'Status', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Status));?>
      <?=$this->validation->Status_error;?></dd>
   </dl>

   <dl>
      <dt><label for="DNSType">DNS Type:</label></dt>
      <dd><?=form_input(array('name'=>'DNSType', 'id'=>'DNSType', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->DNSType));?>
      <?=$this->validation->DNSType_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TransferLock">Transfer Lock:</label></dt>
      <dd><?=form_input(array('name'=>'TransferLock', 'id'=>'TransferLock', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TransferLock));?>
      <?=$this->validation->TransferLock_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegProfileName">Reg Profile Name:</label></dt>
      <dd><?=form_input(array('name'=>'RegProfileName', 'id'=>'RegProfileName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegProfileName));?>
      <?=$this->validation->RegProfileName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegFirstName">Reg First Name:</label></dt>
      <dd><?=form_input(array('name'=>'RegFirstName', 'id'=>'RegFirstName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegFirstName));?>
      <?=$this->validation->RegFirstName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegLastName">Reg Last Name:</label></dt>
      <dd><?=form_input(array('name'=>'RegLastName', 'id'=>'RegLastName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegLastName));?>
      <?=$this->validation->RegLastName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegOrganization">Reg Organization:</label></dt>
      <dd><?=form_input(array('name'=>'RegOrganization', 'id'=>'RegOrganization', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegOrganization));?>
      <?=$this->validation->RegOrganization_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegAddress">Reg Address:</label></dt>
      <dd><?=form_input(array('name'=>'RegAddress', 'id'=>'RegAddress', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegAddress));?>
      <?=$this->validation->RegAddress_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegAddress2">Reg Address 2:</label></dt>
      <dd><?=form_input(array('name'=>'RegAddress2', 'id'=>'RegAddress2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegAddress2));?>
      <?=$this->validation->RegAddress2_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegCity">Reg City:</label></dt>
      <dd><?=form_input(array('name'=>'RegCity', 'id'=>'RegCity', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegCity));?>
      <?=$this->validation->RegCity_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegStateProvince">Reg State Province:</label></dt>
      <dd><?=form_input(array('name'=>'RegStateProvince', 'id'=>'RegStateProvince', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegStateProvince));?>
      <?=$this->validation->RegStateProvince_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegPostalCode">Reg Postal Code:</label></dt>
      <dd><?=form_input(array('name'=>'RegPostalCode', 'id'=>'RegPostalCode', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegPostalCode));?>
      <?=$this->validation->RegPostalCode_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegCountry">Reg Country:</label></dt>
      <dd><?=form_input(array('name'=>'RegCountry', 'id'=>'RegCountry', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegCountry));?>
      <?=$this->validation->RegCountry_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegEmail">Reg Email:</label></dt>
      <dd><?=form_input(array('name'=>'RegEmail', 'id'=>'RegEmail', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegEmail));?>
      <?=$this->validation->RegEmail_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegPhone">Reg Phone:</label></dt>
      <dd><?=form_input(array('name'=>'RegPhone', 'id'=>'RegPhone', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegPhone));?>
      <?=$this->validation->RegPhone_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RegFax">Reg Fax:</label></dt>
      <dd><?=form_input(array('name'=>'RegFax', 'id'=>'RegFax', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RegFax));?>
      <?=$this->validation->RegFax_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminProfileName">Admin Profile Name:</label></dt>
      <dd><?=form_input(array('name'=>'AdminProfileName', 'id'=>'AdminProfileName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminProfileName));?>
      <?=$this->validation->AdminProfileName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminFirstName">Admin First Name:</label></dt>
      <dd><?=form_input(array('name'=>'AdminFirstName', 'id'=>'AdminFirstName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminFirstName));?>
      <?=$this->validation->AdminFirstName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminLastName">Admin Last Name:</label></dt>
      <dd><?=form_input(array('name'=>'AdminLastName', 'id'=>'AdminLastName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminLastName));?>
      <?=$this->validation->AdminLastName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminOrganization">Admin Organization:</label></dt>
      <dd><?=form_input(array('name'=>'AdminOrganization', 'id'=>'AdminOrganization', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminOrganization));?>
      <?=$this->validation->AdminOrganization_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminAddress">Admin Address:</label></dt>
      <dd><?=form_input(array('name'=>'AdminAddress', 'id'=>'AdminAddress', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminAddress));?>
      <?=$this->validation->AdminAddress_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminAddress2">Admin Address 2:</label></dt>
      <dd><?=form_input(array('name'=>'AdminAddress2', 'id'=>'AdminAddress2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminAddress2));?>
      <?=$this->validation->AdminAddress2_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminCity">Admin City:</label></dt>
      <dd><?=form_input(array('name'=>'AdminCity', 'id'=>'AdminCity', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminCity));?>
      <?=$this->validation->AdminCity_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminStateProvince">Admin State Province:</label></dt>
      <dd><?=form_input(array('name'=>'AdminStateProvince', 'id'=>'AdminStateProvince', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminStateProvince));?>
      <?=$this->validation->AdminStateProvince_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminPostalCode">Admin Postal Code:</label></dt>
      <dd><?=form_input(array('name'=>'AdminPostalCode', 'id'=>'AdminPostalCode', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminPostalCode));?>
      <?=$this->validation->AdminPostalCode_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminCountry">Admin Country:</label></dt>
      <dd><?=form_input(array('name'=>'AdminCountry', 'id'=>'AdminCountry', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminCountry));?>
      <?=$this->validation->AdminCountry_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminEmail">Admin Email:</label></dt>
      <dd><?=form_input(array('name'=>'AdminEmail', 'id'=>'AdminEmail', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminEmail));?>
      <?=$this->validation->AdminEmail_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminPhone">Admin Phone:</label></dt>
      <dd><?=form_input(array('name'=>'AdminPhone', 'id'=>'AdminPhone', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminPhone));?>
      <?=$this->validation->AdminPhone_error;?></dd>
   </dl>

   <dl>
      <dt><label for="AdminFax">Admin Fax:</label></dt>
      <dd><?=form_input(array('name'=>'AdminFax', 'id'=>'AdminFax', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AdminFax));?>
      <?=$this->validation->AdminFax_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechProfileName">Tech Profile Name:</label></dt>
      <dd><?=form_input(array('name'=>'TechProfileName', 'id'=>'TechProfileName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechProfileName));?>
      <?=$this->validation->TechProfileName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechFirstName">Tech First Name:</label></dt>
      <dd><?=form_input(array('name'=>'TechFirstName', 'id'=>'TechFirstName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechFirstName));?>
      <?=$this->validation->TechFirstName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechLastName">Tech Last Name:</label></dt>
      <dd><?=form_input(array('name'=>'TechLastName', 'id'=>'TechLastName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechLastName));?>
      <?=$this->validation->TechLastName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechOrganization">Tech Organization:</label></dt>
      <dd><?=form_input(array('name'=>'TechOrganization', 'id'=>'TechOrganization', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechOrganization));?>
      <?=$this->validation->TechOrganization_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechAddress">Tech Address:</label></dt>
      <dd><?=form_input(array('name'=>'TechAddress', 'id'=>'TechAddress', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechAddress));?>
      <?=$this->validation->TechAddress_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechAddress 2">Tech Address 2:</label></dt>
      <dd><?=form_input(array('name'=>'TechAddress2', 'id'=>'TechAddress2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechAddress2));?>
      <?=$this->validation->TechAddress2_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechCity">Tech City:</label></dt>
      <dd><?=form_input(array('name'=>'TechCity', 'id'=>'TechCity', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechCity));?>
      <?=$this->validation->TechCity_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechStateProvince">Tech State Province:</label></dt>
      <dd><?=form_input(array('name'=>'TechStateProvince', 'id'=>'TechStateProvince', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechStateProvince));?>
      <?=$this->validation->TechStateProvince_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechPostalCode">Tech Postal Code:</label></dt>
      <dd><?=form_input(array('name'=>'TechPostalCode', 'id'=>'TechPostalCode', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechPostalCode));?>
      <?=$this->validation->TechPostalCode_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechCountry">Tech Country:</label></dt>
      <dd><?=form_input(array('name'=>'TechCountry', 'id'=>'TechCountry', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechCountry));?>
      <?=$this->validation->TechCountry_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechEmail">Tech Email:</label></dt>
      <dd><?=form_input(array('name'=>'TechEmail', 'id'=>'TechEmail', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechEmail));?>
      <?=$this->validation->TechEmail_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechPhone">Tech Phone:</label></dt>
      <dd><?=form_input(array('name'=>'TechPhone', 'id'=>'TechPhone', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechPhone));?>
      <?=$this->validation->TechPhone_error;?></dd>
   </dl>

   <dl>
      <dt><label for="TechFax">Tech Fax:</label></dt>
      <dd><?=form_input(array('name'=>'TechFax', 'id'=>'TechFax', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->TechFax));?>
      <?=$this->validation->TechFax_error;?></dd>
   </dl>

   <dl>
      <dt><label for="IDNTranslation">IDN Translation:</label></dt>
      <dd><?=form_input(array('name'=>'IDNTranslation', 'id'=>'IDNTranslation', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->IDNTranslation));?>
      <?=$this->validation->IDNTranslation_error;?></dd>
   </dl>

   <dl>
      <dt><label for="LocalLanguage">Local Language:</label></dt>
      <dd><?=form_input(array('name'=>'LocalLanguage', 'id'=>'LocalLanguage', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->LocalLanguage));?>
      <?=$this->validation->LocalLanguage_error;?></dd>
   </dl>

   <dl>
      <dt><label for="DNS1">DNS 1:</label></dt>
      <dd><?=form_input(array('name'=>'DNS1', 'id'=>'DNS1', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->DNS1));?>
      <?=$this->validation->DNS1_error;?></dd>
   </dl>

   <dl>
      <dt><label for="DNS2">DNS 2:</label></dt>
      <dd><?=form_input(array('name'=>'DNS2', 'id'=>'DNS2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->DNS2));?>
      <?=$this->validation->DNS2_error;?></dd>
   </dl>

   <dl>
      <dt><label for="DNS3">DNS 3:</label></dt>
      <dd><?=form_input(array('name'=>'DNS3', 'id'=>'DNS3', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->DNS3));?>
      <?=$this->validation->DNS3_error;?></dd>
   </dl>

   <dl>
      <dt><label for="DNS4">DNS 4:</label></dt>
      <dd><?=form_input(array('name'=>'DNS4', 'id'=>'DNS4', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->DNS4));?>
      <?=$this->validation->DNS4_error;?></dd>
   </dl>

   <dl>
      <dt><label for="Field1">Field1:</label></dt>
      <dd><?=form_input(array('name'=>'Field1', 'id'=>'Field1', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Field1));?>
      <?=$this->validation->Field1_error;?></dd>
   </dl>

   <dl>
      <dt><label for="Field2">Field 2:</label></dt>
      <dd><?=form_input(array('name'=>'Field2', 'id'=>'Field2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Field2));?>
      <?=$this->validation->Field2_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt>Notes:</dt>
      <dd><?=form_textarea(array('name'=>'Notes', 'id'=>'Notes', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Notes, 'class'=>'box'));?>
      <?=$this->validation->Notes_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('domains/index');?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
            <p>&nbsp;</p>
  
            <div class="indent">

               <p>&nbsp;</p>
        
            </div>   <!-- indent -->

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>