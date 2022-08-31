<body>

<script type="text/javascript">
//<![CDATA[

function dodelete()
{
  if (confirm(" Are you sure you want to delete this vendor? "))
  {
    document.location = "<?=site_url('cp/vendors/delete/'.$vendor_id);?>";
  }
}

//]]>
</script>

<?=$this->load->view('cp/tabs');?>

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

   <a class="admin" href="#" onclick="dodelete()">Delete</a> | <a class="admin" href="<?=site_url('cp/vendors/view/'.$vendor_id.'/');?>">Cancel</a>

               </div>

   <h1 id="top"><strong>Vendor Info</a></strong></h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<h1 style="margin-bottom:12px;"><?=$vendor['VendorName'];?></h1>

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/vendors/edit/'.$vendor_id.'/'.$last_action);?>">

<p class="blockintro">Enter the vendor's information.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="VendorName">Vendor's Name:</label></dt>
      <dd><?=form_input(array('name'=>'VendorName', 'id'=>'VendorName', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->VendorName));?>
      <?=$this->validation->VendorName_error;?></dd>
      <dt><label for="Address">Address:</label></dt>
      <dd><?=form_textarea(array('name'=>'Address', 'id'=>'Address', 'cols' => 40, 'rows' => 4, 'value'=>$this->validation->Address, 'class'=>'box'));?>
      <?=$this->validation->Address_error;?></dd>
      <dt><label for="VendorURL">Vendor URL:</label></dt>
      <dd><?=form_input(array('name'=>'VendorURL', 'id'=>'VendorURL', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->VendorURL));?>
      <?=$this->validation->VendorURL_error;?></dd>
   </dl>
</div>

<p class="blockintro">Enter general information about this vendor.</p>
<div class="block">
   <?=form_ckeditor(array('name'=>'AboutThisVendor', 'id'=>'AboutThisVendor', 'cols' => 80, 'rows' => 20, 'value'=>$this->validation->AboutThisVendor, 'class'=>'box'));?>
      <?=$this->validation->AboutThisVendor_error;?>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('cp/vendors/view/'.$vendor_id.'/');?>">Cancel</a>
</div>

</form>

               </div> <?php /* basic-form */ ?>
   
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