<?php

class Links extends Controller {

   var $aco = array();

   function Links()
   {
      parent::Controller();
      $this->load->library('session');

      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->load->library('tacl', $options);

      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper('url');
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of top-level links
    *
    */
   function index()
   {
      $this->administrator->check_login();
      
      $admin['error_msg'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');
   
      $admin['message'] = $this->session->userdata('link_message');
      if ($this->session->userdata('link_message') != '')
         $this->session->set_userdata('link_message', '');

      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Links');
      
      $links = $this->Links->get_links('adm');
      
      $admin['link_exists'] = (count($links) == 0) ? FALSE : TRUE;
      
      $data['links'] = $links;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');
      
      $data['tabs'] = $this->administrator->get_main_tabs('Links');
      $data['admin'] = $admin;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/links/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Adds a link to the main admin site
    *
    */
   function add($this_action) 
   {
      $this->administrator->check_login();
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('Links');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $rules['Title'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['URL'] = 'trim|required';
      $rules['OpenWhere'] = 'trim';
      $rules['Dashboard'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Title'] = 'Title';
      $fields['Description'] = 'Description';
      $fields['URL'] = 'URL';
      $fields['OpenWhere'] = 'Open Where';
      $fields['Dashboard'] = 'Dashboard';
      $fields['AdminOnly'] = 'Admin Only';

      $this->validation->set_fields($fields);

      $defaults['Dashboard'] = 0;
      $defaults['OpenWhere'] = 'same';
      $defaults['AdminOnly'] = 0;

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Links');
         $data['modules'] = $this->Sites->get_modules_list();

         $this->load->vars($data);
         return $this->load->view('cp/links/add', NULL, TRUE);
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
    * Processes the add link form
    *
    */
   function _add()
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = 'adm';
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->Links->insert_link($values);

      $this->session->set_userdata('link_message', 'The new link has been added.');

      redirect("cp/links/index");
   }
   
   // --------------------------------------------------------------------

   /**
    * Edits the specified link
    *
    */
   function edit($link_id, $this_action) 
   {
      $this->administrator->check_login();

      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Links');
      
      $link = $this->Links->get_link_data($link_id);
      
      $site_id = 'adm';
      
      $site = $this->Sites->get_site_data($site_id);
      
      $rules['Title'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['URL'] = 'trim|required';
      $rules['OpenWhere'] = 'trim';
      $rules['Dashboard'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Title'] = 'Title';
      $fields['Description'] = 'Description';
      $fields['URL'] = 'URL';
      $fields['OpenWhere'] = 'Open Where';
      $fields['Dashboard'] = 'Dashboard';
      $fields['AdminOnly'] = 'Admin Only';

      $this->validation->set_fields($fields);

      $defaults = $link;
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Links');
         $data['link_id'] = $link_id;
         $data['site'] = $site;
         $data['modules'] = $this->Sites->get_modules_list();

         $this->load->vars($data);
         return $this->load->view('cp/links/edit', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($link_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit site_link form
    *
    */
   function _edit($link_id)
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = 'adm';
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->Links->update_link($link_id, $values);

      $this->session->set_userdata('link_message', 'The link data has been updated.');

      redirect('cp/links/index');
   }   

   // --------------------------------------------------------------------

   /**
    * Deletes the specified link
    *
    */
   function delete($link_id) 
   {
      $this->administrator->check_login();
      
      $this->load->model('Links');

      $this->Links->delete_link($link_id);

      $this->session->set_userdata('link_message', 'The link record has been deleted.');

      redirect('cp/links/index');
   }

}
?>