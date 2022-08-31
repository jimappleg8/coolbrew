<body id="stores">

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['error'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertbad_icon.gif) #C00 left no-repeat; clear:both; color:#FFF; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #C99;"><?=$admin['error'];?></div>
<?php endif; ?>

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

   <h1>Process duplicate record <span>&mdash; <?=$total;?> found</span></h1>

            </div>

            <div class="innercol">

<?php if ( ! empty($stores)): ?>

<p>The two records below appear to be duplicates. Please choose the one you wish to delete and press the link below it.</p>

<table>

<tr>
<?php foreach ($stores AS $store): ?>
<td style="width:300px;">
   <?php if ( ! empty($store['messages'])): ?><p><a class="admin" href="<?=site_url('stores/edit/'.$store['StoreID'].'/'.$last_action);?>">This store has messages (<?=count($store['messages']);?>).</a></p><?php else: ?><p><a href="<?=site_url('utilities/delete_duplicate/'.$store['StoreID']);?>">Delete this record</a></p><?php endif; ?>
   <p><strong><?=$store['StoreName'];?></strong>
   <br /><?=$store['Address1'];?>
   <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
   <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> <?=$store['Country'];?>
   <br /><strong>Phone:</strong> <?=$store['Phone'];?>
   <br /><strong>Fax:</strong> <?=$store['Fax'];?>
   <br /><strong>Website:</strong> <?=$store['Website'];?>
   </p>

   <p><strong>StoreID:</strong> <?=$store['StoreID'];?>
   <br /><strong>Source:</strong> <?=$store['Source'];?>
   <br /><strong>latitude:</strong> <?=$store['latitude'];?>
   <br /><strong>longitude:</strong> <?=$store['longitude'];?>
   <br /><strong>status:</strong> <?=$store['status'];?>
   <br /><strong>Brands:</strong> <?=$store['Brands'];?>
   <br /><strong>NotBrands:</strong> <?=$store['NotBrands'];?>
   <br /><strong>ContactName:</strong> <?=$store['ContactName'];?>
   <br /><strong>ContactEmail:</strong> <?=$store['ContactEmail'];?>
   <br /><strong>ContactPhone:</strong> <?=$store['ContactPhone'];?>
   <br /><strong>Notes:</strong> <?=$store['Notes'];?>
   </p>
   
</td>
<?php endforeach; ?>
</tr>
</table>

<?php else: ?>

<p>No duplicate records were found.</p>

<?php endif; ?>

            </div>   <?php // innercol ?>

         </div>   <?php // col ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2011 The Hain Celestial Group, Inc.

        </div>   <?php // Footer ?>

      </div>   <?php // Left ?>

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
&nbsp;
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>

   </div>   <?php // class="container" ?>

</div>   <?php // Wrapper ?>

</body>
