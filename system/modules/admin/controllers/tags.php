<?php

class Admin_Tags extends Controller {

   function Admin_Tags()
   {
      parent::Controller();
      $this->load->library('session');
   }
   
   // ---------------------------------------------------------------------
   
   function check_login($action = "check")
   {
      $this->load->helper('url');
      
      $result = array();
      
      if ($this->uri->segment(3) == 'login')
      {
         $this->session->set_flashdata('return_url', site_url());
         header('Location:'.base_url().'/login.php');
         exit;
      }
      elseif ($this->uri->segment(3) == 'edit_account')
      {
         $last_action = $this->session->userdata('last_action') + 1;
         header('Location:'.base_url().'edit-account.php/'.$last_action);
         exit;
      }
      elseif ($this->uri->segment(3) == 'logout')
      {
         $this->session->unset_userdata('username');
         $this->session->unset_userdata('name');
         $this->session->unset_userdata('usercode');
         $this->session->unset_userdata('group');
         header('Location:'.site_url());
         exit;
      }
      
      $username = $this->session->userdata('username');
      
      if ($username == '')
      {
         $result['is_logged_in'] = FALSE;
         $result['username'] = '';
         $result['name'] = '';
         $result['last_action'] = 0;
      }
      else
      {
         $result['is_logged_in'] = TRUE;
         $result['username'] = $username;
         $result['name'] = $this->session->userdata('name');
         $result['last_action'] = $this->session->userdata('last_action') + 1;
      }

      // this gives the page without the login/logout attached.
      $result['index_page'] = '/'.index_page();
      
      // if this is the login or edit info page, keep the flash data
      if ($result['index_page'] == '/login.php' || $result['index_page'] == '/edit-account.php')
      {
         $this->session->keep_flashdata('return_url');
      }
      
      return $result;
   }

   // ---------------------------------------------------------------------
   
   function login_form()
   {
      $this->load->helper(array('form', 'url', 'text'));
      $this->load->library('validation');

      // figure out the redirect
      if ($this->input->post('return_url'))
      {
         $data['return_url'] = $this->input->post('return_url');
      }
      elseif ($this->session->flashdata('return_url') != '')
      {
         $data['return_url'] = $this->session->flashdata('return_url');
      }
      else
      {
         $data['return_url'] = site_url();
      }
      
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
         
         $this->load->model('People');

         $row = $this->People->get_user_login($values['Username'], $values['Password']);
      
         if ($row != FALSE)
         {
            $this->session->set_userdata('username', $row['Username']);
            $this->session->set_userdata('name', $row['FirstName'].' '.$row['LastName']);
            $this->session->set_userdata('usercode', $row['ID'].'-person');
            $this->session->set_userdata('group', $this->People->get_user_group($row['ID'].'-person'));

            // check if this is their first login
            if ($row['FirstLogin'] == '')
               $update['FirstLogin'] = date('Y-m-d H:i:s');
         
            $update['LastLogin'] = date('Y-m-d H:i:s');

            $this->db->where('Username', $values['Username']);
            $this->db->update('adm_person', $update);
            
            header('Location:'.$data['return_url']);
         }
         else
         {
            $data['error_msg'] = "The username and/or password you entered is invalid.";
         }
      }
      else
      {
//         echo '<pre>'; print_r($_POST); echo '</pre>';
//         echo '<pre>'; print_r($this->validation); echo '</pre>';
      }
      
