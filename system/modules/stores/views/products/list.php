<div id="products">

<h2>Products CARRIED by this store</h2>
<div class="block">
<?php if ($admin['products_carried_exist'] == true): ?>

   <div class="listing">

<table style="background-color:transparent;">
   <?php foreach($products_carried AS $prod): ?>
      <?php if ($prod['Carried'] == 1): ?>
<tr>
   <td style="width:60px; background-color:transparent;"><a href="<?=site_url('products/delete/'.$store_id.'/'.$prod['ProductID']);?>" class="admin" onclick="deleteProduct(this.href); return false;">delete</a></td>
   <td style="width:521px; background-color:transparent;"> 
   <?=$prod['SiteID'];?>: <?=$prod['ProductName'];?><?php if ($prod['PackageSize'] != ''): ?> (<?=$prod['PackageSize']; ?>)<?php endif; ?>
   </td>
</tr>
      <?php endif; ?>
   <?php endforeach; ?>
</table>

   </div> <?php /* listing */ ?>

<?php else: ?>

   <p>There are no products to display.</p>
   
<?php endif; ?>
   
</div>


<h2>Products NOT CARRIED by this store</h2>
<div class="block">
<?php if ($admin['products_not_carried_exist'] == true): ?>

   <div class="listing">

<table style="background-color:transparent;">
   <?php foreach($products_not_carried AS $prod): ?>
      <?php if ($prod['Carried'] == 0): ?>
<tr>
   <td style="width:60px; background-color:transparent;"><a href="<?=site_url('products/delete/'.$store_id.'/'.$prod['ProductID']);?>" class="admin" onclick="deleteProduct(this.href); return false;">delete</a></td>
   <td style="width:521px; background-color:transparent;"> 
   <?=$prod['SiteID'];?>: <?=$prod['ProductName'];?><?php if ($prod['PackageSize'] != ''): ?> (<?=$prod['PackageSize']; ?>)<?php endif; ?>
   </td>
</tr>
      <?php endif; ?>
   <?php endforeach; ?>
</table>

   </div> <?php /* listing */ ?>

<?php else: ?>

   <p>There are no products to display.</p>
   
<?php endif; ?>
   
</div>

</div>