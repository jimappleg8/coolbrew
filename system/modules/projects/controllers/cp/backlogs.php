<?php

class Backlogs extends Controller {

   // --------------------------------------------------------------------

   function Backlogs()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of the project backlog
    *
    */
   function index()
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('project_message');
      if ($this->session->userdata('project_message') != '')
         $this->session->set_userdata('project_message', '');

      $admin['error_msg'] = $this->session->userdata('projects_error');
      if ($this->session->userdata('projects_error') != '')
         $this->session->set_userdata('projects_error', '');
         
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Projects');
      $this->load->model('Stories');
      $this->load->model('Resources');
      $this->load->model('Checklists');
   
      $story_list = $this->Stories->get_backlog_stories();
      
//      echo '<pre>'; print_r($story_list); echo '</pre>'; exit;

      $project_list = $this->Projects->get_active_projects();
      
      $project_lookup = array();
      foreach ($project_list AS $key => $item)
      {
         $project_lookup[$item['ID']] = $key;
      }
      
      $num_projects = count($project_list);
      $admin['project_exists'] = ($num_projects == 0) ? false : true;
      
//      $project_list = $this->Checklists->add_status_info($project_list);
            
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');
      
      $data['priorities'] = array(
         2 => 'Very High',
         3 => 'High',
         4 => 'Medium',
         5 => 'Low',
         6 => 'Very Low',
         1 => 'Vendor Support',
         7 => 'Unplanned',
      );

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Projects');
      $data['submenu'] = get_submenu('Project Backlog');
      $data['admin'] = $admin;
      $data['project_list'] = $project_list;
      $data['project_lookup'] = $project_lookup;
      $data['story_list'] = $story_list;
      $data['resources'] = $this->Resources->get_resource_data();
      
//      echo "<pre>"; print_r($data['story_list']); echo "</pre>";
         
      $this->load->vars($data);
   	
      return $this->load->view('cp/backlogs/list', NULL, TRUE);

   }
   

}
?>