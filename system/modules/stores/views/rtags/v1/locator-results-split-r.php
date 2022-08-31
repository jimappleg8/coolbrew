<?php

// start by dividing the $stores list according to the "Carries" field

$product_stores = array();
$brand_stores = array();
$hcg_stores = array();
foreach ($stores AS $store)
{
   switch ($store['Carries'])
   {
      case 'product':
         $product_stores[] = $store;
         break;
      case 'brand':
         $brand_stores[] = $store;
         break;
      case 'hcg':
         $hcg_stores[] = $store;
         break;
   }
}

?>

<script type="text/javascript">
<!--
var newwindow = '';

if ( ! window.centeredFullWindow)
{
   // opens a new window centered on the screen, WITH menu bar, navigation, etc
   function centeredFullWindow(url, myname, w, h, scroll)
   {
      var winl = (screen.width - w) / 2;
      var wint = (screen.height - h) / 2;

      winprops = 'height=' + h + ',width=' + w + ',top=' + wint + ',left=' + winl + ',scrollbars=yes,resizable=yes,toolbar=yes,menubar=yes,location=yes toolbar=yes'

      if ( ! newwindow.closed)
      {
         if (newwindow.location)
         {
            newwindow.location.href = url;
         }
         else
         {
            newwindow = window.open(url, myname, winprops);
            if ( ! newwindow.opener) newwindow.opener = self;
         }
      }
      else
      {
         newwindow = window.open(url, myname, winprops);
         if ( ! newwindow.opener) newwindow.opener = self;
      }
      if (window.focus)
      {
         newwindow.focus()
      }
      return false;
   }
}
//  End -->
</script>

<div id="cb-locator-content-r">

   <div id="cb-locator-header-r">

<h3 class="cb-search-again"><a href="<?=$action;?>">Search Again</a></h3>
<h1 class="cb-product-name"><?=$product_name;?></h1>
<p>Search results for stores within <em><?=$query['radius'];?></em> miles of <em><?=$query['zip'];?></em></p>

   </div>  <?php /* cb-locator-header */ ?>

<?php if ($error != ''): ?>

<p style="color:red;"><?=$error;?></p>

<?php endif; ?>

<?php /* ------------------------------------------------------------
          SECTION 1: stores we know carry this product.
         ------------------------------------------------------------ */ ?>

<div id="product-carried">

   <div id="cb-locator-results-r">

<a name="grocery"></a>
<h2>Stores that carry <?=$product_name;?></h2>

<p class="cb-stores-found">(<?=count($product_stores);?> stores found)</p>

   <?php if (count($product_stores) > 0): ?>

   <p>We have reason to believe these stores carry <strong><?=$product_name;?></strong>, but we recommend that you call the store to make sure.</p>

   </div>  <?php /* cb-locator-results */ ?>

   <div id="cb-locator-stores-r">

      <?php if ($query['sort'] == 'Name'): ?>

<form name="sortdistance" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Distance" />
</form>
<p class="cb-sort-by">Sort by: <strong>store name</strong> | <a href="<?=$action;?>" onclick="document.forms['sortdistance'].submit(); return false;">distance</a></p>

      <?php else: ?>

