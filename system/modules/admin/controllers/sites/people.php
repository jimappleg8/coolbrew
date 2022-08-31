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
   function index($site_id) 
   {
      $admin['error_msg'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');

      $admin['message'] = $this->session->userdata('people_message');
      if ($this->session->userdata('people_message') != '')
         $this->session->set_userdata('people_message', '');

      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');
      $this->load->model('People');
      $this->load->helper('column');
      
      $site = $this->Sites->get_site_data($site_id);

      $people_list = $this->People->get_site_users_companies($site_id);
      
      $admin['people_exist'] = (count($people_list) == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'People &amp; Permissions');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      $data['people_list'] = $people_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/people/list', NULL, TRUE);

   }

   // --------------------------------------------------------------------
   
   /**
    * Removes a person's permission to access a site.
    *
    */
   function remove($site_id, $username) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->model('Permissions');
      
      $this->Permissions->delete_permissions($username, $site_id);

      redirect("sites/people/index/".$site_id);
   }

   // --------------------------------------------------------------------

   /**
    * Updates a people entry
    *
    */
   function edit($site_id, $username, $this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      
      $this->load->model('People');
      $this->load->model('Sites');

      $user = $this->People->get_user_data($username);
      $site = $this->Sites->get_site_data($site_id);

      $this->load->library('validation');
      
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

      $this->validation->set_fields($fields);

      // get the data from current record
      $defaults = $this->People->get_user_data($username);
      
      $defaults['FirstName'] = entities_to_ascii($defaults['FirstName']);
      $defaults['LastName'] = entities_to_ascii($defaults['LastName']);
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['IMName'] = entities_to_ascii($defaults['IMName']);
      unset($defaults['Password']);
      
      $usercode = $this->People->get_usercode($username);
      $defaults['GroupName'] = $this->People->get_user_group($usercode);
         
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'People &amp; Permissions');
         $data['user'] = $user;
         $data['site'] = $site;
         $data['username'] = $username;
         $data['site_id'] = $site_id;
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['im_services'] = $this->People->get_im_services_list();
         $data['companies'] = $this->People->get_company_list();
         $data['groups'] = $this->People->get_group_list();
         $data['genders'] = $this->People->get_gender_list();

         $this->load->vars($data);
   	
         return $this->load->view('sites/people/edit', NULL, TRUE);
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
   function _edit($site_id, $username)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      if ($username == '')
      {
         show_error('_edit_people requires that a username be supplied.');
      }
      
      $fields = $this->validation->_fields;
      unset($fields['Username']);
      unset($fields['GroupName']);
      unset($fields['ResendEmail']);
      
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
      
      redirect("sites/people/index/".$site_id);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add user permissions to access site
    *
    */
   function _add_people_site($username, $site_id)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      // add default permissions to site functions
      foreach ($this->aco['sites'] AS $aco)
      {
         if ($aco[2] == 'ALLOW')
         {
            $this->tacl->add_permission('member', $username, $site_id.'-'.$aco[0], $aco[1]);
         }
      }
      
      // grant access to any activated modules in the site
      $this->load->database('read');
      
      $sql = 'SELECT * FROM adm_site_module '.
             'WHERE SiteID = '.$site_id;
      $query = $this->db->query($sql);
      $actives = $query->result_array();
      
      foreach ($actives AS $active)
      {
         $this->tacl->add_permission('member', $username, $site_id.'-'.$active['ModuleID'], 'access');
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a sites's permissions list
    *
    */
   function assign($site_id, $this_action) 
   {
      $this->load->helper(array('form', 'text'));
      
      $admin['message'] = $this->session->userdata('people_message');
      if ($this->session->userdata('people_message') != '')
         $this->session->set_userdata('people_message', '');

      $this->load->model('Sites');
      $this->load->model('People');
      $this->load->model('Permissions');
      $this->load->library('validation');

      $site = $this->Sites->get_site_data($site_id);
      $user_list = $this->People->get_users_companies();
      
      $admin['user_exists'] = (count($user_list) == 0) ? FALSE : TRUE;
      
      foreach ($user_list AS $company)
      {
         foreach ($company['people'] AS $user)
         {
            $rules['user'.$user['UserID']] = 'trim';
            $fields['user'.$user['UserID']] = 'User #'.$user['UserID'];
            $defaults['user'.$user['UserID']] = 0;
         }
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);

      $assigned = $this->People->get_site_users_companies($site_id);
      foreach ($assigned AS $company)
      {
         foreach ($company['people'] AS $user)
         {
            $defaults['user'.$user['UserID']] = 1;
         }
      }

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'People &amp; Permissions');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['user_list'] = $user_list;
         $data['admin'] = $admin;

         $this->load->vars($data);
   	
         return $this->load->view('sites/people/assign', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_assign($site_id, $assigned);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the assign category form
    *
    */
   function _assign($site_id, $assigned)
   {
      if ($site_id == '')
      {
         show_error('_assign requires that a site ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
      {
         $user_id = substr($key, 4);

         $was_assigned = FALSE;
         foreach ($assigned AS $company)
         {
            foreach ($company['people'] AS $user)
            {
               if ($user['UserID'] == $user_id)
                  $was_assigned = TRUE;
            }
         }

         $is_assigned = ($this->input->post($key) == 1) ? TRUE : FALSE;

         if ($was_assigned && ! $is_assigned)
         {
            // it has been unchecked, delete permission
            $username = $this->People->get_username($user_id);
            $this->Permissions->delete_permissions($username, $site_id);
         }
         elseif ( ! $was_assigned && $is_assigned)
         {
            // it has been newly checked, insert permission
            $username = $this->People->get_username($user_id);
            $this->Permissions->add_permissions($username, $site_id);
         }
      }

      $this->session->set_userdata('people_message', 'The permissions for this site have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('sites/people/assign/'.$site_id.'/'. $last_action.'/');
   }

}
?>