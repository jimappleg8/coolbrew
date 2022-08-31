<body>

<?=$this->load->view('sites/tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('sites/vendors/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add a Site Vendor</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('sites/vendors/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">Please choose the vendor associated with this site:</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Service">Service:</label></dt>
      <dd style="padding-top:6px;">
      <table>
      <tr>
      <td><label for="VendorID">Choose an existing vendor</label></td>
      <td><label for="VendorName"><strong>OR</strong> Enter a new vendor name</label></td>
      </tr>
      <tr>
      <td><?=form_dropdown('VendorID', $vendors, $this->validation->VendorID);?>
         <?=$this->validation->VendorID_error;?></td>
      <td><?=form_input(array('name'=>'VendorName', 'id'=>'VendorName', 'maxlength'=>'64', 'size'=>'30', 'value'=>$this->validation->VendorName));?>
         <?=$this->validation->VendorName_error;?></td>
      </tr>
      </table>
      </dd>

      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>
   </dl>
</div>

<p class="blockintro">Provide a short summary of the service provided by this vendor. You can also add a longer description to add any needed detail. Include any contact information that is specific to this site.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="ServiceID">Service ID:</label></dt>
      <dd style="padding-top:6px;">
      <table>
      <tr>
      <td><label for="ServiceID">Choose an existing service</label></td>
      <td><label for="NewServiceName"><strong>OR</strong> Enter a new service name</label></td>
      </tr>
      <tr>
      <td><?=form_dropdown('ServiceID', $services, $this->validation->ServiceID);?>
         <?=$this->validation->ServiceID_error;?></td>
      <td><?=form_input(array('name'=>'NewServiceName', 'id'=>'NewServiceName', 'maxlength'=>'64', 'size'=>'30', 'value'=>$this->validation->NewServiceName));?>
         <?=$this->validation->NewServiceName_error;?></td>
      </tr>
      </table>
      </dd>

      <dt><label for="ServiceDesc">Service Description:</label></dt>
      <dd><?=form_ckeditor(array('name'=>'ServiceDesc', 'id'=>'ServiceDesc', 'cols' => 60, 'rows' => 12, 'value'=>$this->validation->ServiceDesc, 'class'=>'box'));?>
      <?=$this->validation->ServiceDesc_error;?></dd>
   </dl>
</div>

<p class="blockintro">Enter the URL to the live pages this vendor supplies if applicable.</p>
<div class="block">
   <dl>
      <dt><label for="URL">URL:</label></dt>
      <dd><?=form_input(array('name'=>'URL', 'id'=>'URL', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->URL));?>
      <?=$this->validation->URL_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this vendor'))?> or <a class="admin" href="<?=site_url('sites/vendors/index/'.$site_id);?>">Cancel</a>
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