<form name="sortname" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Name" />
</form>
<p class="cb-sort-by">Sort by: <a href="<?=$action;?>" onclick="document.forms['sortname'].submit(); return false;">store name</a> | <strong>distance</strong></p>

      <?php endif; ?>

   <div class="cb-store-list">

      <div class="cb-store-list-hdr clearfix">
         <div class="cb-col-store">Store Name</div>
         <div class="cb-col-distance">Distance</div>
         <div class="cb-col-map">Map</div>
         <div class="cb-col-tellus">Wrong Store Info?</div>
      </div>
	
      <?php $counterA = 0; ?>
      <?php foreach ($product_stores AS $store): ?>

      <div class="cb-store-list-item clearfix">
         <div class="cb-col-store">
            <div class="cb-store">
            <span class="cb-store-name"><?=$store['Name'];?></span>
            <br /><?=$store['Address1'];?>
            <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
            <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
            <br /><?=$store['Phone'];?>
            </div>
         </div>
   
         <div class="cb-col-distance"><?=$store['Distance'];?><?php if ($store['Distance'] != 'unknown' && strpos($store['Distance'], 'mi') === FALSE): ?> mi<?php endif; ?></div>
   
         <div class="cb-col-map">

   <script type="text/javascript">
   document.write("<form id=\"mapA<?=$counterA;?>\" action=\"<?=$map_action;?>\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"redirect\" value=\"map\" />");
   document.write("<input type=\"hidden\" name=\"Name\" value=\"<?=urlencode($store['Name']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=urlencode($store['Address1']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=($store['Address2'] == '') ? '{null}' : urlencode($store['Address2']);?>\" />");
   document.write("<input type=\"hidden\" name=\"City\" value=\"<?=$store['City'];?>\" />");
   document.write("<input type=\"hidden\" name=\"State\" value=\"<?=$store['State'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Zip\" value=\"<?=$store['Zip'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Phone\" value=\"<?=$store['Phone'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Latitude\" value=\"<?=$store['Latitude'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Longitude\" value=\"<?=$store['Longitude'];?>\" />");
   document.write("<input type=\"submit\" value=\"Map It\" class=\"button\" />");
   document.write("</form>");
   </script>
   
   <noscript>
 	<a href="http://maps.google.com/maps?f=q&hl=en&geocode=&q=<?= $store['Address1'];?>,+<?=$store['City'];?>,+<?=$store['State'];?>+<?=$store['Zip'];?>&sll=37.0625,-95.677068&sspn=35.219929,59.765625&ie=UTF8&ll=39.748592,-105.053244&spn=0.008348,0.014591&z=16&iwloc=addr">Map It</a>
   </noscript>

         </div>
         <div class="cb-col-tellus">

   <script type="text/javascript">
   document.write("<form id=\"messageA<?=$counterA;?>\" action=\"<?=$message_action;?>\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"redirect\" value=\"message\" />");
   document.write("<input type=\"hidden\" name=\"StoreID\" value=\"<?=$store['StoreID'];?>\" />");
   document.write("<input type=\"hidden\" name=\"StoreName\" value=\"<?=urlencode($store['Name']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=urlencode($store['Address1']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=urlencode($store['Address2']);?>\" />");
   document.write("<input type=\"hidden\" name=\"City\" value=\"<?=$store['City'];?>\" />");
   document.write("<input type=\"hidden\" name=\"State\" value=\"<?=$store['State'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Zip\" value=\"<?=$store['Zip'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Phone\" value=\"<?=$store['Phone'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductID\" value=\"<?=$query['item'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductName\" value=\"<?=$product_name;?>\" />");
   document.write("<input type=\"hidden\" name=\"FormName\" value=\"TellMeButton\" />");
   document.write("<input type=\"submit\" value=\"Tell Us\" class=\"button\" />");
   document.write("</form>");
   </script>
   
      <?php $counterA++ ; ?>

         </div>
      </div>

      <?php endforeach; ?>

   </div>
   
   </div>  <?php /* cb-locator-stores */ ?>

   <?php else: ?>

      <?php $plusten = $query['radius'] + 10; ?>
      <?php $plustwenty = $query['radius'] + 20; ?>

   <p>No stores known to carry <?=$product_name;?> were found within <?=$query['radius'];?> miles radius.</p>

   <form id="plus_ten" method="post" action="<?=$action;?>">
   <input type="hidden" name="zip" value="<?=$query['zip'];?>" />
   <input type="hidden" name="item" value="<?=$query['item'];?>" />
   <input type="hidden" name="radius" value="<?=$plusten;?>" />
   <input type="hidden" name="count" value="<?=$query['count'];?>" />
   <input type="hidden" name="brand" value="<?=$query['brand'];?>" />
   <input type="hidden" name="sort" value="<?=$query['sort'];?>" />
   </form>
   
   <form id="plus_twenty" method="post" action="<?=$action;?>">
   <input type="hidden" name="zip" value="<?=$query['zip'];?>" />
   <input type="hidden" name="item" value="<?=$query['item'];?>" />
   <input type="hidden" name="radius" value="<?=$plustwenty;?>" />
   <input type="hidden" name="count" value="<?=$query['count'];?>" />
   <input type="hidden" name="brand" value="<?=$query['brand'];?>" />
   <input type="hidden" name="sort" value="<?=$query['sort'];?>" />
   </form>

   <p><a href="#" onclick="document.forms['plus_ten'].submit(); return false;">Expand search radius to <?=$plusten;?> miles?</a>
   <br /><a href="#" onclick="document.forms['plus_twenty'].submit(); return false;">Expand search radius to <?=$plustwenty;?> miles?</a></p>
   
   </div>  <?php /* cb-locator-results */ ?>

   <?php endif; ?>

<br />

</div>  <?php /* product-carried */ ?>

<?php /* ------------------------------------------------------------
          SECTION 2: stores we know carry this product's brand.
         ------------------------------------------------------------ */ ?>

<div id="brand-carried">

