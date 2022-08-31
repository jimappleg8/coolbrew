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

<div id="cb-locator-content">

   <div id="cb-locator-header">

<h3 class="cb-search-again"><a href="<?=$action;?>">Search Again</a></h3>
<h1 class="cb-product-name"><?=$product_name;?></h1>
<p>Search results for stores within <em><?=$query['radius'];?></em> miles of <em><?=$query['zip'];?></em></p>

   </div>  <?php /* cb-locator-header */ ?>

   <div id="cb-locator-results">

<?php if ($error != ''): ?>

<p style="color:red;"><?=$error;?></p>

<?php endif; ?>

<h2>Stores that carry <?=$product_name;?></h2>

<p class="cb-stores-found">(<?=count($stores);?> stores found)</p>

   </div>  <?php // cb-locator-results ?>

   <div id="cb-locator-stores">

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

   <table>
   
   <tr>
   <th class="cb-col-store">Store Name</th>
   <th class="cb-col-distance">Distance</th>
   <th class="cb-col-map">Map</th>
   <th class="cb-col-tellus">Wrong Store Info?</th>
   </tr>

   <?php $counterA = 0; ?>
   <?php foreach ($stores AS $store): ?>
      <?php if ($store['Name'] != '' && $store['Address1'] != ''): ?>
   <tr>
   <td class="cb-col-store">
      <div class="cb-store">
      <span class="cb-store-name"><?=$store['Name'];?></span>
      <br /><?=$store['Address1'];?>
         <?php if ($store['Address2'] != ''): ?><br /><?=$store['Address2'];?><?php endif; ?>
      <br /><?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?> 
      <br /><?=$store['Phone'];?>
      </div>
   </td>
   <td class="cb-col-distance"><?=$store['Distance'];?><?php if ($store['Distance'] != 'unknown' && strpos($store['Distance'], 'mi') === FALSE): ?> mi<?php endif; ?></td>

   <td class="cb-col-map">
   

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
   
   </td>
   <td class="cb-col-tellus">

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
   document.write("<"+"/form>");
   </script>
   
      <?php $counterA++ ; ?>

   </td>
   </tr>
      <?php endif; ?>
   <?php endforeach; ?>

   </table>

</div>

</div>  <!-- /locator-content -->