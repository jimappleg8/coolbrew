<?php

class Rtags extends Controller {

   function Rtags()
   {
      parent::Controller();	
   }
	
   // ------------------------------------------------------------------------

   /**
    * Displays the store locator form (uses Nielsen data)
    *
    */
   function storeLocator()
   {
      $this->load->library('Rtag');
      
      $cfg = array();

      // (string) the site id
      $cfg['site_id'] = $this->rtag->param('site-id');
      
      // (string) the site host domain
      $cfg['site_host'] = $this->rtag->param('site-host');
      
      // (string) the scope of the search (local, nielsen, all)
      $cfg['scope'] = $this->rtag->param('scope', 'all');
      
      // (string) the form template name
      $cfg['tpl'] = $this->rtag->param('search-tpl', 'locator-form');
      $cfg['tpl'] = (strpos('http://', $cfg['tpl']) === FALSE) ? 'rtags/v1/'.$cfg['tpl'] : $cfg['tpl'];
      
      // (boolean) display the product list by category?
      $cfg['by_category'] = $this->rtag->param('by-category', TRUE);
      $cfg['by_category'] = (strtolower($cfg['by_category']) == 'no') ? FALSE : TRUE;
      
      // (string) the results template name
      $cfg['result_tpl'] = $this->rtag->param('result-tpl', 'locator-results');
      $cfg['result_tpl'] = (strpos('http://', $cfg['result_tpl']) === FALSE) ? 'rtags/v1/'.$cfg['result_tpl'] : $cfg['result_tpl'];

      // (string) the file to point to for results
      $cfg['action'] = $this->rtag->param('action', '#');

      // (string) the file to point to for results
      $cfg['map_action'] = $this->rtag->param('map-action', '#');

      // (string) the file to point to for results
      $cfg['message_action'] = $this->rtag->param('message-action', '#');

      // (string) the UPC to use in "any product" search
      $cfg['any_product'] = $this->rtag->param('any-product', '');
      $cfg['any_product'] = ($cfg['any_product'] != '') ? '0'.$cfg['any_product'] : '';

      // (string) the UPC to select in list
      $cfg['select_upc'] = $this->rtag->param('select-upc', '');
      $cfg['select_upc'] = ($cfg['select_upc'] != '') ? '0'.$cfg['select_upc'] : '';

      // (string) the server level to use for product lists and maps
      $cfg['server_level'] = $this->rtag->param('server-level', 'live');

      // (string) redirect (used internally only)
      $cfg['redirect'] = $this->rtag->param('redirect', '');

      // (string) a API code to use instead of the standard one
      $cfg['map_api_key'] = $this->rtag->param('google-maps-api-key', '');

      // detect a call to load a map
      if ($cfg['redirect'] == 'map')
      {
         $this->map($cfg);
         exit;
      }
      
      // detect a call to send a message
      if ($cfg['redirect'] == 'message')
      {
         $this->message($cfg);
         exit;
      }
      
      $this->load->model('v1/Products');
      $this->Products->init_db($cfg['server_level']);
      $this->load->helper(array('form'));
      $this->load->library('validation');

      $rules['item'] = 'trim|required';
      $rules['zip'] = 'trim|required';
      $rules['radius'] = 'trim|required';
      $rules['count'] = 'trim|required';
      $rules['brand'] = 'trim|required';
      $rules['sort'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['item'] = 'Item';
      $fields['zip'] = 'Zip Code';
      $fields['radius'] = 'Search Radius';
      $fields['count'] = 'Stores Per Page';
      $fields['brand'] = 'Brand ID';
      $fields['sort'] = 'Sort Field';

      $this->validation->set_fields($fields);

      $defaults['radius'] = '10';
      $defaults['count'] = '50';
      
      $this->validation->set_defaults($defaults);
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         if ($cfg['by_category'] && $this->Products->get_category_root($cfg['site_id']))
         {
            $products = array();
            $cats = $this->Products->get_product_category_list($cfg['site_id']);
         }
         else
         {
            $products = $this->Products->get_product_list($cfg['site_id']);
            $cats = array();
         }
         
         $data['items'] = array();
         if ($cfg['any_product'] != '')
         {
            $data['items'][$cfg['any_product']] = '-- Any Product --';
         }
         else
         {
            $data['items'][''] = '-- Please Select a Product --';
         }
         if ($cfg['by_category'])
         {
            foreach ($cats AS $cat)
            {
               if ( ! empty($cat['Products']))
               {
                  $data['items'][$cat['CategoryName']] = array();
                  foreach ($cat['Products'] AS $product)
                  {
                     $data['items'][$cat['CategoryName']][$product['UPC']] = $product['ProductName'];
                  }
               }
            }
         }
         else
         {
            foreach ($products AS $product)
            {
               $data['items'][$product['UPC']] = $product['ProductName'];
            }
         }
         
         $data['radii'] = array(
            '' => '-- Choose a Distance --',
            '5' => 'Within 5 miles',
            '10' => 'Within 10 miles',
            '15' => 'Within 15 miles',
            '20' => 'Within 20 miles',
            '25' => 'Within 25 miles',
            '50' => 'Within 50 miles',
            '100' => 'Within 100 miles',
         );
         
         $data['action'] = $cfg['action'];
         $data['map_action'] = $cfg['map_action'];
         $data['message_action'] = $cfg['message_action'];
         $data['select_upc'] = $cfg['select_upc'];
         $data['products'] = $products;
         $data['cats'] = $cats;
         $data['brand'] = $cfg['site_id'];
	
         $html = $this->load->view($cfg['tpl'], $data, TRUE);

         $results[0] = 1;
         $results[1] = $html;
      }
      else
      {
         switch ($cfg['scope'])
         {
            case 'local':
               $html = $this->_local_locator_results($cfg);
               break;
            case 'nielsen':
               $html = $this->_nielsen_locator_results($cfg);
               break;
            case 'all':
               $html = $this->_all_locator_results($cfg);
               break;
         }
         $results[0] = 2;
         $results[1] = $html;
      }
      echo $html;
   }
   
