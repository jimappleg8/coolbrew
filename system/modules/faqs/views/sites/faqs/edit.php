<body>

<script type="text/javascript">
//<![CDATA[

function dodelete()
{
  if(confirm(" Are you sure you want to delete this FAQ? "))
  {
    document.location = "<?=site_url('sites/faqs/delete/'.$site_id.'/'.$faq_id.'/'.$last_action);?>";
  }
}

//]]>
</script>

<?php if ($this->session->userdata('faq_query') != ''): ?>
   <?php $query = unserialize($this->session->userdata('faq_query')); ?>
<form name="previous" method="POST" action="<?=site_url('sites/search/index/'.$site_id);?>">
<input type="hidden" name="Words" value="<?=$query['Words'];?>" />
</form>
   <?php $cancel_link = '<a class="admin" href="#" onclick="document.forms[\'previous\'].submit(); return false;">Return to list</a>'; ?>
<?php else: ?>
   <?php $cancel_link = '<a class="admin" href="'.site_url('sites/faqs/index/'.$site_id).'">Return to list</a>'; ?>
<?php endif; ?>

<?=$this->load->view('sites/tabs');?>

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

   <?php if ( ! $is_shared): ?><a class="admin" href="#" onclick="dodelete()">Delete FAQ</a> <span class="pipe">|</span> <?php endif; ?><?=$cancel_link;?>

               </div>
               
   <h1 id="top">Edit FAQ Info<span> | <a href="<?=site_url('sites/categories/assign/'.$site_id.'/'.$faq_id.'/'.$answer_id.'/'.$last_action);?>">Assign Categories</a></span></h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<h1 style="font-weight:normal; margin-bottom:1.5em;"><span style="color:#999; font-size:120%;">Q.</span> <?=($faq['ShortQuestion'] != '') ? $faq['ShortQuestion'] : $faq['Question'];?></h1>

<?php if ($is_shared): ?>
<div id="read-only" style="border-left:0; border-right:0; border-top:1px solid #FC0; margin-bottom:9px;"><p>READ ONLY: This FAQ is shared and cannot be edited here.</p></div>
<?php endif; ?>

               <div id="basic-form">

<form method="post" action="<?=site_url('sites/faqs/edit/'.$site_id.'/'.$faq_id.'/'.$answer_id.'/'.$last_action);?>">

<p class="blockintro">Enter the question and answer. The Short Question allows you to have a more concise question for the link; if no short answer is entered then the full question will be used.</p>
<div class="block">
   <dl>
      <dt><label for="ShortQuestion">Short Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'ShortQuestion', 'id'=>'ShortQuestion', 'cols'=>'50', 'rows'=>'3', 'value'=>$this->validation->ShortQuestion));?>
      <?=$this->validation->ShortQuestion_error;?></dd>

      <dt class="required"><label for="Question">Question:</label></dt>
      <dd><?=form_textarea(array('name'=>'Question', 'id'=>'Question', 'cols'=>'50', 'rows'=>'8', 'value'=>$this->validation->Question));?>
      <?=$this->validation->Question_error;?></dd>

      <dt class="required"><label for="Answer">Answer:</label></dt>
      <dd><?=form_ckeditor(array('name'=>'Answer', 'id'=>'Answer', 'width'=>'410', 'height'=>'150', 'value'=>$this->validation->Answer, 'class'=>'box'));?>
      <?=$this->validation->Answer_error;?></dd>
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

<?php if ( ! $is_shared): ?>
<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <?=$cancel_link;?>
</div>
<?php endif; ?>

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