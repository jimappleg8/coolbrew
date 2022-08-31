<?php

class Stores_Tags extends Controller {

   function Stores_Tags()
   {
      parent::Controller();
      $this->load->database('read');
   }
	
   // ------------------------------------------------------------------------

   /**
    * Displays the store locator form (uses Nielsen data)
    *
    */
   function locator($loc_code = "")
   {
      // (string) the scope of the search (local, nielsen, all)
      $scope = $this->tag->param(1, 'all');
      
      // (string) the site id
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) the template name
      $tpl = $this->tag->param(3, 'locator_form');
      
      // (boolean) display the product list by category?
      $by_category = $this->tag->param(4, TRUE);
      
      // (string) the results template name
      $result_tpl = $this->tag->param(5, 'locator_results');

      // (string) the file to point to for results
      $action = $this->tag->param(6, $_SERVER['PHP_SELF']);

      // (string) the UPC to use in "any product" search
      $any_product = $this->tag->param(7, '');

      $this->collector->prepend_css_file('stores-tags');
      $this->collector->append_js_file('stores-tags');

      // detect a call to load a map
      if ($loc_code == 'map')
      {
         $this->map($site_id);
         exit;
      }
      
      // detect a call to send a message
      if ($loc_code == 'message')
      {
         $this->message($site_id);
         exit;
      }
      
      $this->load->model('Products');
      $this->load->library('validation');

