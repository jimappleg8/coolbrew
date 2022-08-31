<?php

class Dashboards extends Controller {

   function Dashboards()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates the project-level dashboard
    *
    */
   function index($project_id)
   {
      $admin['error_msg'] = $this->session->userdata('projects_error');
      if ($this->session->userdata('projects_error') != '')
         $this->session->set_userdata('projects_error', '');
   
      $this->load->database('write');

      // get project data
      $sql = "SELECT * FROM projects " .
             "WHERE ID = ".$project_id;
      
      $query = $this->db->query($sql);
      $project = $query->row_array();
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Projects');
      $data['submenu'] = get_submenu('Projects');
      $data['admin'] = $admin;
      $data['project_id'] = $project_id;
      $data['project'] = $project;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/dashboard/list', NULL, TRUE);

   }
   
}
?>