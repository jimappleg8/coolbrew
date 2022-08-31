<?php

class Login extends Controller {

   function Login()
   {
      parent::Controller();

      $this->load->library('session');

      $this->load->library('access', array('module_id' => 'admin'));
      
      $params = array(
                   'account_suffix' => '',
                   'base_dn' => 'DC=hvntdom, DC=hain-celestial, DC=com',
                   'domain_controllers' => array('capitals'),
                   'ad_username' => 'CN=Data Warehouse,OU=Service Accounts, OU=Boulder, OU=hvntdom,DC=hvntdom,dc=hain-celestial,dc=com',
                   'ad_password' => 'd8awar3z',
                   'real_primarygroup' => true,
                   'use_ssl' => false,
                   'recursive_groups' => true
                );
      $this->load->library('ad_ldap', $params);
   }
   
   // --------------------------------------------------------------------
  
   /**
    * Log into the system
    *
    */
   function login_user()
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

//         $values['Password'] = md5($values['Password']);

         // authenticate the user
         if ($this->ad_ldap->authenticate($values['Username'], $values['Password']))
         {
            $this->session->set_userdata('username', $values['Username']);
//            $this->session->set_userdata('name', $row['FirstName'].' '.$row['LastName']);
            
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
            $data['error_msg'] = "The username and/or password you entered is invalid.";
         }
      }
      
      $this->session->keep_flashdata('return_url');
      
      $this->collector->append_css_file('admin_adm');
      $this->collector->append_css_file('login');

      $this->load->vars($data);

      return $this->load->view('login/login', NULL, TRUE);
  
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
    * Display the "Sorry you don't have access" page.
    *
    */
   function sorry()
   {
      $this->CI->collector->append_css_file('admin_adm');
      $this->CI->collector->append_css_file('login');
      
      return $this->CI->load->view('login/sorry', NULL, TRUE);
   }

}
?>