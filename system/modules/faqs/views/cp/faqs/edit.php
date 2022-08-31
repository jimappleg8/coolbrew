<body>

<script type="text/javascript">
//<![CDATA[

function dodelete()
{
  if(confirm(" Are you sure you want to delete this FAQ? "))
  {
    document.location = "<?=site_url('cp/faqs/delete/'.$site_id.'/'.$faq_id.'/'.$last_action);?>";
  }
}

function do_answer_delete(answer_id)
{
  if(confirm(" Are you sure you want to delete this answer? "))
  {
    document.location = "<?=site_url('cp/answers/delete/'.$site_id);?>" + "/" + answer_id + "/<?=$last_action;?>";
  }
}

//]]>
</script>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

<?php if ($admin['error_msg'] != ''): ?>
<div id="flash_error"><?=$admin['error_msg'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="#" onclick="dodelete()">Delete FAQ</a> <span class="pipe">|</span> 
   <a class="admin" href="<?=site_url('cp/faqs/index/'.$site_id);?>">Return to list</a>

               </div>
               
   <h1 id="top">Edit FAQ Info<span> | <a href="<?=site_url('cp/categories/assign/'.$site_id.'/'.$faq_id.'/'.$last_action);?>">Assign Categories</a></span></h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<h1 style="font-weight:normal; margin-bottom:1.5em;"><span style="color:#999; font-size:120%;">Q.</span> <?=($faq['ShortQuestion'] != '') ? $faq['ShortQuestion'] : $faq['Question'];?></h1>

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/faqs/edit/'.$site_id.'/'.$faq_id.'/'.$last_action);?>">

<p class="blockintro">Enter the question and answer. The Short Question allows you to have a more concise question for the link; if no short question is entered then the full question will be used.</p>
<div class="block">
   <dl>
      <dt><label for="ShortQuestion">Short Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'ShortQuestion', 'id'=>'ShortQuestion', 'cols'=>'50', 'rows'=>'3', 'value'=>$this->validation->ShortQuestion));?>
      <?=$this->validation->ShortQuestion_error;?></dd>

      <dt class="required"><label for="Question">Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'Question', 'id'=>'Question', 'cols'=>'50', 'rows'=>'8', 'value'=>$this->validation->Question));?>
      <?=$this->validation->Question_error;?></dd>
   </dl>
</div>

<p class="blockintro">The content of the questions and answers above will be indexed for the search, but if there are additional keywords you would like to associate with this FAQ, enter them here. Separate keywords with commas.</p>
<div class="block">
   <dl>
      <dt><label for="Keywords">Keywords:</label></dt>
      <dd><?=form_textarea(array('name'=>'Keywords', 'id'=>'Keywords', 'cols'=>'50', 'rows'=>'6', 'value'=>$this->validation->Keywords));?>
      <?=$this->validation->Keywords_error;?></dd>
   </dl>
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
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('cp/faqs/index/'.$site_id);?>">Return to list</a>
</div>

</form>

<h2>Answers</h2>

<?php foreach($answers AS $key => $answer): ?>

   <?php $label = 'Answer'.$answer['ID']; ?>
   <?php $label_error = $label.'_error'; ?>
   <?php $n_label = 'Note'.$answer['ID']; ?>
   <?php $n_label_error = $n_label.'_error'; ?>

<form method="post" action="<?=site_url('cp/answers/edit/'.$site_id.'/'.$answer['ID'].'/'.$last_action);?>">

<p class="blockintro">Answer number <?=$key + 1;?></p>
<div class="block">
   <dl>
      <dt><label for="<?=$n_label;?>">Note:</label></dt>
      <dd><?=form_input(array('name'=>$n_label, 'id'=>$n_label, 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->$n_label));?>
      <?=$this->validation->$n_label_error;?></dd>

      <dt class="required"><label for="<?=$label;?>">Answer:</label></dt>
      <dd><?=form_fckeditor(array('name'=>$label, 'id'=>$label, 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'410', 'height'=>'200', 'value'=>$this->validation->$label));?>
      <?=$this->validation->$label_error;?></dd>
   </dl>
   <div style="text-align:right;"><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes to this answer'))?> or <a class="admin" href="#" onclick="do_answer_delete('<?=$answer['ID'];?>')">Delete this answer</a></div>
</div>

</form>

<?php endforeach; ?>

<h2>Add a new answer</h2>

<form method="post" action="<?=site_url('cp/answers/add/'.$site_id.'/'.$faq_id.'/'.$last_action);?>">

<p class="blockintro">Type in a new answer and click "Add answer" to save it.</p>
<div class="block">
   <dl>
      <dt><label for="NewNote">Note:</label></dt>
      <dd><?=form_input(array('name'=>'NewNote', 'id'=>'NewNote', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->NewNote));?>
      <?=$this->validation->NewNote_error;?></dd>

      <dt class="required"><label for="NewAnswer">Answer:</label></dt>
      <dd><?=form_fckeditor(array('name'=>'NewAnswer', 'id'=>'NewAnswer', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'410', 'height'=>'200', 'value'=>$this->validation->NewAnswer));?>
      <?=$this->validation->NewAnswer_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add answer'))?>
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
           
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>