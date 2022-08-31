<?php

class Modules extends Controller {

   var $aco = array();

   function Modules()
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
    * Interface for activating/deactivating modules
    *
    */
   function index() 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['tabs'] = $this->administrator->get_main_tabs('Modules');
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/modules/list', NULL, TRUE);
   }


}
?>