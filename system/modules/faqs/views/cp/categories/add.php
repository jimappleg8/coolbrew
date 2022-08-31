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

   <a class="admin" href="<?=site_url('cp/categories/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add an FAQ Category</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/categories/add/'.$site_id.'/'.$parent.'/'.$sort.'/'.$last_action);?>">

<p class="blockintro">First, choose a short string ID for the FAQ category. This string is limited to characters, numbers and underlines or dashes. Be careful about changing this as it may be referenced in site pages and it could break how some of the pages work.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="FaqCode">Category Code:</label></dt>
      <dd><?=form_input(array('name'=>'FaqCode', 'id'=>'FaqCode', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->FaqCode));?>
      <?=$this->validation->FaqCode_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, give us the basic category information:</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Name">Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->Name));?>
      <?=$this->validation->Name_error;?></dd>

      <dt>Description:</dt>
      <dd><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 50, 'rows' => 8, 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>
   </dl>
</div>

<p class="blockintro">Technical details:</p>
<div class="block">
   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this FAQ category'))?> or <a class="admin" href="<?=site_url('cp/categories/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy; 2009 The Hain Celestial Group, Inc.

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