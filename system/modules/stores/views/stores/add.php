<body>

<?php $query = unserialize($this->session->userdata('store_query')); ?>
<form id="previous" method="POST" action="<?=site_url('stores/index');?>">
<div>
<input type="hidden" name="StoreName" value="<?=$query['StoreName'];?>" />
<input type="hidden" name="City" value="<?=$query['City'];?>" />
<input type="hidden" name="State" value="<?=$query['State'];?>" />
<input type="hidden" name="Zip" value="<?=$query['Zip'];?>" />
<input type="hidden" name="Phone" value="<?=$query['Phone'];?>" />
</div>
</form>

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

   <a class="admin" href="#" onclick="document.getElementById('previous').submit(); return false;">Cancel</a>

               </div>
               
   <h1>Add a Store</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('stores/add/'.$last_action);?>">

<p class="blockintro">Tell us about the store.</p>
<div class="block">
   <dl>
      <dt><label for="StoreName">Store Name:</label></dt>
      <dd><?=form_input(array('name'=>'StoreName', 'id'=>'StoreName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->StoreName));?>
      <?=$this->validation->StoreName_error;?></dd>

      <dt><label for="Address1">Address 1:</label></dt>
      <dd><?=form_input(array('name'=>'Address1', 'id'=>'Address1', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Address1));?>
      <?=$this->validation->Address1_error;?></dd>

      <dt><label for="Address2">Address 2:</label></dt>
      <dd><?=form_input(array('name'=>'Address2', 'id'=>'Address2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Address2));?>
      <?=$this->validation->Address2_error;?></dd>

      <dt><label for="City">City:</label></dt>
      <dd><?=form_input(array('name'=>'City', 'id'=>'City', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->City));?>
      <?=$this->validation->City_error;?></dd>

      <dt><label for="State">State:</label></dt>
      <dd><?=form_input(array('name'=>'State', 'id'=>'State', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->State));?>
      <?=$this->validation->State_error;?></dd>

      <dt><label for="Zip">Zip/Postal Code:</label></dt>
      <dd><?=form_input(array('name'=>'Zip', 'id'=>'Zip', 'maxlength'=>'15', 'size'=>'10', 'value'=>$this->validation->Zip));?>
      <?=$this->validation->Zip_error;?></dd>

      <dt><label for="Country">Country:</label></dt>
      <dd><?=form_dropdown('Country', $countries, $this->validation->Country);?>
      <?=$this->validation->Country_error;?></dd>

      <dt><label for="Phone">Phone:</label></dt>
      <dd><?=form_input(array('name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->Phone));?>
      <?=$this->validation->Phone_error;?></dd>

      <dt><label for="Fax">Fax:</label></dt>
      <dd><?=form_input(array('name'=>'Fax', 'id'=>'Fax', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->Fax));?>
      <?=$this->validation->Fax_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt><label for="status">Status:</label></dt>
      <dd><?=form_dropdown('status', $statuses, $this->validation->status);?>
      <?=$this->validation->status_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save and continue'))?> or <a class="admin" href="#" onclick="document.getElementById('previous').submit(); return false;">Cancel</a>
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