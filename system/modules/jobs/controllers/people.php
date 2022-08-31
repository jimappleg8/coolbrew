<?php

class People extends Controller {

   function People()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }

   // --------------------------------------------------------------------

   /**
    * Generates a listing of people
    *
    */
   function index() 
   {
      $this->Jobs_people->check('People');
      
      $people['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');

      $people['message'] = $this->session->userdata('people_message');
      if ($this->session->userdata('people_message') != '')
         $this->session->set_userdata('people_message', '');

      $this->load->database('write');

      $sql = "SELECT * FROM jobs_people, jobs_people_module " .
             "WHERE jobs_people.Username = jobs_people_module.Username ".
             "AND jobs_people_module.ModuleID = 'jobs' ".
             "AND jobs_people_module.SiteID = '".SITE_ID."' ".
             "AND jobs_people.Status <= 1 ".
             "ORDER BY LastName";

      $query = $this->db->query($sql);
      $people_list = $query->result_array();

      $num_people = count($people_list);
      $people['people_exist'] = ($num_people == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('jobs_adm');

      $data['tabs'] = $this->Jobs_people->get_tabs('People');
      $data['people'] = $people;
      $data['people_list'] = $people_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('people/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Deletes a person
    *
    */
   function delete($username) 
   {
      $this->Jobs_people->check('People');
      
      $this->load->helper('url');
      $this->load->model('Jobs_people');

      $this->Jobs_people->delete_person($username);

      redirect("people/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a people entry
    *
    */
   function add($this_action) 
   {
      $this->Jobs_people->check('People');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->database('write');

      // Menu Items
      $sql = 'SELECT ID, LinkText '.
             'FROM adm_menu '.
             'WHERE ModuleID = \'jobs\' '.
             'ORDER BY Sort';

      $query = $this->db->query($sql);
      $menus = $query->result_array();
      
      $this->load->library('validation');
      
      $rules['FirstName'] = 'trim|required';
      $rules['LastName'] = 'trim|required';
      $rules['Email'] = 'trim|required';
      $rules['Username'] = 'trim|required';
      $rules['Password'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['FirstName'] = 'First Name';
      $fields['LastName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Username'] = 'Username';
      $fields['Password'] = 'Password';
      foreach ($menus AS $menu)
         $fields[$menu['LinkText']] = $menu['LinkText'];
      $fields['PersonalNote'] = 'Personal Note';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('People');
               
         $data['menus'] = $menus;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('people/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_add($menus);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add people form
    *
    */
   function _add($menus)
   {
      $fields = $this->validation->_fields;
      unset($fields['PersonalNote']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $count = 0;
      foreach ($menus AS $menu)
      {
         if ($values[$menu['LinkText']] != '')
         {
            $rights[$count]['Username'] = $values['Username'];
            $rights[$count]['SiteID'] = SITE_ID;
            $rights[$count]['MenuID'] = $menu['ID'];
            $count++;
         }
         unset($values[$menu['LinkText']]);
      }

      if ($values['Status'] == '')
         $values['Status'] = 1;
      
      // process the form text (convert special characters and the like)
      $values['FirstName'] = ascii_to_entities($values['FirstName']);
      $values['LastName'] = ascii_to_entities($values['LastName']);
      
      $values['Password'] = md5($values['Password']);

      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->insert('jobs_people', $values);
      
      // update the Jobs_people_menu table
      if ( ! empty($rights))
      {
         foreach ($rights AS $right)
            $this->db->insert('jobs_people_menu', $right);
      }
      
      // update the jobs_people_module table
      $module['Username'] = $values['Username'];
      $module['SiteID'] = SITE_ID;
      $module['ModuleID'] = 'jobs';
      $this->db->insert('jobs_people_module', $module);
      
      // send email to new user.
      $values['PersonalNote'] = $this->input->post('PersonalNote');
      $values['Password'] = $this->input->post('Password');

      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail)) {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      // send the email to new user
      $mail_content = $this->load->view('people/email', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
      
      // display a message showing email was sent
      $message = 'An email has been sent to '.$values['FirstName'].' '.$values['LastName'].' with their log-in information.';
      $this->session->set_userdata('people_message', $message);

      redirect("people/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a people entry
    *
    */
   function edit($username, $this_action) 
   {
      $this->Jobs_people->check('People');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->database('write');

      // Menu Items
      $sql = 'SELECT ID, LinkText '.
             'FROM adm_menu '.
             'WHERE ModuleID = \'jobs\' '.
             'ORDER BY Sort';

      $query = $this->db->query($sql);
      $menus = $query->result_array();

      // Menu Rights
      $sql = 'SELECT adm_menu.ID, adm_menu.LinkText '.
             'FROM adm_menu LEFT JOIN jobs_people_menu '.
             'ON adm_menu.ID = jobs_people_menu.MenuID '.
             'WHERE adm_menu.ModuleID = \'jobs\' '.
             'AND jobs_people_menu.Username = \''.$username.'\' '.
             'AND jobs_people_menu.SiteID = \''.SITE_ID.'\' ';

      $query = $this->db->query($sql);
      $rights = $query->result_array();
      
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
      foreach ($menus AS $menu)
         $fields[$menu['LinkText']] = $menu['LinkText'];

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs_people '.
             'WHERE Username = \''.$username.'\' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['FirstName'] = entities_to_ascii($defaults['FirstName']);
      $defaults['LastName'] = entities_to_ascii($defaults['LastName']);
      unset($defaults['Password']);
      
      foreach ($rights AS $right)
         $defaults[$right['LinkText']] = $right['LinkText'];

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('People');
         
         $data['username'] = $username;
         $data['menus'] = $menus;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('people/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($username, $menus, $rights);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit people form
    *
    */
   function _edit($username, $menus, $rights)
   {
      $fields = $this->validation->_fields;
      unset($fields['Username']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      foreach ($rights AS $right)
         $access[] = $right['LinkText'];

      $add_count = 0;
      $delete_count = 0;
      foreach ($menus AS $menu)
      {
         if (($values[$menu['LinkText']] != '') && (! in_array($menu['LinkText'], $access)))
         {
            $adds[$add_count]['Username'] = $username;
            $adds[$add_count]['SiteID'] = SITE_ID;
            $adds[$add_count]['MenuID'] = $menu['ID'];
            $add_count++;
         }
         elseif (($values[$menu['LinkText']] == '') && (in_array($menu['LinkText'], $access)))
         {
            $deletes[$delete_count]['Username'] = $username;
            $deletes[$delete_count]['MenuID'] = $menu['ID'];
            $delete_count++;
         }
         unset($values[$menu['LinkText']]);
      }

      // process the form text (convert special characters and the like)
      $values['FirstName'] = ascii_to_entities($values['FirstName']);
      $values['LastName'] = ascii_to_entities($values['LastName']);
      
      // see if the current user is changing their own data and update the session data just to be sure.
      if ($username == $this->session->userdata('username'))
      {
         $this->session->set_userdata('name', $values['FirstName'].' '.$values['LastName']);
      }
      
      if ($values['Password'] != '')
      {
         $values['Password'] = md5($values['Password']);
      }
      else
      {
         unset($values['Password']);
      }

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->where('Username', $username);
      $this->db->update('jobs_people', $values);
      
      if ( ! empty($adds))
      {
         foreach ($adds AS $add)
            $this->db->insert('jobs_people_menu', $add);
      }
      if ( ! empty($deletes))
      {
         foreach ($deletes AS $delete)
            $this->db->delete('jobs_people_menu', $delete);
      }

      redirect("people/index");
   }

}

?>