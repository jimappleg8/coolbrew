<body onunload="GUnload()" id="map-page">

   <script type="text/javascript">
   //<![CDATA[

   var key = '<?=$map_api_key;?>';
   var api_tag = '<scr'+'ipt src="http://maps.google.com/maps?file=api&v=2.s&key='+key+'" type="text/javascript"></scr'+'ipt>';

   document.write(api_tag);
   //]]>
   </script>

   <div class="popup">
   <a href="javascript:window.close();">Close Window</a>
   </div>

   <div id="map" class="mapdiv"></div>
    
   <script language="JavaScript" type="text/javascript">
   //<![CDATA[
   
   var map;
   var geocoder;
   
   var store = "<?=$store['Name'];?>";
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
   var html1 = '<form action="http://maps.google.com/maps" method="get" style="width:200px; margin:0px; padding:0px; font-size:12px; font-family: Arial, Geneva, sans-serif; color:#000000;">';

   html1 += '<h3 class="headertext3">' + store + '</h3>';
   if (address1 != '{null}') html1 += '<label class="smalltext">' + address1 + '</label><br>';
   if (address2 != '{null}') html1 += '<label class="smalltext">' + address2 + '</label><br>';
   if (zip == '{null}') zip = ""; 
   html1 += '<label class="smalltext">' + city + ', ' + state + ' ' + zip + '</label><br>';
   if (phone != '{null}') html1 += '<label class="smalltext">Phone: ' + phone + '</label><br>';
   html1 += '<label class="smalltext">Get directions to this store from:</label><br>';
   html1 += '<input type="text" name="saddr" id="mapsUsSaddr" value="" size="30" class="smalltext"/><br>';
   html1 += '<input type="submit" class="smalltext" value="Get Directions"/>';
   html1 += '<input type="hidden" name="daddr" value="' + address + '" />';
   html1 += '<input type="hidden" name="hl" value="en"/>';
   html1 += '</form>';

   
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
   
    //]]>
   </script>

</body>
