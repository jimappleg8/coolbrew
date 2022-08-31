<?php

class Projects extends Controller {

   // --------------------------------------------------------------------

   function Projects()
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

      $project_list = $this->Projects->get_active_projects_by_group();

      $num_projects = count($project_list);
      $admin['project_exists'] = ($num_projects == 0) ? false : true;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Projects');
      $data['submenu'] = get_submenu('Projects');
      $data['admin'] = $admin;
      $data['project_list'] = $project_list;
      $data['resources'] = $this->Resources->get_resource_data();
      
//      $this->load->model('Indexes');
//      $this->Indexes->rebuild_index();
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/projects/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Adds a project listing.
    *
    */
   function add($this_action) 
   {
      $this->administrator->check_login();

      $this->load->helper(array('form', 'url', 'text'));
      $this->load->model('Projects');
      $this->load->model('Indexes');
      $this->load->library('validation');
      
      $rules['GroupName'] = 'trim|required';
      $rules['ProjectName'] = 'trim|required';
      $rules['Announcement'] = 'trim';
      $rules['ShowAnnouncement'] = 'trim';
      $rules['StartPage'] = 'trim';
      $rules['ProjectTypeID'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['RequestedDueDate'] = 'trim';
      $rules['DueDateNotes'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['GroupName'] = 'Project Group';
      $fields['ProjectName'] = 'Project Name';
      $fields['Announcement'] = 'Announcement';
      $fields['ShowAnnouncement'] = 'Show Announcement';
      $fields['StartPage'] = 'Start Page';
      $fields['ProjectTypeID'] = 'Project Type';
      $fields['Description'] = 'Description';
      $fields['RequestedDueDate'] = 'Requested Due Date';
      $fields['DueDateNotes'] = 'Due Date Notes';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults['Status'] = 'active';
      $defaults['ProjectTypeID'] = 1;
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('projects_adm');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Projects');
         $data['submenu'] = get_submenu('Projects');
         $data['project_types'] = $this->Projects->get_project_type_list();
         $data['start_pages'] = $this->Projects->get_start_page_list();
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/projects/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_add();
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add project form
    *
    */
   function _add()
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['GroupName'] = ascii_to_entities($values['GroupName']);
      $values['ProjectName'] = ascii_to_entities($values['ProjectName']);
      $values['Announcement'] = ascii_to_entities($values['Announcement']);
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['DueDateNotes'] = ascii_to_entities($values['DueDateNotes']);
      
      $values['NewRecord'] = 1;
      $values['CreatedDate'] = date('Y-m-d H:i:s');
//      $values['CreatedBy'] = $this->session->userdata('username');
      
      $project_id = $this->Projects->insert_project($values);

      // update the search index
      $this->Indexes->update_project_index($project_id);
      
      // display a message showing settings were updated
      $message = 'The project "<strong>'.$values['GroupName'].'</strong> '.$values['ProjectName'].'" has been added.';
      $this->session->set_userdata('project_message', $message);

      redirect('cp/stories/index/'.$project_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Updates a project listing
    *
    */
   function edit($project_id, $this_action) 
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('project_message');
      if ($this->session->userdata('project_message') != '')
         $this->session->set_userdata('project_message', '');

      $admin['error_msg'] = $this->session->userdata('project_error');
      if ($this->session->userdata('project_error') != '')
         $this->session->set_userdata('project_error', '');

      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->model('Projects');
      $this->load->model('Indexes');
      $this->load->library('validation');

      $project = $this->Projects->get_project_data($project_id);

      $rules['GroupName'] = 'trim|required';
      $rules['ProjectName'] = 'trim|required';
      $rules['Announcement'] = 'trim';
      $rules['ShowAnnouncement'] = 'trim';
      $rules['StartPage'] = 'trim';
      $rules['ProjectTypeID'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['RequestedDueDate'] = 'trim';
      $rules['DueDateNotes'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['GroupName'] = 'Project Group';
      $fields['ProjectName'] = 'Project Name';
      $fields['Announcement'] = 'Announcement';
      $fields['ShowAnnouncement'] = 'Show Announcement';
      $fields['StartPage'] = 'Start Page';
      $fields['ProjectTypeID'] = 'Project Type';
      $fields['Description'] = 'Description';
      $fields['RequestedDueDate'] = 'Requested Due Date';
      $fields['DueDateNotes'] = 'Due Date Notes';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults = $project;
      $defaults['GroupName'] = entities_to_ascii($defaults['GroupName']);
      $defaults['ProjectName'] = entities_to_ascii($defaults['ProjectName']);
      $defaults['Announcement'] = entities_to_ascii($defaults['Announcement']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      $defaults['DueDateNotes'] = entities_to_ascii($defaults['DueDateNotes']);

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('projects_adm');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Projects');
         $data['submenu'] = get_submenu('Projects');
         $data['admin'] = $admin;
         $data['project_id'] = $project_id;
         $data['project'] = $project;
         $data['project_types'] = $this->Projects->get_project_type_list();
         $data['start_pages'] = $this->Projects->get_start_page_list();
         $data['project_id'] = $project_id;

         $this->load->vars($data);
   	
         return $this->load->view('cp/projects/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($project_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit settings form
    *
    */
   function _edit($project_id)
   {
      $this->administrator->check_login();

      if ($project_id == 0)
      {
         show_error('_edit_settings requires that a project ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['GroupName'] = ascii_to_entities($values['GroupName']);
      $values['ProjectName'] = ascii_to_entities($values['ProjectName']);
      $values['Announcement'] = ascii_to_entities($values['Announcement']);
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['DueDateNotes'] = ascii_to_entities($values['DueDateNotes']);
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->Projects->update_project($project_id, $values);
      
      // update the search index
      $this->Indexes->update_project_index($project_id);
      
      // display a message showing settings were updated
      $message = 'The settings for this project have been updated.';
      $this->session->set_userdata('project_message', $message);

      $last_action = $this->session->userdata('last_action');
      redirect('cp/projects/edit/'.$project_id.'/'.$last_action.'/');
   }

}
?>