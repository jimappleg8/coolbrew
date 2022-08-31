<?php

include_once $COOLBREW['APPPATH'].'libraries/REST_Controller.php';

class Stores extends REST_Controller {

   var $_headers;
   
   var $params = array();
   
   var $is_error = FALSE;
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';

   function Stores()
   {
      parent::__construct();
      $this->load->helper(array('url', 'v2/xml', 'v2/json'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Accepts form input for a store message, saves it to the database
    *  and returns the same data for further processing.
    *
    * @return array
    */
   function stores_messages_post()
   {
      $this->params['format'] = $this->post('format', TRUE, 'json');
      $this->params['site-id'] = $this->post('site-id');
      $this->params['StoreID'] = $this->post('StoreID', TRUE, '0');
      $this->params['StoreName'] = $this->post('StoreName');
      $this->params['Address1'] = $this->post('Address1');
      $this->params['Address2'] = $this->post('Address2');
      $this->params['City'] = $this->post('City');
      $this->params['State'] = $this->post('State');
      $this->params['Zip'] = $this->post('Zip');
      $this->params['Phone'] = $this->post('Phone');
      $this->params['ProductNum'] = $this->post('ProductNum');
      $this->params['ProductName'] = $this->post('ProductName');
      $this->params['FirstName'] = $this->post('FirstName');
      $this->params['LastName'] = $this->post('LastName');
      $this->params['Email'] = $this->post('Email');
      $this->params['Affiliated'] = $this->post('Affiliated', TRUE, '0');
      $this->params['Message'] = $this->post('Message');
      $this->params['test'] = $this->post('test', TRUE, 'false');
      $this->params['db-level'] = $this->post('db-level', TRUE, 'live');
      $this->params['suppress-response-codes'] = strtolower($this->get('suppress-response-codes', TRUE, 'false'));
      
      // force db-level to 'live' if invalid
      if ( ! in_array($this->params['db-level'], array('local', 'stage', 'live')))
      {
         $this->params['db-level'] = 'live';
      }
      
//      echo '<pre>'; print_r($this->params); echo '</pre>'; exit;

      $this->load->model('v2/Messages');
      $this->Messages->init_db($this->params['db-level']);
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

      $submitted = array();
            
      $message_id = '';
      if ( ! $this->is_error)
      {
         $values = $this->params;
         unset($values['format']);
         unset($values['site-id']);
         unset($values['ProductNum']);
         unset($values['test']);
         unset($values['db-level']);
         unset($values['suppress-response-codes']);

         $values['SiteID'] = $this->params['site-id'];
         $values['ProductID'] = $this->params['ProductNum'];
         $values['DateSent'] = date("Y-m-d H:i:s");
         
         $message_id = $this->Messages->insert_message($values, $this->params['test']);

         // assemble the search data
         $submitted['MessageID'] = $message_id;
         $submitted['SiteID'] = $this->params['site-id'];
         $submitted['BrandName'] = $this->Sites->get_brand_name($this->params['site-id']);
         $submitted['StoreID'] = $this->params['StoreID'];
         $submitted['StoreName'] = $this->params['StoreName'];
         $submitted['Address1'] = $this->params['Address1'];
         $submitted['Address2'] = $this->params['Address2'];
         $submitted['City'] = $this->params['City'];
         $submitted['State'] = $this->params['State'];
         $submitted['Zip'] = $this->params['Zip'];
         $submitted['Phone'] = $this->params['Phone'];
         $submitted['ProductNum'] = $this->params['ProductNum'];
         $submitted['ProductName'] = $this->params['ProductName'];
         $submitted['FirstName'] = $this->params['FirstName'];
         $submitted['LastName'] = $this->params['LastName'];
         $submitted['Email'] = $this->params['Email'];
         $submitted['Affiliated'] = $this->params['Affiliated'];
         $submitted['Message'] = $this->params['Message'];  
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
         $this->http_code = 201;
         $data['Meta'] = array();
         $data['Meta']['HttpCode'] = $this->http_code;
         $data['Meta']['Source'] = $this->params['db-level'];
         $data['Meta']['ResponseTime'] = microtime(true) - $this->_start_rtime;
         $data['Submitted'] = $submitted;
      }

      $http_code = ($this->params['suppress-response-codes'] == 'true') ? 200 : $this->http_code;

      $this->response($data, $http_code);
   }
   

}  // END of Stores Class

/* End of file stores.php */
/* Location: ./system/modules/api/controllers/v2/stores.php */