<?php if (count($brand_stores) > 0): ?>

   <div id="cb-locator-results-r">

<a name="naturalfoods"></a>
<h2>Stores that carry <?=$brand_name;?> products</h2>

<p class="cb-stores-found">(<?=count($brand_stores);?> stores found)</p>

   <?php if (count($brand_stores) > 0): ?>

   <p>We know these stores carry <strong><?=$brand_name;?> products</strong>. Please call the store to find out if they carry the specific product that you are looking for.</p>

   </div>  <?php /* cb-locator-results */ ?>

   <div id="cb-locator-stores-r">

      <?php if ($query['sort'] == 'Name'): ?>

<form name="sortdistance" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Distance" />
</form>
<p class="cb-sort-by">Sort by: <strong>store name</strong> | <a href="<?=$action;?>" onclick="document.forms['sortdistance'].submit(); return false;">distance</a></p>

      <?php else: ?>

<form name="sortname" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Name" />
</form>
<p class="cb-sort-by">Sort by: <a href="<?=$action;?>" onclick="document.forms['sortname'].submit(); return false;">store name</a> | <strong>distance</strong></p>

      <?php endif; ?>

   <div class="cb-store-list">

      <div class="cb-store-list-hdr clearfix">
         <div class="cb-col-store">Store Name</div>
         <div class="cb-col-distance">Distance</div>
         <div class="cb-col-map">Map</div>
         <div class="cb-col-tellus">Wrong Store Info?</div>
      </div>
	
      <?php $counterB = 0; ?>
      <?php foreach ($brand_stores AS $store): ?>

      <div class="cb-store-list-item clearfix">
         <div class="cb-col-store">
            <div class="cb-store">
            <span class="cb-store-name"><?=$store['Name'];?></span>
            <br /><?=$store['Address1'];?>
            <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
            <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
            <br /><?=$store['Phone'];?>
            </div>
         </div>
   
         <div class="cb-col-distance"><?=$store['Distance'];?><?php if ($store['Distance'] != 'unknown' && strpos($store['Distance'], 'mi') === FALSE): ?> mi<?php endif; ?></div>
   
         <div class="cb-col-map">

   <script type="text/javascript">
   document.write("<form id=\"mapA<?=$counterB;?>\" action=\"<?=$map_action;?>\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"redirect\" value=\"map\" />");
   document.write("<input type=\"hidden\" name=\"Name\" value=\"<?=urlencode($store['Name']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=urlencode($store['Address1']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=($store['Address2'] == '') ? '{null}' : urlencode($store['Address2']);?>\" />");
   document.write("<input type=\"hidden\" name=\"City\" value=\"<?=$store['City'];?>\" />");
   document.write("<input type=\"hidden\" name=\"State\" value=\"<?=$store['State'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Zip\" value=\"<?=$store['Zip'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Phone\" value=\"<?=$store['Phone'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Latitude\" value=\"<?=$store['Latitude'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Longitude\" value=\"<?=$store['Longitude'];?>\" />");
   document.write("<input type=\"submit\" value=\"Map It\" class=\"button\" />");
   document.write("</form>");
   </script>
   
   <noscript>
 	<a href="http://maps.google.com/maps?f=q&hl=en&geocode=&q=<?= $store['Address1'];?>,+<?=$store['City'];?>,+<?=$store['State'];?>+<?=$store['Zip'];?>&sll=37.0625,-95.677068&sspn=35.219929,59.765625&ie=UTF8&ll=39.748592,-105.053244&spn=0.008348,0.014591&z=16&iwloc=addr">Map It</a>
   </noscript>

         </div>
         <div class="cb-col-tellus">

   <script type="text/javascript">
   document.write("<form id=\"messageA<?=$counterB;?>\" action=\"<?=$message_action;?>\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"redirect\" value=\"message\" />");
   document.write("<input type=\"hidden\" name=\"StoreID\" value=\"<?=$store['StoreID'];?>\" />");
   document.write("<input type=\"hidden\" name=\"StoreName\" value=\"<?=urlencode($store['Name']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=urlencode($store['Address1']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=urlencode($store['Address2']);?>\" />");
   document.write("<input type=\"hidden\" name=\"City\" value=\"<?=$store['City'];?>\" />");
   document.write("<input type=\"hidden\" name=\"State\" value=\"<?=$store['State'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Zip\" value=\"<?=$store['Zip'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Phone\" value=\"<?=$store['Phone'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductID\" value=\"<?=$query['item'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductName\" value=\"<?=$product_name;?>\" />");
   document.write("<input type=\"hidden\" name=\"FormName\" value=\"TellMeButton\" />");
   document.write("<input type=\"submit\" value=\"Tell Us\" class=\"button\" />");
   document.write("</form>");
   </script>
   
      <?php $counterB++ ; ?>

         </div>
      </div>

      <?php endforeach; ?>

   </div>

   </div>  <?php /* cb-locator-stores */ ?>

   <?php else: ?>

      <?php $plusten = $query['radius'] + 10; ?>
      <?php $plustwenty = $query['radius'] + 20; ?>

   <p>No stores known to carry <strong><?=$brand_name;?> products</strong> were found within <?=$query['radius'];?> miles radius.</p>

   <form id="plus_ten" method="post" action="<?=$action;?>">
   <input type="hidden" name="zip" value="<?=$query['zip'];?>" />
   <input type="hidden" name="item" value="<?=$query['item'];?>" />
   <input type="hidden" name="radius" value="<?=$plusten;?>" />
   <input type="hidden" name="count" value="<?=$query['count'];?>" />
   <input type="hidden" name="brand" value="<?=$query['brand'];?>" />
   <input type="hidden" name="sort" value="<?=$query['sort'];?>" />
   </form>
   
   <form id="plus_twenty" method="post" action="<?=$action;?>">
   <input type="hidden" name="zip" value="<?=$query['zip'];?>" />
   <input type="hidden" name="item" value="<?=$query['item'];?>" />
   <input type="hidden" name="radius" value="<?=$plustwenty;?>" />
   <input type="hidden" name="count" value="<?=$query['count'];?>" />
   <input type="hidden" name="brand" value="<?=$query['brand'];?>" />
   <input type="hidden" name="sort" value="<?=$query['sort'];?>" />
   </form>

   <p><a href="#" onclick="document.forms['plus_ten'].submit(); return false;">Expand search radius to <?=$plusten;?> miles?</a>
   <br /><a href="#" onclick="document.forms['plus_twenty'].submit(); return false;">Expand search radius to <?=$plustwenty;?> miles?</a></p>

   </div>  <?php /* cb-locator-results */ ?>

   <?php endif; ?>

