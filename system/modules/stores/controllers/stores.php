<?php

class Stores extends Controller {

   function Stores()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'stores'));
      $this->load->helper(array('url', 'menu'));

      // this module is set up to write to the store tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('write', TRUE);
      $this->hcg_db = $this->load->database('hcg_write', TRUE);
   }
	
   // --------------------------------------------------------------------

   /**
    * Searches for a store listing
    *
    */
   function index($post = 'form') 
   {
      $this->load->helper(array('form', 'text'));
      $this->load->model('Stores');
      $this->load->model('Messages');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['StoreName'] = 'trim';
      $rules['City'] = 'trim';
      $rules['State'] = 'trim';
      $rules['Zip'] = 'trim';
      $rules['Phone'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['StoreName'] = 'Store Name';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Zip'] = 'Zip/Postal Code';
      $fields['Phone'] = 'Phone';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE && $post == 'form')
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('stores');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Stores');
         $data['submenu'] = get_submenu('Search');

         $this->load->vars($data);
   	
         return $this->load->view('stores/search', NULL, TRUE);
      }
      else
      {
            return $this->_list($post);
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the search form and lists results
    *
    */
   function _list($post)
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      if ($post == 'form')
      {
         $fields = $this->validation->_fields;
      
         foreach ($fields AS $key => $value)
            $values[$key] = $this->input->post($key);
      }
      else
      {
         $query = unserialize($this->session->userdata('store_query'));

         foreach ($query AS $key => $value)
            $values[$key] = $value;
      }
      
      // store the values in the session for use later
      $query = serialize($values);
      $this->session->set_userdata('store_query', $query);
      
      $logic = 'AND';
      $first = TRUE;

      $sql = 'SELECT * FROM stores';
      foreach ($values AS $key => $value)
      {
         if ($value != '')
         {
            $sql .= ($first == TRUE) ? ' WHERE ' : ' '.$logic.' ';
            $sql .= $key.' LIKE "%'.addslashes($value).'%"';
            $first = FALSE;
         }
      }
      $sql .= ' ORDER BY StoreName, State, City';
      
//      echo $sql;
      
      $query = $this->cb_db->query($sql);
      $stores = $query->result_array();
      
      // get message count for each store
      // I have to do this separately because the messages are in
      // the live database and the stores are in the dev database.
      for ($i=0, $cnt=count($stores); $i<$cnt; $i++)
      {
         $stores[$i]['MessageCount'] = $this->Messages->get_store_open_message_count($stores[$i]['StoreID']);
      }
      
//      echo "<pre>"; print_r($stores); echo "</pre>";
      
      $admin['store_found'] = FALSE;
      if ($query->num_rows() > 0)
         $admin['store_found'] = TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('stores');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Stores');
      $data['submenu'] = get_submenu('Search');
      $data['admin'] = $admin;
      $data['stores'] = $stores;

      $this->load->vars($data);
      
      return $this->load->view('stores/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Adds a new store listing
    *
    */
   function add($this_action) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Stores');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['StoreName'] = 'trim';
      $rules['Address1'] = 'trim';
      $rules['Address2'] = 'trim';
      $rules['City'] = 'trim';
      $rules['State'] = 'trim';
      $rules['Zip'] = 'trim';
      $rules['Country'] = 'trim';
      $rules['Phone'] = 'trim';
      $rules['Fax'] = 'trim';
      $rules['status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['StoreName'] = 'Store Name';
      $fields['Address1'] = 'Address 1';
      $fields['Address2'] = 'Address 2';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Zip'] = 'Zip/Postal Code';
      $fields['Country'] = 'Country';
      $fields['Phone'] = 'Store Phone';
      $fields['Fax'] = 'Store Fax';
      $fields['status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults['status'] = 'active';

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('stores');

         // get data for the various pulldown lists
         $data['statuses'] = array('active' => 'active', 
                                   'inactive' => 'inactive',
                                   'pending' => 'pending');
         $data['countries'] = array('' => '', 
                                   'US' => 'US',
                                   'Canada' => 'Canada');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Stores');
         $data['submenu'] = get_submenu('Search');
         $data['admin'] = $admin; // errors and messages

         $this->load->vars($data);
   	
         return $this->load->view('stores/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add();
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add store form
    *
    */
   function _add()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['StoreName'] = ascii_to_entities($values['StoreName']);
      $values['Address1'] = ascii_to_entities($values['Address1']);
      $values['Address2'] = ascii_to_entities($values['Address2']);
      $values['City'] = ascii_to_entities($values['City']);
      
      // auto-insert the Source fields
      $values['SourceStoreName'] = $values['StoreName'];
      $values['SourceAddress1'] = $values['Address1'];
      $values['SourceAddress2'] = $values['Address2'];

      $this->cb_db->insert('stores', $values);
      $this->hcg_db->insert('stores', $values);

      $store_id = $this->cb_db->insert_id();
      
      $this->auditor->audit_insert('stores', '', $values);

      $this->session->set_userdata('admin_message', $values['StoreName'].' has been added. You may now add additional information.');

      $last_action = $this->session->userdata('last_action') + 1;

      redirect('stores/edit/'.$store_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Updates a store listing
    *
    */
   function edit($store_id, $this_action) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Stores');
      $this->load->model('Messages');
      $this->load->model('Api');
      $this->load->library(array('validation', 'auditor'));
      
      $old_values = $this->Stores->get_store_data($store_id);
      
      $rules['StoreName'] = 'trim';
      $rules['Address1'] = 'trim';
      $rules['Address2'] = 'trim';
      $rules['City'] = 'trim';
      $rules['State'] = 'trim';
      $rules['Zip'] = 'trim';
      $rules['Phone'] = 'trim';
      $rules['Fax'] = 'trim';
      $rules['ContactEmail'] = 'trim';
      $rules['Website'] = 'trim';
      $rules['Brands'] = 'trim';
      $rules['NotBrands'] = 'trim';
      $rules['ContactName'] = 'trim';
      $rules['Source'] = 'trim';
      $rules['SourceStoreName'] = 'trim';
      $rules['SourceAddress1'] = 'trim';
      $rules['SourceAddress2'] = 'trim';
      $rules['Country'] = 'trim';
      $rules['Notes'] = 'trim';
      $rules['Etailer'] = 'trim';
      $rules['Retailer'] = 'trim';
      $rules['ContactPhone'] = 'trim';
      $rules['latitude'] = 'trim';
      $rules['longitude'] = 'trim';
      $rules['SalesRegion'] = 'trim';
      $rules['status'] = 'trim';
      
      $this->validation->set_rules($rules);

      $fields['StoreName'] = 'Store Name';
      $fields['Address1'] = 'Address 1';
      $fields['Address2'] = 'Address 2';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Zip'] = 'Zip/Postal Code';
      $fields['Phone'] = 'Store Phone';
      $fields['Fax'] = 'Store Fax';
      $fields['ContactEmail'] = 'Contact Email';
      $fields['Website'] = 'Website';
      $fields['Brands'] = 'Brands';
      $fields['NotBrands'] = 'Not Brands';
      $fields['ContactName'] = 'Contact Name';
      $fields['Source'] = 'Source';
      $fields['SourceStoreName'] = 'Source Store Name';
      $fields['SourceAddress1'] = 'Source Address 1';
      $fields['SourceAddress2'] = 'Source Address 2';
      $fields['Country'] = 'Country';
      $fields['Notes'] = 'Notes';
      $fields['Etailer'] = 'Etailer';
      $fields['Retailer'] = 'Retailer';
      $fields['ContactPhone'] = 'Contact Phone';
      $fields['latitude'] = 'Latitude';
      $fields['longitude'] = 'Longitude';
      $fields['SalesRegion'] = 'Sales Region';
      $fields['status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      $defaults['StoreName'] = entities_to_ascii($defaults['StoreName']);
      $defaults['Address1'] = entities_to_ascii($defaults['Address1']);
      $defaults['Address2'] = entities_to_ascii($defaults['Address2']);
      $defaults['City'] = entities_to_ascii($defaults['City']);
      $defaults['SourceStoreName'] = entities_to_ascii($defaults['SourceStoreName']);
      $defaults['SourceAddress1'] = entities_to_ascii($defaults['SourceAddress1']);
      $defaults['SourceAddress2'] = entities_to_ascii($defaults['SourceAddress2']);
      $defaults['ContactName'] = entities_to_ascii($defaults['ContactName']);
      $defaults['Notes'] = entities_to_ascii($defaults['Notes']);

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('stores');
         
         $map['Name'] = $defaults['StoreName'];
         $map['Address1'] = $defaults['Address1'];
         $map['Address2'] = $defaults['Address2'];
         $map['City'] = $defaults['City'];
         $map['State'] = $defaults['State'];
         $map['Zip'] = $defaults['Zip'];
         $map['Phone'] = $defaults['Phone'];

         // get data for the various pulldown lists
         $data['statuses'] = array('active' => 'active', 
                                   'inactive' => 'inactive',
                                   'pending' => 'pending');
         $data['countries'] = array('' => '', 
                                   'US' => 'US',
                                   'Canada' => 'Canada');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Stores');
         $data['submenu'] = get_submenu('Search');
         $data['admin'] = $admin; // errors and messages
         $data['store_id'] = $store_id;
         $data['map_api_key'] = $this->Api->get_map_key();
         $data['store'] = $map;
         $data['messages'] = $this->Messages->get_open_messages_by_store_id($store_id);
         
         $this->load->vars($data);
   	
         return $this->load->view('stores/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($store_id, $old_values);
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the edit store form
    *
    */
   function _edit($store_id, $old_values)
   {
      if ($store_id == 0)
      {
         show_error('_edit store requires that a store ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['StoreName'] = ascii_to_entities($values['StoreName']);
      $values['Address1'] = ascii_to_entities($values['Address1']);
      $values['Address2'] = ascii_to_entities($values['Address2']);
      $values['City'] = ascii_to_entities($values['City']);
      $values['SourceStoreName'] = ascii_to_entities($values['SourceStoreName']);
      $values['SourceAddress1'] = ascii_to_entities($values['SourceAddress1']);
      $values['SourceAddress2'] = ascii_to_entities($values['SourceAddress2']);
      $values['ContactName'] = ascii_to_entities($values['ContactName']);
      $values['Notes'] = ascii_to_entities($values['Notes']);
      
      // Automatically fill in the Source info if it is left blank
      $values['SourceStoreName'] = ($values['SourceStoreName'] == '') ? $values['StoreName'] : $values['SourceStoreName'];
      $values['SourceAddress1'] = ($values['SourceAddress1'] == '') ? $values['Address1'] : $values['SourceAddress1'];
      $values['SourceAddress2'] = ($values['SourceAddress2'] == '') ? $values['Address2'] : $values['SourceAddress2'];
      
      $tmp = $this->cb_db->where('StoreID', $store_id);
      $this->cb_db->update('stores', $values);
      $this->hcg_db->where('StoreID', $store_id);
      $this->hcg_db->update('stores', $values);

      $this->auditor->audit_update('stores', $tmp->ar_where, $old_values, $values);

      $this->session->set_userdata('admin_message', $values['StoreName'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('stores/edit/'.$store_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a store from the database
    *
    */
   function delete($store_id, $this_action)
   {
      $this->load->model('Stores');
      $this->load->library('auditor');
      
      $old_values = $this->Stores->get_store_data($store_id);
      
      $this->Stores->delete_store($store_id);

      $address = ($old_values['Address2'] != '') ? $old_values['Address1'].', '.$old_values['Address2'] : $old_values['Address1'];
      
      $this->session->set_userdata('admin_message', 'The <b>'.$old_values['StoreName'].'</b> in <b>'.$old_values['City'].', '.$old_values['State'].'</b> has been deleted.');

      redirect('stores/index/delete');
   }
   
   // --------------------------------------------------------------------

   /**
    * Attempts to scrape the latitude and longitude of an address using
    * the Maporama website. Should access via AJAX.
    *
    */
   function get_lat_long($address)
   {
      // using PEAR HTTP_Request
      require 'HTTP/Request.php';
   
      $request = "http://www.maporama.com/share/map.asp".
                 "?COUNTRYCODE=".$address['Country'].
                 "&_XgoGCAddress=".urlencode($address['Address1']).
                 "&Zip=".$address['Zip'].
                 "&State=".$address['State'].
                 "&_XgoGCTownName=".urlencode($address['City']);
//      echo $request;
   
      $r = new HTTP_Request($request);

//      if ($this->config->item('proxy') != "")
//      {
//         $r->setProxy($this->config->item('proxy'), $this->config->item('proxy_port'));
//      }

      $response = $r->sendRequest();

      if (!PEAR::isError($response))
      {
         $page = $r->getResponseBody();
      }
      else
      {
         return "<br>Error Message: ".$response->getMessage();
      }
   
      // scrape the resulting page for latitude and longitude information
   
      $pos = strpos($page, "Lat-Long:");
      $pos2 = strpos($page, "SearchMapFontText\">", $pos) + 19;
      $pos3 = strpos($page, "SearchMapFontText\">", $pos2) + 19;
      $pos4 = strpos($page, "</td>", $pos3);
   
      $lat_long = substr($page, $pos3, ($pos4-$pos3));
   
      $results = explode(",", $lat_long);
   
      $location['latitude'] = trim($results[0]);
      $location['longitude'] = trim($results[1]);
   
      return $location;
   }


} // END Class

?>