<?php

include_once $COOLBREW['APPPATH'].'libraries/REST_Controller.php';

class Sites extends REST_Controller {

   var $_headers;

   var $params = array();

   var $is_error = FALSE;
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';

   // --------------------------------------------------------------------

   function Sites()
   {
      parent::__construct();
      $this->load->helper(array('url', 'v2/xml', 'v2/json'));
   }

   // --------------------------------------------------------------------

   /**
    * GET \sites
    *
    */
   function sites_get()
   {
      $this->params['format'] = $this->get('format', TRUE, 'xml');
      $this->params['db-level'] = strtolower($this->get('db-level', TRUE, 'live'));
      $this->params['module'] = strtolower($this->get('module', TRUE, ''));
      $this->params['suppress-response-codes'] = strtolower($this->get('suppress-response-codes', TRUE, 'false'));
      
      // force db-level to 'live' if invalid
      if ( ! in_array($this->params['db-level'], array('stage', 'live')))
      {
         $this->params['db-level'] = 'live';
      }

      $this->load->model('v2/Sites');
      $this->Sites->init_db($this->params['db-level']);
      
      // check if the format is valid
      if ( ! in_array($this->params['format'], array('xml', 'json')))
      {
         $this->is_error = TRUE;
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$this->params['format'].' is invalid for format. Supported values are "xml" and "json".';
         $this->error_usr_msg = 'The supplied format parameter is invalid.';
      }

      // check if the Module ID is valid
      if ( ! ($this->Sites->valid_module_id($this->params['module'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $this->http_code = $this->Sites->http_code;
         $this->error_dev_msg = $this->Sites->error_dev_msg;
         $this->error_usr_msg = $this->Sites->error_usr_msg;
         $this->error_more_info = $this->Sites->error_more_info;
      }
      
      if ($this->is_error)
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['DeveloperMessage'] = $this->error_dev_msg;
         $data['Meta']['UserMessage'] = $this->error_usr_msg;
         if ($this->error_more_info != '')
         {
            $data['Meta']['MoreInfo'] = $this->error_more_info;
         }
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
      }
      else
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
         if ($this->params['module'] != '')
         {
            $data['Sites'] = $this->Sites->get_sites_list_by_module($this->params['module']);
         }
         else
         {
            $data['Sites'] = $this->Sites->get_sites_list();
         }
      }
      
      $http_code = ($this->params['suppress-response-codes'] == 'true') ? 200 : $this->http_code;

      $this->response($data, $http_code);
   }

   // --------------------------------------------------------------------

   /**
    * GET \sites\:site-id
    *
    */
   function sites_item_get()
   {
      $this->params['site-id'] = $this->uri->segment(3);
      $this->params['format'] = $this->get('format', TRUE, 'xml');
      $this->params['db-level'] = $this->get('db-level', TRUE, 'live');
      $this->params['suppress-response-codes'] = strtolower($this->get('suppress-response-codes', TRUE, 'false'));
      
      // force db-level to 'live' if invalid
      if ( ! in_array($this->params['db-level'], array('local', 'stage', 'live')))
      {
         $this->params['db-level'] = 'live';
      }

      $this->load->model('v2/Sites');
      $this->Sites->init_db($this->params['db-level']);
      
      // check if the format is valid
      if ( ! in_array($this->params['format'], array('xml', 'json')))
      {
         $this->is_error = TRUE;
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$this->params['format'].' is invalid for format. Supported values are "xml" and "json".';
         $this->error_usr_msg = 'The supplied format parameter is invalid.';
      }

      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($this->params['site-id'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $this->http_code = $this->Sites->http_code;
         $this->error_dev_msg = $this->Sites->error_dev_msg;
         $this->error_usr_msg = $this->Sites->error_usr_msg;
         $this->error_more_info = $this->Sites->error_more_info;
      }
      
      if ($this->is_error)
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['DeveloperMessage'] = $this->error_dev_msg;
         $data['Meta']['UserMessage'] = $this->error_usr_msg;
         if ($this->error_more_info != '')
         {
            $data['Meta']['MoreInfo'] = $this->error_more_info;
         }
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
      }
      else
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
         $data['Site'] = $this->Sites->get_site_data($this->params['site-id']);
      }

      $http_code = ($this->params['suppress-response-codes'] == 'true') ? 200 : $this->http_code;

      $this->response($data, $http_code);
   }

   // --------------------------------------------------------------------

   /**
    * GET /sites_products
    *
    */
   function sites_products_get()
   {
      $this->params['site-id'] = $this->uri->segment(3);
      $this->params['categories'] = strtolower($this->get('categories', TRUE, 'false'));
      $this->params['format'] = $this->get('format', TRUE, 'xml');
      $this->params['db-level'] = $this->get('db-level', TRUE, 'live');
      $this->params['suppress-response-codes'] = strtolower($this->get('suppress-response-codes', TRUE, 'false'));
      
      // force db-level to 'live' if invalid
      if ( ! in_array($this->params['db-level'], array('local', 'stage', 'live')))
      {
         $this->params['db-level'] = 'live';
      }

      $this->load->model('v2/Products');
      $this->Products->init_db($this->params['db-level']);
      $this->load->model('v2/Sites');
      $this->Sites->init_db($this->params['db-level']);

      // check if the format is valid
      if ( ! in_array($this->params['format'], array('xml', 'json')))
      {
         $this->is_error = TRUE;
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$this->params['format'].' is invalid for format. Supported values are "xml" and "json".';
         $this->error_usr_msg = 'The supplied format parameter is invalid.';
      }

      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($this->params['site-id'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $this->http_code = $this->Sites->http_code;
         $this->error_dev_msg = $this->Sites->error_dev_msg;
         $this->error_usr_msg = $this->Sites->error_usr_msg;
         $this->error_more_info = $this->Sites->error_more_info;
      }

      if ($this->is_error)
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['DeveloperMessage'] = $this->error_dev_msg;
         $data['Meta']['UserMessage'] = $this->error_usr_msg;
         if ($this->error_more_info != '')
         {
            $data['Meta']['MoreInfo'] = $this->error_more_info;
         }
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
      }
      else
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
         if ($this->params['categories'] == 'true') 
         {
            $data['Categories'] = $this->Products->get_product_category_list($this->params['site-id']);
         }
         else
         {
            $data['Products'] = $this->Products->get_product_list($this->params['site-id']);
         }
      }

      $http_code = ($this->params['suppress-response-codes'] == 'true') ? 200 : $this->http_code;

      $this->response($data, $http_code);
   }


}  // END of Sites Class

/* End of file sites.php */
/* Location: ./system/modules/api/controllers/v2/sites.php */