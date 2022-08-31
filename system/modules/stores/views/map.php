
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

<head>
   <meta http-equiv="content-type" content="text/html; charset=utf-8" />
   <title><?=$brand_name;?> Locator Map</title>

   <script type="text/javascript"
      src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCS2adwBpD2ZACDRb7Piw9UlpNQlao5Xmc&sensor=false">
   </script>

   <?=$css;?>

</head>

<body onunload="GUnload()" id="map-page">

   <div class="popup">
   <a href="javascript:window.close();">Close Window</a>
   </div>

   <div id="map-canvas" class="mapdiv"></div>
    
   <script type="text/javascript">
   
   var map;
   var geocoder;
   
   var store = "<?=$store['Name'];?>";
   var address1 = "<?=$store['Address1'];?>";
   var address2 = "<?=$store['Address2'];?>";
   var city = "<?=$store['City'];?>";
   var state = "<?=$store['State'];?>";
   var zip = "<?=$store['Zip'];?>";
   var phone = "<?=$store['Phone'];?>";
   var latitude = "<?=$store['Latitude'];?>";
   var longitude = "<?=$store['Longitude'];?>";

   var address = address1;
   if (address2 != '{null}') address +=  ' ' + address2;
   address += ', ' + city + ', ' + state + ' ' + zip;
   
   // Generate HTML for the location tab.
   var html1 = '<div style="min-width:225px;"><form action="http://maps.google.com/maps" method="get" style="width:200px; margin:0px; padding:0px; font-size:12px; font-family: Arial, Geneva, sans-serif; color:#000000;">';

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
   html1 += '</form></div>';

   function initialize() {
      var myLatlng = new google.maps.LatLng(latitude, longitude);
      var mapOptions = {
         center: myLatlng,
         zoom: 13,
         mapTypeId: google.maps.MapTypeId.ROADMAP
      };

      var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);
      
      var infowindow = new google.maps.InfoWindow({
         content: html1
      });
      
      var marker = new google.maps.Marker({
         position: myLatlng,
         map: map,
         title: store
      });
      
      infowindow.open(map,marker);
   }

   google.maps.event.addDomListener(window, 'load', initialize);

   </script>

</body>
</html>