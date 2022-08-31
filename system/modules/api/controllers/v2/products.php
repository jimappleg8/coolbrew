<?php

include_once $COOLBREW['APPPATH'].'libraries/REST_Controller.php';

class Products extends REST_Controller {

   var $_headers;

   var $params = array();

   var $is_error = FALSE;
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';

   // --------------------------------------------------------------------

   function Products()
   {
      parent::__construct();
      $this->load->helper(array('url', 'v2/xml', 'v2/json'));
   }

   // --------------------------------------------------------------------

   /**
    * GET /products_stores  searches for stores under the specified variables
    *
    * @return array
    */
   function products_stores_get()
   {
      $this->load->helper('v2/sort');
      
      $this->params['upc'] = $this->uri->segment(3);
      $this->params['site-id'] = $this->get('site-id');
      $this->params['format'] = $this->get('format', TRUE, 'xml');
      $this->params['ip'] = $this->get('ip', TRUE);
      $this->params['zip'] = $this->get('zip', TRUE, '');
      $this->params['latitude'] = $this->get('latitude', TRUE, '');
      $this->params['longitude'] = $this->get('longitude', TRUE, '');
      $this->params['city'] = $this->get('city', TRUE, '');
      $this->params['state'] = $this->get('state', TRUE, '');
      $this->params['radius'] = $this->get('radius', TRUE, 10);
      $this->params['count'] = $this->get('count', TRUE, 50);
      $this->params['sort'] = $this->get('sort', TRUE, 'Distance');
      $this->params['db-level'] = strtolower($this->get('db-level', TRUE, 'live'));
      $this->params['suppress-response-codes'] = strtolower($this->get('suppress-response-codes', TRUE, 'false'));

      // force db-level to 'live' if invalid
      if ( ! in_array($this->params['db-level'], array('stage', 'live')))
      {
         $this->params['db-level'] = 'live';
      }

      $this->load->model('v2/Nielsen');
      $this->load->model('v2/Products');
      $this->Products->init_db($this->params['db-level']);
      $this->load->model('v2/Stores');
      $this->Stores->init_db($this->params['db-level']);
      $this->load->model('v2/Zipcodes');
      $this->Zipcodes->init_db($this->params['db-level']);

      // check if the format is valid
      if ( ! in_array($this->params['format'], array('xml', 'json')))
      {
         $this->is_error = TRUE;
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$this->params['format'].' is invalid for format. Supported values are "xml" and "json".';
         $this->error_usr_msg = 'The supplied format parameter is invalid.';
      }

//      echo '<pre>'; print_r($this->params); echo '</pre>'; exit;
      
      $this->Nielsen->XML_LIST_ELEMENTS = array("store");

      // determine what location type to use: lat/long, zip, city/state
      if ($this->params['latitude'] != '' && $this->params['longitude'] != '')
      {
         $method_name = 'get_store_list_by_latlong';
      }
      elseif ($this->params['city'] != '' && $this->params['state'] != '')
      {
         $method_name = 'get_store_list_by_citystate';
      }
      elseif ($this->params['zip'] != '')
      {
         $method_name = 'get_store_list_by_zip';
      }
      else
      {
         // no valid location supplied.
         $this->is_error = TRUE;
         $this->http_code = 400;
         $this->error_dev_msg = 'The user location is missing. You must supply either a Zip, City/State or Latitude/Longitude.';
         $this->error_usr_msg = 'The user location is missing or incomplete.';
      }

      if ( ! $this->is_error)
      {
         // get the Nielsen data and check for errors along the way
         if (FALSE === $nielsen_stores = $this->Nielsen->$method_name($this->params))
         {
            $this->is_error = TRUE;
            $this->http_code = $this->Nielsen->http_code;
            $this->error_dev_msg = $this->Nielsen->error_dev_msg;
            $this->error_usr_msg = $this->Nielsen->error_usr_msg;
            $this->error_more_info = $this->Nielsen->error_more_info;
         }
         
         // get the local data and check for errors along the way
         if (FALSE === $local_stores = $this->Stores->$method_name($this->params))
         {
            $this->is_error = TRUE;
            $this->http_code = $this->Stores->http_code;
            $this->error_dev_msg = $this->Stores->error_dev_msg;
            $this->error_usr_msg = $this->Stores->error_usr_msg;
            $this->error_more_info = $this->Stores->error_more_info;
         }
         
         $nielsen_stores = ( ! is_array($nielsen_stores)) ? array() : $nielsen_stores;
         $local_stores = ( ! is_array($local_stores)) ? array() : $local_stores;

         $stores = array_merge($nielsen_stores, $local_stores);

         if ( ! empty($stores))
         {
            if ($this->params['sort'] == 'Distance')
            {
               $stores = mu_sort($stores, 'DistanceNum');
            }
            else
            {
               $stores = mu_sort($stores, $this->params['sort']);
            }
         }
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
         $data['Meta']['TotalResults'] = count($stores);
         $data['Meta']['ProductNum'] = $this->params['upc'];
         $data['Meta']['ProductName'] = $this->Products->get_product_name_by_upc($this->params['upc']);
         $data['Meta']['Zip'] = $this->params['zip'];
         $data['Meta']['Latitude'] = $this->params['latitude'];
         $data['Meta']['Longitude'] = $this->params['longitude'];
         $data['Meta']['City'] = $this->params['city'];
         $data['Meta']['State'] = $this->params['state'];
         $data['Meta']['Radius'] = $this->params['radius'];
         $data['Meta']['Count'] = $this->params['count'];
         $data['Meta']['Sort'] = $this->params['sort'];
         $data['Stores'] = $stores;
      }

      $http_code = ($this->params['suppress-response-codes'] == 'true') ? 200 : $this->http_code;

      $this->response($data, $http_code);
   }


}  // END of Products Class

/* End of file products.php */
/* Location: ./system/modules/api/controllers/v2/products.php */