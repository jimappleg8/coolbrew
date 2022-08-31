<?php

/**
 * Import scripts for store databases
 *
 * The goal of these scripts is to create a simple way to import
 * store data that changes periodically.
 *
 * Sample URL to run these:
 *  http://webadmin:8888/admin/stores.php/imports/import_natural_grocers_stores
 *
 */

class Imports extends Controller {

    
   var $store_lookup;
   var $google_key;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Imports()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'stores'));
      $this->load->helper('url');

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }
	
   // --------------------------------------------------------------------
   
   /**
    * Main index page for accessing import scripts
    */
   function index()
   {
      echo 'These scripts are for importing data.';
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Encodes and array for storage in the database
    */
   function encode_me()
   {
      $columns = array('SourceStoreName', 'SourceAddress1', 'SourceAddress2', 'City', 'State', 'Zip', 'Phone');
      
      echo serialize($columns);
      
      exit;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Encodes and array for storage in the database
    */
   function standardize_address()
   {
      $this->load->library('AddressStandardizationSolution');
      
      $address = 'One Main Street Suite 89, Westminster, CO 80020-1234';
      
      $stan = $this->addressstandardizationsolution->AddressLineStandardization($address);
      
      echo $stan;
      
      exit;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Costco stores
    */
   function import_costco_stores()
   {
      $today = date('Y-m-d');
      $file = '/Users/japplega/Desktop/store-locator-data/Costco/costco-stores.csv';
      $columns = array('SourceStoreName', 'SourceAddress1', 'City', 'State', 'Zip', 'Phone', 'SalesRegion');
      $defaults = array (
        'StoreName' => 'Costco',
        'Brands' => '',
        'NotBrands' => 'all',
        'Website' => 'http://www.costco.com/',
        'Notes' => $today.' added/updated from database pulled from Costco website.',
        'Etailer' => '0',
        'Retailer' => '1',
        'Country' => 'US',
        'Source' => 'import-costco',
        'status' => 'active',
      );
      $options = array (
        'upc_field' => '',
        'missing_file' => '',
      );
      
      $this->_import_stores($file, $columns, $defaults, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Costco product/region links
    *
    * This should probably be run each time the import_costco_stores 
    *   script is run.
    */
   function connect_costco_by_region()
   {
      $today = date('Y-m-d');
      $file = '/Users/japplega/Desktop/store-locator-data/Costco/costco-upc-regions.csv';
      $columns = array('ProductName', 'UPC', 'SalesRegion');
      $defaults = array (
        'Source' => 'import-costco',
      );
      $options = array (
        'brand' => '',
      );
      
      $this->_connect_by_region($file, $columns, $defaults, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Whole Foods stores
    */
   function import_whole_foods_stores()
   {
      $today = date('Y-m-d');
      $file = '/Users/japplega/Desktop/store-locator-data/WholeFoods/whole-foods-stores.csv';
      $columns = array('SourceStoreName', 'SourceAddress1', 'SourceAddress2', 'City', 'State', 'Zip', 'Phone', 'Fax', 'SalesRegion');
      $defaults = array (
        'StoreName' => 'Whole Foods Market',
        'Brands' => 'eb, gfc',
        'NotBrands' => 'all',
        'Website' => 'http://www.wholefoodsmarket.com/',
        'Notes' => $today.' added/updated from database pulled from WF website.',
        'Etailer' => '0',
        'Retailer' => '1',
        'Country' => 'US',
        'Source' => 'import-whole-foods',
        'status' => 'active',
      );
      $options = array (
        'upc_field' => '',
        'missing_file' => '',
      );
      
      $this->_import_stores($file, $columns, $defaults, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Whole Foods product/region links
    *
    * This should probably be run each time the import_whole_foods_stores 
    *   script is run.
    */
   function connect_whole_foods_by_region()
   {
      $today = date('Y-m-d');
      $file = '/Users/japplega/Desktop/store-locator-data/WholeFoods/whole-foods-upc-regions.csv';
      $columns = array('ProductName', 'UPC', 'SalesRegion');
      $defaults = array (
        'Source' => 'import-whole-foods',
      );
      $options = array (
        'brand' => 'ccf',
      );
      
      $this->_connect_by_region($file, $columns, $defaults, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Input form for pulling in a CSV file of stores.
    *
    * NOTES:
    *    - If a UPC is included in the data (one per line), then you 
    *      need to call that field "UPC" in the columns.
    *    - Label StoreName, Address1, and Address2 as SourceStoreName,
    *      SourceAddress1, and SourceAddress2 in the columns.
    *
    */
   function import_stores()
   {
      // This is temporary coding to bypass the need to build the full
      // interface. In this case, we are hard-coding which import record
      // we are using.
      $import_rec = 13;
      
      $sql = 'SELECT * '.
             'FROM stores_import_settings '.
             'WHERE ID = '.$import_rec;
      $query = $this->read_db->query($sql);
      $import = $query->row_array();
      
      $today = date('Y-m-d');
      $import_id = $import['ID'];
      $file = $import['ImportFile'];
      $columns = unserialize($import['Columns']);
      $defaults = array (
        'StoreName' => $import['DefaultStoreName'],
        'Brands' => $import['DefaultBrands'],
        'NotBrands' => $import['DefaultNotBrands'],
        'Website' => $import['DefaultWebsite'],
        'Notes' => $today.' - '.$import['DefaultNotes'],
        'Etailer' => $import['DefaultEtailer'],
        'Retailer' => $import['DefaultRetailer'],
        'Country' => $import['DefaultCountry'],
        'Source' => $import['ImportCode'],
        'status' => $import['DefaultStatus'],
      );
      $options = array ();
      if ($import['Products'] != '')
      {
         $options['products'] = unserialize($import['Products']);
      }
      
//      $this->_save_tmp($import_id, $file, $columns, $defaults, $options);
//      $this->_process_tmp($import_id, $file, $columns, $defaults, $options);
//      $this->_phone_tmp($import_id, $file, $columns, $defaults, $options);
      $this->_commit_tmp($import_id, $file, $columns, $defaults, $options);

   }

   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file or Tabbed Text file of store data
    *  and add it to the temporary database table.
    *
    * This script does a number of jobs:
    *   - it creates one record per store in the source data
    *   - if UPCs are linked to the stores, then it combines them 
    *     as part of the one store record that it created.
    *
    */
   function _save_tmp($import_id, $file, $columns, $defaults, $options = array())
   {
      set_time_limit(0);
      ob_start();

      $this->load->helper(array('text','url'));
      $this->load->model('Stores_import_tmp');
      $this->load->library('AddressStandardizationSolution');

      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');

      $today = date('Y-m-d');
      $row = 1;
      $handle = fopen($file, "r");
//      if ($options['missing_file'] != '')
//      {
//         $mhandle = fopen($options['missing_file'], 'w');
//      }
      $missing_products = array();

      // ------------------------------------------------------------------
      // Process each line of the data file
      // ------------------------------------------------------------------
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         $store = array();
         
         $store['ImportID'] = $import_id;
         
         for ($i=0; $i<count($columns); $i++)
         {
            $store[$columns[$i]] = trim($data[$i]);
         }

         foreach ($defaults AS $key => $value)
         {
            $store[$key] = ascii_to_entities($value);
         }
         
         // ------------------------------------------------------------------
         // if there is a product associated with this record, we want to
         // pull it out of the main store data now and get it ready to use.
         // ------------------------------------------------------------------
         
         $upc = '';
         if (isset($store['UPC']))
         {
            $upc = $store['UPC'];
            unset($store['UPC']);
            
            // strip out any extra 0s at front of field
            if (strlen($upc) > 11)
            {
               $upc = substr($upc, strlen($upc)-11, 11);
            }
            elseif (strlen($upc) < 11)
            {
               $upc = str_pad($upc, 11, '0', STR_PAD_LEFT);
            }
         }
         
         // ------------------------------------------------------------------
         // check if the store is already in the temporary table and create  
         // or update it as needed.
         // ------------------------------------------------------------------

         // Auto-fill the StoreName, Address1, and Address2
         if (( ! isset($store['StoreName']) || $store['StoreName'] == '') && isset($store['SourceStoreName']))
         {
            $store['StoreName'] = $store['SourceStoreName'];
         }
         $store['Address1'] = (isset($store['SourceAddress1'])) ? $store['SourceAddress1'] : '';
         $store['Address2'] = (isset($store['SourceAddress2'])) ? $store['SourceAddress2'] : '';
         
         // If the store record exists, then it is returned in the $mystore variable.
         $mystore = array();
         $id = $this->Stores_import_tmp->store_exists($store, $mystore);
         
         // if is not, then add it
         if ($id == FALSE)
         {
            
            $search = $store['Address1'].', '.$store['City'].', '.
                      $store['State'].' '.substr($store['Zip'], 0, 5);
            $store['StandardAddress'] = $this->addressstandardizationsolution->AddressLineStandardization($search);
            
            // create a "products" record
            $products = array();
            if ($upc != '')
            {
               $products[0]['upc'] = $upc;
               $products[0]['id'] = '';
            }

            // we only want to apply products in the options the first time we save the record.
            if (isset($options['products']) && ! empty($options['products']))
            {
               foreach ($options['products'] AS $upc)
               {
                  $products[] = array(
                     'upc' => $upc,
                     'id'  => ''
                  );
               }
            }
            $store['Products'] = serialize($products);

//            echo '<pre>'; print_r($store); echo '</pre>'; exit;

            $id = $this->Stores_import_tmp->insert_store($store);
         
            echo $id.': '.$store['Address1'].' ('.$store['City'].
                 ', '.$store['State'].') created<br />';
         }
         // if it is, update the record
         // usually that will mean that there is more than one product carried at this store.
         else
         {
            // get the products array from the existing record
            $products = $this->Stores_import_tmp->get_products_array($id);
            if ($upc != '')
            {
               $products[0]['upc'] = $upc;
               $products[0]['id'] = '';
            }
            $values['Products'] = serialize($products);
         
            $this->Stores_import_tmp->update_store($id, $values);
            
            echo $id.': '.$store['Address1'].' ('.$store['City'].
                 ', '.$store['State'].') updated<br />';
         }
         
         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

         $row++;
      }
      fclose($handle);
      
      echo '<br />'.($row - 1).' rows processed.<br /><br />';
      echo '<br />Script completed.';
      exit;

   }

   // --------------------------------------------------------------------
   
   /**
    * Analyzes and processes the temp file to reconcile the data with 
    *  the existing database.
    *
    */
   function _process_tmp($import_id, $file, $columns, $defaults, $options = array())
   {
      set_time_limit(0);
      ob_start();

      $this->load->model('Products');
      $this->load->model('Stores');
      $this->load->model('Stores_import_tmp');

      $this->store_lookup = array();
      
      // First, we get a list of stores that are already in the database
      // to use as a reference so we can see if any stores have been removed
      // from the new list. It is assumed that all stores from a data source
      // have the same source ID.

      $this->store_lookup = $this->Stores->get_existing_store_lookup($defaults['Source']);
      
      // Get all the records for this import
      $stores = $this->Stores_import_tmp->get_all_stores($import_id);
      
      $row = 0;
      
      foreach ($stores AS $store)
      {
         $values = array();
         $values['Action'] = '';
         $values['Brands'] = $store['Brands'];
         $values['NotBrands'] = $store['NotBrands'];
         $values['UpdateID'] = '';
         $values['UpdateFields'] = '';
         $values['Products'] = $store['Products'];
         
         // See if this store exists already in the stores database
         // If the store record exists, then it is returned in the $mystore variable.
         $mystore = array();
         $id = $this->Stores->store_exists_tmp($store, $mystore);
         
         // if is not, then add it
         if ($id == FALSE)
         {
            $values['Action'] = 'INSERT';
         }
         else
         {
            $values['Action'] = 'UPDATE';
            $values['UpdateID'] = $mystore['StoreID'];
            
            // compile a list of fields to include in the update
            $update_fields = array();
            foreach ($mystore AS $key => $val)
            {
               if ($val == '')
               {
                  if (isset($store[$key]) && $store[$key] != '')
                  {
                     $update_fields[] = $key;
                  }
               }
               if ($key == 'Brands' && $store['Brands'] != '')
               {
                  if ( ! in_array('Brands', $update_fields))
                  {
                     $update_fields[] = 'Brands';
                  
                     // Append the new Brand values if not already there
                     
                     $brands = array_map('trim', explode(",", $mystore['Brands']));
                     $new_brands = array_map('trim', explode(",", $store['Brands']));
                     foreach ($new_brands AS $new_brand)
                     {
                        if ( ! in_array($new_brand, $brands))
                        {
                           $brands[] = $new_brand;
                        }
                     }
                     $values['Brands'] = implode(',', $brands);
                  }
               }
               if ($key == 'NotBrands' && $store['NotBrands'] != '')
               {
                  if ( ! in_array('NotBrands', $update_fields))
                  {
                     $update_fields[] = 'NotBrands';
                  
                     // Append the new NotBrand values if not already there
                     
                     $brands = array_map('trim', explode(",", $mystore['NotBrands']));
                     $new_brands = array_map('trim', explode(",", $store['NotBrands']));
                     foreach ($new_brands AS $new_brand)
                     {
                        if ( ! in_array($new_brand, $brands))
                        {
                           $brands[] = $new_brand;
                        }
                     }
                     // if any of the entries are 'all', that overrides any others
                     if (in_array('all', $brands))
                     {
                        $brands = array('all');
                     }
                     $values['NotBrands'] = implode(',', $brands);
                  }
               }
            }
            $values['UpdateFields'] = serialize($update_fields);
        
            unset($this->store_lookup[$id]);
         }
               
         // Check and make sure all products listed are in the products 
         // database; in the process, get the internal Product ID for each
         // product in the array.
         
         $products = unserialize($store['Products']);
         
         for ($i=0; $i<count($products); $i++)
         {
            $prod = $this->Products->get_product_data_by_upc($products[$i]['upc']);
      
            if (isset($prod['ProductID']))
            {
               $products[$i]['id'] = $prod['ProductID'];
            }
            else
            {
               // error: the product is not in the products database.
            }
         }
         
         $values['Products'] = serialize($products);

         // Create import records for any stores remaining in the lookup array
         // as these need to be deactivated.

//         foreach ($this->store_lookup AS $key => $value)
//         {
//            if ($value['status'] != 'inactive')
//            {
//               $sql = 'UPDATE stores '.
//                      'SET status = "inactive", '.
//                        'Notes = "'.$value['Notes']."\n".$today.' -- deactivated because the store was not in the updated data file supplied by store." '.
//                      'WHERE StoreID = '.$value['StoreID'];
//               $this->db->query($sql);
//
//               echo $value['StoreID'].': '.$value['Address1'].' ('.$value['City'].
//                    ', '.$value['State'].') deactivated<br />';
//            }
//         }

         // temporary reporting for testing
         echo '<br />--------------------------------------';
         echo '<br />'.$values['Action'];
         if ($values['Action'] == 'UPDATE')
         {
            echo '<br />Existing: '.$mystore['StoreName'].', '.$mystore['Address1'].', '.$mystore['City'].', '.$mystore['State'].' '.$mystore['Zip'];
         }
         echo '<br />New     : '.$store['StoreName'].', '.$store['Address1'].', '.$store['City'].', '.$store['State'].' '.$store['Zip'];

//         echo '<pre>'; print_r($values); echo '</pre>';

         $this->Stores_import_tmp->update_store($store['StoreID'], $values);

         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

         $row++;
      }
      echo '<br />'.($row - 1).' rows processed.<br /><br />';
      echo '<br />Script completed.';
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Once all the temp data is ready and double-checked, this commits
    *  the data to the actual stores database.
    *
    */
   function _commit_tmp($import_id, $file, $columns, $defaults, $options = array())
   {
      set_time_limit(0);
      ob_start();

      $this->load->model('Stores');
      $this->load->model('Stores_import_tmp');
      $this->load->model('Stores_product');

      // We delete any records in stores_products with the idea that we will
      // be rebuilding the links to products from the new data file. Again, we
      // will only delete links that were created as part of a previous upload
      // from this data source; any additional links made will not be lost.

      $this->Stores_product->delete_product_links($defaults['Source']);

       // Get all the records for this import
      $stores = $this->Stores_import_tmp->get_all_stores($import_id);
      
      $row = 0;
      
      foreach ($stores AS $store)
      {
         echo '<br />--------------------------------------';
         echo '<br />'.$store['Action'];

         if ($store['Action'] == 'INSERT')
         {
            $values = array();
            $values['StoreName'] = $store['StoreName'];
            $values['Address1'] = $store['Address1'];
            $values['Address2'] = $store['Address2'];
            $values['City'] = $store['City'];
            $values['State'] = $store['State'];
            $values['Zip'] = $store['Zip'];
            $values['Phone'] = $store['Phone'];
            $values['Fax'] = $store['Fax'];
            $values['ContactEmail'] = $store['ContactEmail'];
            $values['Website'] = $store['Website'];
            $values['Brands'] = $store['Brands'];
            $values['NotBrands'] = $store['NotBrands'];
            $values['ContactName'] = $store['ContactName'];
            $values['Source'] = $store['Source'];
            $values['SourceStoreName'] = $store['SourceStoreName'];
            $values['SourceAddress1'] = $store['SourceAddress1'];
            $values['SourceAddress2'] = $store['SourceAddress2'];
            $values['Country'] = $store['Country'];
            $values['Notes'] = $store['Notes'];
            $values['Etailer'] = $store['Etailer'];
            $values['Retailer'] = $store['Retailer'];
            $values['ContactPhone'] = $store['ContactPhone'];
            $values['latitude'] = $store['latitude'];
            $values['longitude'] = $store['longitude'];
            $values['SalesRegion'] = $store['SalesRegion'];
            $values['status'] = $store['status'];
            $values['StandardAddress'] = $store['StandardAddress'];
            
            // We only need to get lat/lon data for new records, so we do it
            // at this point instead of at the main import.
      
            // get the latitude and longitude
            $search = $store['Address1'].', '.$store['City'].', '.
                      $store['State'].' '.substr($store['Zip'], 0, 5);
            $geocode = $this->_geocode($search);
            $values['latitude'] = $geocode['latitude'];
            $values['longitude'] = $geocode['longitude'];
            
            $id = $this->Stores->insert_store($values);
            
            echo '<br />Inserted: '.$store['StoreName'].', '.$store['Address1'].', '.$store['City'].', '.$store['State'].' '.$store['Zip'];
            if ($values['Phone'] = '')
            {
               echo '<br /><span style="color:red;">No Phone Number.</span>';
            }
         }
         else // We need to update the record
         {
         	$update_fields = unserialize($store['UpdateFields']);
         	
         	$values = array();
         	foreach ($update_fields AS $key)
         	{
         	   $values[$key] = $store[$key];
         	}
         	
         	$id = $store['UpdateID'];
         	$this->Stores->update_store($id, $values);
         	
         	echo '<br />Updated: '.$store['StoreName'].', '.$store['Address1'].', '.$store['City'].', '.$store['State'].' '.$store['Zip'];
         }
         
         // create any product records for this store

         $products = unserialize($store['Products']);
         foreach ($products AS $product)
         {
            $values = array();
            $values['StoreID'] = $id;
            $values['ProductID'] = $product['ProductID'];
            $values['Carried'] = 1;
            $values['Source'] = $store['Source'];
            $this->Stores_product->insert_store_product($values);
         }

         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

         $row++;
      }
      
      // once all the changes are applied, we might want to delete the tmp
      // records, but for now, I will do that manually.
      
      echo '<br />'.($row - 1).' rows processed.<br /><br />';
      echo '<br />Script completed.';
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Runs through the existing stores database and generates standardized
    *  addresses for all records.
    *
    */
   function update_standard_addresses()
   {
      $this->load->model('Stores');
      $this->load->library('AddressStandardizationSolution');
      
      // get a list of all store records
      $sql = 'SELECT StoreID, Address1, City, State, Zip '.
             'FROM stores';
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
//      echo '<pre>'; print_r($stores); echo '</pre>'; exit;
      
      $row = 0;
      foreach ($stores AS $store)
      {
         $values = array();
         $addr = $store['Address1'].', '.$store['City'].', '.
                 $store['State'].' '.substr($store['Zip'], 0, 5);
         $values['StandardAddress'] = $this->addressstandardizationsolution->AddressLineStandardization($addr);
         
         $this->Stores->update_store($store['StoreID'], $values);
         
         echo $addr.' --> '.$values['StandardAddress'].'<br />';
         $row++;
      }
      
      echo 'Script complete. ('.$row.' records processed.)';
   }
   
   // --------------------------------------------------------------------
   
   /**
    * A simple function to geocode addresses using the Google Geocode API.
    *
    */
   function _geocode($string)
   {
      $string = str_replace (" ", "+", urlencode($string));
      $details_url = "http://maps.googleapis.com/maps/api/geocode/json?address=".$string."&sensor=false";
 
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $details_url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $response = json_decode(curl_exec($ch), true);
 
      // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
      if ($response['status'] != 'OK') {
         return null;
      }
 
//      print_r($response);
      $geometry = $response['results'][0]['geometry'];
 
      $longitude = $geometry['location']['lat'];
      $latitude = $geometry['location']['lng'];
 
      $array = array(
         'longitude' => $geometry['location']['lng'],
         'latitude' => $geometry['location']['lat'],
         'location_type' => $geometry['location_type'],
      );
 
      return $array;
   }

   
   // ====================================================================

   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file or Tabbed Text file of store data
    *  and add it to the stores databases.
    *
    * The script is designed to accept either a basic list of stores
    *  or stores with associated products.
    *
    * NOTE: the script assumes that there are not multiple data sources
    *  supplying store/product data for the same set or subset of stores.
    *  Multiple sources with the same stores (but different product data,
    *  for example), make it difficult to tell which stores may have 
    *  been opened or closed since the last data import. Multiple files
    *  should be combined into a single source.
    *
    */
   function _import_stores($file, $columns, $defaults, $options = array())
   {
      set_time_limit(0);
      ob_start();

      $this->load->helper(array('text','url'));
      $this->load->model('Stores');
      $this->load->model('Stores_product');
      $this->load->model('Products');
      $this->load->model('Api');
      $this->load->library('Gmaps');

      $this->google_key = $this->Api->get_map_key();
      $this->gmaps->initialize($this->google_key);
      
      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');

      $today = date('Y-m-d');
      $row = 1;
      $handle = fopen($file, "r");
      if ($options['missing_file'] != '')
      {
         $mhandle = fopen($options['missing_file'], 'w');
      }
      $missing_products = array();
      $this->store_lookup = array();
      
      // ------------------------------------------------------------------
      // First, we get a list of stores that are already in the database
      // to use as a reference so we can see if any stores have been removed
      // from the new list. It is assumed that all stores from a data source
      // have the same source ID.
      // ------------------------------------------------------------------
      $this->store_lookup = $this->Stores->get_existing_store_lookup($defaults['Source']);
      
      // ------------------------------------------------------------------
      // We delete any records in stores_products with the idea that we will
      // be rebuilding the links to products from the new data file. Again, we
      // will only delete links that were created as part of a previous upload
      // from this data source; any additional links made will not be lost.
      // ------------------------------------------------------------------
      $this->Stores_product->delete_product_links($defaults['Source']);

      // ------------------------------------------------------------------
      // Process each line of the data file
      // ------------------------------------------------------------------
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         $store = array();
         
         for ($i=0; $i<count($columns); $i++)
         {
            $store[$columns[$i]] = trim($data[$i]);
         }

         foreach ($defaults AS $key => $value)
         {
            $store[$key] = ascii_to_entities($value);
         }
         
         // ------------------------------------------------------------------
         // if there is a product associated with this record, we want to
         // pull it out of the main store data now and get it ready to use.
         // ------------------------------------------------------------------
         $upc = '';
         if ($options['upc_field'] != '')
         {
            $upc = $store[$options['upc_field']];
            unset($store[$options['upc_field']]);
            
            // strip out any extra 0s at front of field
            if (strlen($upc) > 11)
            {
               $upc = substr($upc, strlen($upc)-11, 11);
            }
            elseif (strlen($upc) < 11)
            {
               $upc = str_pad($upc, 11, '0', STR_PAD_LEFT);
            }
         }
         
         // ------------------------------------------------------------------
         // check if the store is already in the database and create or 
         // update it as needed.
         // ------------------------------------------------------------------

         // Auto-fill the StoreName, Address1, and Address2
         if ( ! isset($store['StoreName']) && isset($store['SourceStoreName']))
         {
            $store['StoreName'] = $store['SourceStoreName'];
         }
         $store['Address1'] = (isset($store['SourceAddress1'])) ? $store['SourceAddress1'] : '';
         $store['Address2'] = (isset($store['SourceAddress2'])) ? $store['SourceAddress2'] : '';
         
//         echo '<pre>'; print_r($store); echo '</pre>'; exit;

         $mystore = array();
         $id = $this->Stores->store_exists($store, $mystore);
         
         // if is not, then add it
         if ($id == FALSE)
         {
            // get the latitude and longitude
            $search = $store['Address1'].', '.$store['City'].', '.
                      $store['State'].' '.$store['Zip'];
            $this->gmaps->getInfoLocation($search);
            $store['latitude'] = $this->gmaps->getLatitude();
            $store['longitude'] = $this->gmaps->getLongitude();

            $id = $this->Stores->insert_store($store);
         
            echo $id.': '.$store['Address1'].' ('.$store['City'].
                 ', '.$store['State'].') created<br />';
         }
         // if it is, update the notes field, but only do it once
         elseif (isset($this->store_lookup[$id]))
         {
            $values = array();
            $values['Notes'] = $mystore['Notes']."\n".$store['Notes'];
            $values['status'] = 'active';
            // fill in any blank lines with supplied data or default
            foreach ($mystore AS $key => $val)
            {
               if ($val == '')
               {
                  if (isset($store[$key]))
                  {
                     $mystore[$key] = $store[$key];
                     $values[$key] = $store[$key];
                  }
                  if (isset($default[$key]) && $mystore[$key] = '')
                  {
                     $values[$key] = $default[$key];
                  }
               }
            }
            $this->write_db->where('StoreID', $id);
            $this->write_db->update('stores', $values);
            
            unset($this->store_lookup[$id]);
   
            echo $id.': '.$store['Address1'].' ('.$store['City'].
                 ', '.$store['State'].') updated<br />';
         }
         
         // ------------------------------------------------------------------
         // get the product information and create the product link
         // ------------------------------------------------------------------
         if ($upc != '')
         {
            $product = $this->Products->get_product_data_by_upc($upc);
            
            if (isset($product['ProductID']))
            {
               $values = array();
               $values['StoreID'] = $id;
               $values['ProductID'] = $product['ProductID'];
               $values['Carried'] = 1;
               $values['Source'] = $store['Source'];
               $this->Stores_product->insert_store_product($values);
         
               // comment out to avoid creating too large a page to display
//               echo '&nbsp; &nbsp;'.$product['ProductID'].': 
//                    '.strtoupper($product['SiteID']).' '.
//                    $product['ProductName'].' ('.$product['UPC'].
//                    ') added<br />';
            }
            else
            {
               // store the missing product info into a format I can use later
               if ($options['missing_file'] != '')
               {
                  $write_me = $id.', '.$upc.', 1, "'.$store['Source'].'"'."\n";
                  fwrite($mhandle, $write_me);
               }
//               echo '<span style="color:red;">&nbsp; &nbsp;ERROR: No product was found for this UPC: '.$upc.'</span><br />';
            }
         }
         elseif (isset($options['products']))
         {
            if ( ! empty($options['products']))
            {
               // go through the list of products for each store in the list
               foreach ($options['products'] AS $upc)
               {
                  $product = $this->Products->get_product_data_by_upc($upc);
            
                  if (isset($product['ProductID']))
                  {
                     $values = array();
                     $values['StoreID'] = $id;
                     $values['ProductID'] = $product['ProductID'];
                     $values['Carried'] = 1;
                     $values['Source'] = $store['Source'];
                     $this->Stores_product->insert_store_product($values);
                  }
               }
            }
         }
         
         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

         $row++;
      }
      fclose($handle);
      
      echo '<br />'.($row - 1).' rows processed.<br /><br />';
      
      // ---------------------------------------------------------------------
      // deactivate any stores remaining in the lookup array
      // ---------------------------------------------------------------------
      foreach ($this->store_lookup AS $key => $value)
      {
         if ($value['status'] != 'inactive')
         {
            $sql = 'UPDATE stores '.
                   'SET status = "inactive", '.
                     'Notes = "'.$value['Notes']."\n".$today.' -- deactivated because the store was not in the updated data file supplied by store." '.
                   'WHERE StoreID = '.$value['StoreID'];
            $this->db->query($sql);

            echo $value['StoreID'].': '.$value['Address1'].' ('.$value['City'].
                 ', '.$value['State'].') deactivated<br />';
         }
      }
      
      echo '<br />Script completed.';
      exit;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Connects products to stores based on the region field
    *
    * This function will take a file of product UPCs, region names and
    *   info about the brand and create stores_product table entries
    *   based on the existing store records.
    *
    * A separate list should be maintained for each store chain to avoid
    *   confusion over identical region names.
    *
    */
   function _connect_by_region($file, $columns, $defaults, $options)
   {
      set_time_limit(0);
      ob_start();

      $this->load->model('Products');
      $this->load->model('Stores');
      $this->load->model('Stores_product');

      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');

      $row = 1;
      $store_total = 1;
      $handle = fopen($file, "r");
      
      // ------------------------------------------------------------------
      // We delete any records in stores_products with the idea that we will
      // be rebuilding the links to products from the new data file. Again, we
      // will only delete links that were created as part of a previous upload
      // from this data source; any additional links made will not be lost.
      // ------------------------------------------------------------------
      $this->Stores_product->delete_product_links($defaults['Source']);
      
      // ------------------------------------------------------------------
      // Process each line of the data file
      // ------------------------------------------------------------------
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         $link = array();
         $product = array();
         $stores = array();
         $store_row = 1;
         
         for ($i=0; $i<count($columns); $i++)
         {
            $link[$columns[$i]] = $data[$i];
         }
      
         // ---------------------------------------------------------------
         // get the product info based on the supplied UPC
         // ---------------------------------------------------------------
         $product = $this->Products->get_product_data_by_upc($link['UPC']);

         // ---------------------------------------------------------------
         // get a list of stores in that region
         // ---------------------------------------------------------------
         
         $stores = $this->Stores->get_stores_by_region($link['SalesRegion'], $defaults['Source']);

         foreach ($stores AS $store)
         {
            // ------------------------------------------------------------
            // in the stores record...
            // make sure the brand being added is not in the brand list
            // make sure the Not Brands field contains "all"
            // ------------------------------------------------------------
            $mystore = array();
            $mystore['Brands'] = $store['Brands'];
            $mystore['NotBrands'] = $store['NotBrands'];
            
            $brand_list = $this->Stores->get_brands_array($store['Brands']);
            $not_brand_list = $this->Stores->get_brands_array($store['NotBrands']);

            if (in_array($options['brand'], $brand_list))
            {
               unset($brand_list[$options['brand']]);
               $mystore['Brands'] = implode(', ', $brand_list);
            }
            if ( ! in_array('all', $not_brand_list))
            {
               $mystore['NotBrands'] = 'all, '.implode(', ', $not_brand_list);
            }
            $this->Stores->update_store($store['StoreID'], $mystore);
            
            // ------------------------------------------------------------
            // create the stores_product records for each store in region
            // ------------------------------------------------------------
            
            // make sure the link doesn't already exist (duplicates are not allowed)
            if ($this->Stores_product->get_store_product_data($store['StoreID'], $product['ProductID']) == FALSE)
            {
               $mylink = array();
               $mylink['StoreID'] = $store['StoreID'];
               $mylink['ProductID'] = $product['ProductID'];
               $mylink['Carried'] = 1;
               $mylink['Source'] = $defaults['Source'];
               $this->Stores_product->insert_store_product($mylink);
            }
            
            $store_row++;
            $store_total++;
         }
         
         echo '('.$row.') '.$link['UPC'].' in '.$link['SalesRegion'].' ('.$store_row.' stores)<br />';
      
         // ---------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ---------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

         $row++;
      }
      fclose($handle);
      
      echo '<br />'.($row - 1).' rows processed.';
      echo '<br />'.($store_total - 1).' stores updated.<br /><br />';
      echo '<br />Script completed.';
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates the latitude and longitude for the selected stores
    *
    * This function selects 100 records that do not have latitude
    *  coordinates each time it is run. It can be run multiple
    *  times, and it could be run via a cron job if desired.
    *
    */
   function update_coordinates()
   {
      set_time_limit(0);
      ob_start();

      $this->load->model('Stores');
      $this->load->model('Api');
      $this->load->library('Gmaps');
      
      $this->google_key = $this->Api->get_map_key();
      $this->gmaps->initialize($this->google_key);

      $sql = 'SELECT * FROM stores '.
             'WHERE latitude = 0 '.
             'LIMIT 100';
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
//      echo '<pre>'; print_r($stores); echo '</pre>'; exit;
      
      $row = 0;
      foreach ($stores AS $store)
      {
         // get the latitude and longitude
         $search = $store['Address1'].', '.$store['City'].', '.$store['State'].' '.$store['Zip'];
         $this->gmaps->getInfoLocation($search);
         $new_store['latitude'] = $this->gmaps->getLatitude();
         $new_store['longitude'] = $this->gmaps->getLongitude();

         $this->Stores->update_store($store['StoreID'], $new_store);

         echo $store['StoreID'].': '.$store['StoreName'].', '.$store['Address1'].' ('.$store['City'].', '.$store['State'].') updated<br />';
         $row++;
         
         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();
      }
      if ($row > 0)
      {
         echo '<br />'.($row - 1).' records updated.';
      }
      else
      {
         echo '<br />No stores were found without coordinates.';
      }
      exit;
   }
   


} // END Class

?>