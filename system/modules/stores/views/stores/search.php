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

               </div>
               
   <h1>Find a Store</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('stores/index');?>">

<p class="blockintro">What are you looking for?</p>
<div class="block">
   <dl>
      <dt><label for="StoreName">Store Name:</label></dt>
      <dd><?=form_input(array('name'=>'StoreName', 'id'=>'StoreName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->StoreName));?>
      <?=$this->validation->StoreName_error;?></dd>

      <dt><label for="City">City:</label></dt>
      <dd><?=form_input(array('name'=>'City', 'id'=>'City', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->City));?>
      <?=$this->validation->City_error;?></dd>

      <dt><label for="State">State:</label></dt>
      <dd><?=form_input(array('name'=>'State', 'id'=>'State', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->State));?>
      <?=$this->validation->State_error;?></dd>

      <dt><label for="Zip">Zip/Postal Code:</label></dt>
      <dd><?=form_input(array('name'=>'Zip', 'id'=>'Zip', 'maxlength'=>'15', 'size'=>'10', 'value'=>$this->validation->Zip));?>
      <?=$this->validation->Zip_error;?></dd>

      <dt><label for="Phone">Phone:</label></dt>
      <dd><?=form_input(array('name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->Phone));?>
      <?=$this->validation->Phone_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Find stores'))?>
</div>

</form>

               </div> <?php // basic-form ?>
   
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
            
            <h1>Resources</h1>

   <div style="margin-left:12px;">
      <p style="margin:0; padding:4px 0;"><a href="/docs/user-manual/stores/updating-stores.html">Guide to updating store data</a></p>
      
      <p style="margin:0; padding:4px 0;"><a href="/docs/user-manual/site-codes-reference.php">Site Codes Reference</a></p>
   </div>
  
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