<?php

class Stores_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $store_exists_cache = array();

   // --------------------------------------------------------------------

   function Stores_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a store record for the indicated store ID
    *
    * @access   public
    * @param    int      The Store ID to search for
    * @return   array
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
      $brand = $search['brand'];
      $item = $search['item'];

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
         $brand_list = $this->get_brands_array($results[$i]['Brands']);
         $not_brand_list = $this->get_brands_array($results[$i]['NotBrands']);

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
         $stores[$cnt]['StoreID'] = $new_results[$i]['StoreID'];
         $stores[$cnt]['Name'] = ascii_to_entities($new_results[$i]['StoreName']);
         $stores[$cnt]['Address1'] = ascii_to_entities($new_results[$i]['Address1']);
         $stores[$cnt]['Address2'] = ascii_to_entities($new_results[$i]['Address2']);
         $stores[$cnt]['City'] = ascii_to_entities($new_results[$i]['City']);
         $stores[$cnt]['State'] = $new_results[$i]['State'];
         $stores[$cnt]['Zip'] = $new_results[$i]['Zip'];
         $stores[$cnt]['Phone'] = $new_results[$i]['Phone'];
         $stores[$cnt]['Website'] = $new_results[$i]['Website'];
         $stores[$cnt]['Latitude'] = $new_results[$i]['latitude'];
         $stores[$cnt]['Longitude'] = $new_results[$i]['longitude'];
         $stores[$cnt]['Distance'] = $new_results[$i]['Distance'];
         $stores[$cnt]['Carries'] = $new_results[$i]['Carries'];
         $stores[$cnt]['Src'] = "local";
         $cnt++;
      }
      return $stores;
   }

   // --------------------------------------------------------------------

   function get_stores_by_province()
   {
   
   }

   // --------------------------------------------------------------------

   function get_stores_by_region($region, $source)
   {
      $sql = 'SELECT * FROM stores '.
             'WHERE SalesRegion LIKE "'.$region.'" '.
             'AND Source = "'.$source.'"';
      $query = $this->read_db->query($sql);
      $results = $query->result_array();
    
      return $results;
   }

   // --------------------------------------------------------------------
   
   /**
    * Returns a store that has a blank phone number field
    */
   function get_store_no_phone()
   {
      $sql = 'SELECT * '.
             'FROM stores '.
             'WHERE Phone = "" '.
             'AND status != "inactive" '.
             'LIMIT 1';
      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      return $result;
   }

   // --------------------------------------------------------------------

   function insert_store($values)
   {
//      $this->CI =& get_instance();
//      $this->CI->load->library('auditor');
   
      $this->write_db->insert('stores', $values);
      $store_id = $this->write_db->insert_id();
      
//      $this->CI->auditor->audit_insert('stores', '', $values);
      
      return $store_id;
   }

   // --------------------------------------------------------------------

   function update_store($store_id, $values)
   {
      $this->write_db->where('StoreID', $store_id);
      $this->write_db->update('stores', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   function delete_store($store_id)
   {
      // delete any product links
      $this->write_db->where('StoreID', $store_id);
      $this->write_db->delete('stores_product');

      // delete any brand links
      $this->write_db->where('StoreID', $store_id);
      $this->write_db->delete('stores_brand');
      
      // close any open messages associated with this store
      $sql = 'SELECT * '.
             'FROM stores_message '.
             'WHERE StoreID = '.$store_id.' '.
             'AND Status != "closed"';
      $query = $this->read_db->query($sql);
      $messages = $query->result_array();
      
      if ($query->num_rows() > 0)
      {
         foreach ($messages AS $message)
         {
            $values = array();
            $values['StatusNotes'] = $message['StatusNotes'].
              "\nThe store has been deleted.";
            $values['Status'] = 'closed';
            $this->write_db->where('ID', $message['ID']);
            $this->write_db->update('stores_message', $values);
         }
      }
      
      // delete the actual stores record
      $tmp = $this->write_db->where('StoreID', $store_id);
      $this->write_db->delete('stores');

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Takes a list of brands and splits them into an array of brand IDs
    * It assumes that some lists may be split by comma and others by spaces.
    *
    */
   function get_brands_array($brands)
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
    * Checks the database to see if this store already has a record.
    *
    * This is a simple check because it is assumed that the store
    *  records will have been created originally from a data source 
    *  and any subsequent updates will come from the same source and 
    *  have same store name and address format. It uses the 
    *  SourceStoreName and SourceAddress1 fields for that reason.
    *
    * A cache of stores is also kept to reduce calls to the database
    *  in situations where a store is listed multiple times in the
    *  data file, one time for each product.
    *
    * @param   array   the info about the store
    * @return  mixed   the store ID if found, otherwise, FALSE
    *
    */
   function store_exists($store, &$mystore)
   {
      $key = $store['SourceStoreName'] . $store['SourceAddress1'] . $store['City'] . $store['State'] . $store['Zip'];
      
      if (isset($this->store_exists_cache[$key]))
      {
         return $this->store_exists_cache[$key];
      }
      
      $sql = 'SELECT * '.
             'FROM stores '.
             'WHERE SourceStoreName LIKE "'.addslashes($store['SourceStoreName']).'" '.
             'AND SourceAddress1 LIKE "'.$store['SourceAddress1'].'" '.
             'AND City LIKE "'.$store['City'].'" '.
             'AND State LIKE "'.$store['State'].'" '.
             'AND Zip LIKE "'.$store['Zip'].'"';
      $query = $this->read_db->query($sql);
      $mystore = $query->row_array();
   
      if ($query->num_rows() == 1)
      {
         $this->store_exists_cache[$key] = $mystore['StoreID'];
         $query->free_result();  // free up the memory used by this query
         return $mystore['StoreID'];
      }
      
      $query->free_result();  // free up the memory used by this query
      return FALSE;
   }
   
   // --------------------------------------------------------------------

   /**
    * Checks the database to see if this store already has a record.
    *
    * This is a slightly more sophisticated check than store_exists()
    *  because it attempts to deal with situations where the original
    *  record in the database did not come from the same source. It uses
    *  a standardized version of the address to try and match records.
    *
    * The cache of stores used in store_exists() is not necessary because
    *  the list should already be consolidated by the import process.
    *
    * @param   array   the info about the store
    * @return  mixed   the store ID if found, otherwise, FALSE
    *
    */
   function store_exists_tmp($store, &$mystore)
   {
      $sql = 'SELECT * '.
             'FROM stores '.
             'WHERE StandardAddress = "'.$store['StandardAddress'].'" '.
             'AND status = "active"';
      $query = $this->read_db->query($sql);
      $mystore = $query->result_array();
   
      if ($query->num_rows() == 1)
      {
         $mystore = $mystore[0];
         $query->free_result();  // free up the memory used by this query
         return $mystore['StoreID'];
      }
      elseif ($query->num_rows() > 1)
      {
         // more than one store has this address.
      }
      
      $query->free_result();  // free up the memory used by this query
      return FALSE;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns a list of stores from this data source in the database
    *
    * Each data source for stores is assigned a unique source ID 
    *  string that is stored in the "Source" field of the database.
    *  that string is used to find all records from that source.
    *
    * @param   str     the data source ID
    * @return  array   the list of existing stores in the database
    *
    */
   function get_existing_store_lookup($source_id)
   {
      $store_lookup = array();
      
      $sql = 'SELECT StoreID, Address1, City, State, Notes, status '.
             'FROM stores '.
             'WHERE Source LIKE "'.$source_id.'"';
      $query = $this->read_db->query($sql);
      $existing = $query->result_array();
      
      $query->free_result();  // free up the memory used by this query
         
      foreach ($existing AS $exists)
      {
         $store_lookup[$exists['StoreID']] = $exists;
      }
      return $store_lookup;
   }

}

/* End of file stores_model.php */
/* Location: ./system/modules/stores/models/stores_model.php */