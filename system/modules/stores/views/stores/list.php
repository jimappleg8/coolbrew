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
               
   <a class="admin" href="<?=site_url('stores/index');?>">New search</a>
               </div>

   <h1>Search results <span>&mdash; <?=count($stores);?> stores found</span></h1>

            </div>

            <div class="innercol">

<?php if ($admin['store_found'] == true): ?>

   <div class="listing">

   <?php foreach($stores AS $store): ?>
   
      <div class="store clearfix">

         
         <div class="store-name"><?php if ($store['longitude'] != 0 && $store['latitude'] != 0): ?><img src="/images/stores/coordinates-yes.gif" width="35" height="21" alt="XY" style="float:right; padding-top:8px;" /><?php else: ?><img src="/images/stores/coordinates-no.gif" width="35" height="21" alt="no-XY" style="float:right; padding-top:8px;" /><?php endif; ?><a href="<?=site_url('stores/edit/'.$store['StoreID']).'/'.$last_action;?>"><?=$store['StoreName'];?></a> <?php if ($store['status'] == 'inactive'): ?>&nbsp;&nbsp;<span style="color:#F00;">(inactive)</span><?php endif; ?><?php if ($store['MessageCount'] > 0): ?><img src="/images/stores/message-icon.gif" width="18" height="11" alt="|M|" style="display:inline; padding:0 4px;" /><span style="color:#000;">(<?=$store['MessageCount'];?>)</span><?php endif; ?>
         <br /><span class="address"><?=$store['Address1'];?><?php if ($store['Address2'] != ''): ?>, <?=$store['Address2'];?><?php endif; ?>, <?=$store['City'];?>, <?=$store['State'];?> <?=($store['Zip'] != '') ? $store['Zip'] : '<span style="color:red;">no zip code</span>';?> | <?=($store['Phone'] != '') ? $store['Phone'] : '<span style="color:red;">no phone number</span>';?></span></div>         
      
      </div> <?php // store ?>
   
   <?php endforeach; ?>
   
   </div> <?php // listing ?>

<?php else: ?>

   <p>No stores were found.</p>

<?php endif; ?>

            </div>   <?php // innercol ?>

         </div>   <?php // col ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007-2009 The Hain Celestial Group, Inc.

        </div>   <?php // Footer ?>

      </div>   <?php // Left ?>

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
            <a href="<?=site_url('stores/add/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_store.gif" width="138" height="31" alt="Add a new store" style="border:0px; margin:4px 0 24px 0;" /></a>

         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>

   </div>   <?php // class="container" ?>

</div>   <?php // Wrapper ?>

</body>
