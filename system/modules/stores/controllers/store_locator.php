<?php

class Store_locator extends Controller {

   function Store_locator()
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
   function index($site_id) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->model('Sites');

      $site = $this->Sites->get_site_data($site_id);

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('stores');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Stores');
      $data['submenu'] = get_submenu($site_id, 'Stores');
      $data['site_id'] = $site_id;
      $data['site'] = $site;

      $this->load->vars($data);

      return $this->load->view('sites/store_locator', NULL, TRUE);

   }

   // --------------------------------------------------------------------
   

} // END Class

?>