      $this->load->vars($data);
      return $this->load->view('login_form', NULL, TRUE);
   }

   //-------------------------------------------------------------------------
   
   /**
    * Creates the support form
    *
    */
   function support_form()
   {
      $form_html = "";
      $display_response = false;

      $this->load->helper(array('form','url'));
      $this->load->model('Sites');

      $this->load->library('validation');

      $rules['FName'] = 'trim|required|max_length[25]';
      $rules['LName'] = 'trim|required|max_length[25]';
      $rules['Phone'] = 'trim|required';
      $rules['Email'] = 'trim|required|valid_email';
      $rules['Website'] = 'trim|required';
      $rules['Issue'] = 'trim|required';
      $rules['Comment'] = 'trim|required';

      // NOTE: vMaliciousAttack should be added when I can determine how 
      // to do it. I'd like to see if there's a way to test multiple fields.

      $this->validation->set_rules($rules);

      $fields['FName'] = 'First Name';
      $fields['LName'] = 'Last Name';
      $fields['Phone'] = 'Phone';
      $fields['Email'] = 'Email';
      $fields['Website'] = 'Website';
      $fields['Issue'] = 'Issue';
      $fields['Comment'] = 'Message';

      $this->validation->set_fields($fields);
      
      // if the user is already logged in, auto-fill some fields
      $username = $this->session->userdata('username');
      
      $defaults = array();
      if ($username != '')
      {
         $this->load->model('People');
         $user = $this->People->get_user_data($username);
         $defaults['FName'] = $user['FirstName'];
         $defaults['LName'] = $user['LastName'];
         $extension = ($user['OfficePhoneExt'] != '') ? ' x'.$user['OfficePhoneExt'] : '';
         $defaults['Phone'] = $user['OfficePhone'].$extension;
         $defaults['Email'] = $user['Email'];
      }
      
      $websites = $this->Sites->get_sites_list(TRUE);
      $websites[''] = '-- Choose an website --';
      
      $issues = array(
         '' => '-- Choose an issue --',
         'Project' => 'Project',
         'New Product' => 'New Product',
         'Product Update' => 'Product Update',
         'Typo Correction' => 'Typo Correction',
         'Bug Fix' => 'Bug Fix',
         'Store Locator Update' => 'Store Locator Update',
         'Other' => 'Other',
         );
         
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_support_form();
         $display_response = true;
      }

      $data['siteid'] = SITE_ID;
      $data['websites'] = $websites;
      $data['issues'] = $issues;
         
      $form_html = $this->load->view('support_form', $data, TRUE);

      $results[0] = $display_response;
      $results[1] = $form_html;

      return $results;
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from 'contact' form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _support_form()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
      {
         $values[$key] = $this->input->post($key);
      }

      $values['DateSubmitted'] = date('Y-m-d h:i:s');
      $values['Subject'] = strtoupper($values['Website']).' '.$values['Issue'];
      unset($values['Website']);
      unset($values['Issue']);
      
      $this->load->database('write');
      $this->db->insert('aa_support', $values);
         
      // send e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail))
      {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      // send the email internally
      $mail_content = $this->load->view('support_mail', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   
      // send reply to user
      $mail_content2 = $this->load->view('support_reply', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);

      // send safe copies to internal folks (to avoid auto-reply issues)
      $mail_content3 = $this->load->view('support_safe', $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content3)."\n");
      pclose($fd);
      
      $this->session->set_userdata('info_message', 'Your message has been sent! You may submit another ticket below.');

      header('Location:'.base_url().'support/submit-a-ticket.php');
      exit;
   }

   // ---------------------------------------------------------------------
   
   /**
    * Updates a person's account
    *
    */
   function edit_account($this_action) 
   {
      $username = $this->session->userdata('username');

      $data['message'] = $this->session->userdata('info_message');
      if ($this->session->userdata('info_message') != '')
         $this->session->set_userdata('info_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('People');
      $this->load->library(array('auditor', 'validation'));
      
      // get the data from current record
      $old_values = $this->People->get_user_data($username);

      $rules['FirstName'] = 'trim|required';
      $rules['LastName'] = 'trim|required';
      $rules['Email'] = 'trim|required';
      $rules['Title'] = 'trim';
      $rules['OfficePhone'] = 'trim';
      $rules['OfficePhoneExt'] = 'trim';
      $rules['MobilePhone'] = 'trim';
      $rules['FaxPhone'] = 'trim';
      $rules['HomePhone'] = 'trim';
      $rules['IMName'] = 'trim';
      $rules['IMService'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['FirstName'] = 'First Name';
      $fields['LastName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Username'] = 'Username';
      $fields['Password'] = 'Password';
      $fields['Title'] = 'Title';
      $fields['OfficePhone'] = 'Office Phone';
      $fields['OfficePhoneExt'] = 'Office Phone Extension';
      $fields['MobilePhone'] = 'Mobile Phone';
      $fields['FaxPhone'] = 'Fax';
      $fields['HomePhone'] = 'Home Phone';
      $fields['IMName'] = 'IM Name';
      $fields['IMService'] = 'IM Service';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      $defaults['FirstName'] = entities_to_ascii($defaults['FirstName']);
      $defaults['LastName'] = entities_to_ascii($defaults['LastName']);
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['IMName'] = entities_to_ascii($defaults['IMName']);
      unset($defaults['Password']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin-styles');

         $data['return_url'] = $this->session->flashdata('return_url');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['im_services'] = $this->People->get_im_services_list();

         $this->session->set_flashdata('return_url', $data['return_url']);

         $this->load->vars($data);
   	
         return $this->load->view('account_edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit_account($username, $old_values);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit account form
    *
    */
   function _edit_account($username, $old_values)
   {
      $fields = $this->validation->_fields;
      unset($fields['Username']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // process the form text (convert special characters and the like)
      $values['FirstName'] = ascii_to_entities($values['FirstName']);
      $values['LastName'] = ascii_to_entities($values['LastName']);
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['IMName'] = ascii_to_entities($values['IMName']);
      
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
      
      $this->load->database('read');
      
      $tmp = $this->db->where('Username', $username);
      $this->db->update('adm_person', $values);   

      $this->auditor->audit_update('adm_person', $tmp->ar_where, $old_values, $values);
      
      $this->session->set_userdata('info_message', 'Your settings have been saved successfully.');

      $last_action = $this->session->userdata('last_action') + 1;
      header('Location:'.base_url().'edit-account.php/'.$last_action);
      exit;
   }

   // ---------------------------------------------------------------------
   
   /**
    * Returns a person's account information
    *
    */
   function account_info() 
   {
      $username = $this->session->userdata('username');

      // get the data from current record
      $sql = 'SELECT * FROM adm_person '.
             'WHERE Username = \''.$username.'\' ';
      $query = $this->db->query($sql);
      $data['user'] = $query->row_array();
      
      return $this->load->view('account_info', $data, TRUE);
   }
   
   // ---------------------------------------------------------------------
   
   function site_header()
   {
      // (string) the site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) the tab that should be highlighted
      $active_tab = $this->tag->param(2, '');

      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->model('Sites');

      $site = $this->Sites->get_site_data($site_id);
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, $active_tab);
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/tabs', NULL, TRUE);
   }
}
?>