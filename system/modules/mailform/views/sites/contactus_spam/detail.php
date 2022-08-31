<body>

<?=$this->load->view('sites/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

    <a class="admin" href="<?=site_url('sites/contactus_spam/index/'.$site_id.'/'.$offset);?>">Cancel</a>

               </div>
               
   <h1>Contact Us: <span><a href="<?=site_url('sites/contactus/index/'.$site_id.'/'.$offset);?>">Messages</a> (<?=$message_count;?>) | </span><a href="<?=site_url('sites/contactus_spam/index/'.$site_id.'/'.$offset);?>">Spam</a> <span>(<?=$spam_count;?>)</span> &mdash; Message detail</h1>

            </div>   <!-- page_header -->

            <div class="innercol">
            
<p>From: <span style="font-size:1.4em;"><strong><?=$message['fname'];?> <?=$message['lname'];?> &lt;<?=$message['email'];?>&gt;</strong></span>
<br />Sent: <?=date('Y-m-d h:i:s', $message['submit_ts']);?></p>

<p><?=auto_typography($message['comment']);?></p>
            
               <div id="basic-form">

<form id="messageForm" method="post" action="<?=site_url('sites/contactus_spam/detail/'.$site_id.'/'.$message_id.'/'.$offset.'/'.$last_action);?>">

<h2 id="status_information">Status Information</h2>
<div class="block">
   <dl>
      <dt><label for="status">Status:</label></dt>
      <dd><?=form_dropdown('status', $statuses, $this->validation->status);?>
      <?=$this->validation->status_error;?></dd>

      <dt><label for="spam">Is this spam?</label></dt>
      <dd><?=form_dropdown('spam', $spams, $this->validation->spam);?>
      <?=$this->validation->spam_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('sites/contactus_spam/index/'.$site_id.'/'.$offset);?>">Cancel</a>
</div>

</form>

               </div>   <!-- basic-form -->
               
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
            
   
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>