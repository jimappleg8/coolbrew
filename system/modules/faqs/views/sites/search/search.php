<body>

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

   <h1>Search FAQs</h1>

            </div>

            <div class="innercol">
    
               <div id="basic-form">

<form method="post" action="<?=site_url('sites/search/index/'.$site_id);?>">

<p class="blockintro">Search all FAQs for this site</p>
<div class="block">
   <dl>
      <dt><label for="Words">Keywords:</label></dt>
      <dd><?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'40', 'value'=>$this->validation->Words));?>
   <?=$this->validation->Words_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Search'))?>
</div>

</form>
               </div> <?php // basic-form ?>


<?php if ($admin['faq_exists'] == true): ?>

   <h2>Search Results</h2>

   <div class="listing">
   
   <?php foreach($faq_list AS $faq): ?>

     <div style="border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><?php if ($faq['Status'] != 'active'): ?>(<?=$faq['Status'];?>) <?php endif; ?><?php if (in_array($faq['ID'], $shared)): ?>(shared) <?php endif; ?><a href="<?=site_url('sites/faqs/edit/'.$site_id.'/'.$faq['ID'].'/'.$faq['AnswerID'].'/'.$last_action);?>"style="text-decoration:none;"><?=nl2br($faq['ShortQuestion']);?></a><?php if ($faq['FlagAsNew'] == 1): ?>&nbsp;&nbsp;<span style="color:red;">NEW!</span><?php endif; ?></p>
         <?php if (in_array($faq['ID'], $shared)): ?>
         <div style="margin-left:2em; border-top:1px dotted #666;"><p style="margin:0; padding:4px 0;"><?=$faq['Answer'];?></p></div>
         <?php endif; ?>
      </div>

   <?php endforeach; ?>
   
   <div style="border-top:1px solid #666; clear:both;"></div>

   </div> <?php // listing ?>

<?php elseif (isset($search)): ?>

   <h2>Search Results</h2>

   <p>No FAQ were found.</p>

<?php endif; ?>

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
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
