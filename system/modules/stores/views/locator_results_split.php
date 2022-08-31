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

<div id="locator-content">

<div id="searchHeader">
   <div id="searchResults"><span class="searchZip"><?=$product_name;?></span>
   <br />SEARCH RESULTS FOR STORES WITHIN <span class="searchZip"><?=$query['radius'];?></span> MILES OF <span class="searchZip"><?=$query['zip'];?></span></div>
   <h3><a href="<?=$action;?>">[ advanced search ]</a></h3>
</div>

<?php if ($error != ''): ?>

<p style="color:red;"><?=$error;?></p>

<?php endif; ?>

   <?php if ($query['sort'] == 'Name'): ?>

<form name="sortdistance" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Distance" />
</form>
<p><strong>Sorted by Store Name</strong> [<a href="#" onclick="document.forms['sortdistance'].submit(); return false;">sort by Distance</a>]</p>

   <?php else: ?>

<form name="sortname" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="item" value="<?=$query['item'];?>" />
<input type="hidden" name="radius" value="<?=$query['radius'];?>" />
<input type="hidden" name="count" value="<?=$query['count'];?>" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Name" />
</form>
<p><strong>Sorted by Distance</strong> [<a href="#" onclick="document.forms['sortname'].submit(); return false;">sort by Store Name</a>]</p>

   <?php endif; ?>

<?php // ------------------------------------------------------------
      //  SECTION 1: stores we know carry this product.
      // ------------------------------------------------------------ ?>

<div id="searchTable">

   <a name="grocery"></a>
   <div class="locator-line"></div>

   <h2>Stores that carry <?=$product_name;?></h2>

   <p>(<strong><?=count($product_stores);?> stores found</strong>)</p>

   <?php if (count($product_stores) > 0): ?>

   <p>We know these stores carry <strong><?=$product_name;?></strong>.</p>

   <table>

   <tr>
   <th class="col-store">Store Name</th>
   <th class="col-distance">Distance</th>
   <th class="col-map">Map</th>
   <th class="col-tellus">Is Our Information Wrong?</th>
   </tr>
	
      <?php $counterA = 0; ?>
      <?php foreach ($product_stores AS $store): ?>

   <tr>
   <td class="col-store">
      <div class="store">
      <?=$store['Name'];?>
      <br /><?=$store['Address1'];?>
      <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
      <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
      <br /><?=$store['Phone'];?>
      </div>
   </td>
   
   <td class="col-distance"><?=$store['DistanceNum'];?><?php if ($store['DistanceNum'] != 'unknown'): ?> mi<?php endif; ?></td>
   
   <td class="col-map">

   <script type="text/javascript">
   document.write("<form id=\"mapA<?=$counterA;?>\" action=\"<?=$action;?>/map\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"Name\" value=\"<?=$store['Name'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=$store['Address1'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=($store['Address2'] == '') ? '{null}' : $store['Address2'];?>\" />");
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

   </td>
   <td class="col-tellus">

   <script type="text/javascript">
   document.write("<form id=\"messageA<?=$counterA;?>\" action=\"<?=$action;?>/message\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"StoreID\" value=\"<?=$store['StoreID'];?>\" />");
   document.write("<input type=\"hidden\" name=\"StoreName\" value=\"<?=$store['Name'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=$store['Address1'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=$store['Address2'];?>\" />");
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

   </td>
   </tr>

      <?php endforeach; ?>

   </table>


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

   <?php endif; ?>

<br />

<?php // ------------------------------------------------------------
      //  SECTION 2: stores we know carry this product's brand.
      // ------------------------------------------------------------ ?>

