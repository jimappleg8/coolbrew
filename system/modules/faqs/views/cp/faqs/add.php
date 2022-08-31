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

   <a class="admin" href="<?=site_url('cp/faqs/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add an FAQ</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/faqs/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">Start by entering the question and your first answer. You'll be able to fill out more fields and more answers after you click "Continue".</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Question">Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'Question', 'id'=>'Question', 'cols'=>'50', 'rows'=>'8', 'value'=>$this->validation->Question));?>
      <?=$this->validation->Question_error;?></dd>

      <dt><label for="Note">Answer Note:</label></dt>
      <dd><?=form_input(array('name'=>'Note', 'id'=>'Note', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Note));?>
      <?=$this->validation->Note_error;?></dd>

      <dt class="required"><label for="Answer">Answer:</label></dt>
      <dd><?=form_fckeditor(array('name'=>'Answer', 'id'=>'Answer', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'410', 'height'=>'200', 'value'=>$this->validation->Answer));?>
      <?=$this->validation->Answer_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Continue'))?> or <a class="admin" href="<?=site_url('cp/faqs/index/'.$site_id);?>">Cancel</a>
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