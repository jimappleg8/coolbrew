<body>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('cp/vendor_services/index/');?>">Cancel</a>

               </div>
               
   <h1>Add a Vendor Category</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('/cp/vendor_services/add/'.$parent.'/'.$sort.'/'.$last_action);?>">

<p class="blockintro">Basic category information. The Category Description is used to explain what a category is.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Name">Category Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->Name));?>
      <?=$this->validation->Name_error;?></dd>

      <dt>Category Description:</dt>
      <dd><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>
   </dl>
</div>

<p class="blockintro">Technical details:</p>
<div class="block">
   <dl>
      <dt><label for="SortOrder">Sort Order:</label></dt>
      <dd><?=form_input(array('name'=>'SortOrder', 'id'=>'SortOrder', 'maxlength'=>'12', 'size'=>'10', 'value'=>$this->validation->SortOrder));?>
      <?=$this->validation->SortOrder_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this category'))?> or <a class="admin" href="<?=site_url('cp/vendor_services/index/');?>">Cancel</a>
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