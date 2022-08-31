#!/usr/local/bin/php

<?php

require_once("config.inc.php");
require_once("dbi_adodb.inc.php");
require_once("storelocator.inc.php");

$db = HCGNewConnection('hcg_public');
$db->SetFetchMode(ADODB_FETCH_ASSOC);

   $query = "SELECT * FROM stores ".
            "WHERE latitude NOT REGEXP '[0-9\-]+' ".
            "AND longitude NOT REGEXP '[0-9\-]+' ".
            "AND Country NOT LIKE 'Canada'";

   if ($address = $db->GetRow($query)) {

      print_r($address);

      $address['Country'] = "US";

      $results = scrape_lat_long($address);

      if ((preg_match("/[0-9\-\.]+/", $results['latitude'])) &&
         (preg_match("/[0-9\-\.]+/", $results['longitude']))) {
      
         $query = "UPDATE stores ".
                  "SET latitude=\"".$results['latitude']."\",".
                  "longitude=\"".$results['longitude']."\",".
                  "Country=\"".$address['Country']."\" ".
                  "WHERE StoreID = ".$address['StoreID'];
         echo $query;
         $db->Execute($query);
         echo "\n\nScript was successful:\n\n";
         print_r($results);
      } else {
         $query = "UPDATE stores ".
                  "SET latitude=\"0\",".
                  "longitude=\"0\",".
                  "Country=\"".$address['Country']."\" ".
                  "WHERE StoreID = ".$address['StoreID'];
         echo $query;
         $db->Execute($query);
         echo "\n\nScript was NOT successful:\n\n";
         print_r($results);
      }
   } 
   
?>