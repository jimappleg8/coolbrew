{ "productSimpleList": {
   "source": "<?=$source;?>",
   "response": {
<?php if ( ! empty($categories)): ?>
      "ProductCategories": [
 <?php for ($i=0, $cnt=count($categories); $i<$cnt; $i++): ?>
     {
  <?php $last = count($categories[$i]); $cnt = 1; ?>
  <?php foreach ($categories[$i] AS $key => $value): ?>
   <?php if ($key == 'Products' && ! empty($value)): ?>
         "Products": [
    <?php $xlast = count($value); $xcnt = 1; ?>
    <?php foreach ($value AS $product): ?>
            {
     <?php $plast = count($product) - 1; $pcnt = 1; ?>
     <?php foreach ($product AS $pkey => $pvalue): ?>
      <?php if ($pkey != 'CategoryName'): ?>
       <?php if ($pvalue != ''): ?>
  "<?=$pkey;?>": <?=$pvalue;?><?php if ($pcnt != $plast): ?>,<?php endif; ?> 
      <?php $pcnt++; ?>
       <?php endif; ?>
      <?php endif; ?>
     <?php endforeach; ?>
           }<?php if ($xcnt != $xlast): ?>,<?php endif; // end Product object ?> 
    <?php $xcnt++; ?>
    <?php endforeach; ?>
         ] <?php // end Products object ?> 
   <?php elseif ($value != ''): ?>
   "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
   <?php endif; ?>
  <?php endforeach; ?>
    }<?php if ($i != $cnt-1): ?>,<?php endif; // end ProductCategory object ?> 
 <?php endfor; ?>
  ], <?php // end ProductCategories object ?> 
<?php endif; ?>

<?php if ( ! empty($products)): ?>
     "Products": [
 <?php $plast = count($products); $pcnt = 1; ?>
 <?php foreach ($products AS $product): ?>
     {
  <?php $last = count($product); $cnt = 1; ?>
  <?php foreach ($product AS $key => $value): ?>
     "<?=$key;?>": <?=$value;?><?php if ($cnt != $last): ?>,<?php endif; ?> 
  <?php $cnt++; ?>
  <?php endforeach; ?>
        }<?php if ($pcnt != $plast): ?>,<?php endif; ?> <?php // end product object ?> 
        <?php $pcnt++; ?>
 <?php endforeach; ?>
      ] <?php // end products array ?> 
<?php endif; ?>
   }, <?php // end response object ?> 
   "status": "<?=$status;?>"
   } <?php // end productSimpleList object ?> 
} <?php // end JSON code ?> 