   // ------------------------------------------------------------------------

   /**
    * Connects to the local database and returns the results it produces.
    *
    */
   function _local_locator_results($cfg)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

      $this->load->helper('sort');
      $this->load->model('v1/Products');
      $this->Products->init_db($cfg['server_level']);
      $this->load->model('v1/Stores');
      $this->Stores->init_db($cfg['server_level']);
      $this->load->model('Sites');
      $this->load->model('Zipcodes');
      
      // check if this is an IRI or Nielsen form
      $radius = (isset($search['radius'])) ? $search['radius'] : $search['searchradius'];
      
      $stores = $this->Stores->get_store_list($search);
      
      if ( ! is_array($stores))
      {
         $data['error'] = $stores;
         $stores = array();
      }
      elseif (count($stores) == 0)
      {
         if ( ! $this->Zipcodes->zipcode_exists($search['zip']))
         {
            $data['error'] = "The ZIP code <b>".$search['zip']."</b> does not currently appear to be assigned by the US Postal Service to any city. Only about 43,000 of the 100,000 possible 5-digit ZIP codes are currently in use. Please check to make sure you entered the correct ZIP code.";
         }
      }
      else
      {
         $data['error'] = '';
         $stores = mu_sort($stores, $search['sort']);
      }
      
      $my_site = $cfg['site_id'];

      $data['action'] = $cfg['action'];
      $data['map_action'] = $cfg['map_action'];
      $data['message_action'] = $cfg['message_action'];
      $data['query'] = $search;
      $data['brand_name'] = $this->Sites->get_brand_name($my_site);
      $data['product_name'] = $this->Products->get_product_by_locator_code($search['productid']);
      $data['stores'] = $stores;
      
