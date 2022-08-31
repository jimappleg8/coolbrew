<?php

class Messages extends Controller {

   function Messages()
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
      $this->live_db = $this->load->database('production', TRUE);
   }
	
   // --------------------------------------------------------------------

   /**
    * Lists all store messages in a paginated view
    *
    */
   function index($offset = 0) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $this->load->helper(array('form', 'text'));    
      $this->load->library('pagination');
      $this->load->model('Messages');
      $this->load->library('validation');
      
      $filter = $this->session->userdata('message_filter');

      $rules['Filter'] = 'trim';
      $this->validation->set_rules($rules);

      $fields['Filter'] = 'Filter';
      $this->validation->set_fields($fields);
      
      $defaults['Filter'] = $filter;
      $this->validation->set_defaults($defaults);

      $messages = $this->Messages->get_open_messages($filter, $offset);
      $message_count = $this->Messages->get_open_message_count($filter);
      
      // pagination config
      $config['base_url'] = site_url('messages/index/');
      $config['total_rows'] = $message_count;
      $config['per_page'] = 20;
      
      $this->pagination->initialize($config);
      

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('stores');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Stores');
      $data['submenu'] = get_submenu('Messages');
      $data['admin'] = $admin;
      $data['messages'] = $messages;
      $data['message_count'] = $message_count;
      $data['offset'] = $offset;
      $data['pagination'] = $this->pagination->create_links();
      
      $this->load->vars($data);
   	
      return $this->load->view('messages/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Sets a filter string to use when displaying list results
    *
    */
   function set_filter($offset = 0) 
   {
      $this->load->library('validation');
      
      $rules['Filter'] = 'trim';
      $this->validation->set_rules($rules);

      $fields['Filter'] = 'Filter';
      $this->validation->set_fields($fields);
      
      $defaults['Filter'] = $this->session->userdata('message_filter');
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == TRUE)
      {
         $filter = $this->input->post('Filter');
         $this->session->set_userdata('message_filter', $filter);
      }
      
      $last_action = $this->session->userdata('last_action') + 1;
      redirect("messages/index/".$offset.'/'.$last_action);
   }

   // --------------------------------------------------------------------

   /**
    * Displays a message
    *
    */
   function detail($message_id, $offset, $this_action) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
         
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Products');
      $this->load->model('Stores_product');
      $this->load->model('Messages');
      $this->load->library('validation');

      $message = $this->Messages->get_message_data($message_id);
      
      if ($message['Status'] = 'unread')
      {
         $values['Status'] = 'active';
         $values['LastUpdated'] = date('Y-m-d H:i:s');
         $this->Messages->update_message($message['ID'], $values);
      }
      
      $others = $this->Messages->get_open_messages_by_store_id($message['StoreID'], $message['ID']);
      
      $rules['Status'] = 'trim';
      $rules['StatusNotes'] = 'trim';
      $rules['SetOthers'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Status'] = 'Status';
      $fields['StatusNotes'] = 'Status';
      $fields['SetOthers'] = 'Set Others';

      $this->validation->set_fields($fields);
      
      $defaults['Status'] = $message['Status'];
      $defaults['StatusNotes'] = $message['StatusNotes'];

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('stores');
         
         $data['statuses'] = array('active' => 'active',
                                   'in-progress' => 'in-progress',
                                   'closed' => 'closed');

         $upc = $message['ProductID'];
         // strip out any extra 0s at front of field
         if (strlen($upc) > 11)
         {
            $upc = substr($upc, strlen($upc)-11, 11);
         }
         elseif (strlen($upc) < 11)
         {
            $upc = str_pad($upc, 11, '0', STR_PAD_LEFT);
         }
         $product = $this->Products->get_product_data_by_upc($upc);
         if (isset($product['ProductID']))
         {
            $data['carried'] = $this->Stores_product->get_carried_status($message['StoreID'], $product['ProductID']);
         }
         else
         {
            $data['carried'] = 'not-found';
         }

         $data['last_action'] = $this->session->userdata('message_last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Stores');
         $data['submenu'] = get_submenu('Messages');
         $data['admin'] = $admin;
         $data['message_id'] = $message_id;
         $data['offset'] = $offset;
         $data['message'] = $message;
         $data['others'] = $others;
      
         $this->load->vars($data);
   	
         return $this->load->view('messages/detail', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('message_last_action'))
         {
            $this->session->set_userdata('message_last_action', $this_action);
            $this->_detail($message, $offset, $others);
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the message detail form
    *
    */
   function _detail($message, $offset, $others)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $set_others = 0;
      if (isset($values['SetOthers']))
      {
         $set_others = $values['SetOthers'];
         unset($values['SetOthers']);
      }
      
      $values['LastUpdated'] = date('Y-m-d H:i:s');
      
      $this->Messages->update_message($message['ID'], $values);
      
      $other_txt = ' has ';
      if ($set_others == 1)
      {
         foreach ($others AS $other)
         {
            $this->Messages->update_message($other['ID'], $values);
         }
         $other_txt = ' and related records have ';
      }

      $this->session->set_userdata('admin_message', 'Message '.$message['ID'] . $other_txt . 'been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect("messages/index/".$offset.'/'.$last_action);
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates, deletes or updates a stores_product link
    *
    */
   function edit_link($message_id, $offset, $action)
   {
      $this->load->model('Messages');
      $this->load->model('Products');
      $this->load->model('Stores_product');
      
      $message = $this->Messages->get_message_data($message_id);
      
      $upc = $message['ProductID'];
      // strip out any extra 0s at front of field
      if (strlen($upc) > 11)
      {
         $upc = substr($upc, strlen($upc)-11, 11);
      }
      elseif (strlen($upc) < 11)
      {
         $upc = str_pad($upc, 11, '0', STR_PAD_LEFT);
      }
      $product = $this->Products->get_product_data_by_upc($upc);

      switch ($action)
      {
         case 'carried':
            $values = array();
            $values['StoreID'] = $message['StoreID'];
            $values['ProductID'] = $product['ProductID'];
            $values['Carried'] = 1;
            $values['Source'] = 'consumer-message';
            $this->Stores_product->insert_store_product($values);
            $notice = 'Link set to CARRIED.';
            break;
         case 'not-carried':
            $notice = 'Link set to NOT CARRIED.';
            $values = array();
            $values['StoreID'] = $message['StoreID'];
            $values['ProductID'] = $product['ProductID'];
            $values['Carried'] = 0;
            $values['Source'] = 'consumer-message';
            $this->Stores_product->insert_store_product($values);
            break;
         case 'remove':
            $this->Stores_product->delete_store_product($message['StoreID'], $product['ProductID']);
            $notice = 'Link to product was removed.';
            break;
         default:
            $notice = 'No action was specified.';
            break;
      }
   
      $this->session->set_userdata('admin_message', $notice);
   
      $last_action = $this->session->userdata('message_last_action') + 1;
      redirect("messages/detail/".$message_id.'/'.$offset.'/'.$last_action);
   }

} // END Class

?>