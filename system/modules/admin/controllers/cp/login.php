<?php

class Login extends Controller {

   function Login()
   {
      parent::Controller();
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------
  
   /**
    * Log into the system
    *
    * TODO: make sure that cookies are enabled? WordPress does.
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

      return $this->load->view('cp/login/login', NULL, TRUE);
  
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
      $this->session->unset_userdata('usercode');
      $this->session->unset_userdata('group');
      header('Location:'.base_url().'/login.php');
   }

   // --------------------------------------------------------------------
   
   /**
    * Display the "Sorry you don't have access" page.
    *
    */
   function sorry()
   {
      $this->collector->append_css_file('admin_adm');
      $this->collector->append_css_file('login');
      
      return $this->load->view('cp/login/sorry', NULL, TRUE);
   }

}
?>