      return $this->load->view($cfg['result_tpl'], $data, TRUE);
   }

   // ------------------------------------------------------------------------

   /**
    * Connects to the Nielsen Product Locator and returns the results.
    *
    */
   function _nielsen_locator_results($cfg)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

      $this->load->helper('sort');
      $this->load->model('v1/Products');
      $this->Products->init_db($cfg['server_level']);
      $this->load->model('Nielsen');
      $this->load->model('Sites');
      $this->load->model('Zipcodes');
      
      $this->Nielsen->XML_LIST_ELEMENTS = array("store");
   
      $stores = $this->Nielsen->get_store_list($search);
      
      if ( ! is_array($stores))
      {
         $data['error'] = $stores;
         $stores = array();
      }
      elseif (count($stores) == 0)
      {
         if ( ! $this->Zipcodes->zipcode_exists($search['zip']))
         {
            $data['error'] = "The ZIP code <b>".$search['zip']."</b> does not currently appear to be assigned by the US Postal Service to any city. Only about 43,000 of the 100,000 possible 5-digit ZIP codes are currently in use. Please check to make sure you entered the correct ZIP code.";
         }
      }
      else
      {
         $data['error'] = '';

         // create a number-only distance field if necessary
         if ($search['sort'] == 'Distance')
         {
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
            $stores = mu_sort($stores, 'DistanceNum');
         }
         else
         {
            $stores = mu_sort($stores, $search['sort']);
         }
      }

      // get the site ID for this product
      // this may not be ideal - we should probably get it using the UPC
      $my_site = $cfg['site_id'];

      $data['action'] = $cfg['action'];
      $data['map_action'] = $cfg['map_action'];
      $data['message_action'] = $cfg['message_action'];
      $data['query'] = $search;
      $data['brand_name'] = $this->Sites->get_brand_name($my_site);
      $data['product_name'] = $this->Products->get_product_by_upc($search['item']);
      $data['stores'] = $stores;

      return $this->load->view($cfg['result_tpl'], $data, TRUE);
   }


   // ------------------------------------------------------------------------

   /**
    * Connects to both the Nielsen Product Locator and the local database
    * and returns the results it produces.
    * 
    */
   function _all_locator_results($cfg)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);
         
      $this->load->helper('sort');
      $this->load->model('v1/Products');
      $this->Products->init_db($cfg['server_level']);
      $this->load->model('Nielsen');
      $this->load->model('v1/Stores');
      $this->Stores->init_db($cfg['server_level']);
      $this->load->model('Sites');
      $this->load->model('Zipcodes');
      
      $this->Nielsen->XML_LIST_ELEMENTS = array("store");
   
      $nielsen_stores = $this->Nielsen->get_store_list($search);
      
      if ( ! is_array($nielsen_stores))
      {
         $data['error'] = $nielsen_stores;
         $nielsen_stores = array();
      }

      $local_stores = $this->Stores->get_store_list($search);

      if ( ! is_array($local_stores))
      {
         $data['error'] = $local_stores;
         $local_stores = array();
      }

      $stores = array_merge($nielsen_stores, $local_stores);
      
      $data['error'] = '';
      if (count($stores) == 0)
      {
         if ( ! $this->Zipcodes->zipcode_exists($search['zip']))
         {
            $data['error'] = "The ZIP code <b>".$search['zip']."</b> was not found. Please check and make sure you typed it in correctly.";
         }
      }
      else
      {
         $search['sort'] = ($search['sort'] != '') ? $search['sort'] : 'Distance';
         
         // create a number-only distance field if necessary
         if ($search['sort'] == 'Distance')
         {
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
            $stores = mu_sort($stores, 'DistanceNum');
         }
         else
         {
            $stores = mu_sort($stores, $search['sort']);
         }
      }

      // get the site ID for this product
      // this may not be ideal - we should probably get it using the UPC
      $my_site = $cfg['site_id'];

      $data['action'] = $cfg['action'];
      $data['map_action'] = $cfg['map_action'];
      $data['message_action'] = $cfg['message_action'];
      $data['query'] = $search;
      $data['brand_name'] = $this->Sites->get_brand_name($my_site);
      $data['product_name'] = $this->Products->get_product_by_upc($search['item']);
      $data['stores'] = $stores;
      
      return $this->load->view($cfg['result_tpl'], $data, TRUE);
   }

   // ------------------------------------------------------------------------

   /**
    * Display a Google map of the supplied address
    *
    */
   function map($cfg) 
   {
      $this->load->model('Sites');

      $fields['Name'] = 'Store Name';
      $fields['Address1'] = 'Address1';
      $fields['Address2'] = 'Address2';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Zip'] = 'Zip Code';
      $fields['Phone'] = 'Phone';
      $fields['Latitude'] = 'Latitude';
      $fields['Longitude'] = 'Longitude';
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $data['store'] = $values;
      $data['brand_name'] = $this->Sites->get_brand_name($cfg['site_id']);
      
      if ($values['Latitude'] == 0 || $values['Longitude'] == 0)
      {
         // get the coordinates from Google
         $this->load->library('Gmap_geocoder');
         $search = $values['Address1'].', '.$values['City'].', '.$values['State'].' '.$values['Zip'];
         $coords = $this->gmap_geocoder->getGeoCoords($search);
         $values['Latitude'] = $coords['lat'];
         $values['Longitude'] = $coords['lng'];
      }

      $data['store'] = $values;
      
      echo $this->load->view('rtags/v1/map', $data, TRUE);
      exit;
   }

   //-------------------------------------------------------------------------
   
   /**
    * Creates a form allowing the user to tell us more about a store
    *
    */
   function message($cfg)
   {            
      $display_response = false;

      $this->load->helper(array('form','url'));
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $rules['FirstName'] = 'trim|required|max_length[25]|alpha_dash';
      $rules['LastName'] = 'trim|required|max_length[25]|alpha_dash';
      $rules['Email'] = 'trim|required|valid_email';
      $rules['Message'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['FirstName'] = 'First Name';
      $fields['LastName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Affiliated'] = 'Affiliated';
      $fields['Message'] = 'Message';
      $fields['StoreID'] = 'Store ID';
      $fields['StoreName'] = 'Store Name';
      $fields['Address1'] = 'Address1';
      $fields['Address2'] = 'Address2';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Zip'] = 'Zip Code';
      $fields['Phone'] = 'Phone';
      $fields['ProductID'] = 'Product Locator Code';
      $fields['ProductName'] = 'Product Name';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      // check first to see if the submitted form was just the button
      if ( ! $this->input->post('FormName'))
      {
         if ($this->validation->run() == TRUE)
         {
            $this->_message($cfg);
            $display_response = true;
         }
      }

      $data['action'] = $cfg['message_action'];
      $data['display_response'] = $display_response;
      $data['brand_name'] = $this->Sites->get_brand_name($cfg['site_id']);

      echo $this->load->view('rtags/v1/message_form', $data, TRUE);
      exit;
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Processes the form data from the message form;
    *
    */
   function _message($cfg)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $cfg['site_id'];
      $values['DateSent'] = date("Y-m-d H:i:s");
      
      $this->load->database('write');
      $this->db->insert('stores_message', $values);
      
      $values['brand_name'] = $this->Sites->get_brand_name($cfg['site_id']);
      $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
   
      // send e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail))
      {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      // send the email internally
      $mail_content = $this->load->view('message_mail', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   
      // send reply to user
      $mail_content2 = $this->load->view('message_reply', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);

      // send safe copies to internal folks (to avoid auto-reply issues)
      $mail_content3 = $this->load->view('message_safe', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content3)."\n");
      pclose($fd);
   }


} // END Class

?>