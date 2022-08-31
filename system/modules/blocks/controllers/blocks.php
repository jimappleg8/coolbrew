<?php

class Blocks extends Controller {

   function Blocks()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'blocks'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of the list items
    *
    */
   function index($site_id)
   {
      $admin['message'] = $this->session->userdata('block_message');
      if ($this->session->userdata('block_message') != '')
         $this->session->set_userdata('block_message', '');

      $admin['error_msg'] = $this->session->userdata('block_error');
      if ($this->session->userdata('block_error') != '')
         $this->session->set_userdata('block_error', '');

      $this->load->helper('text');
      $this->load->model('Sites');
      $this->load->model('Blocks');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $blocks = $this->Blocks->get_blocks($site_id);

      $admin['block_exists'] = (count($blocks) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('blocks');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Blocks');
      $data['submenu'] = get_submenu($site_id, 'Blocks');
      $data['admin'] = $admin;
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['blocks'] = $blocks;
      
      $this->load->vars($data);
   	
      return $this->load->view('blocks/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an block
    *
    */
   function delete($block_id, $this_action) 
   {
      $this->load->model('Blocks');
      
      $block = $this->Blocks->delete_block($block_id);
      
      // display a message showing settings were updated
      $message = 'This block "'.$block['Name'].'" has been deleted.';
      $this->session->set_userdata('block_message', $message);

      redirect('blocks/index/'.$site_id);
   }

   // --------------------------------------------------------------------

   /**
    * Adds a block record
    *
    */
   function add($site_id, $this_action) 
   {
      $this->load->helper(array('fckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Blocks');
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['Name'] = 'trim|required';
      $rules['Block'] = 'trim|required';
      $rules['Language'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Name'] = 'Name';
      $fields['Block'] = 'Block';
      $fields['Language'] = 'Language';

      $this->validation->set_fields($fields);

      $defaults['Language'] = 'en_US';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('blocks');
         
         $data['languages'] = array(
            'en_US' => 'en_US',
            'en_CA' => 'en_CA',
            'fr_CA' => 'fr_CA',
         );

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Blocks');
         $data['submenu'] = get_submenu($site_id, 'Blocks');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('blocks/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add block form
    *
    */
   function _add($site_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;      

      $values['Name'] = ascii_to_entities($values['Name']);
      $values['Block'] = ascii_to_entities($values['Block']);

      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $block_id = $this->Blocks->insert_block($values);
      
      // display a message showing settings were updated
      $message = 'The block "'.$values['Name'].'" has been added.';
      $this->session->set_userdata('block_message', $message);

      redirect('blocks/index/'.$site_id);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a block record
    *
    */
   function edit($site_id, $block_id, $this_action) 
   {
      $admin['message'] = $this->session->userdata('block_message');
      if ($this->session->userdata('block_message') != '')
         $this->session->set_userdata('block_message', '');

      $admin['error_msg'] = $this->session->userdata('block_error');
      if ($this->session->userdata('block_error') != '')
         $this->session->set_userdata('block_error', '');

      $this->load->helper(array('fckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Blocks');

      $site = $this->Sites->get_site_data($site_id);

      $block = $this->Blocks->get_block_data($block_id);

      $rules['Name'] = 'trim|required';
      $rules['Block'] = 'trim|required';
      $rules['Language'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Name'] = 'Name';
      $fields['Block'] = 'Block';
      $fields['Language'] = 'Language';

      $this->validation->set_fields($fields);

      $defaults = $block;
      $defaults['Name'] = entities_to_ascii($defaults['Name']);
      $defaults['Block'] = entities_to_ascii($defaults['Block']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('lists');

         $data['languages'] = array(
            'en_US' => 'en_US',
            'en_CA' => 'en_CA',
            'fr_CA' => 'fr_CA',
         );

         $data['last_action'] = $this->session->userdata('last_action') + 1;      
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Blocks');
         $data['submenu'] = get_submenu($site_id, 'Blocks');
         $data['admin'] = $admin;
         $data['block_id'] = $block_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('blocks/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $block_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a list item record
    *
    */
   function _edit($site_id, $block_id)
   {
      if ($block_id == 0)
      {
         show_error('_edit block requires that a block ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['Name'] = ascii_to_entities($values['Name']);
      $values['Block'] = ascii_to_entities($values['Block']);

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Blocks->update_block($block_id, $values);
      
      // display a message showing settings were updated
      $message = 'This block has been updated.';
      $this->session->set_userdata('block_message', $message);

      $last_action = $this->session->userdata('last_action');
      redirect('blocks/edit/'.$site_id.'/'.$block_id.'/'.$last_action);
   }


}
?>