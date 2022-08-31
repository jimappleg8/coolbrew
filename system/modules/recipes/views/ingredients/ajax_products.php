      <option value="">None</option>
<?php foreach ($cats AS $cat): ?>
   <?php if ( ! empty($cat['Products'])): ?>
   <optgroup label="<?=$cat['CategoryName'];?>">
      <?php foreach ($cat['Products'] AS $product): ?>
         <?php if ($product_id == $product['ProductID']): ?>
      <option value="<?=$product['ProductID'];?>" selected="selected">	<?=$product['ProductName'];?></option>
	     <?php else: ?>
      <option value="<?=$product['ProductID'];?>"><?=$product['ProductName'];?></option>
	      <?php endif; ?>
      <?php endforeach; ?>
   </optgroup>
   <?php endif; ?>
<?php endforeach; ?>