<br />

<?php endif; ?>

</div>  <?php /* brand-carried */ ?>

<?php /* ------------------------------------------------------------
          SECTION 3: stores we know carry some HCG products.
         ------------------------------------------------------------ */ ?>

<div id="hcg-carried">

   <div id="cb-locator-results">

<a name="others"></a>
<h2>Stores that carry some Hain Celestial Group products</h2>

<p class="cb-stores-found">(<?=count($hcg_stores);?> stores found)</p>

   <?php if (count($hcg_stores) > 0): ?>

   <p>We know these stores carry at least some of the <strong>Hain Celestial Group family of products</strong>. There's a possibility they'll have what you want, but you'll want to call ahead to make sure.</p>

   </div>  <?php /* cb-locator-results */ ?>

   <div id="cb-locator-stores-r">

      <?php if ($query['sort'] == 'Name'): ?>

<form name="sortdistance" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Distance" />
</form>
<p class="cb-sort-by">Sort by: <strong>store name</strong> | <a href="<?=$action;?>" onclick="document.forms['sortdistance'].submit(); return false;">distance</a></p>

      <?php else: ?>

<form name="sortname" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Name" />
</form>
<p class="cb-sort-by">Sort by: <a href="<?=$action;?>" onclick="document.forms['sortname'].submit(); return false;">store name</a> | <strong>distance</strong></p>

      <?php endif; ?>

   <div class="cb-store-list">

      <div class="cb-store-list-hdr clearfix">
         <div class="cb-col-store">Store Name</div>
         <div class="cb-col-distance">Distance</div>
         <div class="cb-col-map">Map</div>
         <div class="cb-col-tellus">Wrong Store Info?</div>
      </div>
	
      <?php $counterC = 0; ?>
      <?php foreach ($hcg_stores AS $store): ?>

      <div class="cb-store-list-item clearfix">
         <div class="cb-col-store">
            <div class="cb-store">
            <span class="cb-store-name"><?=$store['Name'];?></span>
            <br /><?=$store['Address1'];?>
            <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
            <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
            <br /><?=$store['Phone'];?>
            </div>
         </div>
   
         <div class="cb-col-distance"><?=$store['Distance'];?><?php if ($store['Distance'] != 'unknown' && strpos($store['Distance'], 'mi') === FALSE): ?> mi<?php endif; ?></div>
   
         <div class="cb-col-map">

   <script type="text/javascript">
   document.write("<form id=\"mapA<?=$counterC;?>\" action=\"<?=$map_action;?>\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"redirect\" value=\"map\" />");
   document.write("<input type=\"hidden\" name=\"Name\" value=\"<?=urlencode($store['Name']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=urlencode($store['Address1']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=($store['Address2'] == '') ? '{null}' : urlencode($store['Address2']);?>\" />");
   document.write("<input type=\"hidden\" name=\"City\" value=\"<?=$store['City'];?>\" />");
   document.write("<input type=\"hidden\" name=\"State\" value=\"<?=$store['State'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Zip\" value=\"<?=$store['Zip'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Phone\" value=\"<?=$store['Phone'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Latitude\" value=\"<?=$store['Latitude'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Longitude\" value=\"<?=$store['Longitude'];?>\" />");
   document.write("<input type=\"submit\" value=\"Map It\" class=\"button\" />");
   document.write("</form>");
   </script>
   
   <noscript>
 	<a href="http://maps.google.com/maps?f=q&hl=en&geocode=&q=<?= $store['Address1'];?>,+<?=$store['City'];?>,+<?=$store['State'];?>+<?=$store['Zip'];?>&sll=37.0625,-95.677068&sspn=35.219929,59.765625&ie=UTF8&ll=39.748592,-105.053244&spn=0.008348,0.014591&z=16&iwloc=addr">Map It</a>
   </noscript>

         </div>
         <div class="cb-col-tellus">

   <script type="text/javascript">
   document.write("<form id=\"messageA<?=$counterC;?>\" action=\"<?=$message_action;?>\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"redirect\" value=\"message\" />");
   document.write("<input type=\"hidden\" name=\"StoreID\" value=\"<?=$store['StoreID'];?>\" />");
   document.write("<input type=\"hidden\" name=\"StoreName\" value=\"<?=urlencode($store['Name']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=urlencode($store['Address1']);?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=urlencode($store['Address2']);?>\" />");
   document.write("<input type=\"hidden\" name=\"City\" value=\"<?=$store['City'];?>\" />");
   document.write("<input type=\"hidden\" name=\"State\" value=\"<?=$store['State'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Zip\" value=\"<?=$store['Zip'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Phone\" value=\"<?=$store['Phone'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductID\" value=\"<?=$query['item'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductName\" value=\"<?=$product_name;?>\" />");
   document.write("<input type=\"hidden\" name=\"FormName\" value=\"TellMeButton\" />");
   document.write("<input type=\"submit\" value=\"Tell Us\" class=\"button\" />");
   document.write("</form>");
   </script>
   
      <?php $counterC++ ; ?>

         </div>
      </div>

      <?php endforeach; ?>

   </div>

   </div>  <?php /* cb-locator-stores */ ?>

   <?php else: ?>

      <?php $plusten = $query['radius'] + 10; ?>
      <?php $plustwenty = $query['radius'] + 20; ?>

   <p>No stores known to carry at least some of the <strong>Hain Celestial Group family of products</strong> were found within <?=$query['radius'];?> miles radius.</p>

   <form id="plus_ten" method="post" action="<?=$action;?>">
   <input type="hidden" name="zip" value="<?=$query['zip'];?>" />
   <input type="hidden" name="item" value="<?=$query['item'];?>" />
   <input type="hidden" name="radius" value="<?=$plusten;?>" />
   <input type="hidden" name="count" value="<?=$query['count'];?>" />
   <input type="hidden" name="brand" value="<?=$query['brand'];?>" />
   <input type="hidden" name="sort" value="<?=$query['sort'];?>" />
   </form>
   
   <form id="plus_twenty" method="post" action="<?=$action;?>">
   <input type="hidden" name="zip" value="<?=$query['zip'];?>" />
   <input type="hidden" name="item" value="<?=$query['item'];?>" />
   <input type="hidden" name="radius" value="<?=$plustwenty;?>" />
   <input type="hidden" name="count" value="<?=$query['count'];?>" />
   <input type="hidden" name="brand" value="<?=$query['brand'];?>" />
   <input type="hidden" name="sort" value="<?=$query['sort'];?>" />
   </form>

   <p><a href="#" onclick="document.forms['plus_ten'].submit(); return false;">Expand search radius to <?=$plusten;?> miles?</a>
   <br /><a href="#" onclick="document.forms['plus_twenty'].submit(); return false;">Expand search radius to <?=$plustwenty;?> miles?</a></p>

   </div>  <?php /* cb-locator-results */ ?>

   <?php endif; ?>

</div>  <?php /* hcg-carried */ ?>

</div>  <?php /* cb-locator-content */ ?>