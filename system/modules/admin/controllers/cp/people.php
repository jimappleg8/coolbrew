<?php

class People extends Controller {

   var $aco = array();

   function People()
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
    * Generates a listing of people
    *
    */
   function index() 
   {
      $this->administrator->check_login();
      
      $people['error_msg'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');

      $people['message'] = $this->session->userdata('people_message');
      if ($this->session->userdata('people_message') != '')
         $this->session->set_userdata('people_message', '');

      $people['group'] = $this->session->userdata('group');
      
      $this->load->model('People');
      $this->load->helper('column');

      $user = $this->People->get_user_data($this->session->userdata('username'));

      $people_list = $this->People->get_users_companies();

      $people['people_exist'] = (count($people_list) == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['tabs'] = $this->administrator->get_main_tabs('All People');
      $data['people'] = $people;
      $data['user'] = $user;
      $data['people_list'] = $people_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/people/list', NULL, TRUE);

   }

   // --------------------------------------------------------------------
   
   /**
    * Deletes a person from the admin module
    *
    */
   function delete($username) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->database('read');
      $this->load->model('People');
      
      $user = $this->People->get_user_data($username);
      
      // will need to delete the user from adm_memberships
      $usercode = $this->People->get_usercode($username);
      $old_group = $this->People->get_user_group($usercode);
      $this->tacl->remove_from_group($old_group, $usercode);
      
      // delete member record (also removes all permissions)
      $this->tacl->remove_member($usercode);
      
      // finally, delete from adm_person table
      $sql = "DELETE FROM adm_person " . 
             "WHERE Username = '".$username."' ".
             "LIMIT 1";
      $this->db->query($sql);
      
      $this->session->set_userdata('people_message', 'The user "'.$user['FirstName'].' '.$user['LastName'].'" has been deleted.');
             
      redirect("cp/people/index");
   }

   // --------------------------------------------------------------------

   /**
    * Adds a people entry
    *
    */
   function add($company_id, $this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('People');
      $this->load->library('validation');
      
      $rules['FirstName'] = 'trim|required';
      $rules['LastName'] = 'trim|required';
      $rules['Email'] = 'trim|required';
      $rules['Username'] = 'trim|required';
      $rules['Password'] = 'trim|required';
      $rules['CompanyID'] = 'trim|required';
      $rules['GroupName'] = 'trim|required';
      $rules['Title'] = 'trim';
      $rules['OfficePhone'] = 'trim';
      $rules['OfficePhoneExt'] = 'trim';
      $rules['MobilePhone'] = 'trim';
      $rules['FaxPhone'] = 'trim';
      $rules['HomePhone'] = 'trim';
      $rules['IMName'] = 'trim';
      $rules['IMService'] = 'trim';
      $rules['Gender'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['FirstName'] = 'First Name';
      $fields['LastName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Username'] = 'Username';
      $fields['Password'] = 'Password';
      $fields['PersonalNote'] = 'Personal Note';
      $fields['CompanyID'] = 'CompanyID';
      $fields['GroupName'] = 'Group Name';
      $fields['Title'] = 'Title';
      $fields['OfficePhone'] = 'Office Phone';
      $fields['OfficePhoneExt'] = 'Office Phone Extension';
      $fields['MobilePhone'] = 'Mobile Phone';
      $fields['FaxPhone'] = 'Fax';
      $fields['HomePhone'] = 'Home Phone';
      $fields['IMName'] = 'IM Name';
      $fields['IMService'] = 'IM Service';
      $fields['Gender'] = 'Gender';

      $this->validation->set_fields($fields);

      $defaults['CompanyID'] = $company_id;
      $defaults['Gender'] = 'F';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['tabs'] = $this->administrator->get_main_tabs('All People');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['company_id'] = $company_id;
         $data['im_services'] = $this->People->get_im_services_list();
         $data['companies'] = $this->People->get_company_list();
         $data['groups'] = $this->People->get_group_list();
         $data['genders'] = $this->People->get_gender_list();

         $this->load->vars($data);
   	
         return $this->load->view('cp/people/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add();
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add people form
    *
    */
   function _add()
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $fields = $this->validation->_fields;
      unset($fields['PersonalNote']);
      unset($fields['GroupName']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['FirstName'] = ascii_to_entities($values['FirstName']);
      $values['LastName'] = ascii_to_entities($values['LastName']);
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['IMName'] = ascii_to_entities($values['IMName']);
      
      $values['Password'] = md5($values['Password']);

      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->load->database('read');
      
      $this->db->insert('adm_person', $values);

      // add person to member table
      $usercode = $this->db->insert_id().'-person';
      $this->tacl->create_member($usercode);

      // assign the new person to a group
      $new_group = $this->input->post('GroupName');
      $this->tacl->add_to_group($new_group, $usercode);

      // send email to new user.
      $values['PersonalNote'] = $this->input->post('PersonalNote');
      $values['Password'] = $this->input->post('Password');
      $values['Resend'] = FALSE;

      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail))
      {
         $sendmail = "/usr/sbin/sendmail -t ";
      }

      $mail_content = $this->load->view('cp/people/email', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
      
      // display a message showing email was sent
      $message = 'An email has been sent to '.$values['FirstName'].' '.$values['LastName'].' with their log-in information.';
      $this->session->set_userdata('people_message', $message);

      redirect("cp/people/index");
   }
   
   // --------------------------------------------------------------------

   /**
    * Updates a people entry
    *
    */
   function edit($username, $this_action) 
   {
      if ( ! $this->administrator->check_group('admin') && 
      $username != $this->session->userdata('username'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('People');
      $this->load->model('Permissions');
      $this->load->library('validation');
      
      $user = $this->People->get_user_data($username);
      $sites = $this->Permissions->get_sites($username);
      
      $rules['FirstName'] = 'trim|required';
      $rules['LastName'] = 'trim|required';
      $rules['Email'] = 'trim|required';
      $rules['Password'] = 'trim';
      $rules['ResendEmail'] = 'trim';
      $rules['CompanyID'] = 'trim';
      $rules['GroupName'] = 'trim';
      $rules['Title'] = 'trim';
      $rules['OfficePhone'] = 'trim';
      $rules['OfficePhoneExt'] = 'trim';
      $rules['MobilePhone'] = 'trim';
      $rules['FaxPhone'] = 'trim';
      $rules['HomePhone'] = 'trim';
      $rules['IMName'] = 'trim';
      $rules['IMService'] = 'trim';
      $rules['Gender'] = 'trim';
      $rules['NewSite'] = 'trim'; // for permissions

      $this->validation->set_rules($rules);

      $fields['FirstName'] = 'First Name';
      $fields['LastName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Username'] = 'Username';
      $fields['Password'] = 'Password';
      $fields['ResendEmail'] = 'Resend Email';
      $fields['CompanyID'] = 'CompanyID';
      $fields['GroupName'] = 'Group Name';
      $fields['Title'] = 'Title';
      $fields['OfficePhone'] = 'Office Phone';
      $fields['OfficePhoneExt'] = 'Office Phone Extension';
      $fields['MobilePhone'] = 'Mobile Phone';
      $fields['FaxPhone'] = 'Fax';
      $fields['HomePhone'] = 'Home Phone';
      $fields['IMName'] = 'IM Name';
      $fields['IMService'] = 'IM Service';
      $fields['Gender'] = 'IM Service';
      $fields['NewSite'] = 'New Site'; // for permissions

      $this->validation->set_fields($fields);

      // get the data from current record
      $defaults = $user;
      
      $defaults['FirstName'] = entities_to_ascii($defaults['FirstName']);
      $defaults['LastName'] = entities_to_ascii($defaults['LastName']);
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['IMName'] = entities_to_ascii($defaults['IMName']);
      unset($defaults['Password']);
      
      $usercode = $this->People->get_usercode($username);
      $defaults['GroupName'] = $this->People->get_user_group($usercode);
      
      $user['Group'] = $defaults['GroupName'];
         
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');
         
         $admin['group'] = $this->session->userdata('group');

         $data['tabs'] = $this->administrator->get_main_tabs('All People');
         
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['admin'] = $admin;
         $data['username'] = $username;
         $data['user'] = $user;
         $data['im_services'] = $this->People->get_im_services_list();
         $data['companies'] = $this->People->get_company_list();
         $data['groups'] = $this->People->get_group_list();
         $data['genders'] = $this->People->get_gender_list();
         $data['sites'] = $sites;
         
         $mydata['last_action'] = $this->session->userdata('last_action') + 1;
         $mydata['admin']['sites_exist'] = (count($sites) > 0) ? TRUE : FALSE;
         $mydata['admin']['group'] = $this->session->userdata('group');
         $mydata['domains'] = $this->Permissions->get_site_domains($username);
         $mydata['username'] = $username;
         $mydata['user'] = $user;
         $mydata['sites'] = $sites;
         $mydata['form_open'] = FALSE;
         $this->load->vars($mydata);
         $data['permissions'] = $this->load->view('cp/permissions/list', NULL, TRUE);

         $this->load->vars($data);
   	
         return $this->load->view('cp/people/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($username);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit people form
    *
    */
   function _edit($username)
   {
      if ( ! $this->administrator->check_group('admin') && 
      $username != $this->session->userdata('username'))
         redirect('cp/login/sorry');

      if ($username == '')
      {
         show_error('_edit_people requires that a username be supplied.');
      }
      
      $fields = $this->validation->_fields;
      unset($fields['Username']);
      unset($fields['GroupName']);
      unset($fields['ResendEmail']);
      unset($fields['NewSite']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['FirstName'] = ascii_to_entities($values['FirstName']);
      $values['LastName'] = ascii_to_entities($values['LastName']);
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['IMName'] = ascii_to_entities($values['IMName']);
      
      // see if the current user is changing their own data and update the session data just to be sure.
      if ($username == $this->session->userdata('username'))
      {
         $this->session->set_userdata('name', $values['FirstName'].' '.$values['LastName']);
      }
      
      if ($values['Password'] != '')
      {
         $password = $values['Password'];
         $values['Password'] = md5($values['Password']);
         $no_email = FALSE;
      }
      else
      {
         unset($values['Password']);
         $no_email = TRUE;
      }

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->load->database('read');
      
      $this->db->where('Username', $username);
      $this->db->update('adm_person', $values);
      
      // make sure group information is updated
      $new_group = $this->input->post('GroupName');
      $usercode = $this->People->get_usercode($username);
      $old_group = $this->People->get_user_group($usercode);
      if ($new_group != $old_group)
      {
         $this->tacl->remove_from_group($old_group, $usercode);
         $this->tacl->add_to_group($new_group, $usercode);
      }
      
      if ($this->input->post('ResendEmail') == 1 && $no_email == FALSE)
      {
         $values['PersonalNote'] = '';
         $values['Username'] = $username;
         $values['Password'] = $password;
         $values['Resend'] = TRUE;

         $sendmail = ini_get('sendmail_path');
         if (empty($sendmail))
         {
            $sendmail = "/usr/sbin/sendmail -t ";
         }

         $mail_content = $this->load->view('cp/people/email', $values, TRUE);
         $fd = popen($sendmail,"w");
         fputs($fd, stripslashes($mail_content)."\n");
         pclose($fd);
      
         // display a message showing email was sent
         $message = 'An email has been sent to '.$values['FirstName'].' '.$values['LastName'].' with their log-in information.';
         $this->session->set_userdata('people_message', $message);
      }
      elseif ($this->input->post('ResendEmail') == 1 && $no_email == TRUE)
      {
         // display a message indicating email was not sent
         $message = 'No email was sent to '.$values['FirstName'].' '.$values['LastName'].' because the password was not reset.';
         $this->session->set_userdata('people_message', $message);
      }
      
      redirect("cp/people/index");
   }
   

}
?>