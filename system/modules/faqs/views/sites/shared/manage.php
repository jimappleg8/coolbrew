<body>

<script type="application/javascript">
<!--
$(document).ready(function()
{
<?php foreach($shared_list AS $shared): ?>
   $('div#<?="faq-answers-".$shared["ID"];?>').hide();

   // toggles the slickbox on clicking the noted link  
   $('a#<?="faq-answers-".$shared["ID"];?>-toggle').click(function() {
      $('div#<?="faq-answers-".$shared["ID"];?>').slideToggle(400);
      return false;
   });
<?php endforeach; ?>
});
//-->
</script>


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

               </div>

   <h1>Manage Shared FAQs</h1>

            </div>

            <div class="innercol">

<?php if ($admin['shared_exists'] == true): ?>

   <form method="post" action="<?=site_url('sites/shared/manage/'.$site_id.'/'.$last_action);?>">
   
   <div class="listing">
   
   <?php foreach($shared_list AS $shared): ?>

      <?php $fieldname = 'faq-'.$shared['ID']; ?>

      <div style="border-top:1px solid #666; clear:both;">
      <div style="float:right;"><a href="#" id="<?='faq-answers-'.$shared['ID'];?>-toggle"><img src="/images/expand.gif" style="border:0; margin-top:6px;" /></a></div>
      <p style="margin:0; padding:4px 0; font-weight:bold;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$shared['ShortQuestion'];?></label></p>
      <div id="<?='faq-answers-'.$shared['ID'];?>">
      <?php foreach($shared['Answers'] AS $answer): ?>
         <?php $answername = 'answer-'.$shared['ID'].'-'.$answer['ID']; ?>
         <div style="margin-left:4em; text-indent:-2em; border-top:1px dotted #666;"><p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$answername;?>" id="<?=$answername;?>" value="1" <?=$this->validation->set_checkbox($answername, '1');?> /> <label for="<?=$answername;?>" style="font-style:italic;">(<?=($answer['Note'] != '') ? $answer['Note'] : 'No descriptive note given';?>)</label></p>
         <div class="faq-answer"><?=$answer['Answer'];?></div></div>
      <?php endforeach; ?>
      </div>
      </div>

   <?php endforeach; ?>

   </div> <?php /* listing */ ?>
   
   <div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?>
   </div>

   </form>

<?php else: ?>

   <p>There are no shared FAQs to display.</p>
   

<?php endif; ?>

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
            
         </div>   <?php /* col */ ?>
         
      </div>   <?php /* Right */ ?>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>
