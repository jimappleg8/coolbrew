<?php
/**
 * Auth Controller Class
 *
 * Security controller that provides functionality to handle logins, logout, registration
 * and forgotten password requests.  
 * It also can verify the logged in status of a user and his permissions.
 *
 * The class requires the use of the DB_Session and FreakAuth libraries.
 *
 * @package     FreakAuth_light
 * @subpackage  Controllers
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license      http://www.gnu.org/licenses/lgpl.html
 * @inpiredFrom Auth class by Jaapio (fphpcode.nl)
 * @link       http://4webby.com/FreakAuth
 * @version    1.0.2-Beta
 *
 */

class Auth extends Controller
{   
   /**
    * Initialises the controller
    *
    * @return Auth
    */
   function Auth()
   {
      parent::Controller();
        
      //loads necessary libraries
      $this->lang->load('freakauth');
      $this->load->model('Usermodel');
      $this->load->library('validation');
      $this->validation->set_error_delimiters($this->config->item('FAL_error_delimiter_open'), $this->config->item('FAL_error_delimiter_close'));
      
      //sets the necessary form fields
      $fields['user_name'] = $this->lang->line('FAL_user_name_label');
      $fields['password'] = $this->lang->line('FAL_user_password_label');
      $fields['password_confirm'] = $this->lang->line('FAL_user_password_confirm_label');
      $fields['email'] = $this->lang->line('FAL_user_email_label');
      $fields['security'] = $this->lang->line('FAL_user_security_code_label');
        
      //if activated in config, sets the select country box
      if ($this->config->item('FAL_use_country'))
      {
         $fields['country_id'] = $this->lang->line('FAL_user_country_label');
      }
        
      //-------------------------------------
      //ADD MORE FIELDS HERE IF YOU NEED THEM
      //-------------------------------------
      $fields = array_merge($fields, $this->config->item('FAL_user_profile_fields_names'));
        
      $this->validation->set_fields($fields);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Checks if the user meets all specified requirements.
    *
    */
   function check()
   {
      // (string) the role to check
      $role = $this->tag->param(1, null);
       
      // (bool) whether to deny access to higher roles
      $only = $this->tag->param(2, null);
       
      return $this->freakauth_light->check($role, $only);
   }
    
   // --------------------------------------------------------------------
   
    /**
     * Checks if the user is an admin user.
     *
     */
   function is_admin()
   {
      return $this->freakauth_light->isAdmin();
   }
    
   // --------------------------------------------------------------------
    
   /**
    * Checks if the user is a Super Amin user.
    *
    */
   function is_super_admin()
   {
      return $this->freakauth_light->isSuperAdmin();
   }
    
   // --------------------------------------------------------------------
    
   /**
    * Checks if the user is logged in.
    *
    */
   function is_valid_user()
   {
      return $this->freakauth_light->isValidUser();
   }
    
   // --------------------------------------------------------------------
    
   /**
    * Checks if the user belongs to the given group or groups
    *
    */
   function belongs_to_group($_group=null, $_only=null)
   {
      // (string) the group to check
      $group = $this->tag->param(1, null);
       
      // (bool) whether to deny access to higher roles
      $only = $this->tag->param(2, null);

      return $this->freakauth_light->belongsToGroup($group, $only);
   }

   // --------------------------------------------------------------------
   
   /**
    * Displays the login form.
    *
    */
   function login()
   {   
      // if an user, admin or superadmin is already logged in   
      if ($this->freakauth_light->isValidUser() OR $this->freakauth_light->isAdmin())
      {
         // Display user name and an 'already logged in' flash message...
         $msg = $this->session->userdata('username').', you have already logged in!';
         $this->session->set_flashdata('flashMessage', $msg, 1);

         //redirects to homepage
         // Change, Begin - coolbrew: adjust redirect
         /*
         // Original
         redirect ('', 'location');
         // Original
         */
         header('location:'.$this->config->item('base_url'));
         // Change, End
       }
       else
       {        
          $this->_login();
       }       
   }
    
   // --------------------------------------------------------------------
   
   /**
    * Handles the post from the login form.
    *
    */
   function _login()
   {
      $rules['user_name'] = $this->config->item('FAL_user_name_field_validation_login');
      $rules['password'] = $this->config->item('FAL_user_password_field_validation_login');
        
      //-------------------------------------
      //ADD MORE RULES HERE IF YOU NEED THEM
      //-------------------------------------
         
      // do we want chaptcha for login?
      if ($this->config->item('FAL_use_security_code_login'))
      {
         $rules['security'] = $this->config->item('FAL_user_security_code_field_validation_register');
      }
      
      $this->validation->set_rules($rules);
        
      //everything went ok login the user and redirect him to the homepage
      if ($this->validation->run() && $this->freakauth_light->login())
      {
         $role= $this->session->userdata('role');
            
         switch ($role)
         {
            case ('superadmin'):
            case ('admin'):
               // On success redirect user to default page
               // Change, Begin - coolbrew: redirect to page
               /*
               // Original
               redirect($this->config->item('FAL_admin_login_success_action'), 'location');
               // Original
               */
               header('location:'.$this->config->item('base_url').$this->config->item('FAL_admin_login_success_action'));
               // Change, End
               break;
                  
            default:
               // On success redirect user to default page
               // Change, Begin - coolbrew: redirect to page
               /*
               // Original
               redirect($this->config->item('FAL_login_success_action'), 'location');
               // Original
               */
               header('location:'.$this->config->item('base_url').$this->config->item('FAL_login_success_action'));
               // Change, End
               break;
         }
      }
      else  // display the login form again
      {   
         //page title
         $data['heading']='login';
           
         if ($this->config->item('FAL_use_security_code_login'))
         {   
            $action='_login';
            $this->freakauth_light->captcha_init($action);
            $data['captcha'] = $this->config->item('FAL_security_code_image');
         }

         $data['page']= $this->config->item('FAL_login_view');
               
         $this->load->vars($data);
   
         $this->load->view($this->config->item('FAL_template_dir').'template/container');
         
         //$this->output->enable_profiler(TRUE);
      }
   }

   // --------------------------------------------------------------------
    
   /**
    * Handles the logout action.
    *
    */
   function logout()
   {
      $this->freakauth_light->logout();
   }
    
   // --------------------------------------------------------------------
   
   /**
    * Handles the post from the registration form.
    *
    */
    
   function register()
   {
      // Addition, Begin - coolbrew: add activation toggle
      // (bool) Whether to require activation
      $require_activation = $this->tag->param(1, TRUE);
      // Addition, End
      
      // if users are not allowed to register
      if (!$this->config->item('FAL_allow_user_registration'))
      {
         redirect('auth/login', 'location');
      }
      else // if they are allowed to register
      {
         // set validation rules
         $rules['user_name'] = $this->config->item('FAL_user_name_field_validation_register');
         $rules['password'] = $this->config->item('FAL_user_password_field_validation_register');
         $rules['password_confirm'] = $this->config->item('FAL_password_required_confirm_validation')."|matches[".'password'."]";
         $rules['email'] = $this->config->item('FAL_user_email_field_validation_register');
        
         // do we also want to know the user country?
         if ($this->config->item('FAL_use_country'))
         {
            $rules['country_id'] = $this->config->item('FAL_user_country_field_validation_register');
         }

         // do we also want to secure the registration with CAPTCHA?
         if ($this->config->item('FAL_use_security_code_register'))
         {
            $rules['security'] = $this->config->item('FAL_user_security_code_field_validation_register');
         }
        
         //additionalRegistrationRules($rules);
         $rules = array_merge($rules, $this->config->item('FAL_user_profile_fields_validation_rules'));
         
         $this->validation->set_rules($rules);
        
         // if everything went ok
         // Change, Begin - coolbrew: add no activation option
         /*
         // Original
         if ($this->validation->run() && $this->freakauth_light->register())
         {
            $data = array(
               'heading' => 'Registration',
               'page' => $this->config->item('FAL_register_success_view')
            );

            $this->load->vars($data);

            $this->load->view($this->config->item('FAL_template_dir').'template/container');
            //$this->output->enable_profiler(TRUE);
         // Original
         */
         if ($this->validation->run() && $this->freakauth_light->register($require_activation))
         {
            if ($require_activation)
            {
               $data = array(
                  'heading' => 'Registration',
                  'page' => $this->config->item('FAL_register_success_view')
               );
               $this->load->vars($data);
               $this->load->view($this->config->item('FAL_template_dir').'template/container');
            }
            else
            {
               $data = array(
                  'heading' => 'Registration',
                  'page' => $this->config->item('FAL_register_activation_success_view')
               );
               $this->load->vars($data);
               $this->load->view($this->config->item('FAL_template_dir').'template/container');
            }
         // Change, End
         }
         else // redisplay the register form
         {   
            // if we want to know the user country let's populate the select menu       
            if ($this->config->item('FAL_use_country'))
            {
               $this->load->model('country'); 
               //SELECT * FROM country
               $data['countries'] = $this->country->getCountriesForSelect();
            }

            // if we want to secure the registration with CAPTCHA let's generate it
            if ($this->config->item('FAL_use_security_code_register'))
            {   
               $action='_register';
               $this->freakauth_light->captcha_init($action);
               $data['captcha'] = $this->config->item('FAL_security_code_image');
            }
              
            //displays the view
            $data['heading'] = 'register';
            $data['page'] = $this->config->item('FAL_register_view');
   
            $this->load->view($this->config->item('FAL_template_dir').'template/container', $data);
         
            //$this->output->enable_profiler(TRUE);
   
            //$this->session->flashdata_mark();
         }
      }
   }
    
   // --------------------------------------------------------------------
    
   /**
    * Handles the user activation.
    *
    */
   function activation($id, $activation_code)
   {   
      // passes the URI segments to freakauth-ligh [UserTemp id segment(3) and the activation code segment(4)]
      // if the activation is successfull displays the success page 
      if ($this->freakauth_light->activation($id, $activation_code))
      {
         $data = array(
            'heading' => 'Registration',
            'page' => $this->config->item('FAL_register_activation_success_view')
         );

         $this->load->vars($data);

         $this->load->view($this->config->item('FAL_template_dir').'template/container');
      }
      else // if activation unsuccessful redisplay the failure view message
      {
         $data = array(
            'heading' => 'Registration',
            'page' => $this->config->item('FAL_register_activation_failed_view')
         );

         $this->load->vars($data);

         $this->load->view($this->config->item('FAL_template_dir').'template/container');
      }
   }
    
   // --------------------------------------------------------------------
   
   /**
    * Handles the post from the forgotten password form.
    *
    */
   function forgotten_password()
   {   
      // Addition, Begin - coolbrew: add activation toggle
      // (bool) Whether to require activation
      $require_activation = $this->tag->param(1, TRUE);
      // Addition, End
      
      // set necessary validation rules
      $rules['email'] = "trim|required|valid_email|xss_clean|callback__email_exists_check";
         
      // do we also want CAPTCHA?
      if ($this->config->item('FAL_use_security_code_forgot_password'))
      {
         $rules['security'] = $this->config->item('FAL_user_security_code_field_validation_register');
      }
        
      $this->validation->set_rules($rules);
        
      // if it got post data and they validate display the success page
      // Change, Begin - coolbrew: add no activation option
      /*
      // Original
      if ($this->validation->run() && $this->freakauth_light->forgotten_password())
      {           
         $data['heading'] = 'Remember password';
         $data['page'] = $this->config->item('FAL_forgotten_password_success_view');

         $this->load->vars($data);

         $this->load->view($this->config->item('FAL_template_dir').'template/container');
      // Original
      */
      if ($this->validation->run() && $this->freakauth_light->forgotten_password($require_activation))
      {
         if ($require_activation)
         {
            $data = array(
               'heading' => 'Remember password',
               'page' => $this->config->item('FAL_forgotten_password_success_view')
            );
            $this->load->vars($data);
            $this->load->view($this->config->item('FAL_template_dir').'template/container');
         }
         else
         {
            $data = array(
               'heading' => 'Remember password',
               'page' => $this->config->item('FAL_forgotten_password_reset_success_view')
            );
            $this->load->vars($data);
            $this->load->view($this->config->item('FAL_template_dir').'template/container');
         }
      // Change, End
      }
      else // display the initial forgotten password form 
      {
         $this->session->flashdata_mark();
           
         // do we want captcha
         if ($this->config->item('FAL_use_security_code_forgot_password'))
         {
            $action='_forgot_password';
            $this->freakauth_light->captcha_init($action);
            $data['captcha'] = $this->config->item('FAL_security_code_image');
         }
            
         // display the form
         $data['heading'] ='Remember password';
         $data['page'] = $this->config->item('FAL_forgotten_password_view');

         $this->load->view($this->config->item('FAL_template_dir').'template/container', $data);
      }
   }
    
   // --------------------------------------------------------------------
   
   /**
    * Displays the forgotten password reset.
    *
    */
   function forgotten_password_reset($id, $activation_code)
   {   
      // if password has been successfully reset (randomly generate, ins in DB and sent to the user)
      // display success
      if ($this->freakauth_light->forgotten_password_reset($id, $activation_code))
      {
         $data = array(
            'heading' => 'Remember password',
            'page' => $this->config->item('FAL_forgotten_password_reset_success_view')
         );

         $this->load->vars($data);
         $this->load->view($this->config->item('FAL_template_dir').'template/container');
            
      }
      else // tell the user about the problems and display unsuccess view
      {
         $data = array(
            'heading' => 'Remember password',
            'page' => $this->config->item('FAL_forgotten_password_reset_failed_view')
         );

         $this->load->vars($data);
         $this->load->view($this->config->item('FAL_template_dir').'template/container');
      }
   }

    
   // --------------------------------------------------------------------
    
   /**
    * Function that handles the change password procedure
    * needed to let the user set the password he wants after the
    * forgotten_password_reset() procedure
    *
    */
   function changepassword()
   {
      $rules['user_name'] = $this->config->item('FAL_user_name_field_validation_login');
      // old password
      $rules['password'] = $this->config->item('FAL_user_password_field_validation_login');
      // new password
      $rules['new_password'] = $this->config->item('FAL_password_required_validation');  
      // new password confirmation
      $rules['password_confirm'] = 'trim|required|xss_clean|matches[new_password]';
        
      $this->validation->set_rules($rules);
        
      // sets the necessary form fields
      $fields['new_password'] = 'new_password';
        
      $this->validation->set_fields($fields);
        
      // if it got post data and they validate display the success page
      if ($this->validation->run() && $this->freakauth_light->_change_password())
      {           
         // set FLASH MESSAGE
         $msg = $this->lang->line('FAL_change_password_success');
         $this->session->set_flashdata('flashMessage', $msg, 1);
         // Change, Begin - coolbrew: redirect to page
         /*
         // Original
         redirect('', 'location');
         // Original
         */
         header('location:'.$this->config->item('base_url').'login.php');
         // Change, End
         
      }
      else // else display the initial change password form 
      {   
         // page title            
         $data['heading'] = 'Change password';
         // page content
         $data['page'] = $this->config->item('FAL_change_password_view');

         $this->load->vars($data);
         //page display
         $this->load->view($this->config->item('FAL_template_dir').'template/container');
      }
   }
    
   // --------------------------------------------------------------------

   /**
    * RULES HELPER FUNCTION
    * Password validation callback for password validation
    * 
    * @access private
    * @param varchar $value
    * @return boolean
    * 
    */
   
   function _password_check($value)
   {   
      $callback = '_password_check';
       return $this->_is_valid_text($callback, $value, $this->config->item('FAL_user_password_min'), $this->config->item('FAL_user_password_max'));
   }
   
   // --------------------------------------------------------------------
   
    /**
     * RULES HELPER FUNCTION
     * Security code validation callback for validation
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
   function _securitycode_check($value)
   {
       if ($this->config->item('FAL_use_security_code_register') OR $this->config->item('FAL_use_security_code_login') OR $this->config->item('FAL_use_security_code_forgot_password'))
       {
           //gets the security code stored in the session
          $securityCode = $this->session->userdata('FreakAuth_security_code');
         
          if ($this->config->item('FAL_security_code_case_sensitive')==FALSE)
          {
             $control= strcmp(strtolower($value), strtolower($securityCode));
          }
          else {$control= strcmp($value, $securityCode);}
          
          //compares the security code provided in the input field with that stored in session
           if ($control != 0)
           {
               $this->validation->set_message('_securitycode_check', $this->lang->line('FAL_captcha_message'));
              return false;
          }
       }
      
      return true;
   }
   
   // --------------------------------------------------------------------

    /**
     * RULES HELPER FUNCTION
     * User name validation callback for validation against min-max length settings
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _username_check($value)
   {   
      $callback = '_username_check';
       return $this->_is_valid_text($callback, $value, $this->config->item('FAL_user_name_min'), $this->config->item('FAL_user_name_max'));
   }
   
   // --------------------------------------------------------------------

    /**
     * RULES HELPER FUNCTION
     * User name validation callback for login
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _username_login_check($value)
   {   
      $this->username = $value; 
      
      //Use the input username and checks against 'users' table
        $query = $this->usermodel->getUserByUsername($value);
   
        if (($query != null) && ($query->num_rows() == 0))
       {
           $this->validation->set_message('_username_login_check', $this->lang->line('FAL_invalid_username_message'));
          return false;
      }
      
      else {return true;}
         
   }
   
   // --------------------------------------------------------------------

    /**
     * RULES HELPER FUNCTION
     * Password validation callback for login
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _password_login_check($value)
   {   
      if (isset($this->username) AND $this->username!='')
      {
           //encrypts the random password using the md5 encryption
           $encrypted_password = $this->freakauth_light->_encode($value);                    
           $query = $this->usermodel->getUserForLogin($this->username , $encrypted_password);
      
           if (($query != null) && ($query->num_rows() == 0))
          {
              $this->validation->set_message('_password_login_check', $this->lang->line('FAL_invalid_password_message'));
             return false;
         }
         else {return true;}
      }
      else 
      {
         $this->validation->set_message('_password_login_check', $this->lang->line('FAL_username_first_password_message'));
         return false;
      }
      
   }
   
   // --------------------------------------------------------------------

    /**
     * RULES HELPER FUNCTION
     * Password validation callback for change password
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _password_change_check($value)
   {              
          $fields='id';    
         //encrypts the random password using the md5 encryption
           $encrypted_password = $this->freakauth_light->_encode($value); 
         //WHERE password=$encrypted_password
           $where = array('password' =>$encrypted_password);
                   
           $query = $this->usermodel->getUsers($fields, $limit=null, $where);
      
           if (($query != null) && ($query->num_rows() == 0))
          {
              $this->validation->set_message('_password_change_check', $this->lang->line('FAL_invalid_password_message'));
             return false;
         }
         else {return true;}
      
   }
   
   // --------------------------------------------------------------------
   
    /**
     * RULES HELPER FUNCTION
     * User name duplicate validation callback for validation against duplicate username in DB
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _username_duplicate_check($value)
   {
       //Use the input username and check against 'users' table
        //query in main user table (users already activated)
        $query = $this->usermodel->getUserByUsername($value);
        //query in temporary user table (users waiting for activation)
        $this->load->model('Usertemp', 'UserTemp');
        $fields='id';
        $where=array('user_name'=>$value);
        $query_temp = $this->UserTemp->getUserTempWhere($fields, $where);

        if (($query != null) && ($query->num_rows() > 0) OR ($query_temp != null) && ($query_temp->num_rows() > 0))
       {
           $this->validation->set_message('_username_duplicate_check', $this->lang->line('FAL_in_use_validation_message'));
          return false;
      }
      
      return true;
   }
   
   // --------------------------------------------------------------------
   
    /**
     * RULES HELPER FUNCTION
     * User name duplicate validation callback for validation against duplicate username in DB
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _email_duplicate_check($value)
   {
       //Use the input e-mail and check against 'users' table
        //query in main user table (users already activated)
        $query = $this->usermodel->getUserForForgottenPassword($value);
        //query in temporary user table (users waiting for activation)
      $fields='id';
        $where=array('email'=>$value);
        $query_temp = $this->UserTemp->getUserTempWhere($fields, $where);
        
        if (($query != null) && ($query->num_rows() > 0))
       {
           $this->validation->set_message('_email_duplicate_check', 'A user with this e-mail has already registered. If you have forgotten your login details you can get them here');
          return false;
      }
      
       if (($query_temp != null) && ($query_temp->num_rows() > 0))
       {
           $this->validation->set_message('_email_duplicate_check', 'A user with this e-mail has already registered and is waiting for activation. If this is your e-mail address please check your e-mail inbox and activate your ');
          return false;
      }
      
      
      return true;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Checks if the e-mail is already in use 
    * namely if an user in DB has the same e-mail
    *
    * @param string $value
    * @return unknown
    */
   function _email_exists_check($value)
   {
       //Use the input e-mail and check against 'users' table
        //query in main user table (users already activated)
         $query = $this->usermodel->getUserForForgottenPassword($value);


        if (($query != null) && ($query->num_rows() == 0))
       {
           $this->validation->set_message('_email_exists_check', $this->lang->line('FAL_forgotten_password_user_not_found_message'));
          return false;
      }      
      
      return true;
   }
   
   // --------------------------------------------------------------------
   
    /**
     * RULES HELPER FUNCTION
     * Checks if at least 1 country has been chosen in the select country form element
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _country_check($country_id)
   {
       if ($this->config->item('FAL_use_country'))
       {
           if ($country_id == 0)
           {
               $this->validation->country_id= $country_id;
              $this->validation->set_message('_country_check', $this->lang->line('FAL_country_validation_message'));
              return FALSE;
          }
       }
      
      return true;
   }
   
   
   // --------------------------------------------------------------------
   
    /**
     * RULES HELPER FUNCTION
     * Determines if a input text has valid characters and meets min/max length requirements
     *
     * @access private
     * @param unknown_type $callback
     * @param varchar $value
     * @param integer $min
     * @param integer $max
     * @param varchar $invalid_message
     * @param unknown_type $expression
     * @return boolean
     */
    function _is_valid_text($callback, $value, $min, $max, $invalid_message = null, $expression = '/^([a-z0-9])([a-z0-9_\-])*$/ix')
   {
       $message = '';
       if ((strlen($value) < $min) ||
           (strlen($value) > $max))
           $message .= sprintf($this->lang->line('FAL_lenght_validation_message'), $min, $max);
           
       if (!preg_match($expression, $value))
           $message .= $this->lang->line('FAL_allowed_characters_validation_message');
      
      if ($message != '')
      {
          if (!isset($invalid_message))
              $invalid_message = $this->lang->line('FAL_invalid_validation_message');
          $this->validation->set_message($callback, $invalid_message.$message);
           return false;
      }
      
      return true;
   }
}
?>