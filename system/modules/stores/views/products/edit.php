<body>

<script type="text/javascript">
//<![CDATA[

var key = '<?=$map_api_key;?>';
var api_tag = '<scr'+'ipt src="http://maps.google.com/maps?file=api&v=2.118&key='+key+'" type="text/javascript"></scr'+'ipt>';

document.write(api_tag);

function listProducts()
{
   $('#ProductID').empty();
   mysiteid = $('select#ProductSiteID').val();
   $.ajax({
      type: 'post',
      url: "<?=site_url('products/ajax_products/');?>"+'/'+mysiteid,
      success: function(r) {
         $("#ProductID").html(r);
      }
   });
   if (mysiteid == '')
      $('#ProductID').attr('disabled', 'disabled');
   else
      $('#ProductID').removeAttr('disabled');
}

function addProductLink(store, last_action)
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('products/add');?>"+'/'+store+'/'+last_action,
      data: $("#add_product_link").serialize(),
      success: function(r) {
         $("#product_list").html(r);
      }
   });
}

function deleteProduct(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#product_list").html(r);
      }
   });
}


//]]>
</script>

<?php $query = unserialize($this->session->userdata('store_query')); ?>
<form id="previous" method="post" action="<?=site_url('stores/index');?>">
<div>
<input type="hidden" name="StoreName" value="<?=$query['StoreName'];?>" />
<input type="hidden" name="City" value="<?=$query['City'];?>" />
<input type="hidden" name="State" value="<?=$query['State'];?>" />
<input type="hidden" name="Zip" value="<?=$query['Zip'];?>" />
<input type="hidden" name="Phone" value="<?=$query['Phone'];?>" />
</div>
</form>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

    <a class="admin" href="#" onclick="document.getElementById('previous').submit(); return false;">Cancel</a>

               </div>
               
   <h1><span><a href="<?=site_url('stores/edit/'.$store_id.'/'.$last_action);?>">Edit a Store</a> |</span> Product links</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<h1 style="margin-bottom:24px; line-height:1em;"><?=$store['Name'];?>
<br /><span style="font-size:0.6em;"><?=$store['Address1'];?>, <?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?></span></h1>

               <div id="basic-form">

<p class="blockintro">To add product links, select the product, and whether to mark it as CARRIED or NOT CARRIED. Linked products are listed below.</p>
<div class="block">

   <div class="listing">
   <dl>

<form id="add_product_link" onsubmit="return false;">

   <div style="margin:6px 0; border-top:1 px solid #000; border-bottom:1 px solid #000;">

      <dt><label for="ProductSiteID">Product:</label></dt>
      <dd><?=form_dropdown('ProductSiteID', $site_list, $this->validation->ProductSiteID, 'id="ProductSiteID" onchange="listProducts();"');?>
      <?=$this->validation->ProductSiteID_error;?></dd>

      <dt><label for="ProductID">&nbsp;</label></dt>
      <dd><select id="ProductID" name="ProductID" <?php if ($this->validation->ProductSiteID == ''): ?>disabled="disabled" <?php endif; ?>style="width:350px;">
      <?=$product_select;?>
      </select>
      <?=$this->validation->ProductID_error;?></dd>

      <dt>&nbsp;</dt>
      <dd class="Radio">
      <input type="radio" name="Carried" id="Carried" value="1" <?=$this->validation->set_radio('Carried', '1');?> \>
      This product is CARRIED by this store.<br />
      <input type="radio" name="Carried" id="Carried" value="0" <?=$this->validation->set_radio('Carried', '0');?> \>
      This product is NOT CARRIED by this store.<br />
      </dd>

      <dt>&nbsp;</dt>
      <dd><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add product link', 'onclick'=>"addProductLink('$store_id', '$last_action');"))?></dd>
   </div>
   
</form>

   </dl>
   </div> <?php /* listing */ ?>

</div>


