<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * FreakAuth_light Class
 * Security handler that provides functionality to handle login, logout, registration,
 * and reset password requests.  
 * It also can verify the logged in status of 3 user classes
 * 
 * => superadmin (has permissions on everything and can also create other admin)
 * => admin       (you can choose what to let him manage)
 * => user       (it is a registered user, and you can decide to give in rights to access
 *               some specific areas (controllers) of your application
 *
 * The class requires the use of
 * 
 * => Database CI official library
 * => BD Ssession library (included in the download)
 * => URL, FORM and FreakAuth (included in the download) helpers
 * 
 * The FreakAuth_light library should be auto loaded in the core classes section
 * of the autoloader.
 * 
 * Passwords are encripted with md5 algorithm by the method _encode($password)
 * 
 * ---------------------------------------------------------------------------------
 * Copyright (C) 2007  Daniel Vecchiato (4webby.com)
 * ---------------------------------------------------------------------------------
 *This library is free software; you can redistribute it and/or
 *modify it under the terms of the GNU Lesser General Public
 *License as published by the Free Software Foundation; either
 *version 2.1 of the License, or (at your option) any later version.
 *
 *This library is distributed in the hope that it will be useful,
 *but WITHOUT ANY WARRANTY; without even the implied warranty of
 *MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *Lesser General Public License for more details.
 *
 *You should have received a copy of the GNU Lesser General Public
 *License along with this library; if not, write to the Free Software
 *Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *-----------------------------------------------------------------------------------
 * @package     FreakAuth_light
 * @subpackage  Libraries
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license      http://www.gnu.org/licenses/gpl.html
 * @inpiredFrom Auth class by Jaapio (fphpcode.nl)
 * @link       http://4webby.com/FreakAuth
 * @todo       store referrer page in session in order to redirect to the page where the call for login was originated
 * @version    1.0.2-Beta
 *
 */

// ------------------------------------------------------------------------

/**
 * Security handler that provides functionality to handle logins and logout
 * requests.  It also can verify the logged in status of a user and permissions.
 * The class requires the use of the Database and Encrypt CI libraries and the
 * URL, and CI helper.  It also requires the use of the 3rd party DB_Session
 * library.  The Auth library should be auto loaded in the core classes section
 * of the autoloader.
 * 
 * Passwords are encripted with md5 algorithm.
 */
class Freakauth_light
{   
   // --------------------------------------------------------------------
   
   /**
   * Function FreakAuth inizialises the class loading the right libraries, helpers and models
   * 
   * @uses libraries (encrypt, session), helpers (form, url, FreakAuth), modules (Usermodel)
   * */
    function Freakauth_light()
    {
        $this->CI=& get_instance();

        log_message('debug', "FreakAuth Class Initialized");

        $this->CI->load->library('session');
        $this->CI->load->helper('form');
        $this->CI->load->helper('url');
        $this->CI->load->helper('freakauth_light');
        $this->CI->load->model('FreakAuth_light/usertemp', 'UserTemp');
        $this->CI->load->model('Usermodel', 'usermodel'); 

        $this->_init();
    }
   
    // --------------------------------------------------------------------
    
    /**
     * Initializes the security settings and checks for autologin
     *
     * @return boolean
     */
    function _init()
    {
        //checks if the Freakauth system is turned on
       if (!$this->CI->config->item('FAL'))
       {
            echo '<h1>The website '.$this->CI->config->item('FAL_website_name').' is actually down for maintanance.</h1>';
            
            exit;
       }

    }

   // --------------------------------------------------------------------
       
   /**
    * Method used to restrict access to controllers or methods of controllers to the specified category of users
    * it requires 2 optional parameters
    * The first parameterspecifies the user group i.e. ('admin')
    * The second parameter specifies whether the area is reserved ONLY to that group (true) or if it is accessible by groups higher in the hierarchy
    * 
    * example usage in a controller
    * 
    * 1) $this->freakauth_light->check()  //this restricts access to registered users and user-groups higher in the hierarchy (i.e. admin, superadmin)
    * 2) $this->freakauth_light->check('admin')  //this restricts access to 'admin' and user-groups higher in the hierarchy (i.e. superadmin)
    * 3) $this->freakauth_light->check('admin', true)  //this restricts access to 'admin' ONLY
    * 
    * @param string (to specify the role to whom the area is restricted to) $lock_to_role
    * @param boolean (true/false) $only
    */
   function check($_lock_to_role=null, $_only=null)
   {
      // check who did the request and build role hierarchy
      $_who_is = $this->CI->session->userdata('role');
           
      // if we have a role stored in DB session for this user
      if ($this->CI->session AND $this->CI->config->item('FAL') AND !empty($_who_is))
      {   
         // gets the locked role hierarchy value
         $_hierarchy = $this->CI->config->item('FAL_roles');
               
         // if we didn't specify to who we will reserve the action
         // let's restrict it to registered users
         if ($_lock_to_role==null)
         {
            $_lock_to_role = 'user';
         }
               
         // let's see who did we reserve the area to
         $_lock_hierarchy = $_hierarchy[$_lock_to_role];
         // let's see who requested to access this area
         $_request_hierarchy = $_hierarchy[$_who_is];
            
         // let's see if we decided to restrict access ONLY to a given category
         switch ($_only)
         {
            case true:
               $_request_hierarchy == $_lock_hierarchy ? $_condition = true : $_condition = false;
               break;
                  
               //only false or not specified   
               default:
                  $_request_hierarchy <= $_lock_hierarchy ? $_condition = true : $_condition = false;
                  break; 
         }
               
         // if who did the request doesn't have enough credentials
         if ($_condition==false)
         {
            // set a flash message
            $msg = "You do not have the credentials to access this reserved area";
            $this->CI->session->set_flashdata('flashMessage', $msg, 1);
            redirect('', 'location');
         }   
      }
      else // user is a guest because they have no role stored in session 
      {
         // Change, Begin - coolbrew: change to standard redirect
         /*
         // Original
         redirect('auth/index', 'location');
         // Original
         */
         header('location: '.$this->CI->config->item('base_url').'login.php');
         // Change, End
      }
      return TRUE;
   }

    // --------------------------------------------------------------------

    /**
     * Checks to see if a user is an administrator
     * uses Class Db_session method userdata
     * Returns false if FreakAuth system is not activated
     * Returns true if admin or superadmin, otherwise false
     * 
     * @return true if admin/superadmin or false otherwise
     */
    function isAdmin()
    {

        if ($this->CI->session AND $this->CI->config->item('FAL'))
        {
            $_username = $this->CI->session->userdata('username');
            $_role = $this->CI->session->userdata('role');
            
            if ($_username != false && $_role != false AND ($_role=='admin' OR $_role=='superadmin'))
                
               //returns the user id
               return true;
        }
      
        //if user_id not activated or not existent
        return false;
    } 

        // --------------------------------------------------------------------

    /**
     * Checks to see if an administrator has superadmin credentials
     * uses Class Db_session method userdata
     * Returns false if FreakAuth system is not activated
     * Returns true if superadmin, otherwise false
     * 
     * @return boolean
     */
    function isSuperAdmin()
    {

        if ($this->CI->session AND $this->CI->config->item('FAL'))
        {
            $_username = $this->CI->session->userdata('username');
            $_role = $this->CI->session->userdata('role');
            
            if ($_username != false AND $_role != false AND $_role=='superadmin')
                
               return true;
        }
      
        return false;
    } 
    // --------------------------------------------------------------------
    
    /**
     * Checks to see if a user is logged in
     * Returns false if FreakAuth system is not activated
     * Returns true if a valid user is logged, false otherwise
     * 
     * @return unknown
     */
    function isValidUser()
    {

        if ($this->CI->session AND $this->CI->config->item('FAL'))
        {
            $_username = $this->CI->session->userdata('username');
            $_role = $this->CI->session->userdata('role');
            
            if ($_username != false AND $_role != false AND $_role=='user')

               return true;
        }
      
        //if user not activated or not existent
        return false;
    } 
    
    // --------------------------------------------------------------------
    
    /**
     * Method used to used to check if a logged in members belongs to the custom role (group) specified in the first parameter
     * it requires 2 optional parameters
     * The first parameter specifies the user groups as a comma separated string (NB: just comma separated WITHOUT SPACES->'user,admin'<--RIGHT 'user,admin'<--WRONG)
     * The second parameter specifies whether we want to check to the specified groups ONLY or for AT LEAST those group membership in the hierarchy
     * (returns true also if the logged user belongs to a group higher in the hierarchy)
     * 
     * example usage in a controller (see the relative helper belongsToGroup() to use it in views)
     * 
     * 1) $this->freakauth_light->belongsToGroup()  //returns true if the visitor is logged in and he is AT LEAST an user
     * 2) $this->freakauth_light->belongsToGroup('user,editor')  //returns true if the visitor is logged in and he is AT LEAST an user or an editor (therefore it returns true also if he belongs to user-groups higher in the hierarchy (i.e. superadmin)
     * 3) $this->freakauth_light->belongsToGroup('admin', true)  //this true if the visitor is logged in and is an 'admin' ONLY 
     * 
     * @param string containing comma separated user groups i.e. "user,editor,moderator"
     * @param boolean $_only
     * @return true/false
     */
    function belongsToGroup($_group=null, $_only=null)
    {
        if ($this->CI->session AND $this->CI->config->item('FAL'))
        {
            $_username = $this->CI->session->userdata('username');
            $_who_is = $this->CI->session->userdata('role');
            
            if ($_username != false AND $_who_is != false)
            {
               //if we didn't specify who we are looking for 
               //let's look if the request comes from an 'user'
               if ($_group==null){$_group='user';}
 
               $_group = explode(",", $_group);
            
            //let's see if we decided to check if it belongs ONLY to a given group
               switch ($_only)
               {   
                  //$_only = true
                  case true: //we decided to check if it belongs ONLY to a given group
                     in_array($_who_is, $_group) ? $_condition = true : $_condition = false;
                     break;
                  
                  //$_only false or not specified
                  //we decided to check if it belongs AT LEAST to a given group   
                  default:
                     //gets the locked role hierarchy value
                     $_hierarchy = $this->CI->config->item('FAL_roles');
                     //let's see who we are looking for
                     

                        foreach ($_group as $value)
                        {
                           $_group_hierarchy []= $_hierarchy[$value];
                        }

                        $_group_hierarchy = max($_group_hierarchy);
                     
                     //let's see who accessed
                     $_who_hierarchy = $_hierarchy[$_who_is];//gets the role-hierarchy-value of the subject that did the request

                     $_who_hierarchy <= $_group_hierarchy ? $_condition = true : $_condition = false;
                     break;
                     
               }
               
               //if who did the request doesn't have enough credentials
               if ($_condition==true)
               {
                  return TRUE;
               }
           }
        }
   //if condition==false, session turner off or user not found (namely not logged in) in ci_session
    return false;     
    } 
       
       // --------------------------------------------------------------------

       /**
        * Performs the login procedure both for user login
        * and form administrators login
        *
        * @return unknown
        */
    function login()
    {            
        if (!$this->CI->config->item('FAL')) 
        {
           redirect($this->CI->config->item('FAL_login_success_action'), 'location');
        }

        $message = $this->CI->lang->line('FAL_invalid_user_message');

        if ($this->CI->session)
        {
            $values = $this->getLoginForm();
            $username = (isset($values['user_name']) ? $values['user_name'] : false);
            $password = (isset($values['password']) ? $values['password'] : false);

            if (($username != false) && ($password != false))
            {
                $password = $this->_encode($password);

                   //Use the input username and password and check against 'user' table
                   //to check if user banned
                   $query = $this->CI->usermodel->getUserForLogin($username, $password);


                if ($query->num_rows() == 1)
                {
                    $row = $query->row();
                    $userdata['id'] = $row->{'id'};
                    $userdata['username'] = $row->{'user_name'};                    
                    $userdata['role'] = $row->{'role'};
                    $banned = $row->{'banned'};
                    //verifies if an user has not been banned from the site (i.e. user table, banned=1)
                    if ($banned == 0)
                    {
                        $this->_set_logindata($userdata);
                        
                        //set FLASH MESSAGE
                        $this->CI->session->set_flashdata('flashMessage', $this->CI->lang->line('FAL_login_message'), 1);
                        

                        return true;
                    }
                    else
                        $message = $this->CI->lang->line('FAL_banned_user_message');
                }
            }
        }

        //On error send user back to login page, and add error message
        //set FLASH MESSAGE
        $this->CI->session->set_flashdata('flashMessage', $message, 1);

        return false;
    }
    
        // --------------------------------------------------------------------
   
   /**
    * Performs the logout procedure
    *
    */
   function logout()
   {      
      //checks if a session exists
      if ($this->CI->session)
      {
         $_username = $this->CI->session->userdata('username');

         if ($_username != false)
         {
            //deletes the userdata stored in DB for the user that logged out
            $this->_unset_user($_username);
         }
      }
       
      //set FLASH MESSAGE
      $msg = $this->CI->lang->line('FAL_logout_message');
      $this->CI->session->set_flashdata('flashMessage', $msg, 1);
        
      // Change, Begin - coolbrew: change to standard redirect
      /*
      // Original
      redirect($this->CI->config->item('FAL_logout_success_action'), 'location');
      // Original
      */
      header('location: '.$this->CI->config->item('base_url').'login.php');
      // Change, End
   }
    
   // --------------------------------------------------------------------
    
   /**
    * Performs the registration procedure
    * Returns true if successful registration, false if unsucessful
    * 
    * @return boolean
    */
   // Change, Begin - coolbrew: add no activation option
   /*
   // Original
   function register()
   {
      // clean the user_temp table
      $this->cleanExpiredUserTemp();
       
      // check if the system is turned on and if we allow users to register
      if (!$this->CI->config->item('FAL') OR $this->CI->config->item('FAL_allow_user_registration')!=TRUE)
      {
         return false;
      }

      if ($this->CI->session)
      {   
         $values = $this->getRegistrationForm();
         $username = (isset($values['user_name']) ? $values['user_name'] : false);
         $password = (isset($values['password']) ? $values['password'] : false);
         $email = (isset($values['email']) ? $values['email'] : false);

         if (($username != false) && ($password != false) && ($email != false))
         {
            $password_email = $password;
            $password = $this->_encode($password);
            $activation_code = $this->_generateRandomString(50, 50);

            $values['password'] = $password;
            $values['activation_code'] = $activation_code;

            $query = $this->CI->UserTemp->insertUserForRegistration($values);
            
            // use the input username and password and check against 'users' table
            $query = $this->CI->UserTemp->getUserLoginData($username, $password);

            $user_id = 0;
            if (($query != null) && ($query->num_rows() > 0))
            {
               $row = $query->row();
               $user_id = $row->id;

               $this->_sendActivationEmail($user_id, $username, $password_email, $email, $activation_code);

               return true;
            }
         }
      }
      
      // set FLASH MESSAGE
      $this->CI->session->set_flashdata('flashMessage', $this->CI->lang->line('FAL_invalid_register_message'), 1);
      return false;
   }
   // Original
   */
   function register($require_activation = TRUE)
   {
      // clean the user_temp table
      $this->cleanExpiredUserTemp();
       
      // check if the system is turned on and if we allow users to register
      if (!$this->CI->config->item('FAL') OR $this->CI->config->item('FAL_allow_user_registration')!=TRUE)
      {
         return false;
      }

      if ($this->CI->session)
      {   
         $values = $this->getRegistrationForm();
         $username = (isset($values['user_name']) ? $values['user_name'] : false);
         $password = (isset($values['password']) ? $values['password'] : false);
         $email = (isset($values['email']) ? $values['email'] : false);

         if (($username != false) && ($password != false) && ($email != false))
         {
            if ($require_activation)
            {
               $password_email = $password;
               $password = $this->_encode($password);
               $activation_code = $this->_generateRandomString(50, 50);

               $values['password'] = $password;
               $values['activation_code'] = $activation_code;

               $query = $this->CI->UserTemp->insertUserForRegistration($values);
            
               // use the input username and password and check against 'users' table
               $query = $this->CI->UserTemp->getUserLoginData($username, $password);

               $user_id = 0;
               if (($query != null) && ($query->num_rows() > 0))
               {
                  $row = $query->row();
                  $user_id = $row->id;

                  $this->_sendActivationEmail($user_id, $username, $password_email, $email, $activation_code);

                  return true;
               }
            }
            else
            {
               $values['password'] = $this->_encode($password);

               // insert the new user data in USER table
               $this->CI->db->trans_start();
               $this->CI->usermodel->insertUser($values);
                   
               // if we want the user profile as well
               if ($this->CI->config->item('FAL_create_user_profile'))
               {   
                  // get the last insert id
                  $data_profile['id'] = $this->CI->db->insert_id();
                  $this->CI->load->model('Userprofile');
                  $this->CI->Userprofile->insertUserProfile($data_profile);
               }
                
               $this->CI->db->trans_complete();

               return true;
            }
         }
      }
      
      // set FLASH MESSAGE
      $this->CI->session->set_flashdata('flashMessage', $this->CI->lang->line('FAL_invalid_register_message'), 1);
      return false;
   }
   // Change, End
    
   // --------------------------------------------------------------------

   /**
    * Handles the user activation requests.
    *
    * @param int $id user id
    * @param varchar $activation_code user activation code
    * @var $id user id
    * @var $activation_code user activation code
    * @return true if successful activation, false if unsucessful
    */
   function activation($id, $activation_code)
   {   
      // clean the user_temp table
      $this->cleanExpiredUserTemp();
            
      if (($id > 0) && ($activation_code != ''))
      {
         // get userdata from USER_TEMP table
         $query = $this->CI->UserTemp->getUserForActivation($id, $activation_code);
         
         // delete the record from USER_TEMP
         $this->CI->UserTemp->deleteUserAfterActivation($id);
            
         if ($query->num_rows() > 0)
         {
            foreach ($query->result() as $row)
            {
               $data['user_name'] = $row->user_name;
               $data['country_id'] = $row->country_id;
               $data['password'] = $row->password;
               $data['email'] = $row->email;
               // Addition, Begin - coolbrew
               $data['store'] = $row->store;
               $data['contact'] = $row->contact;
               $data['phone'] = $row->phone;
               $data['favorite'] = $row->favorite;
               // Addition, End
            }
               
            // insert the new user data in USER table
            $this->CI->db->trans_start();
            $this->CI->usermodel->insertUser($data);
                   
            // if we want the user profile as well
            if ($this->CI->config->item('FAL_create_user_profile'))
            {   
               // get the last insert id
               $data_profile['id'] = $this->CI->db->insert_id();
               $this->CI->load->model('Userprofile');
               $this->CI->Userprofile->insertUserProfile($data_profile);
            }
                
            $this->CI->db->trans_complete();

            return true;
         }
      }
      return false;
   }
    
        // --------------------------------------------------------------------
    
    /**
     * Handles the user forgotten password $_POST requests
     * returns true if password sent to user, false otherwise
     * @return true if password sent to user
     */
   // Change, Begin - coolbrew: add no activation option
   /*
   // Original
   function forgotten_password()
   {
      if ($this->CI->session)
      {
         $email = $this->CI->input->post('email');
         
         // if $email not false checks the relative password for that user querying the DB
         if (($email != false))
         {
            $query = $this->CI->usermodel->getUserForForgottenPassword($email);

            if (($query != null) && ($query->num_rows() > 0))
            {
               $row = $query->row();
               $user_id = $row->{'id'};
               $user = $row->{'user_name'};
               
               // generate the activation code
               $activation_code = $this->_generateRandomString(50, 50);
               
               // update the user table
               $this->CI->usermodel->updateUserForForgottenPassword($user_id, $activation_code);
               
               // send e-mail to user
               $this->_sendForgottenPasswordEmail($user_id, $user, $email, $activation_code);
                    
               return true;
            }
         }
            
         //set unsuccess FLASH MESSAGE
         $msg = $this->CI->lang->line('FAL_forgotten_password_user_not_found_message');
         $this->CI->session->set_flashdata('flashMessage', $msg, 1);
            
         return false;
      }
   }
   // Original
   */
   function forgotten_password($require_activation = TRUE)
   {
      if ($this->CI->session)
      {
         $email = $this->CI->input->post('email');
         
         // if $email not false checks the relative password for that user querying the DB
         if (($email != false))
         {
            $query = $this->CI->usermodel->getUserForForgottenPassword($email);

            if (($query != null) && ($query->num_rows() > 0))
            {
               $row = $query->row();
               $user_id = $row->{'id'};
               $user = $row->{'user_name'};
               
               if ($require_activation)
               {
                  // generate the activation code
                  $activation_code = $this->_generateRandomString(50, 50);
               
                  // update the user table
                  $this->CI->usermodel->updateUserForForgottenPassword($user_id, $activation_code);
               
                  // send e-mail to user
                  $this->_sendForgottenPasswordEmail($user_id, $user, $email, $activation_code);
                    
                  return true;
               }
               else
               {
                  // generate a random password
                  $password = $this->_generateRandomString($this->CI->config->item('FAL_user_password_min'), $this->CI->config->item('FAL_user_password_max'));
                
                  // encrypt the random password using the md5 encryption
                  $encrypted_password = $this->_encode($password);
            
                  // send the new generated password to the user
                  $this->_sendForgottenPasswordResetEmail($user_id, $user, $email, $password);
            
                  // update the password in the database
                  $this->CI->usermodel->updateUserForForgottenPasswordReset($user_id, $encrypted_password);

                  return true;
               }
            }
         }
            
         //set unsuccess FLASH MESSAGE
         $msg = $this->CI->lang->line('FAL_forgotten_password_user_not_found_message');
         $this->CI->session->set_flashdata('flashMessage', $msg, 1);
            
         return false;
      }
   }
   // Change, End
   
   // --------------------------------------------------------------------
    
    /**
     * Handles the user forgotten password reset requests, when the user clicks on the e-mail link
     * Returns true if the process has been successful, false otherwise
     *
     * @param integer $id
     * @param varchar $activation_code
     * @return true
     */
    function forgotten_password_reset($id, $activation_code)
    {   
       //checks if $id>0 and if $activation_code not null
        if (($id > 0) && ($activation_code != ''))
        {   
           /**
            * recalls the function getUserForForgottenPasswordReset($id, $activation_code)
            * from the class Usermodel
            * it queries the database looking for the user's $id and $activation_code
            */
            $query = $this->CI->usermodel->getUserForForgottenPasswordReset($id, $activation_code);
         
            //if the query returns at least a result namely num_rows() > 0
            if ($query->num_rows() > 0)
            {
                $row = $query->row();
                $user_id = $row->{'id'};
                $user = $row->{'user_name'};
                $email = $row->{'email'};
            
                //generates a random password
                $password = $this->_generateRandomString($this->CI->config->item('FAL_user_password_min'), $this->CI->config->item('FAL_user_password_max'));
                
                //encrypts the random password using the md5 encryption
                $encrypted_password = $this->_encode($password);
            
                //sends the new generated password to the user
                $this->_sendForgottenPasswordResetEmail($user_id, $user, $email, $password);
            
                //updates the password in the database
                $this->CI->usermodel->updateUserForForgottenPasswordReset($user_id, $encrypted_password);

                return true;
            }
        }

        return false;
    }
    
        // --------------------------------------------------------------------
    
    /**
     * Handles the user change password $_POST requests
     * returns true if password sent to user, false otherwise
     * @return true if password sent to user
     */
    function _change_password()
    {
        if ($this->CI->session)
        {
            $username = $this->CI->input->post('user_name');
           $old_password = $this->CI->input->post('password');
            $new_password = $this->CI->input->post('new_password');
         
            //if $email not false checks the relative password for that user querying the DB
            if ($username != false AND $old_password != false AND $new_password != false)
            {
                $query = $this->CI->usermodel->getUserForLogin($username, $this->_encode($old_password));

                if (($query != null) && ($query->num_rows() == 1))
                {
                    $row = $query->row();
                    $user_id = $row->{'id'};
                    $user = $row->{'user_name'};
                    $email = $row->{'email'};
               
                    //clear text password for e-mail
                    $password_email = $new_password;
                    
                    //encrypts the password for DB update
                    $new_password = $this->_encode($new_password);
                    
                    //updates the user table
                    $this->CI->usermodel->updateUserForForgottenPasswordReset($user_id, $new_password);
               
                    //sends e-mail to user
                    $this->_sendChangePasswordEmail($user_id, $user, $email, $password_email);
                    
                    return true;
                }
            }
            
            //set unsuccess FLASH MESSAGE
            $msg = $this->CI->lang->line('FAL_forgotten_password_user_not_found_message');
            $this->CI->session->set_flashdata('flashMessage', $msg, 1);
            
            return false;
        }
    }   
    
    // --------------------------------------------------------------------
    
    /**
     * Sets the userdata in the Db_session table
     * and updates the user table for Last_login
     *
     * @param array $userdata
     */
    function _set_logindata($userdata)
    {                
        //updates the Last_visit field in the user table
        $this->CI->usermodel->updateUserForLogin($userdata['id']);
       $this->CI->session->set_userdata($userdata);           
    }
   
   
    // --------------------------------------------------------------------
    
    /**
     * Unsets user data in session_data DB field of table ci_session
     *
     * @param integer $user_id
     */
    function _unset_user($_username)
    {
        $users = $this->CI->session->userdata('username');
        
        if (isset($users))
        {
            unset($users);
            //is better to do a 1 call to unset_userdata passing an array?
            $this->CI->session->unset_userdata('id');
            $this->CI->session->unset_userdata('username');
            $this->CI->session->unset_userdata('role');
        }
        
    }

   
    // --------------------------------------------------------------------
    
    /**
     * Needed to clean the UserTemp table from not completed registration
     * The records get removed if older than what you set in the configuration file
     * $config['FreakAuthL_temporary_users_expiration']
     * Cleaning get performed after activation and on new registrations
     *
     */
    function cleanExpiredUserTemp()
    {
       $expiration = $this->CI->config->item('FAL_temporary_users_expiration');
       
       $query = $this->CI->UserTemp->getUserTempCreated();
       
       if ($query->num_rows() > 0)
            {
                 foreach ($query->result() as $row)
               {   
                  
                  if (time()>($row->created + $expiration))
                  {
                     $this->CI->UserTemp->deleteUserAfterActivation($row->id);
                  }
               }
            }
    }
     

    // --------------------------------------------------------------------
    
    /**
     * Returns the currently logged in user's name
     * Returns an empty string if no user is logged in
     * uses function isValidUser()
     * and Class session method "userdata".
     * 
     * @return username string of currently logged in user
     * @return empty string if user not logged in
     */
    function getUserName()
    {
        if ($this->CI->config->item('FAL') && $this->CI->session && ($this->isValidUser() OR $this->isAdmin()))
            
           // returns username string of currently logged in user
           return $this->CI->session->userdata('username');
        
        // returns empty string if user not logged in
        return '';
    }
    

    // --------------------------------------------------------------------
    
    /**
     * Checks if Captcha is required
     * if it is required in the config settings recalls function _generateRandomSecurityCodeImage()
     * to build it
     */
    function captcha_init($action)
    {   
       //checks FreakAuth security code configuration
        if (!$this->CI->config->item('FAL_use_security_code'.$action))
            
           //if not set or FALSE
           return;
        
        //ELSE unsets userdata from session table         
        $this->CI->session->unset_userdata('FreakAuth_security_code');
        
        //loads the captcha plugin
        //$this->CI->load->plugin('captcha');
        list($usec, $sec) = explode(" ", microtime());
        $now = ((float)$usec + (float)$sec);
        
        //deletes captcha images
        $this->_deleteOldSecurityCodeImage($now);
        
        //generates security code image
        $this->_generateRandomSecurityCodeImage($now);
    }
   
    // --------------------------------------------------------------------
    /**
     * Deletes the captcha images generated
     * it deletes them if they "expired". The "expiration" (in seconds) signifies how long an image will
     * remain in the captcha folder before it will be deleted.  The default is 20 minuts. Change the value of $expiration
     * if you want them to be deleted more or less often
     *
     * @param float $now
     */
    function _deleteOldSecurityCodeImage($now)
    {
       list($usec, $sec) = explode(" ", microtime());
      
       //sets the expiration time of the captcha image
       $expiration=60*10; //10 min
         
      $current_dir = @opendir($this->CI->config->item('FAL_security_code_image_path'));
      
      while($filename = @readdir($current_dir))
      {
         if ($filename != "." AND $filename != ".." AND $filename != "index.html")
         {
            $name = str_replace(".jpg", "", $filename);
         
            if (($name + $expiration) < $now)
            {
               @unlink($this->CI->config->item('FAL_security_code_image_path').$filename);
            }
         }
      }
      
      @closedir($current_dir);
    }
    // --------------------------------------------------------------------
    
    /**
     * Creates a random security code image (Captcha).
     *
     * @return unknown
     */
    function _generateRandomSecurityCodeImage($now)
    {
        
            $securityCode = $this->_generateRandomString($this->CI->config->item('FAL_security_code_min'), $this->CI->config->item('FAL_security_code_max'));
         //$image = 'security-'.$this->_generateRandomString(16, 32).'.jpg';
         $image = $now.'.jpg';
            $this->CI->config->set_item('FAL_security_code_image', $image);
            
            $config['image_library'] = $this->CI->config->item('FAL_security_image_library');
            $config['source_image'] = $this->CI->config->item('FAL_security_code_base_image_path').$this->CI->config->item('FAL_security_code_image_base_image');
            $config['new_image'] = $this->CI->config->item('FAL_security_code_image_path').$image;
            $config['wm_text'] = $securityCode;
            $config['wm_type'] = 'text';
            $config['wm_font_path'] = $this->CI->config->item('FAL_security_code_image_font');
            $config['wm_font_size'] = $this->CI->config->item('FAL_security_code_image_font_size');
            $config['wm_font_color'] = $this->CI->config->item('FAL_security_code_image_font_color');
            $config['wm_vrt_alignment'] = 'top';
         $config['wm_hor_alignment'] = 'left';
         $config['wm_padding'] = '10';

            $image =& get_instance();
            $image->load->library('image_lib');
            $image->image_lib->initialize($config); 
            
            if ( ! $image->image_lib->watermark())
         {
             echo $image->image_lib->display_errors();
         };
         
            $this->CI->session->set_userdata('FreakAuth_security_code', $securityCode);         
            return $this->CI->config->item('FAL_security_code_image');
            
        
    }
   
    // --------------------------------------------------------------------
    
    /**
     * Generates a random string.
     *
     * @param integer $minLength
     * @param integer $maxLength
     * @param boolean $useUpper
     * @param boolean $useNumbers
     * @param boolean $useSpecial
     * @return $key random string
     */
    function _generateRandomString()
    {
        $charset = "abcdefghijklmnopqrstuvwxyz";
        if ($this->CI->config->item('FAL_security_code_upper_lower_case'))
            $charset .= "ABCDEFGHIJKLMNPQRSTUVWXYZ";
        if ($this->CI->config->item('FAL_security_code_use_numbers'))
            $charset .= "23456789";
      if ($this->CI->config->item('FAL_security_code_use_specials'))
            $charset .= "~@#$%^*()_+-={}|][";
            
        $length = mt_rand($this->CI->config->item('FAL_security_code_min'), $this->CI->config->item('FAL_security_code_max'));
        if ($this->CI->config->item('FAL_security_code_min') > $this->CI->config->item('FAL_security_code_max'))
            $length = mt_rand($this->CI->config->item('FAL_security_code_max'), $this->CI->config->item('FAL_security_code_min'));

        $key = '';
        for ($i = 0; $i < $length; $i++)
            $key .= $charset[(mt_rand(0, (strlen($charset)-1)))];

        return $key;
    }

    // --------------------------------------------------------------------
    
    /**
     * Sends an email from the system to a given email address.
     *
     * @access private
     * @param varchar $email
     * @param varchar $subject
     * @param text $message
     */
    function _sendEmail($email, $subject, $message)
    {
        $tobj =& get_instance(); 
        $tobj->load->library('email');
        $tobj->email->clear();
        $tobj->email->from($this->CI->config->item('FAL_user_support'), $this->CI->config->item('FAL_website_name').' '.$this->CI->config->item('FAL_email_from'));
        $tobj->email->to($email);
        $tobj->email->subject($subject);
        $tobj->email->message($message);
        $tobj->email->send();
    }
   
    // --------------------------------------------------------------------
    
    /**
     * Sends an activation email from the system to the newly registered user
     *
     * @access private
     * @param integer $id
     * @param unknown_type $user
     * @param unknown_type $email
     * @param varchar $activation_code
     */
    function _sendActivationEmail($id, $user, $password_email, $email, $activation_code)
    {
       // Change, Begin - coolbrew: change activation URL
       /*
       // Original
        $activation_url = site_url('auth/activation/'.$id.'/'.$activation_code);
        // Original
        */
        $activation_url = $this->CI->config->item('base_url') . 'activate.php/' . $id . '/' . $activation_code;
      // Change, End
        $data = array('activation_url' => $activation_url,
                      'user_name' => $user,
                      'password'=>$password_email);

        $message = $this->CI->load->view($this->CI->config->item('view').$this->CI->config->item('FAL_activation_email').EXT, $data, true);
      
        $subject= '['.$this->CI->config->item('FAL_website_name').'] '.$this->CI->lang->line('FAL_activation_email_subject');
        $this->_sendEmail($email, $subject , $message);
    }
   
    // --------------------------------------------------------------------
    
    /**
     * Sends an email from the system to the user that has forgotten the password
     * the e-mail contains the link to make the reset password start
     * 
     * @access private
     * @param unknown_type $id
     * @param unknown_type $user
     * @param unknown_type $email
     * @param unknown_type $activation_code
     */
    function _sendForgottenPasswordEmail($id, $user, $email, $activation_code)
    {
       // Change, Begin - coolbrew: change activation URL
       /*
       // Original
        $activation_url = site_url('auth/forgotten_password_reset/'.$id.'/'.$activation_code);
        // Original
        */
        $activation_url = $this->CI->config->item('base_url') . 'reset.php/' . $id . '/' . $activation_code;
      // Change, End
        $data = array('activation_url' => $activation_url,
                      'user_name' => $user);

        $message = $this->CI->load->view($this->CI->config->item('view').$this->CI->config->item('FAL_forgotten_password_email').EXT, $data, true);
      
        $subject= '['.$this->CI->config->item('FAL_website_name').'] '.$this->CI->lang->line('FAL_forgotten_password_email_subject');
        
        $this->_sendEmail($email, $subject, $message);
    }

        // --------------------------------------------------------------------
    
   /**
    * Sends and e-mail to the user after resetting the password
    * The e-mail contains the new login informations
    *
    * @access private
    * @param integer $id
    * @param varchar $user
    * @param varchar $email
    * @param varchar $password
    */
    function _sendForgottenPasswordResetEmail($id, $user, $email, $password)
    {
        $data = array('password' => $password,
                      'user_name' => $user,
                      'change_password_link'=> base_url().'change.php'
                      );
                      
      
        //displays message to the user on screen
        $message = $this->CI->load->view($this->CI->config->item('view').$this->CI->config->item('FAL_forgotten_password_reset_email').EXT, $data, true);
      
        $subject= '['.$this->CI->config->item('FAL_website_name').'] '.$this->CI->lang->line('FAL_forgotten_password_email_reset_subject');
        //sends e-mail to the user to reset password
        $this->_sendEmail($email, $subject, $message);
    }
    
        // --------------------------------------------------------------------
    
    /**
     * Sends an email from the system to the user that has changed the password
     * the e-mail has the newly generated password
     * @access private
     * @param unknown_type $id
     * @param unknown_type $user
     * @param unknown_type $email
     * @param unknown_type $activation_code
     */
    function _sendChangePasswordEmail($id, $user, $email, $password_email)
    {
        $data = array('user_name' => $user,
                      'password'=>$password_email);

        $message = $this->CI->load->view($this->CI->config->item('view').$this->CI->config->item('FAL_change_password_email').EXT, $data, true);
      
        $subject= '['.$this->CI->config->item('FAL_website_name').'] '.$this->CI->lang->line('FAL_forgotten_password_email_reset_subject');
        $this->_sendEmail($email, $subject , $message);
    }
    

    // --------------------------------------------------------------------
    
    /**
     * Gets login form input values.
     *
     * @return array
     */
    function getLoginForm()
    {
        $values['user_name'] = $this->CI->input->post('user_name');
        $values['password'] = $this->CI->input->post('password');
        
        //$values[$this->CI->config->item('FAL_<your field>_field')] = $this->CI->input->post($this->CI->config->item('FAL_<your field>_field'));
        
        return $values;
    }

   // --------------------------------------------------------------------

   /**
    * Gets registration form input values.
    *
    * @return array
    */
   function getRegistrationForm()
   {
      $values['user_name'] = $this->CI->input->post('user_name', TRUE);
      $values['password'] = $this->CI->input->post('password');
      $values['email'] = $this->CI->input->post('email');
      if ($this->CI->config->item('FAL_use_country'))
      {
         $values['country_id'] = $this->CI->input->post('country_id');
      }
            
      // enter custom fields here...
      $values['store'] = $this->CI->input->post('store');					
      $values['contact'] = $this->CI->input->post('contact');
      $values['phone'] = $this->CI->input->post('phone');
      $values['favorite'] = $this->CI->input->post('favorite');
        
      return $values;
   }
    
     // --------------------------------------------------------------------
     /**
      * Custom encoding method for added security
      *
      * @param string $_password
      * @return encoded password
      */
     function _encode($password)
     {
      $majorsalt=null;
      
      //if you set your encryption key let's use it
        if ($this->CI->config->item('encryption_key')!='')
      {
         //conctenates the encryption key and the password
         $_password = $this->CI->config->item('encryption_key').$password;
      }
      else {$_password=$password;}
      
      //if PHP5
      if (function_exists('str_split'))
      {
          $_pass = str_split($_password);       
      }
      //if PHP4
      else
      {
         $_pass = array();
          if (is_string($_password))
          {
             for ($i = 0; $i < strlen($_password); $i++)
             {
                 array_push($_pass, $_password[$i]);
              }
           }
      }
      
      //encrypts every single letter of the password
      foreach ($_pass as $_hashpass) 
      {
         $majorsalt .= md5($_hashpass);
      }
      
      //encrypts the string combinations of every single encrypted letter
      //and finally returns the encrypted password 
      return $password=md5($majorsalt);
      
     }
     
   // --------------------------------------------------------------------
     
   /**
    * Needed to display and edit user profile data
    *
    * @param integer user id $id
    * @return array of user profile data-> $data['user_profile']
    */
   function _getUserProfile($id)
   {
      // get fields names from config
      $field_name = $this->CI->config->item('FAL_user_profile_fields_names');
        
      // get fields validation rules from config
      $field_rule = $this->CI->config->item('FAL_user_profile_fields_validation_rules');
        
      $this->CI->load->model('Userprofile', 'userprofile');
        
      //array of fields
      $db_fields = $this->CI->userprofile->getTableFields();

      //number of DB fields -1
      //I put a -1 because I must subtract the 'id' field
      $num_db_fields = count($db_fields) - 1;

      if ($num_db_fields!=0) 
      {   
         $query = $this->CI->userprofile->getUserProfileById($id);
              
         if ($query->num_rows() == 1)
         {
            $row = $query->row();
      
            for ($i=1; $i<=$num_db_fields; $i++)
            {
               $field = $db_fields[$i];
               $data[$field] = $row->$db_fields[$i];                   
            }
            return $data;

         }
           else 
         {
            //set_error_flash_message
            //set FLASH MESSAGE
            $this->CI->session->set_flashdata('flashMessage', 'No profile found for this user', 1);
         }
      }
      else 
      {
         return false;
      }
   }
     
   // --------------------------------------------------------------------
     
   /**
    * Needed to dynamically build rules and fields from config array for add and edit custom user profile
    *
    * @return array of data['rules'] and data['fields']
    */
   function _buildUserProfileFieldsRules()
   {
       //lets get fields names from config
       $field_name = $this->CI->config->item('FAL_user_profile_fields_names');
        
       //lets get fields validation rules from config
       $field_rule = $this->CI->config->item('FAL_user_profile_fields_validation_rules');
     
       $this->CI->load->model('Userprofile', 'userprofile');
        
       //array of fields
       $db_fields = $this->CI->userprofile->getTableFields();

       //number of DB fields -1
       //I put a -1 because I must subtract the 'id' field
       $num_db_fields = count($db_fields) - 1;
      
       //I use 'for' instead of 'foreach' because I have to escape the 'id' field that has key=0 in my array
       for ($i=1; $i<=$num_db_fields;  $i++)
       {
          $field = $db_fields[$i];
          //creates rules
          //$data['rules'][$field] = $field_rule[$field];
          //if the rule for the fields in DB has been specified in the config array
          //let's assign it otherwise don't assign nothing
          array_key_exists($field, $field_rule) ? $data['rules'][$field] = $field_rule[$field] : '';
          //creates fields
          //if the custom field name for the field in DB has been specified in the config array
          //let's assign it otherwise let's call it with the name in DB
          array_key_exists($field, $field_name) ? $data['fields'][$field] = $field_name[$field] : $data['fields'][$field] = $field;
      }

      return $data;
   }
     
   // --------------------------------------------------------------------
     
        
}

?>