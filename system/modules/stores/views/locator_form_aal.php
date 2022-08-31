<div id="store-locator-start">
   <div id="store-locator-start-inner">

<form method="post" action="<?=$action;?>" class="horizontal">
   <fieldset>
<?php if ($by_category == TRUE): ?>

      <div class="field">
         <label for="item">Item:</label>
         <select name="item" class="item">
   <?php if ($any_product != ''): ?>
   <option value="<?=$any_product;?>">-- Any Product --</option>
   <?php endif; ?>
   <?php foreach ($cats AS $cat): ?>
      <?php if ( ! empty($cat['Products'])): ?>
         <optgroup label="<?=$cat['CategoryName'];?>">
         <?php foreach ($cat['Products'] AS $product): ?>
            <?php if ($loc_code == $product['UPC'] && $loc_code != ''): ?>
         <option value="<?=$product['UPC'];?>" selected="selected">-- 	<?=$product['ProductName'];?> --</option>
	        <?php else: ?>
         <option value="<?=$product['UPC'];?>"><?=$product['ProductName'];?></option>
	         <?php endif; ?>
         <?php endforeach; ?>
         </optgroup>
      <?php endif; ?>
   <?php endforeach; ?>
         </select>
      </div>

<?php else: ?>

      <div class="field">
         <label for="item">Item:</label>
         <select name="item">
   <?php if ($any_product != ''): ?>
   <option value="<?=$any_product;?>">-- Any Product --</option>
   <?php endif; ?>
   <?php foreach ($products AS $product): ?>
      	<?php if ($loc_code == $product['UPC'] && $loc_code != ''): ?>
         <option value="<?=$product['UPC'];?>" selected="selected">-- 	<?=$product['ProductName'];?> --</option>
	   <?php else: ?>
         <option value="<?=$product['UPC'];?>"><?=$product['ProductName'];?></option>
	   <?php endif; ?>
   <?php endforeach; ?>
         </select>
      </div>

<?php endif; ?>

      <div class="field">
         <label for="zip">Zip:</label>
         <input type="text" class="text" name="zip" size="15" maxlength="5">
      </div>

      <div class="field">
         <label for="radius">Distance:</label>
         <select name="radius">
         <option value="0">-- Choose a Distance --</option>
         <option value="5">Within 5 miles</option>
         <option value="10" selected="selected">Within 10 miles</option>
         <option value="15">Within 15 miles</option>
         <option value="20">Within 20 miles</option>
         <option value="25">Within 25 miles</option>
         <option value="50">Within 50 miles</option>
         <option value="100">Within 100 miles</option>
         </select>
      </div>

      <div class="field">
         <input type="hidden" name="count" value="50">
      </div>
      <div class="field">
         <input type="hidden" name="brand" value="<?=$brand;?>">
      </div>
      <div class="field">
         <input type="hidden" name="sort" value="Distance">
      </div>

      <div class="buttons">
         <input type="submit" class="button" value="Find Stores">
      </div>
   </fieldset>
</form>

   </div>   <?php // store-locator-start-inner ?>
</div>   <?php // store-locator-start ?>
