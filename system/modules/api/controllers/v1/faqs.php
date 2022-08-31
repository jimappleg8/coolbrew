<?php

class Faqs extends Controller {

   var $_headers;

   var $post = array();
   
   var $is_error = FALSE;
   var $error_msg = '';

   function Faqs()
   {
      parent::Controller();   
      $this->load->helper(array('url', 'v1/xml', 'v1/json'));
      $this->load->model('v1/Keys');
      $this->load->model('v1/Sites');
   }
   
   // --------------------------------------------------------------------

   /**
    * faqCategoryList
    *
    * @param string  the service key
    * @param string  the service type (e.g. xml or json)
    * @param string  the site ID
    * @return array
    */
   function faqCategoryList($key = '', $service_type = '', $site_id = '')
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

      $this->load->model('v1/faqs/Categories');
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
            $response = $this->load->view('v1/faqs/category-list-json', $data, TRUE);
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
            $response = $this->load->view('v1/faqs/category-list-xml', $data, TRUE);
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
    * faqList
    *
    * @param string  the service key
    * @param string  the service type (e.g. xml or json)
    * @param string  the site ID
    * @param string  the type of identifier used in the next parameter
    * @param string  the faq list identifier
    * @return array
    */
   function faqList($key = '', $service_type = '', $site_id = '', $type = '', $identifier = '')
   {
      $id = ''; $code = ''; $upc = '';
      
      $$type = $identifier;
      
      $this->is_error = FALSE;
      $category = array();
      $faqs = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/faqs/Categories');
      $this->Categories->init_db($level);
      $this->load->model('v1/faqs/Items');
      $this->Items->init_db($level);
      
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
               $faqs = $this->Items->get_faqs_in_category($id);
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
         $faqs = $this->Items->get_faqs_in_site($site_id);
         $status = 'success';
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['category'] = process_json_array($category);
            $data['faqs'] = array();
            foreach ($faqs AS $faq)
            {
               $data['faqs'][] = process_json_array($faq);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/faqs/faq-list-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['category'] = process_xml_array($category);
            $data['faqs'] = array();
            foreach ($faqs AS $faq)
            {
               $data['faqs'][] = process_xml_array($faq);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/faqs/faq-list-xml', $data, TRUE);
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
    * faqDetail
    *
    * @param string  the service key
    * @param string  the service type (e.g. xml or json)
    * @param string  the site ID
    * @param string  the FAQ ID
    * @param string  the Answer ID
    * @return array
    */
   function faqDetail($key = '', $service_type = '', $site_id = '', $faq_id = '', $answer_id = '')
   {
      $this->is_error = FALSE;
      $faq = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/faqs/Items');
      $this->Items->init_db($level);
      
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
      
      if ($faq_id != '' && $answer_id != '' && ! $this->is_error)
      {
         $faq = $this->Items->get_faq_data($faq_id, $answer_id);
            
         if ( ! empty($faq))
         {
            $status = 'success';
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: the FAQ was not found.';
         }
      }
      elseif ( ! $this->is_error)
      {
         $this->is_error = TRUE;
         $status = 'error: the FAQ ID is missing.';
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['faq'] = process_json_array($faq);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/faqs/faq-detail-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['faq'] = process_xml_array($faq);
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/faqs/faq-detail-xml', $data, TRUE);
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
    * faqSearch
    *
    * @param string  the service key
    * @param string  the service type (e.g. xml or json)
    * @param string  the site ID
    * @param string  the FAQ ID
    * @param string  the Answer ID
    * @return array
    */
   function faqSearch()
   {
      // establish what information was submitted
      $this->_get_value(4, 'api-key');
      $this->_get_value(5, 'format');
      $this->_get_value(6, 'site-id');
      $this->_get_value(7, 'Words');

      $this->is_error = FALSE;
      $search = array();
      $faqs = array();

      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($this->post['api-key'], $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/faqs/Items');
      $this->Items->init_db($level);
      $this->load->model('v1/faqs/Keywords');
      $this->Keywords->init_db($level);
      
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

      // assemble the search data
      $search['SiteID'] = $this->post['site-id'];
      $search['Words'] = $this->post['Words'];

      if ( ! $this->is_error)
      {
         $faqs = $this->Items->search_faqs($search, $this->post['site-id'], TRUE);

         // make sure each ShortQuestion has a value
         for ($i=0, $total_faqs = count($faqs); $i<$total_faqs; $i++)
         {
            if (trim($faqs[$i]['ShortQuestion']) == '')
            {
               $faqs[$i]['ShortQuestion'] = $faqs[$i]['Question'];
            }
         }

         // add search request to keywords database
         $this->Keywords->insert_keywords($this->post['site-id'], $search['Words'], $faqs);
      }
      
      $search['ResultsFound'] = count($faqs);
      
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
            $data['source'] = $level;
            $data['search'] = process_json_array($search);
            $data['faqs'] = array();
            foreach ($faqs AS $faq)
            {
               $data['faqs'][] = process_json_array($faq);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/faqs/faq-search-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['search'] = process_xml_array($search);
            $data['faqs'] = array();
            foreach ($faqs AS $faq)
            {
               $data['faqs'][] = process_xml_array($faq);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/faqs/faq-search-xml', $data, TRUE);
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
    * faqPopularSearches
    *
    * @param string  the service key
    * @param string  the service type (e.g. xml or json)
    * @param string  the site ID
    * @param string  the FAQ ID
    * @param string  the Answer ID
    * @return array
    */
   function faqPopularSearches($key = '', $service_type = '', $site_id = '', $days = '', $limit = '')
   {
      $this->load->model('v1/faqs/Keywords');
      
      $this->is_error = FALSE;
      $searches = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/faqs/Keywords');
      $this->Keywords->init_db($level);
      
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
      
      // set defaults
      $days = ($days == '') ? 30 : $days;
      $limit = ($limit == '') ? 5 : $limit;
      
      if ( ! $this->is_error)
      {
         $searches = $this->Keywords->get_popular_searches($site_id, $days, $limit);
         $status = 'success';
      }
      
//      echo '<pre>'; print_r($searches); echo '</pre>'; exit;
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            foreach ($searches AS $search)
            {
               $data['searches'][] = process_json_array($search);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/faqs/faq-popular-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            foreach ($searches AS $search)
            {
               $data['searches'][] = process_xml_array($search);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/faqs/faq-popular-xml', $data, TRUE);
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


}  // END of Faqs Class

/* End of file stores.php */
/* Location: ./system/modules/api/controllers/v1/faqs.php */