<body id="stores">

<?=$this->load->view('sites/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
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

   <h1>Contact Us: <span><a href="<?=site_url('sites/contactus/index/'.$site_id.'/'.$offset);?>">Messages</a> (<?=$message_count;?>) |</span> Spam <span>(<?=$spam_count;?>)</span></h1>

            </div>

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('sites/contactus_spam/set_filter/'.$offset);?>">

<div class="block">
   <dl>
      <dt><label for="Filter">Filter the list:</label></dt>
      <dd><?=form_input(array('name'=>'Filter', 'id'=>'Filter', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Filter));?>
      <?=$this->validation->Filter_error;?> <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Apply filter'))?></dd>
   </dl>
</div>

</form>

               </div> <?php /* basic-form */ ?>

<div style="margin-bottom:9px; border-bottom:1px solid #666">

<?php foreach ($messages AS $message): ?>

   <?php
   switch ($message['status'])
   {
      case 'unread':
         $color = '#FFF';
         break;
      case 'read':
         $color = '#DDD';
         break;
      default:
         $color = '#FFF';
         break;
   }
   ?>

<div style="border-top:1px solid #666; padding:3px 6px; background-color:<?=$color;?>;">
<a style="text-decoration:none; <?php if ($message['status'] == 'unread'): ?> font-weight:bold;<?php endif; ?>" href="<?=site_url('sites/contactus_spam/detail/'.$site_id.'/'.$message['id'].'/'.$offset.'/'.$last_action);?>"<?php if ($message['status'] == 'unread'): ?> style="font-weight:bold;"<?php endif; ?>><?=character_limiter(strip_tags($message['comment']), 60);?></a>
<br /><span style="color:#999;"><?=$message['fname'];?> <?=$message['lname'];?> &mdash; <?=date('Y-m-d', $message['submit_ts']);?></span>
</div>

<?php endforeach; ?>

</div>

<?=$pagination;?>

            </div>   <?php /* innercol */ ?>

         </div>   <?php /* col */ ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <?php /* Footer */ ?>

      </div>   <?php /* Left */ ?>

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>

   </div>   <?php /* class="container" */ ?>

</div>   <?php /* Wrapper */ ?>

</body>
