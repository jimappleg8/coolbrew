<?php

class Hours extends Controller {

   function Hours()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }

   // --------------------------------------------------------------------

   /**
    * Lists the hours for the given story and sprint
    *
    */
   function index($story_id, $sprint_id)
   {
      $this->administrator->check_login();
      
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Hours');
      
      $hours = $this->Hours->get_hours($story_id, $sprint_id);
      $admin['hours_exist'] = (count($hours) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin'] = $admin;
      $data['story_id'] = $story_id;
      $data['sprint_id'] = $sprint_id;
      $data['hours'] = $hours;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/hours/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an hours record
    *
    */
   function delete($hour_id)
   {
      $this->administrator->check_login();

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Hours');
      
      $old_values = $this->Hours->get_hours_data($hour_id);
      $story_id = $old_values['StoryID'];
      $sprint_id = $old_values['SprintID'];

      $this->Hours->delete_hours($hour_id, $old_values);
      
      $hours = $this->Hours->get_hours($story_id, $sprint_id);
      $admin['hours_exist'] = (count($hours) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin'] = $admin;
      $data['story_id'] = $story_id;
      $data['sprint_id'] = $sprint_id;
      $data['hours'] = $hours;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/hours/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Adds an hours record
    *
    * Auditing: complete
    */
   function add($story_id, $sprint_id) 
   {
      $this->administrator->check_login();
      
      $admin['message'] = $this->session->userdata('projects_message');
      if ($this->session->userdata('projects_message') != '')
         $this->session->set_userdata('projects_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Hours');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['DateSpent'.$sprint_id] = 'trim';
      $rules['HoursSpent'.$sprint_id] = 'trim';
      $rules['Username'.$sprint_id] = 'trim';
      $rules['IsCapitalExpense'.$sprint_id] = 'trim';

      $this->validation->set_rules($rules);

      $fields['DateSpent'.$sprint_id] = 'Date Spent';
      $fields['HoursSpent'.$sprint_id] = 'Hours Spent';
      $fields['Username'.$sprint_id] = 'Username';
      $fields['IsCapitalExpense'.$sprint_id] = 'Is Capital Expense';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_add($story_id, $sprint_id);
      }

      $hours = $this->Hours->get_hours($story_id, $sprint_id);
      $admin['hours_exist'] = (count($hours) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin'] = $admin;
      $data['story_id'] = $story_id;
      $data['sprint_id'] = $sprint_id;
      $data['hours'] = $hours;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/hours/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add hours form
    *
    * Auditing: complete
    */
   function _add($story_id, $sprint_id)
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // make sure IsCapitalExpense is set either way
      if ( ! isset($values['IsCapitalExpense'.$sprint_id]))
         $values['IsCapitalExpense'.$sprint_id] = 0;

      $values['DateSpent'] = $values['DateSpent'.$sprint_id];
      $values['HoursSpent'] = $values['HoursSpent'.$sprint_id];
      $values['Username'] = $values['Username'.$sprint_id];
      $values['IsCapitalExpense'] = $values['IsCapitalExpense'.$sprint_id];
      unset($values['DateSpent'.$sprint_id]);
      unset($values['HoursSpent'.$sprint_id]);
      unset($values['Username'.$sprint_id]);
      unset($values['IsCapitalExpense'.$sprint_id]);

      $values['StoryID'] = $story_id;
      $values['SprintID'] = $sprint_id;
      
      $this->Hours->insert_hours($values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an hours record
    *
    * Auditing: complete
    */
   function edit($hour_id, $this_action) 
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('projects_message');
      if ($this->session->userdata('projects_message') != '')
         $this->session->set_userdata('projects_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Hours');
      $this->load->library(array('validation', 'auditor'));
      
      $old_values = $this->Hours->get_hours_data($hour_id);
//      echo '<pre>'; print_r($old_values); echo '</pre>';
      $story_id = $old_values['StoryID'];
      $sprint_id = $old_values['SprintID'];
      
      $rules['DateSpent'] = 'trim';
      $rules['HoursSpent'] = 'trim';
      $rules['Username'] = 'trim';
      $rules['IsCapitalExpense'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['DateSpent'] = 'Date Spent';
      $fields['HoursSpent'] = 'Hours Spent';
      $fields['Username'] = 'Username';
      $fields['IsCapitalExpense'] = 'Is Capital Expense';

      $this->validation->set_fields($fields);

      $defaults = $old_values;

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['admin'] = $admin;
         $data['hour_id'] = $hour_id;
         $data['story_id'] = $story_id;
         $data['sprint_id'] = $sprint_id;
         $data['hours'] = $this->Hours->get_hours($story_id, $sprint_id);

         $this->load->vars($data);
   	
         echo $this->load->view('cp/hours/edit', NULL, TRUE);
         exit;
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($hour_id, $old_values);
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the edit hours form
    *
    * Auditing: complete
    */
   function _edit($hour_id, $old_values)
   {
      $this->administrator->check_login();

      if ($hour_id == 0)
      {
         show_error('_edit hours requires that an hour ID be supplied.');
      }
      
      $story_id = $old_values['StoryID'];
      $sprint_id = $old_values['SprintID'];

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // make sure IsCapitalExpense is set either way
      if ( ! isset($values['IsCapitalExpense']))
         $values['IsCapitalExpense'] = 0;

      $values['StoryID'] = $story_id;
      $values['SprintID'] = $sprint_id;
      
      $this->Hours->update_hours($hour_id, $values, $old_values);

      $hours = $this->Hours->get_hours($story_id, $sprint_id);
      $admin['hours_exist'] = (count($hours) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['admin'] = $admin;
      $data['story_id'] = $story_id;
      $data['sprint_id'] = $sprint_id;
      $data['hours'] = $hours;

      $this->load->vars($data);
   	
      echo $this->load->view('cp/hours/list', NULL, TRUE);
      exit;
   }


}
?>
