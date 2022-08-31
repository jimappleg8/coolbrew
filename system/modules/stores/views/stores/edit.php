<body>

<script type="text/javascript">
//<![CDATA[

var key = '<?=$map_api_key;?>';
var api_tag = '<scr'+'ipt src="http://maps.google.com/maps?file=api&v=2.118&key='+key+'" type="text/javascript"></scr'+'ipt>';

document.write(api_tag);

function dodelete()
{
  if(confirm("Are you sure you want to delete this store?"))
  {
    document.location = "<?=site_url('stores/delete/'.$store_id.'/'.$last_action);?>";
  }
}

function zp(n)
{
   return n<10?("0"+n):n;
}

function appendDate()
{
   var target = document.getElementById("Notes");
   var oldvalue = target.value;
   var now = new Date();
   var DD = zp(now.getDate());
   var MM = zp(now.getMonth()+1);
   var YY = zp(now.getFullYear()%100);
   format = MM + '/' + DD + '/' + YY;
   target.value = oldvalue + format + ' - ';
   target.focus();
}

function listProducts()
{
   $('ProductID').update();
   mysiteid = $('ProductSiteID').value;
   new Ajax.Updater('ProductID', "<?=site_url('products/ajax_products/');?>"+'/'+mysiteid, { });
   if (mysiteid == '')
      $('ProductID').disable();
   else
      $('ProductID').enable();
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

    <a class="admin" href="#" onclick="dodelete()">Delete this store</a> | <a class="admin" href="#" onclick="document.getElementById('previous').submit(); return false;">Cancel</a>

               </div>
               
   <h1>Edit a Store<span> | <a href="<?=site_url('products/edit/'.$store_id.'/'.$last_action);?>">Product links</a></span></h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<h1 style="margin-bottom:24px; line-height:1em;"><?=$store['Name'];?>
<br /><span style="font-size:0.6em;"><?=$store['Address1'];?>, <?=$store['City'];?>, <?=$store['State'];?> <?=$store['Zip'];?></span></h1>

               <div id="basic-form">

<form method="post" action="<?=site_url('stores/edit/'.$store_id.'/'.$last_action);?>">

<p class="blockintro">Tell us about the store.</p>
<div class="block">
   <dl>
      <dt><label for="StoreName">Store Name:</label></dt>
      <dd><?=form_input(array('name'=>'StoreName', 'id'=>'StoreName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->StoreName));?>
      <?=$this->validation->StoreName_error;?></dd>

      <dt><label for="Address1">Address 1:</label></dt>
      <dd><?=form_input(array('name'=>'Address1', 'id'=>'Address1', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Address1));?>
      <?=$this->validation->Address1_error;?></dd>

      <dt><label for="Address2">Address 2:</label></dt>
      <dd><?=form_input(array('name'=>'Address2', 'id'=>'Address2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Address2));?>
      <?=$this->validation->Address2_error;?></dd>

      <dt><label for="City">City:</label></dt>
      <dd><?=form_input(array('name'=>'City', 'id'=>'City', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->City));?>
      <?=$this->validation->City_error;?></dd>

      <dt><label for="State">State:</label></dt>
      <dd><?=form_input(array('name'=>'State', 'id'=>'State', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->State));?>
      <?=$this->validation->State_error;?></dd>

      <dt><label for="Zip">Zip/Postal Code:</label></dt>
      <dd><?=form_input(array('name'=>'Zip', 'id'=>'Zip', 'maxlength'=>'15', 'size'=>'10', 'value'=>$this->validation->Zip));?>
      <?=$this->validation->Zip_error;?></dd>

      <dt><label for="Country">Country:</label></dt>
      <dd><?=form_dropdown('Country', $countries, $this->validation->Country);?>
      <?=$this->validation->Country_error;?></dd>

      <dt><label for="Phone">Phone:</label></dt>
      <dd><?=form_input(array('name'=>'Phone', 'id'=>'Phone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->Phone));?>
      <?=$this->validation->Phone_error;?></dd>

      <dt><label for="Fax">Fax:</label></dt>
      <dd><?=form_input(array('name'=>'Fax', 'id'=>'Fax', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->Fax));?>
      <?=$this->validation->Fax_error;?></dd>

      <dt><label for="Website">Website:</label></dt>
      <dd><?=form_input(array('name'=>'Website', 'id'=>'Website', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Website));?>
      <?=$this->validation->Website_error;?></dd>
   </dl>
</div>

<p class="blockintro">Give the source info for this store. You can specify the 
Store Name and Address fields that are listed in a source file if they are different 
than the data you want to display. If you leave those fields blank, they will be 
filled in automatically when you save.</p>
<div class="block">
   <dl>
      <dt><label for="Source">Source:</label></dt>
      <dd><?=form_input(array('name'=>'Source', 'id'=>'Source', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Source));?>
      <?=$this->validation->Source_error;?></dd>

      <dt><label for="SourceStoreName">Source Store Name:</label></dt>
      <dd><?=form_input(array('name'=>'SourceStoreName', 'id'=>'SourceStoreName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->SourceStoreName));?>
      <?=$this->validation->SourceStoreName_error;?></dd>

      <dt><label for="SourceAddress1">Source Address 1:</label></dt>
      <dd><?=form_input(array('name'=>'SourceAddress1', 'id'=>'SourceAddress1', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->SourceAddress1));?>
      <?=$this->validation->SourceAddress1_error;?></dd>

      <dt><label for="SourceAddress2">Source Address 2:</label></dt>
      <dd><?=form_input(array('name'=>'SourceAddress2', 'id'=>'SourceAddress2', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->SourceAddress2));?>
      <?=$this->validation->SourceAddress2_error;?></dd>

      <dt><label for="SalesRegion">Sales Region:</label></dt>
      <dd><?=form_input(array('name'=>'SalesRegion', 'id'=>'SalesRegion', 'maxlength'=>'128', 'size'=>'30', 'value'=>$this->validation->SalesRegion));?>
      <?=$this->validation->SalesRegion_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="Retailer" id="Retailer" value="1" <?=$this->validation->set_checkbox('Retailer', '1');?> />  This is a BRICK &amp; MORTAR store.
      <?=$this->validation->Retailer_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="Etailer" id="Etailer" value="1" <?=$this->validation->set_checkbox('Etailer', '1');?> />  This is an ONLINE store.
      <?=$this->validation->Etailer_error;?></dd>

      <dt><label for="latitude">Latitude:</label></dt>
      <dd><?=form_input(array('name'=>'latitude', 'id'=>'latitude', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->latitude));?>
      <?=$this->validation->latitude_error;?><a href="#" onclick="insertLatLng(); return false;">auto enter</a></dd>

      <dt><label for="longitude">Longitude:</label></dt>
      <dd><?=form_input(array('name'=>'longitude', 'id'=>'longitude', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->longitude));?>
      <?=$this->validation->longitude_error;?></dd>

      <dt><label for="status">Status:</label></dt>
      <dd><?=form_dropdown('status', $statuses, $this->validation->status);?>
      <?=$this->validation->status_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, tell us about what we know about what brands the store carries. For code information, please refer to the <a href="/docs/user-manual/site-codes-reference.php">Site Codes Reference</a>.</p>
<div class="block">
   <dl>
      <dt>Brands:</dt>
      <dd><?=form_textarea(array('name'=>'Brands', 'id'=>'Brands', 'cols' => 45, 'rows' => 2, 'value'=>$this->validation->Brands, 'class'=>'box'));?>
      <?=$this->validation->Brands_error;?></dd>

      <dt>Not Brands:</dt>
      <dd><?=form_textarea(array('name'=>'NotBrands', 'id'=>'NotBrands', 'cols' => 45, 'rows' => 2, 'value'=>$this->validation->NotBrands, 'class'=>'box'));?>
      <?=$this->validation->NotBrands_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, tell us about any contact info we have.</p>
<div class="block">
   <dl>
      <dt><label for="ContactName">Contact Name:</label></dt>
      <dd><?=form_input(array('name'=>'ContactName', 'id'=>'ContactName', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->ContactName));?>
      <?=$this->validation->ContactName_error;?></dd>

      <dt><label for="ContactEmail">Contact Email:</label></dt>
      <dd><?=form_input(array('name'=>'ContactEmail', 'id'=>'ContactEmail', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->ContactEmail));?>
      <?=$this->validation->ContactEmail_error;?></dd>

      <dt><label for="ContactPhone">Contact Phone:</label></dt>
      <dd><?=form_input(array('name'=>'ContactPhone', 'id'=>'ContactPhone', 'maxlength'=>'30', 'size'=>'30', 'value'=>$this->validation->ContactPhone));?>
      <?=$this->validation->ContactPhone_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt>Notes:
      <br />
      <br /><a href="#" onclick="appendDate(); return false;">append date</a></dt>
      <dd><?=form_textarea(array('name'=>'Notes', 'id'=>'Notes', 'cols' => 45, 'rows' => 10, 'value'=>$this->validation->Notes, 'class'=>'box'));?>
      <?=$this->validation->Notes_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="#" onclick="document.getElementById('previous').submit(); return false;">Cancel</a>
</div>

</form>

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