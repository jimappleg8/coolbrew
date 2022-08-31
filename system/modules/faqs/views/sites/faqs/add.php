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

   <a class="admin" href="<?=site_url('sites/faqs/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add an FAQ</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('sites/faqs/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">Enter the question and answer. The Short Question allows you to have a more concise question for the link; if no short question is entered then the full question will be used.</p>
<div class="block">
   <dl>
      <dt><label for="ShortQuestion">Short Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'ShortQuestion', 'id'=>'ShortQuestion', 'cols'=>'50', 'rows'=>'3', 'value'=>$this->validation->ShortQuestion));?>
      <?=$this->validation->ShortQuestion_error;?></dd>

      <dt class="required"><label for="Question">Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'Question', 'id'=>'Question', 'cols'=>'50', 'rows'=>'8', 'value'=>$this->validation->Question));?>
      <?=$this->validation->Question_error;?></dd>

      <dt class="required"><label for="Answer">Answer:</label></dt>
      <dd><?=form_fckeditor(array('name'=>'Answer', 'id'=>'Answer', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'410', 'height'=>'200', 'value'=>$this->validation->Answer));?>
      <?=$this->validation->Answer_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this FAQ'))?> or <a class="admin" href="<?=site_url('sites/faqs/index/'.$site_id);?>">Cancel</a>
</div>

<p class="blockintro">The content of the questions and answers above will be indexed for the search, but if there are additional keywords you would like to associate with this FAQ, enter them here. Separate keywords with commas.</p>
<div class="block">
   <dl>
      <dt><label for="Keywords">Keywords:</label></dt>
      <dd><?=form_textarea(array('name'=>'Keywords', 'id'=>'Keywords', 'cols'=>'50', 'rows'=>'6', 'value'=>$this->validation->Keywords));?>
      <?=$this->validation->Keywords_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this FAQ'))?> or <a class="admin" href="<?=site_url('sites/faqs/index/'.$site_id);?>">Cancel</a>
</div>

<div class="block">
   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>

      <dt><label for="Sort">Sort:</label></dt>
      <dd><?=form_input(array('name'=>'Sort', 'id'=>'Sort', 'maxlength'=>'8', 'size'=>'4', 'value'=>$this->validation->Sort));?>
      <?=$this->validation->Sort_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="FlagAsNew" id="FlagAsNew" value="1" <?=$this->validation->set_checkbox('FlagAsNew', '1');?> \><label for="FlagAsNew">  Flag as new.</label>
      <?=$this->validation->FlagAsNew_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this FAQ'))?> or <a class="admin" href="<?=site_url('sites/faqs/index/'.$site_id);?>">Cancel</a>
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