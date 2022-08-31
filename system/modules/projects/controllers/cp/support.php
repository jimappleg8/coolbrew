<?php

class Support extends Controller {

   function Support()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of projects; the main dashboard
    *
    */
   function index($sprint_id = '')
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
      $this->load->model('Sprints');
      $this->load->model('Resources');
      $this->load->model('Checklists');
   
      $story_list = $this->Stories->get_support_stories();

      $project_list = $this->Projects->get_all_projects();
      
      $project_lookup = array();
      foreach ($project_list AS $key => $item)
      {
         $project_lookup[$item['ID']] = $key;
      }
      
      $num_projects = count($project_list);
      $admin['project_exists'] = ($num_projects == 0) ? false : true;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');
      
      $data['priorities'] = array(
         0 => 'This Sprint',
      );

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Projects');
      $data['submenu'] = get_submenu('Projects');
      $data['admin'] = $admin;
      $data['project_list'] = $project_list;
      $data['project_lookup'] = $project_lookup;
      $data['story_list'] = $story_list;
      $data['resources'] = $this->Resources->get_resource_data();
      
//      echo "<pre>"; print_r($data['story_list']); echo "</pre>";
         
      $this->load->vars($data);
   	
      return $this->load->view('cp/support/list', NULL, TRUE);

   }
   

}
?>