<div class="product_list" id="product_list">
<?=$products;?>
</div>


               </div> <?php /* basic-form */ ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
   <h2>Google Map</h2>
   
   <div id="map" class="mapdiv"></div>

<script type="text/javascript">
//<![CDATA[
   
   var map;
   var geocoder;
   
   var store = "<?=addslashes($store['Name']);?>";
   var address1 = "<?=$store['Address1'];?>";
   var address2 = "<?=$store['Address2'];?>";
   var city = "<?=$store['City'];?>";
   var state = "<?=$store['State'];?>";
   var zip = "<?=$store['Zip'];?>";
   var phone = "<?=$store['Phone'];?>";

   var address = address1;
   if (address2 != '{null}') address +=  ' ' + address2;
   address += ', ' + city + ', ' + state + ' ' + zip;
   
   // Generate HTML for the location tab.
   var html1 = '<form action="http://maps.google.com/maps" method="get">';

   html1 += '<div><h3 class="headertext3">' + store + '<'+'/h3>';
   if (address1 != '{null}') html1 += '<label class="smalltext">' + address1 + '<'+'/label><br>';
   if (address2 != '{null}' && address2 != '') html1 += '<label class="smalltext">' + address2 + '<'+'/label><br>';
   if (zip == '{null}') zip = ""; 
   html1 += '<label class="smalltext">' + city + ', ' + state + ' ' + zip + '<'+'/label><br>';
   if (phone != '{null}') html1 += '<label class="smalltext">Phone: ' + phone + '<'+'/label><br>';
   html1 += '<label class="smalltext">Get directions to this store from:<'+'/label><br>';
   html1 += '<input type="text" name="saddr" id="mapsUsSaddr" value="" size="30" class="smalltext" /><br>';
   html1 += '<input type="submit" class="smalltext" value="Get Directions"/>';
   html1 += '<input type="hidden" name="daddr" value="' + address + '" />';
   html1 += '<input type="hidden" name="hl" value="en"/><'+'/div>';
   html1 += '<'+'/form>';

   
   if (GBrowserIsCompatible())
   {
      var map = new GMap2(document.getElementById("map"));

      map.setCenter(new GLatLng(37.4419, -122.1419), 13);

      geocoder = new GClientGeocoder();
      geocoder.getLatLng(
         address,
         function(point)
         {
            if (!point) {
               alert(address + " not found");
            } else {
               map.setCenter(point, 13);
               var marker = new GMarker(point);
               map.addOverlay(marker);
               marker.openInfoWindowHtml(html1);
            }
         }
      );
   }
   
   function insertLatLng() 
   {
      if (geocoder) {
      geocoder.getLatLng(address,
         function(point)
         {
            if ( ! point)
            {
               alert(address + " not found");
            }
            else
            {
               document.getElementById("latitude").value = point.lat();
               document.getElementById("longitude").value = point.lng();
            }
         }
      );
      }
   }
   
//]]>
</script>
   
   <?php 
   $address_line = ($store['Address2'] != '') ? $store['Address1'].', '.$store['Address2'] : $store['Address1'];
   $encoded_address = urlencode($store['Name'].', '.$address_line.', '.$store['City'].' '.$store['State'].' '.$store['Zip']); 
   ?>
   <p style="margin-top:30px;"><a href="http://www.google.com/search?q=<?=$encoded_address;?>" rel="external">Search Google for this store.</a></p>
   
   
   <?php if (count($messages) > 0):?>
<h2>Messages for this store</h2>
<p class="blockintro"><?php if (count($messages) == 1): ?>There is 1 message regarding this store:<?php else: ?>There are <?=count($messages);?> messages regarding this store:<?php endif; ?></p>
<div class="block">
   <ul>
   <?php foreach ($messages AS $message): ?>
   <li><a href="<?=site_url('messages/detail/'.$message['ID'].'/0/'.$last_action);?>" target="_blank"><?=$message['DateSent'];?></a> - <?=$message['Message'];?></li>
   <?php endforeach; ?>
   </ul>
</div>
<?php endif; ?>

   
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>