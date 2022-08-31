<body id="stores">

<?=$this->load->view('tabs');?>

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

   <h1>Process Messages <span>&mdash; <?=$message_count;?> open messages.</span></h1>

            </div>

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('messages/set_filter/'.$offset);?>">

<div class="block">
   <dl>
      <dt><label for="Filter">Filter the list:</label></dt>
      <dd><?=form_input(array('name'=>'Filter', 'id'=>'Filter', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Filter));?>
      <?=$this->validation->Filter_error;?> <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Apply filter'))?></dd>
   </dl>
</div>

</form>

               </div> <?php // basic-form ?>

<div style="margin-bottom:9px; border-bottom:1px solid #666">

<?php foreach ($messages AS $message): ?>

   <?php
   switch ($message['Status'])
   {
      case 'active':
         $color = '#FFF';
         break;
      case 'in-progress':
         $color = '#FF0';
         break;
      default:
         $color = '#FFF';
         break;
   }
   if ($message['StoreID'] == 0)
   {
      $color = '#F99';
   }
   ?>

<div style="border-top:1px solid #666; padding:3px 6px; background-color:<?=$color;?>;<?php if ($message['Status'] == 'unread'): ?>  font-weight:bold;<?php endif; ?>">
<?=date('Y-m-d', strtotime($message['DateSent']));?> | <a href="<?=site_url('messages/detail/'.$message['ID'].'/'.$offset.'/'.$last_action);?>"<?php if ($message['Status'] == 'unread'): ?> style="font-weight:bold;"<?php endif; ?>><?=$message['StoreName'];?>, <?=$message['City'];?>, <?=$message['State'];?></a> | <?=$message['Status'];?><?php if ($message['StoreID'] == 0): ?> (Nielsen)<?php endif; ?>
</div>

<?php endforeach; ?>

</div>

<?=$pagination;?>

            </div>   <?php /* innercol */ ?>

         </div>   <?php /* col */ ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007 The Hain Celestial Group, Inc.

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
