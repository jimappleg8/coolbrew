
<form method="post" action="<?=$action;?>">

<?php if ($by_category == TRUE): ?>

<p>Item: <select name="productid" style="width:400px;">
<?php if ($any_product != ''): ?>
<option value="<?=$any_product;?>">-- Any Product --</option>
<?php endif; ?>
<?php foreach ($cats AS $cat): ?>
   <?php if ( ! empty($cat['Products'])): ?>
   <optgroup label="<?=$cat['CategoryName'];?>">
      <?php foreach ($cat['Products'] AS $product): ?>
         <?php if ($loc_code == $product['LocatorCode']): ?>
      <option value="<?=$product['LocatorCode'];?>" selected="selected">-- 	<?=$product['ProductName'];?> --</option>
	     <?php else: ?>
      <option value="<?=$product['LocatorCode'];?>"><?=$product['ProductName'];?></option>
	      <?php endif; ?>
      <?php endforeach; ?>
   </optgroup>
   <?php endif; ?>
<?php endforeach; ?>
</select></p>

<?php else: ?>

<p>Item: <select name="productid">
<?php if ($any_product != ''): ?>
<option value="<?=$any_product;?>">-- Any Product --</option>
<?php endif; ?>
   <?php foreach ($products AS $product): ?>
      	<?php if ($loc_code == $product['LocatorCode']): ?>
   <option value="<?=$product['LocatorCode'];?>" selected="selected">-- 	<?=$product['ProductName'];?> --</option>
	   <?php else: ?>
   <option value="<?=$product['LocatorCode'];?>"><?=$product['ProductName'];?></option>
	   <?php endif; ?>
   <?php endforeach; ?>
</select></p>

<?php endif; ?>

<p>Zip: <input type="text" name="zip" size="15" maxlength="5"></p>

<p>Distance: <select name="searchradius">
   <option value="0">-- Choose a Distance --
   <option value="5">Within 5 miles</option>
   <option value="10" selected>Within 10 miles</option>
   <option value="15">Within 15 miles</option>
   <option value="20">Within 20 miles</option>
   <option value="25">Within 25 miles</option>
   <option value="50">Within 50 miles</option>
   <option value="100">Within 100 miles</option>
</select></p>

<input type="hidden" name="productfamilyid" value="HNCL">
<input type="hidden" name="clientid" value="69">
<input type="hidden" name="template" value="default.xsl">
<input type="hidden" name="stores" value="1">
<input type="hidden" name="storespagenum" value="1">
<input type="hidden" name="storesperpage" value="50">
<input type="hidden" name="etailers" value="0">
<input type="hidden" name="producttype" value="agg">
<input type="hidden" name="brand" value="<?=$brand;?>">
<input type="hidden" name="sort" value="Distance">

<p><input type="submit" value="Find Stores"></p>

</form>