      $rules['item'] = 'trim';
      $rules['zip'] = 'trim';
      $rules['radius'] = 'trim';
      $rules['count'] = 'trim';
      $rules['brand'] = 'trim';
      $rules['sort'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['item'] = 'Locator Code';
      $fields['zip'] = 'Zip Code';
      $fields['radius'] = 'Search Radius';
      $fields['count'] = 'Stores Per Page';
      $fields['brand'] = 'Brand ID';
      $fields['sort'] = 'Sort Field';

      $this->validation->set_fields($fields);

      $defaults['count'] = '50';
      
      $this->validation->set_defaults($defaults);
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         if ($by_category && $this->Products->get_category_root($site_id))
         {
            $products = array();
            $cats = $this->Products->get_product_category_list($site_id);
         }
         else
         {
            $products = $this->Products->get_product_list($site_id);
            $cats = array();
         }
         
         $data['any_product'] = $any_product;
         $data['action'] = $action;
         $data['loc_code'] = $loc_code;
         $data['by_category'] = $by_category;
         $data['products'] = $products;
         $data['cats'] = $cats;
         $data['brand'] = $site_id;
	
         $html = $this->load->view($tpl, $data, TRUE);

         $results[0] = 1;
         $results[1] = $html;

         return $results;
      }
      else
      {
         switch ($scope)
         {
            case 'local':
               $html = $this->_local_locator_results($result_tpl);
               break;
            case 'nielsen':
               $html = $this->_nielsen_locator_results($result_tpl);
               break;
            case 'all':
               $html = $this->_all_locator_results($result_tpl);
               break;
         }
         $results[0] = 2;
         $results[1] = $html;

         return $results;
      }
   }
   
   // ------------------------------------------------------------------------

   /**
    * Connects to the local database and returns the results it produces.
    *
    */
   function _local_locator_results($tpl)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

      $this->load->helper('sort');
      $this->load->model('Products');
      $this->load->model('Stores');
      $this->load->model('Sites');
      $this->load->model('Zipcodes');
      
      $data['error'] = '';

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
         $stores = mu_sort($stores, $search['sort']);
      }
      
      // get the site ID for this product by parsing the LocatorCode
      $my_site = strtolower(preg_replace('/[0-9]/', '', $search['productid']));

      $data['action'] = $_SERVER['PHP_SELF'];
      $data['query'] = $search;
      $data['brand_name'] = $this->Sites->get_brand_name($my_site);
      $data['product_name'] = $this->Products->get_product_by_locator_code($search['productid']);
      $data['stores'] = $stores;
      
      return $this->load->view($tpl, $data, TRUE);
   }

   // ------------------------------------------------------------------------

   /**
    * Connects to the Nielsen Product Locator and returns the results.
    *
    */
   function _nielsen_locator_results($tpl)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

      $this->load->helper('sort');
      $this->load->model('Products');
      $this->load->model('Nielsen');
      $this->load->model('Sites');
      $this->load->model('Zipcodes');
      
      $data['error'] = '';

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
      $my_site = SITE_ID;

      $data['action'] = $_SERVER['PHP_SELF'];
      $data['query'] = $search;
      $data['brand_name'] = $this->Sites->get_brand_name($my_site);
      $data['product_name'] = $this->Products->get_product_by_upc($search['item']);
      $data['stores'] = $stores;

      return $this->load->view($tpl, $data, TRUE);
   }


   // ------------------------------------------------------------------------

   /**
    * Connects to both the Nielsen Product Locator and the local database
    * and returns the results it produces.
    * 
    */
   function _all_locator_results($tpl)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);
         
      $this->load->helper('sort');
      $this->load->model('Products');
      $this->load->model('Nielsen');
      $this->load->model('Stores');
      $this->load->model('Sites');
      $this->load->model('Zipcodes');
      
      $data['error'] = '';
      
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
         if ($search['sort'] == 'Distance')
         {
            $stores = mu_sort($stores, 'DistanceNum');
         }
         else
         {
            $stores = mu_sort($stores, $search['sort']);
         }
      }

      // get the site ID for this product
      // this may not be ideal - we should probably get it using the UPC
      $my_site = SITE_ID;

      $data['action'] = $_SERVER['PHP_SELF'];
      $data['query'] = $search;
      $data['brand_name'] = $this->Sites->get_brand_name($my_site);
      $data['product_name'] = $this->Products->get_product_by_upc($search['item']);
      $data['stores'] = $stores;
      
      return $this->load->view($tpl, $data, TRUE);
   }

   // ------------------------------------------------------------------------

   /**
    * Return the Store name given a CITY 
    *
    * This function is not fully converted.
    */
   function canadian_City_results($province) 
   {
      // how many rows to show per page
      $rowsPerPage = 20;

      // by default we show first page
      $pageNum = 1;

      // if $_GET['page'] defined, use it as page number
      if ($this->input->post('page'))
      {
         $pageNum = $this->input->post('page');
      }

      // counting the offset
      $offset = ($pageNum - 1) * $rowsPerPage;
  
      // assign template to display the CITY
      if ($tpl == "")
      {
         $tpl = "canadian_City_results";
      }

      $this->load->database('read');
      
      // Query City data by PROVINCE
      $sql = 'SELECT * FROM stores '.
	         'WHERE State LIKE "'.$province.'" '.
	         'ORDER BY StoreName ASC, City ASC '.
	         'LIMIT '.$offset.', '.$rowsPerPage;

      $query = $this->db->query($sql);
      $cities = $query->result_array();

      //*** Total rows we have in database ***
      $sql = 'SELECT * FROM stores '.
	         'WHERE State LIKE "'.$province.'" '.
	         'ORDER BY City ASC';

      $query = $this->db->query($sql);
      $numrows = $query->num_rows();

      // Total page?
	  $maxPage = ceil($numrows/$rowsPerPage);

	  $self = $_SERVER['PHP_SELF'];
      /*** End of Total Page calculation ***/

      //*** Creating Prev, first
      if ($pageNum > 1)
      {
         $page = $pageNum - 1;
         $prev = " <a href=\"$self?page=$page&province=$province\">[Prev]</a> ";
         $first = " <a href=\"$self?page=1&province=$province\">[First Page]</a> ";
      } 
      else
      {
         $prev  = ' [Prev] ';       // we're on page one, don't enable 'previous' link
         $first = ' [First Page] '; // nor 'first page' link
      }

      // print 'next' link only if we're not on the last page
      if ($pageNum < $maxPage)
      {
         $page = $pageNum + 1;
         $next = " <a href=\"$self?page=$page&province=$province\">[Next]</a> ";  
         $last = " <a href=\"$self?page=$maxPage&province=$province\">[Last Page]</a> ";
      } 
      else
      {
         $next = ' [Next] ';      // we're on the last page, don't enable 'next' link
         $last = ' [Last Page] '; // nor 'last page' link
      }

      $data['cities'] = $cities;
      $data['num_cities'] = $numrows;
      $data['province'] = $province;
      $data['first'] = $first;
      $data['prev'] = $prev;
      $data['next'] = $next;
      $data['last'] = $last;
      $data['pageNum'] = $pageNum;
      $data['maxPage'] = $maxPage;

      echo $this->load->view($tpl, $data, TRUE);
   }

   // ------------------------------------------------------------------------

   /**
    * Display a Google map of the supplied address
    *
    */
   function map($site_id) 
   {
      $tpl = 'map';

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

      $data['css'] = $this->collector->wrap_css();
      $data['brand_name'] = $this->Sites->get_brand_name($site_id);
      
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

      echo $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------
   
   /**
    * Creates a form allowing the user to tell us more about a store
    *
    */
   function message($site_id)
   {            
      $display_response = false;

      $this->load->helper(array('form','url'));
      $this->load->model('Sites');
      $this->load->model('Messages');
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
            $this->_message($site_id);
            $display_response = true;
         }
      }

      $data['css'] = $this->collector->wrap_css();
      $data['action'] = $_SERVER['PHP_SELF'];
      $data['display_response'] = $display_response;
      $data['brand_name'] = $this->Sites->get_brand_name($site_id);

      echo $this->load->view('message_form', $data, TRUE);

   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Processes the form data from the message form;
    *
    */
   function _message($site_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;
      $values['DateSent'] = date("Y-m-d H:i:s");
      
      $this->Messages->insert_message($values);
      
      $values['brand_name'] = $this->Sites->get_brand_name($site_id);
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

}

/* End of file tags.php */
/* Location: ./system/modules/stores/controllers/tags.php */