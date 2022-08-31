<?php

class Stories extends Controller {

   function Stories()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of stories for the specified project
    *
    */
   function index($project_id)
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
   
      $project = $this->Projects->get_project_data($project_id);
      
      $story_list = $this->Stories->get_project_stories($project_id);

      $num_stories = count($story_list);
      $admin['story_exists'] = ($num_stories == 0) ? false : true;
      
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
      $data['submenu'] = get_submenu('Projects');
      $data['admin'] = $admin;
      $data['project_id'] = $project_id;
      $data['project'] = $project;
      $data['story_list'] = $story_list;
      $data['resources'] = $this->Resources->get_resource_data();
      
//      echo "<pre>"; print_r($data['story_list']); echo "</pre>";
         
      $this->load->vars($data);
   	
      return $this->load->view('cp/stories/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes a story
    *
    */
   function delete($story_id, $this_action) 
   {
      $this->administrator->check_login();

      $this->load->helper('text');
      $this->load->model('Stories');
      $this->load->model('Indexes');
      
      // get the current record so we can display a status message
      $story = $this->Stories->get_story_data($story_id);
      
      // delete the story record and associated records
      $this->Stories->delete_story($story_id, $story);
      
      // delete the search index
      $this->Indexes->delete_story_index_records($story_id);

      $this->session->set_userdata('project_message', 'The story  "'.$story['Description'].'" has been deleted.');
      
      redirect("cp/stories/index/".$story['ProjectID']);
   }

   // --------------------------------------------------------------------

   /**
    * Adds a story listing.
    *
    */
   function add($project_id, $this_action) 
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('project_message');
      if ($this->session->userdata('project_message') != '')
         $this->session->set_userdata('project_message', '');

      $admin['error_msg'] = $this->session->userdata('project_error');
      if ($this->session->userdata('project_error') != '')
         $this->session->set_userdata('project_error', '');

      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->model('Stories');
      $this->load->model('Sprints');
      $this->load->model('Story_sprints');
      $this->load->model('Indexes');
      $this->load->library('validation');
      
      $rules['HeatID'] = 'trim';
      $rules['HeatAssignment'] = 'trim';
      $rules['Description'] = 'trim|required';
      $rules['Client'] = 'trim|required';
      $rules['Points'] = 'trim';
      $rules['Assigned'] = 'trim';
      $rules['Deadline'] = 'trim';
      $rules['Priority'] = 'trim';
      $rules['Notes'] = 'trim';
      $rules['NewSprint'] = 'trim';
      $rules['NewStatus'] = 'trim';
      $rules['NewEstimatedHours'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['HeatID'] = 'Heat ID';
      $fields['HeatAssignment'] = 'Heat Assignment';
      $fields['Description'] = 'Description';
      $fields['Client'] = 'Client';
      $fields['Points'] = 'Points';
      $fields['Assigned'] = 'Assigned';
      $fields['Deadline'] = 'Deadline';
      $fields['Priority'] = 'Priority';
      $fields['Notes'] = 'Priority';
      $fields['NewSprint'] = 'New Sprint';
      $fields['NewStatus'] = 'New Status';
      $fields['NewEstimatedHours'] = 'New Estimated Hours';

      $this->validation->set_fields($fields);

      $current_sprint = $this->Sprints->get_this_sprint();
      $defaults['Priority'] = 7;
      $defaults['NewSprint'] = $current_sprint['ID'];
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('projects_adm');

         $data['priorities'] = array(2 => 'Very High',
                                     3 => 'High',
                                     4 => 'Medium',
                                     5 => 'Low',
                                     6 => 'Very Low',
                                     1 => 'Vendor Support',
                                     7 => 'Unplanned'
                                    );
         $data['statuses'] = array('Not Started' => 'Not Started', 
                                   'In Progress' => 'In Progress',
                                   'Completed' => 'Completed',
                                   'Deferred' => 'Deferred',
                                   'Testing' => 'Testing',
                                   'Cancelled' => 'Cancelled'
                                  );
         $data['sprint_list'] = $this->Sprints->get_sprint_list();

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Projects');
         $data['submenu'] = get_submenu('Projects');
         $data['admin'] = $admin;
         $data['project_id'] = $project_id;
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/stories/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_add($project_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add story form
    *
    */
   function _add($project_id)
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // pull out the sprint variables
      $sprint['SprintID'] = $values['NewSprint'];
      $sprint['Status'] = $values['NewStatus'];
      $sprint['EstimatedHours'] = $values['NewEstimatedHours'];
      unset($values['NewSprint']);
      unset($values['NewStatus']);
      unset($values['NewEstimatedHours']);
      
      // process the form text (convert special characters and the like)
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['Client'] = ascii_to_entities($values['Client']);
      $values['Assigned'] = ascii_to_entities($values['Assigned']);
      $values['Notes'] = ascii_to_entities($values['Notes']);
      
      $values['ProjectID'] = $project_id;
//      $values['CreatedDate'] = date('Y-m-d H:i:s');
//      $values['CreatedBy'] = $this->session->userdata('username');
      
      $story_id = $this->Stories->insert_story($values);

      // insert the sprint if one is indicated
      if ($sprint['SprintID'] != '')
      {
         $sprint['StoryID'] = $story_id;
         $this->Story_sprints->insert_link($sprint);
      }

      // update the search index
      $this->Indexes->update_story_index($project_id, $story_id);

      // display a message showing settings were updated
      $message = 'The story "'.$values['Description'].'" has been added.';
      $this->session->set_userdata('project_message', $message);

      redirect('cp/stories/index/'.$project_id);
   }

   // --------------------------------------------------------------------

   /**
    * Updates a story listing
    *
    */
   function edit($story_id, $this_action) 
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('project_message');
      if ($this->session->userdata('project_message') != '')
         $this->session->set_userdata('project_message', '');

      $admin['error_msg'] = $this->session->userdata('project_error');
      if ($this->session->userdata('project_error') != '')
         $this->session->set_userdata('project_error', '');

      $this->load->helper(array('form', 'url', 'text'));
      $this->load->model('Stories');
      $this->load->model('Sprints');
      $this->load->model('Story_sprints');
      $this->load->model('Hours');
      $this->load->model('Indexes');

      $story = $this->Stories->get_story_data($story_id);
      $sprints = $this->Sprints->get_sprints($story_id);
      
      $this->load->library('validation');
      
      $rules['HeatID'] = 'trim';
      $rules['HeatAssignment'] = 'trim';
      $rules['Description'] = 'trim|required';
      $rules['Client'] = 'trim|required';
      $rules['Points'] = 'trim';
      $rules['Assigned'] = 'trim';
      $rules['Deadline'] = 'trim';
      $rules['Priority'] = 'trim';
      $rules['Notes'] = 'trim';
      $rules['NewSprint'] = 'trim';
      $rules['NewStatus'] = 'trim';
      $rules['NewEstimatedHours'] = 'trim';
      foreach($sprints AS $sprint)
      {
         $rules['Status'.$sprint['ID']] = 'trim';
         $rules['EstimatedHours'.$sprint['ID']] = 'trim';
         $rules['DateSpent'.$sprint['ID']] = 'trim';
         $rules['HoursSpent'.$sprint['ID']] = 'trim';
         $rules['Username'.$sprint['ID']] = 'trim';
         $rules['IsCapitalExpense'.$sprint['ID']] = 'trim';
      }

      $this->validation->set_rules($rules);

      $fields['HeatID'] = 'Heat ID';
      $fields['HeatAssignment'] = 'Heat Assignment';
      $fields['Description'] = 'Description';
      $fields['Client'] = 'Client';
      $fields['Points'] = 'Points';
      $fields['Assigned'] = 'Assigned';
      $fields['Deadline'] = 'Deadline';
      $fields['Priority'] = 'Priority';
      $fields['Notes'] = 'Priority';
      $fields['NewSprint'] = 'New Sprint';
      $fields['NewStatus'] = 'New Status';
      $fields['NewEstimatedHours'] = 'New Estimated Hours';
      foreach($sprints AS $sprint)
      {
         $fields['Status'.$sprint['ID']] = 'Status '.$sprint['ID'];
         $fields['EstimatedHours'.$sprint['ID']] = 'Estimated Hours '.$sprint['ID'];
         $fields['DateSpent'.$sprint['ID']] = 'DateSpent '.$sprint['ID'];
         $fields['HoursSpent'.$sprint['ID']] = 'HoursSpent '.$sprint['ID'];
         $fields['Username'.$sprint['ID']] = 'Username '.$sprint['ID'];
         $fields['IsCapitalExpense'.$sprint['ID']] = 'IsCapitalExpense '.$sprint['ID'];
      }

      $this->validation->set_fields($fields);

      $current_sprint = $this->Sprints->get_this_sprint();
      $defaults = $story;
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      $defaults['Client'] = entities_to_ascii($defaults['Client']);
      $defaults['Assigned'] = entities_to_ascii($defaults['Assigned']);
      $defaults['Notes'] = entities_to_ascii($defaults['Notes']);
      $defaults['NewSprint'] = $current_sprint['ID'];
      foreach($sprints AS $sprint)
      {
         $defaults['Status'.$sprint['ID']] = $sprint['Status'];
         $defaults['EstimatedHours'.$sprint['ID']] = $sprint['EstimatedHours'];
         $defaults['DateSpent'.$sprint['ID']] = date('Y-m-d');
         $defaults['HoursSpent'.$sprint['ID']] = 0;
         $defaults['Username'.$sprint['ID']] = $this->session->userdata('username');;
         $defaults['IsCapitalExpense'.$sprint['ID']] = 1;
      }

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('projects_adm');

         $data['priorities'] = array(2 => 'Very High',
                                     3 => 'High',
                                     4 => 'Medium',
                                     5 => 'Low',
                                     6 => 'Very Low',
                                     1 => 'Vendor Support',
                                     7 => 'Unplanned'
                                    );
         $data['statuses'] = array('Not Started' => 'Not Started', 
                                   'In Progress' => 'In Progress',
                                   'Completed' => 'Completed',
                                   'Deferred' => 'Deferred',
                                   'Testing' => 'Testing',
                                   'Cancelled' => 'Cancelled'
                                  );
         $data['sprint_list'] = $this->Sprints->get_sprint_list();

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Projects');
         $data['submenu'] = get_submenu('Projects');
         $data['admin'] = $admin;
         $data['story_id'] = $story_id;
         $data['story'] = $story;
         $data['project_id'] = $story['ProjectID'];
         $data['sprints'] = $sprints;
         $data['current_sprint'] = $current_sprint;

         // get the results of the sprint and hours templates
         foreach ($sprints AS $sprint)
         {
            // sprint
            $mydata = array();
            $mydata['last_action'] = $data['last_action'];
            $mydata['story_id'] = $story_id;
            $mydata['sprint_id'] = $sprint['ID'];
            $mydata['current_sprint'] = $current_sprint;
            $mydata['sprint'] = $sprint;
            $this->load->vars($mydata);
            $data['sprint_'.$sprint['ID']] = $this->load->view('cp/story_sprints/list', NULL, TRUE);
            
            // hours
            $hours = $this->Hours->get_hours($story_id, $sprint['ID']);
            $admin['hours_exist'] = (count($hours) > 0) ? TRUE : FALSE;

            $mydata = array();
            $mydata['last_action'] = $data['last_action'];
            $mydata['admin'] = $admin;
            $mydata['story_id'] = $story_id;
            $mydata['sprint_id'] = $sprint['ID'];
            $mydata['hours'] = $hours;
            $this->load->vars($mydata);
            $data['hours_'.$sprint['ID']] =  $this->load->view('cp/hours/list', NULL, TRUE);
         }

         $this->load->vars($data);
   	
         return $this->load->view('cp/stories/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($story['ProjectID'], $story_id, $sprints);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit settings form
    *
    */
   function _edit($project_id, $story_id, $sprints)
   {
      $this->administrator->check_login();

      if ($story_id == 0)
      {
         show_error('_edit stories requires that a story ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // remove all unneeded fields
      unset($values['NewSprint']);
      unset($values['NewStatus']);
      unset($values['NewEstimatedHours']);
      foreach($sprints AS $sprint)
      {
         unset($values['Status'.$sprint['ID']]);
         unset($values['EstimatedHours'.$sprint['ID']]);
         unset($values['DateSpent'.$sprint['ID']]);
         unset($values['HoursSpent'.$sprint['ID']]);
         unset($values['Username'.$sprint['ID']]);
         unset($values['IsCapitalExpense'.$sprint['ID']]);
      }

      // process the form text (convert special characters and the like)
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['Client'] = ascii_to_entities($values['Client']);
      $values['Assigned'] = ascii_to_entities($values['Assigned']);
      $values['Notes'] = ascii_to_entities($values['Notes']);
      
//      $values['RevisedDate'] = date('Y-m-d H:i:s');
//      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->Stories->update_story($story_id, $values);
      
      // update the search index
      $this->Indexes->update_story_index($project_id, $story_id);

      // display a message showing settings were updated
      $message = 'The story has been updated.';
      $this->session->set_userdata('project_message', $message);

      $last_action = $this->session->userdata('last_action');
      redirect('cp/stories/edit/'.$story_id.'/'.$last_action.'/');
   }

}
?>