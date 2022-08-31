IRI Store Locator Product Report
Generated <?=date('Y-m-d');?> 

Summary,Individual UPCs,Product Groups
<?php foreach ($sites as $site): ?>
<?=$brandname[$site];?>:,<?=$product_count[$site];?>,<?=$group_count[$site];?> 
<?php endforeach; ?>
TOTAL:,<?=$product_count['total'];?>,<?=$group_count['total'];?> 

  
   
<?php foreach ($sites as $site): ?>
<?=$brandname[$site];?> 

Individual UPCs: <?=$product_count[$site];?> 
Product Groups: <?=$group_count[$site];?> 

<?=$brandname[$site];?> Individual UPCs:
*part of a multi-sku product group

<?=$line1[$site];?> 

<?=$brandname[$site];?> Product Groups:

<?=$line2[$site];?> 
<?=$line3[$site];?> 


<?php endforeach; ?>
