<div id="store-locator-start">
   <div id="store-locator-start-inner">

<!-- <p>Gluten Free Caf&eacute; products are available in many natural foods stores. To locate the retailer closest to you, simply enter your zip code.</p> -->

<form method="post" action="<?=$action;?>" class="horizontal">
   <fieldset>
<?php if ($by_category == TRUE): ?>

      <div class="field">
         <label for="productid">Item:</label>
         <select name="productid" style="width:400px;">
   <?php if ($any_product != ''): ?>
   <option value="<?=$any_product;?>">-- Any Product --</option>
   <?php endif; ?>
   <?php foreach ($cats AS $cat): ?>
      <?php if ( ! empty($cat['Products'])): ?>
         <optgroup label="<?=$cat['CategoryName'];?>">
         <?php foreach ($cat['Products'] AS $product): ?>
            <?php if ($loc_code == $product['LocatorCode'] && $loc_code != ''): ?>
         <option value="<?=$product['LocatorCode'];?>" selected="selected">-- 	<?=$product['ProductName'];?> --</option>
	        <?php else: ?>
         <option value="<?=$product['LocatorCode'];?>"><?=$product['ProductName'];?></option>
	         <?php endif; ?>
         <?php endforeach; ?>
         </optgroup>
      <?php endif; ?>
   <?php endforeach; ?>
         </select>
      </div>

<?php else: ?>

      <div class="field">
         <label for="productid">Item:</label>
         <select name="productid">
   <?php if ($any_product != ''): ?>
   <option value="<?=$any_product;?>">-- Any Product --</option>
   <?php endif; ?>
   <?php foreach ($products AS $product): ?>
      	<?php if ($loc_code == $product['LocatorCode'] && $loc_code != ''): ?>
         <option value="<?=$product['LocatorCode'];?>" selected="selected">-- 	<?=$product['ProductName'];?> --</option>
	   <?php else: ?>
         <option value="<?=$product['LocatorCode'];?>"><?=$product['ProductName'];?></option>
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
         <label for="searchradius">Distance:</label>
         <select name="searchradius">
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
         <input type="hidden" name="productfamilyid" value="HNCL">
      </div>
      <div class="field">
         <input type="hidden" name="clientid" value="69">
      </div>
      <div class="field">
         <input type="hidden" name="template" value="default.xsl">
      </div>
      <div class="field">
         <input type="hidden" name="stores" value="1">
      </div>
      <div class="field">
         <input type="hidden" name="storespagenum" value="1">
      </div>
      <div class="field">
         <input type="hidden" name="storesperpage" value="10">
      </div>
      <div class="field">
         <input type="hidden" name="etailers" value="0">
      </div>
      <div class="field">
         <input type="hidden" name="producttype" value="agg">
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
