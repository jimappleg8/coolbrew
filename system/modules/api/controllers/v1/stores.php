<?php

class Stores extends Controller {

   var $_headers;
   
   var $post = array();
   
   var $is_error = FALSE;
   var $error_msg = '';

   function Stores()
   {
      parent::Controller();   
      $this->load->helper(array('url', 'v1/xml', 'v1/json'));
      $this->load->model('v1/Keys');
      $this->load->model('v1/Sites');
   }
   
   // --------------------------------------------------------------------

   /**
    * storeProductList
    *
    * @param string $key
    * @param string $service_type
    * @param string $site_id
    * @return array
    */
   function storeProductList($key = '', $service_type = '', $site_id = '')
   {
      $categories = array();
      $products = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/stores/Products');
      $this->Products->init_db($level);
      
      // check if service type is valid
      $valid_service_types = array('xml', 'json');
      if ( ! $this->_valid_service_type($service_type, $valid_service_types))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }
      
      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($site_id, $this->is_error)))
      {
         $this->is_error = TRUE;
         $status = $this->Sites->error_msg;
      }

      if ( ! $this->is_error)
      {
         $categories = $this->Products->get_product_category_list($site_id);
         $products = $this->Products->get_product_list($site_id);
         
         if ( ! empty($categories))
         {
            $status = 'success';
         }
         else
         {
            if (empty($products))
            {
               $this->is_error = TRUE;
               $status = 'error: no categories or products were found.';
            }
            else
            {
               $status = 'success';            
            }
         }
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['categories'] = array();
            for ($i=0, $cnt=count($categories); $i<$cnt; $i++)
            {
               foreach ($categories[$i] AS $key => $value)
               {
                  if ($key == 'Products' && ! empty($value))
                  {
                     foreach ($value AS $product)
                     {
                        $data['categories'][$i]['Products'][] = process_json_array($product);
                     }
                  }
                  else
                  {
                     $data['categories'][$i][$key] = process_json_element($value);
                  }
               }
            }
            $data['products'] = array();
            foreach ($products AS $product)
            {
               $data['products'][] = process_json_array($product);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/stores/product-list-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['categories'] = array();
            for ($i=0, $cnt=count($categories); $i<$cnt; $i++)
            {
               foreach ($categories[$i] AS $key => $value)
               {
                  if ($key == 'Products' && ! empty($value))
                  {
                     foreach ($value AS $product)
                     {
                        $data['categories'][$i]['Products'][] = process_xml_array($product);
                     }
                  }
                  else
                  {
                     $data['categories'][$i][$key] = process_xml_element($value);
                  }
               }
            }
            $data['products'] = array();
            foreach ($products AS $product)
            {
               $data['products'][] = process_xml_array($product);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/stores/product-list-xml', $data, TRUE);
            break;
      }
      
      if ( ! headers_sent() && ! empty($this->_headers))
      {
         foreach ($this->_headers as $header)
         {
            header($header);
         }
      }
      echo $response;
      return;
   }

   // --------------------------------------------------------------------

   /**
    * storeLocator
    *
    * @return array
    */
   function storeLocator()
   {
      $this->load->helper('v1/sort');
      
      // establish what information was submitted
      $this->_get_value(4, 'api-key');
      $this->_get_value(5, 'format');
      $this->_get_value(6, 'site-id');
      $this->_get_value(7, 'product-num');
      $this->_get_value(8, 'zip');
      $this->_get_value(9, 'radius', 10);
      $this->_get_value(10, 'count', 50);
      $this->_get_value(11, 'sort', 'Distance');
      
      $nielsen_stores = array();
      $local_stores = array();
      
      $search = array();
      $stores = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($this->post['api-key'], $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/stores/Api');
      $this->Api->init_db($level);
      $this->load->model('v1/stores/Nielsen');
      $this->load->model('v1/stores/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/stores/Stores');
      $this->Stores->init_db($level);
      $this->load->model('v1/stores/Zipcodes');
      $this->Zipcodes->init_db($level);

      // check if service type is valid
      $valid_service_types = array('xml', 'json');
      if ( ! $this->_valid_service_type($this->post['format'], $valid_service_types))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }
      
      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($this->post['site-id'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $status = $this->Sites->error_msg;
      }

//      echo '<pre>'; print_r($this->post); echo '</pre>'; exit;
      
      if ( ! $this->is_error)
      {
         $this->Nielsen->XML_LIST_ELEMENTS = array("store");

         $nielsen_stores = $this->Nielsen->get_store_list($this->post);
         if ( ! is_array($nielsen_stores))
         {
            $nielsen_stores = array();
         }
         
         $local_stores = $this->Stores->get_store_list($this->post);
         if ( ! is_array($local_stores))
         {
            $local_stores = array();
         }

         $stores = array_merge($nielsen_stores, $local_stores);

         if (empty($stores))
         {
            if ( ! $this->Zipcodes->zipcode_exists($this->post['zip']))
            {
               $this->is_error = TRUE;
               $this->error_msg = 'The ZIP code "'.$this->post['zip'].'" was not found.';
            }
         }
         else
         {
            // create a number-only distance field
            for ($i=0, $cnt=count($stores); $i<$cnt; $i++)
            {
               if ($stores[$i]['Distance'] != 'unknown')
               {
                  $stores[$i]['DistanceNum'] = 0 + $stores[$i]['Distance'];
                  $stores[$i]['DistanceNum'] = (string) $stores[$i]['DistanceNum'];
               }
               else
               {
                  $stores[$i]['DistanceNum'] = $stores[$i]['Distance'];
               }
            }
            if ($this->post['sort'] == 'Distance')
            {
               $stores = mu_sort($stores, 'DistanceNum');
            }
            else
            {
               $stores = mu_sort($stores, $this->post['sort']);
            }
         }
      }
      
      // capture error message is there is one
      if ($this->is_error)
      {
         $status = $this->error_msg;
      }
      else
      {
         $status = 'success';
      }
      
      // assemble the search data
      $search['SiteID'] = $this->post['site-id'];
      $search['BrandName'] = $this->Sites->get_brand_name($this->post['site-id']);
      $search['ProductNum'] = $this->post['product-num'];
      $search['ProductName'] = $this->Products->get_product_name_by_upc($this->post['product-num']);
      $search['Zip'] = $this->post['zip'];
      $search['Radius'] = $this->post['radius'];
      $search['Count'] = $this->post['count'];
      $search['Sort'] = $this->post['sort'];
      $search['GoogleMapKey'] = $this->Api->get_map_key($this->post['site-id'], 'live');
      
      switch ($this->post['format'])
      {
         case 'json':
            $data['source'] = $level;
            $data['search'] = process_json_array($search);
            $data['stores'] = array();
            foreach ($stores AS $store)
            {
               $data['stores'][] = process_json_array($store);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/stores/locator-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['search'] = process_xml_array($search);
            $data['stores'] = array();
            foreach ($stores AS $store)
            {
               $data['stores'][] = process_xml_array($store);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/stores/locator-xml', $data, TRUE);
            break;
      }
      
      if ( ! headers_sent() && ! empty($this->_headers))
      {
         foreach ($this->_headers as $header)
         {
            header($header);
         }
      }
      echo $response;
      return;
   }

   // --------------------------------------------------------------------

   /**
    * Accepts form input for a store message, saves it to the database
    *  and returns the same data for further processing.
    *
    * @return array
    */
   function storeMessage()
   {
      $this->load->model('v1/stores/Messages');
      
      // establish what information was submitted
      $this->_get_value(4, 'api-key');
      $this->_get_value(5, 'format');
      $this->_get_value(6, 'site-id');
      $this->_get_value(7, 'StoreID', '0');
      $this->_get_value(8, 'StoreName');
      $this->_get_value(9, 'Address1');
      $this->_get_value(10, 'Address2');
      $this->_get_value(11, 'City');
      $this->_get_value(12, 'State');
      $this->_get_value(13, 'Zip');
      $this->_get_value(14, 'Phone');
      $this->_get_value(15, 'ProductNum');
      $this->_get_value(16, 'ProductName');
      $this->_get_value(17, 'FirstName');
      $this->_get_value(18, 'LastName');
      $this->_get_value(19, 'Email');
      $this->_get_value(20, 'Affiliated', '0');
      $this->_get_value(21, 'Message');
      $this->_get_value(22, 'Mode', 'active');
      
      $submitted = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($this->post['api-key'], $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }
      
      // check if service type is valid
      $valid_service_types = array('xml', 'json');
      if ( ! $this->_valid_service_type($this->post['format'], $valid_service_types))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }
      
      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($this->post['site-id'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $status = $this->Sites->error_msg;
      }

      $message_id = '';
      if ( ! $this->is_error)
      {
         $values = $this->post;
         unset($values['api-key']);
         unset($values['format']);
         unset($values['site-id']);
         unset($values['ProductNum']);
         unset($values['Mode']);

         $values['SiteID'] = $this->post['site-id'];
         $values['ProductID'] = $this->post['ProductNum'];
         $values['DateSent'] = date("Y-m-d H:i:s");
         
         $message_id = $this->Messages->insert_message($values, $this->post['Mode']);

         // assemble the search data
         $submitted['MessageID'] = $message_id;
         $submitted['SiteID'] = $this->post['site-id'];
         $submitted['BrandName'] = $this->Sites->get_brand_name($this->post['site-id']);
         $submitted['StoreID'] = $this->post['StoreID'];
         $submitted['StoreName'] = $this->post['StoreName'];
         $submitted['Address1'] = $this->post['Address1'];
         $submitted['Address2'] = $this->post['Address2'];
         $submitted['City'] = $this->post['City'];
         $submitted['State'] = $this->post['State'];
         $submitted['Zip'] = $this->post['Zip'];
         $submitted['Phone'] = $this->post['Phone'];
         $submitted['ProductNum'] = $this->post['ProductNum'];
         $submitted['ProductName'] = $this->post['ProductName'];
         $submitted['FirstName'] = $this->post['FirstName'];
         $submitted['LastName'] = $this->post['LastName'];
         $submitted['Email'] = $this->post['Email'];
         $submitted['Affiliated'] = $this->post['Affiliated'];
         $submitted['Message'] = $this->post['Message'];  
      }
      
      // capture error message is there is one
      if ($this->is_error)
      {
         $status = $this->error_msg;
      }
      else
      {
         $status = 'success';
      }
      
      switch ($this->post['format'])
      {
         case 'json':
            $data['submitted'] = process_json_array($submitted);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/stores/message-json', $data, TRUE);
            break;
         default:  // xml
            $data['submitted'] = process_xml_array($submitted);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/stores/message-xml', $data, TRUE);
            break;
      }
      
      if ( ! headers_sent() && ! empty($this->_headers))
      {
         foreach ($this->_headers as $header)
         {
            header($header);
         }
      }
      echo $response;
      return;
   }
   
   // --------------------------------------------------------------------

   /**
    * Gets the correct parameter value from either URL segment or POST
    *
    * @access   public
    * @return   array
    */
   function _get_value($segment, $key, $default = '')
   {
      // if both URl and Post are used, POST takes precedence
      if ($this->input->post($key))
      {
         $this->post[$key] = $this->input->post($key, TRUE);
      }
      elseif ($this->uri->segment($segment))
      {
         $this->post[$key] = $this->uri->segment($segment);
      }
      elseif ($default != '')
      {
         $this->post[$key] = $default;
      }
      else
      {
         $this->post[$key] = '';
         // see if an error has already been thrown
         if ($this->is_error == TRUE)
         {
            return TRUE;
         }
         else
         {
            $this->is_error = TRUE;
            $this->error_msg = 'The required field "'.$key.'" is missing.';
         }
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Checks the validity of the Service Type variable
    *
    * @access   public
    * @return   array
    */
   function _valid_service_type($type, $valid_types)
   {
      // see if an error has already been thrown
      if ($this->is_error == TRUE)
      {
         return TRUE;
      }
      
      $type = strtolower($type);

      if ($type == '')
      {
         $this->error_msg = 'error: the service type is missing.';
         return FALSE;
      }
      
      if ( ! in_array($type, $valid_types))
      {
         $this->error_msg = 'error: the service type is invalid.';
         return FALSE;
      }
      return TRUE;
   }
   

}  // END of Stores Class

/* End of file stores.php */
/* Location: ./system/modules/api/controllers/v1/stores.php */