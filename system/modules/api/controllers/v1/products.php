<?php

class Products extends Controller {

   var $_headers;
   
   var $is_error = FALSE;
   var $error_msg = '';

   function Products()
   {
      parent::Controller();   
      $this->load->helper(array('url', 'v1/xml', 'v1/json'));
      $this->load->model('v1/Keys');
      $this->load->model('v1/Sites');
   }
   
   // --------------------------------------------------------------------

   /**
    * productList
    *
    * @param string $site_id
    * @param string $cat_id
    * @param string $cat_code
    * @return array
    */
   function productList($key = '', $service_type = '', $site_id = '', $type = '', $identifier = '')
   {
      $id = ''; $code = ''; $upc = '';

      $$type = $identifier;

      $this->is_error = FALSE;
      $category = array();
      $products = array();

      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);

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

      // Check if type and identifier are valid
      $valid_types = array('id' => 'integer', 'code' => 'string');
      if ( ! $this->_valid_type_id($type, $identifier, $valid_types, FALSE))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }

      if ( ! ($id == '' && $code == '')  && ! $this->is_error)
      {
         // convert the product code to an ID number
         if ($code != '')
         {
            $id = $this->Categories->get_category_id_by_code($code, $site_id);
         }
   
         if ($id != '' && $id != FALSE)
         {
            $category = $this->Categories->get_category_data($id, $site_id);
            
            if ( ! empty($category))
            {
               // get all products linked to the category
               $products = $this->Products->get_products_in_category($id, $site_id);
               $status = 'success';
            }
            else
            {
               $this->is_error = TRUE;
               $status = 'error: the category was not found.';
            }
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: the category was not found.';
         }
      }
      elseif ( ! $this->is_error)
      {
         // get all the products associated with the site
         $products = $this->Products->get_products_in_site($site_id);
         $status = 'success';
      }
      
      // make sure SiteID and NutritionFacts are correct for products
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $products[$i]['SiteID'] = $site_id;
         $products[$i]['NutritionFacts'] = $this->Products->nutrition_facts($products[$i]['ProductID'], $site_id);
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['category'] = process_json_array($category);
            $data['products'] = array();
            foreach ($products AS $product)
            {
               $data['products'][] = process_json_array($product);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/products/product-list-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['category'] = process_xml_array($category);
            $data['products'] = array();
            foreach ($products AS $product)
            {
               $data['products'][] = process_xml_array($product);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/products/product-list-xml', $data, TRUE);
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
    * productSimpleList
    *
    * @param string $site_id
    * @param string $cat_id
    * @param string $cat_code
    * @return array
    */
   function productSimpleList($key = '', $service_type = '', $site_id = '', $use_groups = 'no')
   {
      $use_groups = (strtolower($use_groups) == 'yes') ? TRUE : FALSE;
      
      $this->is_error = FALSE;
      $category = array();
      $products = array();

      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);

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
         $categories = $this->Categories->get_product_category_list($site_id, $use_groups);
         $products = $this->Products->get_product_list($site_id, $use_groups);
         
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
            $response = $this->load->view('v1/products/product-simple-list-json', $data, TRUE);
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
            $response = $this->load->view('v1/products/product-simple-list-xml', $data, TRUE);
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

   // -----------------------------------------------------------------

   /**
    * categoryList
    *
    * @param string $site_id
    * @return array
    */
   function categoryList($key = '', $service_type = '', $site_id = '')
   {
      $this->is_error = FALSE;
      $categories = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);
      
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
         $categories = $this->Categories->get_category_tree($site_id);
         
         if ( ! empty($categories))
         {
            $status = 'success';
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: no categories were found.';
         }
      }

      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['categories'] = array();
            foreach ($categories AS $category)
            {
               $data['categories'][] = process_json_array($category);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/products/category-list-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['categories'] = array();
            foreach ($categories AS $category)
            {
               $data['categories'][] = process_xml_array($category);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/products/category-list-xml', $data, TRUE);
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

   // -----------------------------------------------------------------
   
   /**
    * Category Detail
    *
    * @param string $site_id
    * @param string $type     id, code
    * @param string $identifier
    * @return array
    */
   function categoryDetail($key = '', $service_type = '', $site_id = '', $type = '', $identifier = '')
   {
      $id = ''; $code = '';
      
      $$type = $identifier;
      
      $this->is_error = FALSE;
      $category = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);

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
      
      // Check if type and identifier are valid
      $valid_types = array('id' => 'integer', 'code' => 'string');
      if ( ! $this->_valid_type_id($type, $identifier, $valid_types, TRUE))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }      
      
      if ( ! $this->is_error)
      {
         // convert the product code to an ID number
         if ($code != '')
         {
            $id = $this->Categories->get_category_id_by_code($code, $site_id);
         }
   
         if ($id != '')
         {
            $category = $this->Categories->get_category_data($id, $site_id);
            
            if ( ! empty($category))
            {
               $status = 'success';
            }
            else
            {
               $this->is_error = TRUE;
               $status = 'error: the category was not found.';
            }
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: the category was not found.';
         }
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['category'] = process_json_array($category);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/products/category-detail-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['category'] = process_xml_array($category);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/products/category-detail-xml', $data, TRUE);
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
   
   // -----------------------------------------------------------------
   
   /**
    * Product Detail
    *
    * @param string $site_id
    * @param string $type     id, code, upc
    * @param string $identifier
    * @return array
    */
   function productDetail($key = '', $service_type = '', $site_id = '', $type = '', $identifier = '')
   {
      $id = ''; $code = ''; $upc = '';
      
      $$type = $identifier;
      
      $this->is_error = FALSE;
      $product = array();
      $categories = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);

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
      
      // Check if type and identifier are valid
      $valid_types = array('id' => 'integer', 'code' => 'string', 'upc' => 'integer');
      if ( ! $this->_valid_type_id($type, $identifier, $valid_types, TRUE))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }      
      
      if ( ! $this->is_error)
      {
         // convert the product code to an ID number
         if ($code != '')
         {
            $id = $this->Products->get_product_id_by_code($code, $site_id);
         }
   
         if ($upc != '')
         {
            $id = $this->Products->get_product_id_by_upc($upc, $site_id);
         }
   
         if ($id != '')
         {
            $product = $this->Products->get_product_data($id, $site_id);
            
            if ( ! empty($product))
            {
               $product['SiteID'] = $site_id;
               $product['NutritionFacts'] = $this->Products->nutrition_facts($id, $site_id);
   
               $categories = $this->Categories->get_all_category_ids($id);

               $status = 'success';
            }
            else
            {
               $this->is_error = TRUE;
               $status = 'error: the product was not found.';
            }
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: the product was not found.';
         }
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['product'] = process_json_array($product);
            $data['categories'] = $categories;
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/products/product-detail-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['product'] = process_xml_array($product);
            $data['categories'] = $categories;
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/products/product-detail-xml', $data, TRUE);
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
   
   // -----------------------------------------------------------------
   
   /**
    * Product NLEA
    *
    * @param string $site_id
    * @param string $type     id, code, upc
    * @param string $identifier
    * @return array
    */
   function productNLEA($key = '', $service_type = '', $site_id = '', $type = '', $identifier = '')
   {
      $id = ''; $code = ''; $upc = '';
      
      $$type = $identifier;
      
      $this->is_error = FALSE;
      $nlea = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);

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
      
      // Check if type and identifier are valid
      $valid_types = array('id' => 'integer', 'code' => 'string', 'upc' => 'integer');
      if ( ! $this->_valid_type_id($type, $identifier, $valid_types, TRUE))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }      
      
      if ( ! $this->is_error)
      {
         // convert the product code to an ID number
         if ($code != '')
         {
            $id = $this->Products->get_product_id_by_code($code, $site_id);
         }
   
         if ($upc != '')
         {
            $id = $this->Products->get_product_id_by_upc($upc, $site_id);
         }
   
         if ($id != '')
         {
            $product = $this->Products->get_product_data($id, $site_id);
            
            if ( ! empty($product))
            {
               $nlea = $this->Products->get_nlea_data($id);
               $nlea['SiteID'] = $site_id;
               $nlea['ProductName'] = $product['ProductName'];
               $nlea['ProductCode'] = $product['ProductCode'];
               $nlea['UPC'] = $product['UPC'];
   
               $status = 'success';
            }
            else
            {
               $this->is_error = TRUE;
               $status = 'error: the product was not found.';
            }
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: the product was not found.';
         }
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['nlea'] = process_json_array($nlea);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/products/product-nlea-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['nlea'] = process_xml_array($nlea);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/products/product-nlea-xml', $data, TRUE);
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
   
   // --------------------------------------------------------------------

   /**
    * Checks the validity of the type and identifier variables
    *
    * @access   public
    * @return   array
    */
   function _valid_type_id($type, $identifier, $valid_types, $type_is_required)
   {
      // see if an error has already been thrown
      if ($this->is_error == TRUE)
      {
         return TRUE;
      }
      
      if ($type_is_required && $type == '')
      {
         $this->error_msg = 'error: the identifier type is missing.';
         return FALSE;
      }
      
      $type = strtolower($type);
      
      if ($type != '')
      {
         if ($identifier == '')
         {
            $this->error_msg = 'error: the category identifier is missing.';
            return FALSE;
         }
         
         $valid_type_found = FALSE;
         foreach ($valid_types AS $key => $data_type)
         {
            if ($type == $key)
            {
               if (($data_type == 'integer' && ! is_numeric($identifier))
                  || ($data_type != 'integer' && is_numeric($identifier)))
               {
                  $this->error_msg = 'error: the category identifier is the wrong type.';
                  return FALSE;
               }
               $valid_type_found = TRUE;
            }
         }
         if ($valid_type_found == FALSE)
         {
            $this->error_msg = 'error: the supplied identifier type is invalid.';
            return FALSE;
         }
      }
      
      return TRUE;
   }


}  // END of Products Class

/* End of file products.php */
/* Location: ./system/modules/api/controllers/v1/products.php */