<?php

class Story_sprints extends Controller {

   function Story_sprints()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of a single sprint as linked to the story
    *
    */
   function index($story_id, $sprint_id)
   {
      $this->administrator->check_login();

      $this->load->model('Sprints');
      $this->load->model('Story_sprints');
   
      $sprint = $this->Story_sprints->get_story_sprint_data($story_id, $sprint_id);
      $current_sprint = $this->Sprints->get_this_sprint();
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['story_id'] = $story_id;
      $data['sprint_id'] = $sprint_id;
      $data['current_sprint'] = $current_sprint;
      $data['sprint'] = $sprint;
      
      $this->load->vars($data);
   	
      echo $this->load->view('cp/story_sprints/list', NULL, TRUE);
      exit;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Deletes an Sprint from a story
    *
    */
   function delete($story_id, $sprint_id, $this_action) 
   {
      $this->administrator->check_login();

      $this->load->helper('text');
      $this->load->model('Sprints');
      $this->load->model('Story_sprints');
      
      // delete the link to the sprint
      $this->Story_sprints->delete_link($story_id, $sprint_id);
      
      echo ' ';
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Adds an sprint to a story
    *
    */
   function add($story_id, $this_action) 
   {
      $this->administrator->check_login();

      $this->load->helper(array('form', 'text'));
      $this->load->model('Story_sprints');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['NewSprint'] = 'trim|required';
      $rules['NewStatus'] = 'trim|required';
      $rules['NewEstimatedHours'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['NewSprint'] = 'Sprint';
      $fields['NewStatus'] = 'Status';
      $fields['NewEstimatedHours'] = 'Estimated Hours';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $last_action = $this->session->userdata('last_action') + 1;
         redirect('cp/stories/edit/'.$story_id.'/'.$last_action);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($story_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add sprint link form
    *
    */
   function _add($story_id)
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $values['SprintID'] = $values['NewSprint'];
      $values['Status'] = $values['NewStatus'];
      $values['EstimatedHours'] = $values['NewEstimatedHours'];
      unset($values['NewSprint']);
      unset($values['NewStatus']);
      unset($values['NewEstimatedHours']);
      
      $values['StoryID'] = $story_id;

      $this->Story_sprints->insert_link($values);
      
      $this->session->set_userdata('project_message', 'The new sprint has been added.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/stories/edit/'.$story_id.'/'.$last_action);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a sprint link
    *
    */
   function edit($story_id, $sprint_id, $this_action) 
   {
      $this->administrator->check_login();

      $this->load->helper(array('form', 'text'));
      $this->load->model('Sprints');
      $this->load->model('Story_sprints');
      $this->load->library('validation');
      
      $old_values = $this->Story_sprints->get_link_data($story_id, $sprint_id);
      $current_sprint = $this->Sprints->get_this_sprint();

      $rules['Status'] = 'trim|required';
      $rules['EstimatedHours'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Status'] = 'Status';
      $fields['EstimatedHours'] = 'Estimated Hours';

      $this->validation->set_fields($fields);

      $defaults = $old_values;

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $data['statuses'] = array('Not Started' => 'Not Started', 
                                   'In Progress' => 'In Progress',
                                   'Completed' => 'Completed',
                                   'Deferred' => 'Deferred',
                                   'Testing' => 'Testing',
                                   'Cancelled' => 'Cancelled'
                                  );
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['story_id'] = $story_id;
         $data['sprint_id'] = $sprint_id;
         $data['current_sprint'] = $current_sprint;

         $this->load->vars($data);
   	
         echo $this->load->view('cp/story_sprints/edit', NULL, TRUE);
         exit;
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($story_id, $sprint_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the update Sprint link form
    *
    */
   function _edit($story_id, $sprint_id)
   {
      $this->administrator->check_login();

      if ($story_id == 0 || $sprint_id == 0)
      {
         show_error('_edit_sprint requires that an Story ID and Sprint ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $this->Story_sprints->update_link($story_id, $sprint_id, $values);
      
      $sprint = $this->Story_sprints->get_story_sprint_data($story_id, $sprint_id);
      $current_sprint = $this->Sprints->get_this_sprint();
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['story_id'] = $story_id;
      $data['sprint_id'] = $sprint_id;
      $data['current_sprint'] = $current_sprint;
      $data['sprint'] = $sprint;
      
      $this->load->vars($data);
   	
      echo $this->load->view('cp/story_sprints/list', NULL, TRUE);
      exit;
   }

}
?>