<?php if (count($brand_stores) > 0): ?>

   <a name="naturalfoods"></a>
   <div class="locator-line"></div>

   <h2>Stores that carry <?=$brand_name;?> products</h2>

   <p>(<strong><?=count($brand_stores);?> stores found</strong>)</p>

   <?php if (count($brand_stores) > 0): ?>

   <p>We know these stores carry <strong><?=$brand_name;?> products</strong>. Please call the store to find out if they carry the specific product that you are looking for.</p>

   <table>

   <tr>
   <th class="col-store">Store Name</th>
   <th class="col-distance">Distance</th>
   <th class="col-map">Map</th>
   <th class="col-tellus">Is Our Information Wrong?</th>
   </tr>
	
      <?php $counterB = 0; ?>
      <?php foreach ($brand_stores AS $store): ?>

   <tr>
   <td class="col-store">
      <div class="store">
      <?=$store['Name'];?>
      <br /><?=$store['Address1'];?>
      <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
      <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
      <br /><?=$store['Phone'];?>
      </div>
   </td>
   
   <td class="col-distance"><?=$store['DistanceNum'];?><?php if ($store['DistanceNum'] != 'unknown'): ?> mi<?php endif; ?></td>
   
   <td class="col-map">

   <script type="text/javascript">
   document.write("<form id=\"mapA<?=$counterB;?>\" action=\"<?=$action;?>/map\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"Name\" value=\"<?=$store['Name'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=$store['Address1'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=($store['Address2'] == '') ? '{null}' : $store['Address2'];?>\" />");
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

   </td>
   <td class="col-tellus">

   <script type="text/javascript">
   document.write("<form id=\"messageA<?=$counterB;?>\" action=\"<?=$action;?>/message\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"StoreID\" value=\"<?=$store['StoreID'];?>\" />");
   document.write("<input type=\"hidden\" name=\"StoreName\" value=\"<?=$store['Name'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=$store['Address1'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=$store['Address2'];?>\" />");
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

   </td>
   </tr>

      <?php endforeach; ?>

   </table>


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

   <?php endif; ?>

<br />

<?php endif; ?>

<?php // ------------------------------------------------------------
      //  SECTION 3: stores we know carry some HCG products.
      // ------------------------------------------------------------ ?>

   <a name="others"></a>
   <div class="locator-line"></div>

   <h2>Stores that carry some Hain Celestial Group products</h2>

   <p>(<strong><?=count($hcg_stores);?> stores found</strong>)</p>

   <?php if (count($hcg_stores) > 0): ?>

   <p>We know these stores carry at least some of the <strong>Hain Celestial Group family of products</strong>. There's a possibility they'll have what you want, but you'll want to call ahead to make sure.</p>

   <table>

   <tr>
   <th class="col-store">Store Name</th>
   <th class="col-distance">Distance</th>
   <th class="col-map">Map</th>
   <th class="col-tellus">Is Our Information Wrong?</th>
   </tr>
	
      <?php $counterC = 0; ?>
      <?php foreach ($hcg_stores AS $store): ?>

   <tr>
   <td class="col-store">
      <div class="store">
      <?=$store['Name'];?>
      <br /><?=$store['Address1'];?>
      <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
      <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
      <br /><?=$store['Phone'];?>
      </div>
   </td>
   
   <td class="col-distance"><?=$store['DistanceNum'];?><?php if ($store['DistanceNum'] != 'unknown'): ?> mi<?php endif; ?></td>
   
   <td class="col-map">

   <script type="text/javascript">
   document.write("<form id=\"mapA<?=$counterC;?>\" action=\"<?=$action;?>/map\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"Name\" value=\"<?=$store['Name'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=$store['Address1'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=($store['Address2'] == '') ? '{null}' : $store['Address2'];?>\" />");
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

   </td>
   <td class="col-tellus">

   <script type="text/javascript">
   document.write("<form id=\"messageA<?=$counterC;?>\" action=\"<?=$action;?>/message\" method=\"post\" target=\"map\" onsubmit=\"centeredFullWindow('','map','640','640');\">");
   document.write("<input type=\"hidden\" name=\"StoreID\" value=\"<?=$store['StoreID'];?>\" />");
   document.write("<input type=\"hidden\" name=\"StoreName\" value=\"<?=$store['Name'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address1\" value=\"<?=$store['Address1'];?>\" />");
   document.write("<input type=\"hidden\" name=\"Address2\" value=\"<?=$store['Address2'];?>\" />");
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

   </td>
   </tr>

      <?php endforeach; ?>

   </table>


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

   <?php endif; ?>

</div>

</div>  <!-- /locator-content -->