<?php

include_once $COOLBREW['APPPATH'].'libraries/REST_Controller.php';

class Brands extends REST_Controller {

   var $_headers;

   var $params = array();

   var $is_error = FALSE;
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';

   // --------------------------------------------------------------------

   function Brands()
   {
      parent::__construct();
      $this->load->helper(array('url', 'v2/xml', 'v2/json'));
   }

   // --------------------------------------------------------------------

   /**
    * GET /v2
    *
    * @return array
    */
   function item_get()
   {
      $this->params['format'] = $this->get('format', TRUE, 'xml');
      $this->params['db-level'] = strtolower($this->get('db-level', TRUE, 'live'));
      $this->params['suppress-response-codes'] = strtolower($this->get('suppress-response-codes', TRUE, 'false'));

      // force db-level to 'live' if invalid
      if ( ! in_array($this->params['db-level'], array('stage', 'live')))
      {
         $this->params['db-level'] = 'live';
      }

      // check if the format is valid
      if ( ! in_array($this->params['format'], array('xml', 'json')))
      {
         $this->is_error = TRUE;
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$this->params['format'].' is invalid for format. Supported values are "xml" and "json".';
         $this->error_usr_msg = 'The supplied format parameter is invalid.';
      }

      if ( ! $this->is_error)
      {
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = 200;
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
         
         $data['Endpoints'] = array();
         $data['Endpoints']['sites_url'] = base_url().'v2/sites';
         $data['Endpoints']['products_url'] = base_url().'v2/sites/{site-id}/products';
         $data['Endpoints']['stores_url'] = base_url().'v2/products/{upc}/stores';
      }

      $http_code = ($this->params['suppress-response-codes'] == 'true') ? 200 : $this->http_code;

      $this->response($data, $http_code);
   }


}  // END of Brands Class

/* End of file brands.php */
/* Location: ./system/modules/api/controllers/v2/brands.php */