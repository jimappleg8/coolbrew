<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('domains/index');?>">Cancel</a>

               </div>
               
   <h1>Add a Domain</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('domains/add/'.$last_action);?>">

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

<div class="block">
   <dl>
      <dt>Notes:</dt>
      <dd><?=form_textarea(array('name'=>'Notes', 'id'=>'Notes', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Notes, 'class'=>'box'));?>
      <?=$this->validation->Notes_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this domain'))?> or <a class="admin" href="<?=site_url('domains/index');?>">Cancel</a>
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