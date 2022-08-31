<?php

class Stores_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';

   // --------------------------------------------------------------------

   function Stores_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      $this->read_db = $this->load->database($level.'-read', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Connects to the local store locator database and returns the results.
    *
    */
   function get_store_data($store_id)
   {
      $sql = 'SELECT * '.
             'FROM stores '.
             'WHERE StoreID = '.$store_id;
      
      $query = $this->read_db->query($sql);
      $store = $query->row_array();
      
      return $store;
   }

   // --------------------------------------------------------------------

   /**
    * Prepares the API request for a Zip search
    */
   function get_store_list_by_zip($search)
   {
      // get the latitude and longitude of this zipcode
      $sql = 'SELECT * '.
             'FROM zipcodes_us '.
             'WHERE zipcode="'.$search['zip'].'"';
      $query = $this->read_db->query($sql);
      $zipRec = $query->row_array();
      
      // check if the zip code was found
      if (count($zipRec) < 1)
      {
         $this->http_code = 400;
         $this->error_dev_msg = 'The Zipcode '.$search['zip'].' is not in our database. It appears to be invalid.';
         $this->error_usr_msg = 'The supplied Zipcode is invalid.';
         return FALSE;
      }

      $new_search['latitude'] = $zipRec["latitude"];
      $new_search['longitude'] = $zipRec["longitude"];
      $new_search['upc'] = $search['upc'];
      $new_search['radius'] = $search['radius'];
      $new_search['site-id'] = $search['site-id'];
      $new_search['count'] = $search['count'];

      return $this->get_store_list($new_search);
   }
   
   // --------------------------------------------------------------------

   /**
    * Prepares the API request for a Latitude/Longitude search
    */
   function get_store_list_by_latlong($search)
   {
      $new_search['latitude'] = $search["latitude"];
      $new_search['longitude'] = $search["longitude"];
      $new_search['upc'] = $search['upc'];
      $new_search['radius'] = $search['radius'];
      $new_search['site-id'] = $search['site-id'];
      $new_search['count'] = $search['count'];

      return $this->get_store_list($new_search);
   }
   
   // --------------------------------------------------------------------

   /**
    * Prepares the API request for a City/State search
    */
   function get_store_list_by_citystate($search)
   {
      // get the lat/long for all zip codes with this city/state
      $sql = 'SELECT latitude, longitude '.
             'FROM zipcodes_us '.
             'WHERE place_name = "'.$search['city'].'" '.
             'AND admin_code1 = "'.$search['state'].'"';
      $query = $this->read_db->query($sql);
      $points = $query->result_array();
      
      // check if the city and state were found
      if (count($points) < 1)
      {
         $this->http_code = 400;
         $this->error_dev_msg = 'The City '.$search['city'].' and State '.$search['state'].' were not in our database.';
         $this->error_usr_msg = 'The supplied City and State were not found. Try entering the nearest larger city or your zipcode.';
         return FALSE;
      }

      $coords = $this->_get_center_from_degrees($points);
      
      $new_search['latitude'] = $coords[0];
      $new_search['longitude'] = $coords[1];
      $new_search['upc'] = $search['upc'];
      $new_search['radius'] = $search['radius'];
      $new_search['site-id'] = $search['site-id'];
      $new_search['count'] = $search['count'];

      return $this->get_store_list($new_search);
   }
   
   // --------------------------------------------------------------------

   /**
    * Connects to the local store locator database and returns the results.
    *
    * @param  string   The zip code
    * @param  integer  The search radius
    * @param  string   The brand ID (site id)
    * @return array
    */
   function get_store_list($search)
   {
      $this->load->helper('text');

      // Reference: http://www.plumislandmedia.net/mysql/haversine-mysql-nearest-loc/
      $sql = 'SELECT d.StoreID, d.StoreName, d.Address1, d.Address2, '.
               'd.City, d.State, d.Zip, d.Phone, d.Website, d.Brands, '.
               'd.Carried, d.DistanceNum, d.radius, d.latitude, d.longitude '.
             'FROM ('.
               'SELECT s.*, '.
                 'p.radius, '.
                 'p.distance_unit '.
                   '* DEGREES(ACOS(COS(RADIANS(p.latpoint)) '.
                   '* COS(RADIANS(s.latitude)) '.
                   '* COS(RADIANS(p.longpoint) - RADIANS(s.longitude)) '.
                   '+ SIN(RADIANS(p.latpoint)) '.
                   '* SIN(RADIANS(s.latitude)))) AS DistanceNum, '.
                 'pp.Carried '.
               'FROM stores AS s '.
               'JOIN ('.
                 'SELECT '.$search['latitude'].' AS latpoint, '.
                   $search['longitude'].' AS longpoint, '.
                   $search['radius'].' AS radius, '.
                   '69.0 AS distance_unit '.
                 ') AS p '.
               'LEFT JOIN ('.
                 'SELECT sp.* '.
                 'FROM stores_product AS sp, pr_product AS pr '.
                 'WHERE pr.ProductID = sp.ProductID '.
                 'AND concat("0", pr.UPC) = "'.$search['upc'].'" '.
                 ') AS pp '.
                 'ON pp.StoreID = s.StoreID '.
               'WHERE s.latitude '.
                 'BETWEEN p.latpoint - (p.radius / p.distance_unit) '.
                 'AND p.latpoint + (p.radius / p.distance_unit) '.
               'AND s.longitude '.
                 'BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint)))) '.
                 'AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint)))) '.
               ') AS d '.
             'WHERE d.Status != "inactive" '.
             'AND d.Status != "pending" '.
             'AND d.NotBrands NOT LIKE "%'.$search['site-id'].'%" '.
             'AND d.NotBrands NOT LIKE "%all%" '.
             'HAVING DistanceNum <= d.radius '.
             'ORDER BY DistanceNum '.
             'LIMIT '.$search['count'];

      $query = $this->read_db->query($sql);
      $results = $query->result_array();

//      echo '<pre>'; print_r($results); echo '</pre>'; exit;

      $stores = array();
      for ($i=0, $cnt=count($results); $i<$cnt; $i++)
      {
         // mark what we know this store carries
         $brand_list = $this->_get_brands_array($results[$i]['Brands']);

         if ($results[$i]['Carried'] == 1)
         {
            // we know this store carries the product
            $results[$i]['Carries'] = 'product';
         }
         elseif (in_array($search['site-id'], $brand_list))
         {
            // the store carries some products in this brand
            $results[$i]['Carries'] = 'brand';
         }
         else
         {
            // we can list it as a remote possibility
            $results[$i]['Carries'] = 'hcg';
         }

         $results[$i]['DistanceNum'] = number_format($results[$i]['DistanceNum'], 1);
         $results[$i]['Distance'] = $results[$i]['DistanceNum'].' mi';

         // clean the data and translate field names.
         $stores[$i]['StoreID'] = trim($results[$i]['StoreID']);
         $stores[$i]['Name'] = ascii_to_entities(trim($results[$i]['StoreName']));
         $stores[$i]['Address1'] = ascii_to_entities(trim($results[$i]['Address1']));
         $stores[$i]['Address2'] = ascii_to_entities(trim($results[$i]['Address2']));
         $stores[$i]['City'] = ascii_to_entities(trim($results[$i]['City']));
         $stores[$i]['State'] = trim($results[$i]['State']);
         $stores[$i]['Zip'] = trim($results[$i]['Zip']);
         $stores[$i]['Phone'] = trim($results[$i]['Phone']);
         $stores[$i]['Latitude'] = trim($results[$i]['latitude']);
         $stores[$i]['Longitude'] = trim($results[$i]['longitude']);
         $stores[$i]['Website'] = trim($results[$i]['Website']);
         $stores[$i]['Distance'] = trim($results[$i]['Distance']);
         $stores[$i]['DistanceNum'] = trim($results[$i]['DistanceNum']);;
         $stores[$i]['Carries'] = trim($results[$i]['Carries']);
         $stores[$i]['Src'] = "local";
      }
      return $stores;
   }

   // --------------------------------------------------------------------

   /**
    * Takes a list of brands and splits them into an array of brand IDs
    * It assumes that some lists may be split by comma and others by spaces.
    *
    */
   function _get_brands_array($brands)
   {
      $results = array();
      $brands = strtolower($brands);
      
      $my_brands = explode(',', $brands);
      foreach ($my_brands AS $brand)
      {
         $brand = trim($brand);
         if (strpos($brand, ' ') !== FALSE)
         {
            $more_brands = explode(' ', $brand);
            foreach ($more_brands AS $mbrand);
            {
               if ($mbrand != '')
               {
                  $results[] = $mbrand;
               }
            }
         }
         else
         {
            if ($brand != '')
            {
               $results[] = $brand;
            }
         }
      }
      return $results;
   }
   
   // --------------------------------------------------------------------

   /**
    * Get a center latitude,longitude from an array of like geopoints
    *
    * @param array data 2 dimensional array of latitudes and longitudes
    * For Example:
    * $data = array
    * (
    *   0 = > array(45.849382, 76.322333),
    *   1 = > array(45.843543, 75.324143),
    *   2 = > array(45.765744, 76.543223),
    *   3 = > array(45.784234, 74.542335)
    * );
    */
   function _get_center_from_degrees($data)
   {
      if (!is_array($data)) return FALSE;

      $num_coords = count($data);

      $X = 0.0;
      $Y = 0.0;
      $Z = 0.0;

      foreach ($data as $coord)
      {
         $lat = $coord['latitude'] * pi() / 180;
         $lon = $coord['longitude'] * pi() / 180;

         $a = cos($lat) * cos($lon);
         $b = cos($lat) * sin($lon);
         $c = sin($lat);

         $X += $a;
         $Y += $b;
         $Z += $c;
      }

      $X /= $num_coords;
      $Y /= $num_coords;
      $Z /= $num_coords;

      $lon = atan2($Y, $X);
      $hyp = sqrt($X * $X + $Y * $Y);
      $lat = atan2($Z, $hyp);

      return array($lat * 180 / pi(), $lon * 180 / pi());
   }

}

/* End of file stores_model.php */
/* Location: ./system/modules/api/models/v1/stores_model.php */