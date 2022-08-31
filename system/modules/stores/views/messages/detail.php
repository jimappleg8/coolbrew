<body>

<?=$this->load->view('tabs');?>

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

    <a class="admin" href="<?=site_url('messages/index/'.$offset);?>">Cancel</a>

               </div>
               
   <h1>Message Detail</h1>

            </div>   <!-- page_header -->

            <div class="innercol">
            
<p>From: <?=$message['FirstName'];?> <?=$message['LastName'];?> &lt;<?=$message['Email'];?>&gt; visiting the <?=$message['SiteID'];?> website.
<br />Sent: <?=$message['DateSent'];?></p>
            
<p><?=$message['Message'];?></p>
            
<p>Store: <?php if ($message['StoreID'] != 0): ?><a class="admin" href="<?=site_url('stores/edit/'.$message['StoreID'].'/'.$last_action);?>" target="_blank">edit</a><?php endif; ?>
<br /><?=$message['StoreName'];?>
<br /><?=$message['Address1'];?>
<?=($message['Address2'] != '') ? '<br />'.$message['Address2'] : '';?>
<br /><?=$message['City'];?>, <?=$message['State'];?> <?=$message['Zip'];?>
<br /><?=$message['Phone'];?></p>
            
<p>Product:
<br /><?=$message['ProductName'];?> (<?=$message['ProductID'];?>)
<br /><?php if ($carried == 'yes'): ?>
<span style="color:green;">This product is marked as CARRIED by this store.</span>
<br /><a class="admin" href="<?=site_url('messages/edit_link/'.$message_id.'/'.$offset.'/remove');?>">Remove this link</a> | <a class="admin" href="<?=site_url('messages/edit_link/'.$message_id.'/'.$offset.'/not-carried');?>">Set to NOT CARRIED</a>
<?php elseif ($carried == 'no'): ?>
<span style="color:red;">This product is marked as NOT CARRIED by this store.</span>
<br /><a class="admin" href="<?=site_url('messages/edit_link/'.$message_id.'/'.$offset.'/remove');?>">Remove this link</a> | <a class="admin" href="<?=site_url('messages/edit_link/'.$message_id.'/'.$offset.'/carried');?>">Set to CARRIED</a>
<?php elseif ($carried == 'not-found'): ?>
<span style="color:red;">This product was not found in the database.</span>
<?php else: ?>
<span style="color:#F90;">This product is not connected to this store.</span>
<br /><a class="admin" href="<?=site_url('messages/edit_link/'.$message_id.'/'.$offset.'/carried');?>">Set to CARRIED</a> | <a class="admin" href="<?=site_url('messages/edit_link/'.$message_id.'/'.$offset.'/not-carried');?>">Set to NOT CARRIED</a>
<?php endif; ?></p>
            
               <div id="basic-form">

<form id="messageForm" method="post" action="<?=site_url('messages/detail/'.$message_id.'/'.$offset.'/'.$last_action);?>">

<h2 id="status_information">Status Information</h2>
<div class="block">
   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>

      <dt><label for="StatusNotes">Status Notes:</label></dt>
      <dd><?=form_textarea(array('name'=>'StatusNotes', 'id'=>'StatusNotes', 'cols' => 50, 'rows' => 3, 'value'=>$this->validation->StatusNotes, 'class'=>'box'));?>
      <?=$this->validation->StatusNotes_error;?></dd>
   </dl>
</div>

<?php if (count($others) > 0):?>
<h2 id="status_information">Other Messages</h2>
<p class="blockintro"><?php if (count($others) == 1): ?>There is 1 other message regarding this store:<?php else: ?>There are <?=count($others);?> other messages regarding this store:<?php endif; ?></p>
<div class="block">
   <ul>
   <?php foreach ($others AS $other): ?>
   <li><?=$other['DateSent'];?> - <?=$other['Message'];?></li>
   <?php endforeach; ?>
   </ul>
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="SetOthers" id="SetOthers" value="1" <?=$this->validation->set_checkbox('SetOthers', '1');?> />  <span style="font-size:11px;"><?php if (count($others) == 1): ?>Set this other message with the same status update.<?php else: ?>Set these other messages with the same status update.<?php endif; ?></span>
      <?=$this->validation->SetOthers_error;?></dd>
   </dl>
</div>
<?php endif; ?>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('messages/index/'.$offset);?>">Cancel</a>
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