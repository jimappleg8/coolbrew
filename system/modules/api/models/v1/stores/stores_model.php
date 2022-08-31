<?php

class Stores_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
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
      // we use the "write" database because it points to a specific server
      // where the "read" database should stay "localhost" to balance load.
      $this->read_db = $this->load->database($level.'-write', TRUE);
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
      $this->load->library('Ziplocator');
      
      $zip = $search['zip'];
      $radius = $search['radius'];
      $brand = $search['site-id'];
      $item = $search['product-num'];
      
      if ($zip == "") return FALSE;
      
      $zip_array = $this->ziplocator->inradius($zip, $radius);

      // check if the zip code was found.
      if ( ! $zip_array)
         return array();

      $sql = 'SELECT s.*, pp.Carried '.
             'FROM stores AS s '.
             'LEFT JOIN ('.
               'SELECT sp.* '.
               'FROM stores_product AS sp, pr_product AS p '.
               'WHERE p.ProductID = sp.ProductID '.
               'AND concat("0", p.UPC) = "'.$item.'" '.
               ') AS pp '.
             'ON pp.StoreID = s.StoreID';
      for ($i=0; $i<count($zip_array); $i++)
      {
         $sql .= ($i == 0) ? ' WHERE (' : ' OR ';
         $sql .= 's.Zip = '.$zip_array[$i];
      }
      $sql .= ') AND s.Status != "inactive" '.
              'AND s.Status != "pending" '.
              'AND s.NotBrands NOT LIKE "%'.$brand.'%"';
              
      $query = $this->read_db->query($sql);
      $results = $query->result_array();

      $j = 0;
      $new_results = array();
      for ($i=0, $cnt=count($results); $i<$cnt; $i++)
      {
         // mark what we know this store carries
         $keeper = TRUE;
         $brand_list = $this->_get_brands_array($results[$i]['Brands']);
         $not_brand_list = $this->_get_brands_array($results[$i]['NotBrands']);

         if ($results[$i]['Carried'] == 1)
         {
            // we know this store carries the product
            $results[$i]['Carries'] = 'product';
         }
         elseif (in_array($brand, $brand_list))
         {
            // the store carries some products in this brand
            $results[$i]['Carries'] = 'brand';
         }
         else
         {
            if (in_array('all', $not_brand_list) || 
                in_array($brand, $not_brand_list))
            {
               // we don't want to display this store for this brand
               $keeper = FALSE;
            }
            else
            {
               // we can list it as a remote possibility
               $results[$i]['Carries'] = 'hcg';
            }
         }

         if ($keeper == TRUE)
         {
            // calculate each store's distance from the main zip's location

            if ($results[$i]['latitude'] == 0 || $results[$i]['longitude'] == 0)
            {
               $distance = "unknown";
            }
            else
            {
               $distance = $this->ziplocator->distance($zip, $results[$i]['latitude'], $results[$i]['longitude']);
            }

            $new_results[$j] = $results[$i];

            if ($distance != "unknown")
            {
               $new_results[$j]['Distance'] = number_format($distance, 1);
            }
            else
            {
               $new_results[$j]['Distance'] = $distance;
            }

            $j++;
         }
      }
      
      $cnt = 0;
      $stores = array();
      // translate field names.
      for ($i=0; $i<count($new_results); $i++)
      {
         $stores[$cnt]['StoreID'] = trim($new_results[$i]['StoreID']);
         $stores[$cnt]['Name'] = ascii_to_entities(trim($new_results[$i]['StoreName']));
         $stores[$cnt]['Address1'] = ascii_to_entities(trim($new_results[$i]['Address1']));
         $stores[$cnt]['Address2'] = ascii_to_entities(trim($new_results[$i]['Address2']));
         $stores[$cnt]['City'] = ascii_to_entities(trim($new_results[$i]['City']));
         $stores[$cnt]['State'] = trim($new_results[$i]['State']);
         $stores[$cnt]['Zip'] = trim($new_results[$i]['Zip']);
         $stores[$cnt]['Phone'] = trim($new_results[$i]['Phone']);
         $stores[$cnt]['Website'] = trim($new_results[$i]['Website']);
         $stores[$cnt]['Distance'] = trim($new_results[$i]['Distance']);
         if ($stores[$cnt]['Distance'] != 'unknown')
         {
            $stores[$cnt]['Distance'] = $stores[$cnt]['Distance'].' mi';
         }
         $stores[$cnt]['DistanceNum'] = '';
         $stores[$cnt]['Carries'] = trim($new_results[$i]['Carries']);
         $stores[$cnt]['Src'] = "local";
         $cnt++;
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

}

/* End of file stores_model.php */
/* Location: ./system/modules/api/models/v1/stores_model.php */