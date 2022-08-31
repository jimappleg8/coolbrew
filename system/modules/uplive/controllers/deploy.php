<?php

class Deploy extends Controller {

   // --------------------------------------------------------------------

   function Deploy()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'uplive'));
      $this->load->helper(array('url', 'menu'));
   }
	
   // --------------------------------------------------------------------

   function index()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $this->collector->append_css_file('admin');
         
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Uplive');
      $data['submenu'] = get_submenu('Deploy');
      $data['admin'] = $admin; // errors and messages

      $this->load->vars($data);
   	
      return $this->load->view('deploy/list', NULL, TRUE);
   }
}
?>