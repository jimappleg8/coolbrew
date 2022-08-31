<?php

class Contactus_spam extends Controller {

   function Contactus_spam()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'mailform'));
      $this->load->helper(array('url', 'menu'));

      $this->cb_db = $this->load->database('write', TRUE);
      $this->hcg_db = $this->load->database('hcg_write', TRUE);
      $this->live_db = $this->load->database('production', TRUE);
   }
	
   // --------------------------------------------------------------------

   /**
    * Lists all store messages in a paginated view
    *
    */
   function index($site_id, $offset = 0) 
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $this->load->helper(array('form', 'text'));    
      $this->load->library('pagination');
      $this->load->model('Contactus');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $filter = $this->session->userdata('message_filter');

      $rules['Filter'] = 'trim';
      $this->validation->set_rules($rules);

      $fields['Filter'] = 'Filter';
      $this->validation->set_fields($fields);
      
      $defaults['Filter'] = $filter;
      $this->validation->set_defaults($defaults);

      $messages = $this->Contactus->get_spam_messages($site_id, $filter, $offset);
      $spam_count = $this->Contactus->get_spam_count($site_id, $filter);
      $message_count = $this->Contactus->get_message_count($site_id, $filter);
      
      // pagination config
      $config['base_url'] = site_url('sites/contactus_spam/index/'.$site_id.'/');
      $config['total_rows'] = $spam_count;
      $config['per_page'] = 20;
      $config['uri_segment'] = 5;
      
      $this->pagination->initialize($config);
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('mailform');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Mail');
      $data['submenu'] = get_submenu($site_id, 'Contact Us');
      $data['admin'] = $admin;
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['messages'] = $messages;
      $data['message_count'] = $message_count;
      $data['spam_count'] = $spam_count;
      $data['offset'] = $offset;
      $data['pagination'] = $this->pagination->create_links();
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/contactus_spam/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Sets a filter string to use when displaying list results
    *
    */
   function set_filter($site_id, $offset = 0) 
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
      
      redirect("sites/contactus_spam/index/".$site_id.'/'.$offset);
   }

   // --------------------------------------------------------------------

   /**
    * Displays a message
    *
    */
   function detail($site_id, $message_id, $offset, $this_action) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
         
      $this->load->helper(array('form', 'text', 'typography'));    
      $this->load->model('Contactus');
      $this->load->model('Sites');
      $this->load->library('validation');

      $site = $this->Sites->get_site_data($site_id);
      $filter = $this->session->userdata('message_filter');
      $message = $this->Contactus->get_message_data($message_id);
      
      if ($message['status'] == 'unread')
      {
         $values['status'] = 'read';
         $message['status'] = 'read';
         $values['lastupdated'] = date('Y-m-d H:i:s');
         $this->Contactus->update_message($message['id'], $values);
      }
      
      $rules['status'] = 'trim';
      $rules['spam'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['status'] = 'Status';
      $fields['spam'] = 'Spam';

      $this->validation->set_fields($fields);
      
      $defaults['status'] = $message['status'];
      $defaults['spam'] = $message['spam'];

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('mailform');
         
         $data['statuses'] = array('read' => 'read',
                                   'unread' => 'unread');
         $data['spams'] = array('0' => 'No',
                                '1' => 'Yes');

         $data['last_action'] = $this->session->userdata('message_last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Mail');
         $data['submenu'] = get_submenu($site_id, 'Contact Us');
         $data['admin'] = $admin;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['message_id'] = $message_id;
         $data['spam_count'] = $this->Contactus->get_spam_count($site_id, $filter);
         $data['message_count'] = $this->Contactus->get_message_count($site_id, $filter);
         $data['offset'] = $offset;
         $data['message'] = $message;
      
         $this->load->vars($data);
   	
         return $this->load->view('sites/contactus_spam/detail', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('message_last_action'))
         {
            $this->session->set_userdata('message_last_action', $this_action);
            $this->_detail($site_id, $message, $offset);
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the message detail form
    *
    */
   function _detail($site_id, $message, $offset)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['lastupdated'] = date('Y-m-d H:i:s');
      
      $this->Contactus->update_message($message['id'], $values);
      
      $this->session->set_userdata('admin_message', 'Message '.$message['id'].' has been updated.');

//      $last_action = $this->session->userdata('last_action') + 1;
      redirect("sites/contactus/index/".$site_id.'/'.$offset);
   }


} // END Class

/* End of file contactus_spam.php */
/* Location: ./system/modules/mailform/controllers/contactus_spam.php */