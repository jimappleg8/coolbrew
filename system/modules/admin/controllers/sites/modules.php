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
      
      include APPPATH().'/config/acl_admin.php';
      $this->aco['adm'] = $acl_admin;

      include APPPATH().'/config/acl_sites.php';
      $this->aco['sites'] = $acl_sites;

   }
   
   // --------------------------------------------------------------------

   /**
    * Interface for activating/deactivating modules
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $admin['error_msg'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');
   
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $modules = $this->Sites->get_site_modules($site_id);
      
      $admin['module_exists'] = (count($modules) == 0) ? FALSE : TRUE;
      
      $data['modules'] = $modules;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Modules');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;

      $this->load->vars($data);
   	
      return $this->load->view('sites/modules/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Toggles a module to activate it or deactivate it
    *
    */
   function toggle($site_id, $module_id, $action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->database('read');
      
      switch (strtoupper($action))
      {
         case 'ON':
            $values['ModuleID'] = $module_id;
            $values['SiteID'] = $site_id;
            $this->db->insert('adm_site_module', $values);
            break;

         case 'OFF';
            $this->db->where('ModuleID', $module_id);
            $this->db->where('SiteID', $site_id);
            $this->db->delete('adm_site_module');
            break;
      }
      
      redirect('sites/modules/index/'.$site_id.'/');
   }


}
?>