<?php

class Admin extends Controller {

   function Admin()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }

   // --------------------------------------------------------------------
   
   /**
    * Log into the system
    *
    */
   function login()
   {
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['Username'] = 'trim|required';
      $rules['Password'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['Username'] = 'Username';
      $fields['Password'] = 'Password';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<p class="error">', '</p>');
      
      $data['error_msg'] = '';

      if ($this->validation->run() == TRUE)
      {
         foreach ($fields AS $key => $value)
            $values[$key] = $this->input->post($key);

         $values['Password'] = md5($values['Password']);

         $this->load->database('write');

         $sql = 'SELECT * FROM jobs_people '.
             'WHERE Username = \''.$values['Username'].'\' '.
             'AND Password = \''.$values['Password'].'\'';

         $query = $this->db->query($sql);
      
         if ($query->num_rows() > 0)
         {
            $row = $query->row_array();
            $this->session->set_userdata('username', $row['Username']);
            $this->session->set_userdata('name', $row['FirstName'].' '.$row['LastName']);
            
            $url = $this->session->flashdata('return_url');
            if ($url != '')
            {
               redirect($url);
            }
            else
            {
               redirect('');
            }
         }
         else
         {
            $this->session->keep_flashdata('return_url');
            $data['error_msg'] = "The username and/or password you entered is invalid.";
         }
      }
      
      $this->session->keep_flashdata('return_url');
      
      $this->collector->append_css_file('jobs_adm');
      $this->collector->append_css_file('login');

      $this->load->vars($data);
   	
      return $this->load->view('admin/login', NULL, TRUE);
  
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Log out of the system
    *
    */
   function logout()
   {
      $this->load->helper('url');
      $this->session->unset_userdata('username');
      $this->session->unset_userdata('name');
      redirect('');
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a person's account
    *
    */
   function edit_account($this_action, $return_url) 
   {
      $this->Jobs_people->check();
      
      $username = $this->session->userdata('username');

      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['FirstName'] = 'trim|required';
      $rules['LastName'] = 'trim|required';
      $rules['Email'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['FirstName'] = 'First Name';
      $fields['LastName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Username'] = 'Username';
      $fields['Password'] = 'Password';

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs_people '.
             'WHERE Username = \''.$username.'\' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['FirstName'] = entities_to_ascii($defaults['FirstName']);
      $defaults['LastName'] = entities_to_ascii($defaults['LastName']);
      unset($defaults['Password']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs();
         $data['return_url'] = $return_url;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('admin/account_edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit_account($username, $return_url);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit account form
    *
    */
   function _edit_account($username, $return_url)
   {
      $fields = $this->validation->_fields;
      unset($fields['Username']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // process the form text (convert special characters and the like)
      $values['FirstName'] = ascii_to_entities($values['FirstName']);
      $values['LastName'] = ascii_to_entities($values['LastName']);
      
      // update the session data, just in case
      $this->session->set_userdata('name', $values['FirstName'].' '.$values['LastName']);
      
      if ($values['Password'] != '')
      {
         $values['Password'] = md5($values['Password']);
      }
      else
      {
         unset($values['Password']);
      }

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $username;
      
      $this->load->database('write');
      
      $this->db->where('Username', $username);
      $this->db->update('jobs_people', $values);
      
      redirect('admin/'.$return_url);
   }

   // --------------------------------------------------------------------
   
   /**
    * Display the "Sorry you don't have access" page.
    *
    */
   function sorry()
   {
      $this->collector->append_css_file('jobs_adm');
      $this->collector->append_css_file('login');
      return $this->load->view('admin/sorry', NULL, TRUE);
   }
   
}

?>