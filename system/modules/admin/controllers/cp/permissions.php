<?php

class Permissions extends Controller {

   function Permissions()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper('url');
   }

   // --------------------------------------------------------------------

   /**
    * Lists the sites this user has full access to
    *
    */
   function index($username)
   {
      $this->administrator->check_login();
      
      $this->load->helper(array('form', 'text'));    
      $this->load->model('People');
      $this->load->model('Permissions');
      $this->load->library('validation');
      
      $user = $this->People->get_user_data($username);

      // validation needed to build the Add Permissions form
      $rules['NewSite'] = 'trim';
      $this->validation->set_rules($rules);
      $fields['NewSite'] = 'New Site';
      $this->validation->set_fields($fields);
      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $user = $this->People->get_user_data($username);
      $usercode = $this->People->get_usercode($username);
      $user['Group'] = $this->People->get_user_group($usercode);

      $sites = $this->Permissions->get_sites($username);
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin']['sites_exist'] = (count($sites) > 0) ? TRUE : FALSE;
      $data['admin']['group'] = $this->session->userdata('group');
      $data['domains'] = $this->Permissions->get_site_domains($username);
      $data['username'] = $username;
      $data['user'] = $user;
      $data['sites'] = $sites;
      $data['form_open'] = TRUE;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/permissions/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an permissions record - called via Ajax
    *
    */
   function delete($username, $site_id, $form_open)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->helper(array('form', 'text'));
      $this->load->model('People');
      $this->load->model('Permissions');
      $this->load->library('validation');
      
      $user = $this->People->get_user_data($username);

      // validation needed to build the Add Permissions form
      $rules['NewSite'] = 'trim';
      $this->validation->set_rules($rules);
      $fields['NewSite'] = 'New Site';
      $this->validation->set_fields($fields);
      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $this->Permissions->delete_permissions($username, $site_id);
      
      $user = $this->People->get_user_data($username);
      $usercode = $this->People->get_usercode($username);
      $user['Group'] = $this->People->get_user_group($usercode);

      $sites = $this->Permissions->get_sites($username);
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin']['sites_exist'] = (count($sites) > 0) ? TRUE : FALSE;
      $data['admin']['group'] = $this->session->userdata('group');
      $data['domains'] = $this->Permissions->get_site_domains($username);
      $data['username'] = $username;
      $data['user'] = $user;
      $data['sites'] = $sites;
      $data['form_open'] = ($form_open == 'block') ? TRUE : FALSE;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/permissions/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Adds an permissions record - called via Ajax
    */
   function add($username) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('projects_message');
      if ($this->session->userdata('people_message') != '')
         $this->session->set_userdata('people_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('People');
      $this->load->model('Permissions');
      $this->load->library('validation');
      
      $user = $this->People->get_user_data($username);

      $rules['NewSite'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['NewSite'] = 'New Site';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_add($username);
      }

      $usercode = $this->People->get_usercode($username);
      $user['Group'] = $this->People->get_user_group($usercode);

      $sites = $this->Permissions->get_sites($username);

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin'] = $admin;
      $data['admin']['sites_exist'] = (count($sites) > 0) ? TRUE : FALSE;
      $data['admin']['group'] = $this->session->userdata('group');
      $data['domains'] = $this->Permissions->get_site_domains($username);
      $data['username'] = $username;
      $data['user'] = $user;
      $data['sites'] = $sites;
      $data['form_open'] = TRUE;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/permissions/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add permissions form
    */
   function _add($username)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $this->Permissions->add_permissions($username, $values['NewSite']);

      return TRUE;
   }

}
?>
