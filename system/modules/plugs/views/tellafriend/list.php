<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

               </div>

   <h1>All tell-a-friend widgets</h1>

            </div>

            <div class="innercol">

<?php if ($admin['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$admin['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($admin['tell_exists'] == true): ?>

   <div class="listing">
   
   <?php foreach($tell_list AS $item): ?>

      <div style="border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><a style="text-decoration:none;" href="<?=site_url('tellafriend/edit/'.$site_id.'/'.$item['ID'].'/'.$last_action);?>"><?=$item['TellName'];?></a> (<?=$item['Language'];?>)</p>
      </div>

   <?php endforeach; ?>
   
   <div style="border-top:1px solid #666; clear:both;"></div>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no tell-a-friend widgets to display.</p>
   
   <p><a href="<?=site_url('tellafriend/add/'.$site_id.'/'.$last_action);?>" class="admin">Create the first tell-a-friend widget.</a></p>

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
            
            <a href="<?=site_url('tellafriend/add/'.$site_id.'/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_tell.gif" width="138" height="31" alt="Add a new tell" style="border:0px; margin:4px 0 24px 0;" /></a>

         </div>   <?php // col ?>
         
      </div>   <?php // Right ?>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
