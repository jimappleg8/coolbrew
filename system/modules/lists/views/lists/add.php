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

   <a class="admin" href="<?=site_url('lists/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add a List</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('lists/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">First, choose a short string ID for the list. This string is limited to characters, numbers and underlines or dashes. Be careful about changing this as it may be referenced in site pages and it could break how some of the pages work.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="ListCode">List Code:</label></dt>
      <dd><?=form_input(array('name'=>'ListCode', 'id'=>'ListCode', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->ListCode));?>
      <?=$this->validation->ListCode_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, give us the basic list information:</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Name">List Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->Name));?>
      <?=$this->validation->Name_error;?></dd>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="IsHTMLDefault" id="IsHTMLDefault" value="1" <?=$this->validation->set_checkbox('IsHTMLDefault', '1');?> \><label for="IsHTMLDefault">  Contents will generally be HTML.</label>
      <?=$this->validation->IsHTMLDefault_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this list'))?> or <a class="admin" href="<?=site_url('lists/index/'.$site_id);?>">Cancel</a>
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