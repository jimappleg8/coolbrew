<div id="locator-content">

<div id="searchHeader">
   <div id="searchResults"><span class="searchZip"><?=$product_name;?></span>
   <br />SEARCH RESULTS FOR STORES WITHIN <span class="searchZip"><?=$query['searchradius'];?></span> MILES OF <span class="searchZip"><?=$query['zip'];?></span></div>
   <h3><a href="<?=$action;?>">[ advanced search ]</a></h3>
</div>
					
<?php if ($error != ''): ?>

<p style="color:red;"><?=$error;?></p>

<?php else: ?>

   <?php if ($query['sort'] == 'Name'): ?>

<form name="sortdistance" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="productid" value="<?=$query['productid'];?>" />
<input type="hidden" name="searchradius" value="<?=$query['searchradius'];?>" />
<input type="hidden" name="productfamilyid" value="HNCL" />
<input type="hidden" name="clientid" value="69" />
<input type="hidden" name="template" value="default.xsl" />
<input type="hidden" name="stores" value="1" />
<input type="hidden" name="storespagenum" value="<?=$query['storespagenum'];?>" />
<input type="hidden" name="storesperpage" value="<?=$query['storesperpage'];?>" />
<input type="hidden" name="etailers" value="0" />
<input type="hidden" name="producttype" value="agg" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Distance" />
</form>
<p><strong>Sorted by Store Name</strong> [<a href="#" onclick="document.forms['sortdistance'].submit(); return false;">sort by Distance</a>]</p>

   <?php else: ?>

<form name="sortname" method="post" action="<?=$action;?>">
<input type="hidden" name="zip" value="<?=$query['zip'];?>" />
<input type="hidden" name="productid" value="<?=$query['productid'];?>" />
<input type="hidden" name="searchradius" value="<?=$query['searchradius'];?>" />
<input type="hidden" name="productfamilyid" value="HNCL" />
<input type="hidden" name="clientid" value="69" />
<input type="hidden" name="template" value="default.xsl" />
<input type="hidden" name="stores" value="1" />
<input type="hidden" name="storespagenum" value="<?=$query['storespagenum'];?>" />
<input type="hidden" name="storesperpage" value="<?=$query['storesperpage'];?>" />
<input type="hidden" name="etailers" value="0" />
<input type="hidden" name="producttype" value="agg" />
<input type="hidden" name="brand" value="<?=$query['brand'];?>" />
<input type="hidden" name="sort" value="Name" />
</form>
<p><strong>Sorted by Distance</strong> [<a href="#" onclick="document.forms['sortname'].submit(); return false;">sort by Store Name</a>]</p>

   <?php endif; ?>

<p>(<b><?=count($stores);?> stores found</b>)</p>

<p>Please call the store to check inventory.</p>
					
<div id="searchTable">

   <table>
   
   <tr>
   <th class="col-store">Store Name</th>
   <th class="col-distance">Distance</th>
   <th class="col-map">Map</th>
   <th class="col-tellus">Is Our Information Wrong?</th>
   </tr>

   <?php $counterA = 0; ?>
   <?php foreach ($stores AS $store): ?>
      <?php if ($store['Name'] != '' && $store['Address1'] != ''): ?>
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

   <td class="col-distance"><?=$store['Distance'];?><?php if ($store['Distance'] != 'unknown'): ?> mi<?php endif; ?></td>

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
   document.write("<input type=\"hidden\" name=\"ProductID\" value=\"<?=$query['productid'];?>\" />");
   document.write("<input type=\"hidden\" name=\"ProductName\" value=\"<?=$product_name;?>\" />");
   document.write("<input type=\"submit\" value=\"Tell Us\" class=\"button\" />");
   document.write("</form>");
   </script>
   
      <?php $counterA++ ; ?>

   </td>
   </tr>
      <?php endif; ?>
   <?php endforeach; ?>

   </table>

</div>

<?php endif; // no error found ?>

</div>  <!-- /locator-content -->
