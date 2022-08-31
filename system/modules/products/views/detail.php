<div class="product">

   <div class="productDetails productLeft">

      <h2 style="font-size:16px; color:#3f0400; height:20px; margin:1em 0;"><?=$product['ProductName'];?></h2>

      <p><?=$product['LongDescription'];?></p>

   <?php if ($product['Footnotes'] != ''): ?>
      <div class="productFootnote">
         <p><?=$product['Footnotes'];?></p>
      </div>
   <?php endif; ?>

   <?php if ($product['ProductType'] == 'spice'): ?>
      <div class="productSubTitle">About the Spice</div>
   <?php else: ?>
      <div class="productSubTitle">About the Tea</div>
   <?php endif; ?>

      <p><?=$product['AllNatural'];?></p>

   <?php if ($product['Warning'] != ''): ?>
      <p><b>WARNING:</b> <?=$product['Warning'];?></p>
   <?php endif; ?>
   
 

   <?php if ($product['ProductType'] == 'supplement'): ?>
      <p><span> <?php include DOCROOT.$product['NutritionFacts'];?></span></p>
      <p><br><b>Other&nbsp;Ingredients:</b>
    <?php else: ?>
         <p>This product contains <?=num2words($nlea['CAL']);?> calories.
       <br /><span class="productNutrition leftLink"><a href="/products/nutrition-facts.html/<?=$product['SESFilename'];?>" onclick="centeredWindow(this.href,'pop','300','550'); return false;">View nutritional information</a> ></span></p>
      <p><b>Ingredients:</b>
   <?php endif; ?>
   
  <?php if (isset($product['KosherFile'])): ?>
      <img src="<?=$product['KosherFile'];?>" width="<?=$product['KosherWidth'];?>" height="<?=$product['KosherHeight'];?>" alt="<?=$product['KosherAlt'];?>" style="float:right; border:0; padding-left:6px;" />
   <?php endif; ?>
<?=$product['Ingredients'];?></p>

      <p><?=$product['Gluten'];?></p>

   <?php if ($product['CaffeineHeight'] > 0): ?>
      <p>This product contains caffeine.
      <br /><span class="productNutrition leftLink"><a href="/products/caffeine.html/<?=$product['SESFilename'];?>" onclick="centeredWindow(this.href,'pop','500','300'); return false;">View caffeine meter</a> ></span></p>
   <?php elseif ($product['CaffeineHeight'] == -1): ?>
      <p>This product contains caffeine.</p>
   <?php else: ?>
      <p>This product is naturally caffeine-free.</p>
   <?php endif; ?>

      <br />
   </div>

   <div class="productDetails productRight">
      <img src="/images/products/<?=$category['CategoryCode'];?>/<?=$product['SmallFile'];?>" width="<?=$product['SmallWidth'];?>" height="<?=$product['SmallHeight'];?>" alt="<?=$product['SmallAlt'];?>" style="width:<?=$product['SmallWidth'];?>px;" />

      <div class="findProduct">
         <form name="search" method="post" action="/products/where-to-buy.html">
         <label for="zip">Find this product in a store near you:</label>
         <input id="zip" name="zip" type="text" class="inputText" value="Your Zip Code" onclick="emptyField('zip');" onblur="refillField('zip', 'Your Zip Code');" style="width:150px; padding:3px; font-size:11px;" />
         <input type="hidden" name="productid" value="<?=$product['LocatorCode'];?>" />
         <input type="hidden" name="searchradius" value="10" />
         <input type="hidden" name="productfamilyid" value="HNCL" />
         <input type="hidden" name="clientid" value="69" />
         <input type="hidden" name="template" value="default.xsl" />
         <input type="hidden" name="stores" value="1" />
         <input type="hidden" name="storespagenum" value="1" />
         <input type="hidden" name="storesperpage" value="10" />
         <input type="hidden" name="producttype" value="agg" />
         <input type="hidden" name="brand" value="<?=$product['SiteID'];?>" />
         <input type="hidden" name="sort" value="Distance">
         <input type="image" src="/images/btn_triangle_white.gif" alt="go" />
         </form>
         <p />
   <?php if ($product['StoreDetail'] != 0): ?>
         <div class="productNutrition"><a href="http://celestialseasonings.elsstore.com/view/product/?id=<?=$product['StoreDetail'];?>&cid=<?=$product['StoreSection'];?>">Buy this product online</a> ></div>
   <?php endif; ?>
         <!-- <div class="productNutrition"><a href="#">Request a free sample</a> ></div> -->
      </div>
   </div>
</div>

<div style="clear:both;"></div>

