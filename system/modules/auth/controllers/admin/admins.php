<?php
/**
 * Auth Controller Class
 *
 * Security controller that provides functionality to handle logins, logout and registration
 * requests.  It also can verify the logged in status of a user and his permissions.
 *
 * The class requires the use of the DB_Session and FreakAuth libraries.
 *
 * @package     FreakAuth
 * @subpackage  Controllers
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/gpl.html
 * @inpiredFrom Auth class by Jaapio (fphpcode.nl)
 * @link 		http://4webby.com/FreakAuth
 * @version 	1.0.2-Beta
 *
 */

class Admins extends Controller
{	
	/**
	 * Initialises the controller
	 *
	 * @return Admin
	 */
    function Admins()
    {
        parent::Controller();
        
        ////////////////////////////
		//CHECKING FOR PERMISSIONS
		///////////////////////////
		//-------------------------------------------------
        //only SuperAdmin can manage users
        //if you are not a superAdmin go away
        
        $this->freakauth_light->check('superadmin', true);
        
        //-------------------------------------------------
        //END CHECKING FOR PERMISSION
        
        //loads necessary libraries
        $this->lang->load('freakauth');
        $this->load->model('FreakAuth_light/usermodel', 'usermodel');
        $this->load->library('validation');
		$this->validation->set_error_delimiters($this->config->item('FAL_error_delimiter_open'), $this->config->item('FAL_error_delimiter_close'));

		//sets the necessary form fields
		$fields['user_name'] = $this->lang->line('FAL_user_name_label');
        $fields['password'] = $this->lang->line('FAL_user_password_label');
        $fields['password_confirm'] = $this->lang->line('FAL_user_password_confirm_label');
        $fields['email'] = $this->lang->line('FAL_user_email_label');
        $fields['role'] = 'role';
        $fields['banned'] = 'banned';
        
        //if activated in config, sets the select country box
        if ($this->config->item('FAL_use_country'))
        {
            $fields['country_id'] = $this->lang->line('FAL_user_country_label');
        }
        
        //additionalFields($fields);
        
        $this->validation->set_fields($fields);
    	
    }
	
    	// --------------------------------------------------------------------
	
    /**
     * Displays the login form.
     *
     */
    function index()
    {
		//let's paginate results
		$this->load->library('pagination');
		
		$config['base_url'] = base_url().'admin/users';
		$config['uri_segment'] = 3;
		$config['per_page'] = $this->config->item('FAL_admin_console_records_per_page');  			//20 records per page
		$config['full_tag_open'] = '<p>';
		$config['full_tag_close'] = '</p>';
		$config['cur_tag_open'] = '<b>';
		$config['cur_tag_close'] = '</b>';
		$config['next_link'] = '&gt';
		$config['prev_link'] = '&lt';	
		$fields='id';
		$query = $this->usermodel->getAdmins($fields);
		
		$config['total_rows'] = $query->num_rows();
		$this->pagination->initialize($config);
		$query->free_result();
			
		$page = $this->uri->segment(3, 0);
    	
    	$fields= 'id, user_name, role';
    	
    	$limit= array('start'=>$config['per_page'],
    				  'end'=>$page
    					);
		
    	$query = $this->usermodel->getAdmins($fields, $limit);

		
		if ($query->num_rows()>0)
		{ 
			 $i=1;
			 foreach ($query->result() as $row)
			{
				$data['user'][$i]['id']= $row->id;
				$data['user'][$i]['user_name']= $row->user_name;
				$data['user'][$i]['role']= $row->role;
				$i++;
			}
			
			$query->free_result();
		}
		else 
		{
			$msg= $this->lang->line('FAL_no_records');
			$this->session->set_flashdata('flashMessage', $msg, 1);
			
		}
			
			//let's display the page
	    	$data['heading'] = 'VIEW admins';
	    	$data['action'] = 'Manage admins';
	    	$data['pagination_links'] = $this->pagination->create_links();
	    	$data['controller'] = 'admins';
	    	$data['page'] = 'FreakAuth_light/template_admin/users/list';
            				
	        $this->load->vars($data);
	        
	    	$this->load->view($this->config->item('FAL_template_dir').'template_admin/container');
        	//$this->output->enable_profiler(TRUE);
    }


    // --------------------------------------------------------------------
    
	/**
	 * View record details
	 *
	 * @param record id $id
	 */
    function show($id)
    {	    			
    	$query = $this->usermodel->getUserById($id);
		
		if ($query->num_rows() == 1)
        {
			$row = $query->row();
			$data['user']['id']= $row->id;
			$data['user']['user_name']= $row->user_name;
			$data['user']['email']= $row->email;
			$data['user']['role']= $row->role;
			$data['user']['banned']= $row->banned;
			
			//$countries = null;            
		    if ($this->config->item('FAL_use_country') && strlen($row->country_id))
		    {
		    	$this->load->model('country'); 
		        
		    	$query = $this->country->getCountryById($row->country_id);
		    	$row = $query->row();
		    		
		    	//SELECT name FROM country WHERE id= $data['user']['country_id']
		        $data['user']['country'] = $row->name;
		    }
		    
		    $query->free_result();
		    
		    if ($this->config->item('FAL_create_user_profile')==TRUE)
		    {
		    	$data['user_profile']= $this->freakauth_light->_getUserProfile($id);
		    	$data['f_r'] = $this->freakauth_light->_buildUserProfileFieldsRules();
		    	$data['label'] = $data['f_r']['fields']; 
		    }
		    
		    
		    
        }
        else 
        {
        	$data['error_message']='The record you are looking for does not exist';
        }
    	
	    	$data['heading'] = 'Manage admin';
	    	$data['action'] = 'View admin';
	    	$data['controller'] = 'admins';
	    	$data['page'] = 'FreakAuth_light/template_admin/users/detail';
            				
	        $this->load->vars($data);
	        
	    	$this->load->view($this->config->item('FAL_template_dir').'template_admin/container');
	    	
	    	//for debagging
	    	//$this->output->enable_profiler(TRUE);
	    	
    }
    
    // --------------------------------------------------------------------
    
    /**
     * Handles the post from the add admin form.
     *
     */
    
    function add()
    {      
    	//set validation rules
    	$rules['user_name'] = 'trim|required|xss_clean|callback__username_check|callback__username_duplicate_check';
        $rules['password'] = 'trim|required|xss_clean|callback__password_check';
        $rules['password_confirm'] = "trim|required|xss_clean|matches[password]";
        $rules['email'] = 'trim|required|valid_email|xss_clean|callback__email_duplicate_check';
        $rules['role'] = 'required';
        $rules['banned'] = 'is_numeric';
        
        $this->validation->set_message('is_numeric', 'must be numeric');
        //do we want to set the country?
        //(looks what we set in the freakauth_light.php config)
        if ($this->config->item('FAL_use_country'))
        {
            $rules['country_id'] = $this->config->item('FAL_user_country_field_validation_register');
        }
        
        //getting user profile custom data
	    if ($this->config->item('FAL_create_user_profile')==TRUE)
		{	
		    $data = $this->freakauth_light->_buildUserProfileFieldsRules();
		    $rules_profile = $data['rules'];
		    $fields = $data['fields']; 
		    
		    $this->validation->set_rules($rules_profile);
		    	
		    $this->validation->set_fields($fields);
		}
		        
        $this->validation->set_rules($rules);
       
    	
    	
    	//if validation unsuccesfull & data not ok
        if ($this->validation->run() == FALSE)
		{
			//$countries = null;            
	        if ($this->config->item('FAL_use_country'))
	        {
	    		$this->load->model('country'); 
	        	
	    		//SELECT * FROM country
	            $data['countries'] = $this->country->getCountriesForSelect();
	        }

			$data['heading'] = 'Admins management';
	    	$data['action'] = 'Add admin';
	    	$data['role_options'] = array_keys($this->config->item('FAL_roles'));
	    	$data ['page'] = 'FreakAuth_light/template_admin/users/add';
            				
	        $this->load->vars($data);
	        
	    	$this->load->view($this->config->item('FAL_template_dir').'template_admin/container');
	    	//$this->output->enable_profiler(TRUE);
		}
		//if everything ok
		else
		{	 
			$values=$this->_get_form_values();
			
        	//insert data in DB
        	$this->usermodel->insertUser($values['user']);
        	
        	
        	//if we want the user profile as well
	        if($this->config->item('FAL_create_user_profile'))
	        {	
	              //let's get the last insert id
	              $values['user_profile']['id']= $this->db->insert_id();
	              $this->load->model('Userprofile');
	              $this->Userprofile->insertUserProfile($values['user_profile']);
	        }
			//set a flash message
			$msg = $this->db->affected_rows().$this->lang->line('FAL_user_added');
			$this->session->set_flashdata('flashMessage', $msg, 1);
			
			//redirect to list
			redirect('admin/users', 'location');
		}
        	
    }
    

    // --------------------------------------------------------------------
    
    /**
     * Manages the edit
     * 
     * @access private
     *
     */
    function edit()
    {
    	$id = $this->uri->segment(4);
    	
    	//set validation rules
    	$rules['user_name'] = 'trim|required|xss_clean|callback__username_check|callback__username_duplicate_check';
        $rules['password'] = 'trim|xss_clean|callback__password_check';
        $rules['password_confirm'] = "trim|xss_clean|matches[password]";
        $rules['email'] = 'trim|required|valid_email|xss_clean|callback__email_duplicate_check';
        $rules['role'] = 'required';
        $rules['banned'] = 'is_numeric';
        
        
        $this->validation->set_message('is_numeric', 'must be numeric');
        //do we want to set the country?
        //(looks what we set in the freakauth_light.php config)
        if ($this->config->item('FAL_use_country'))
        {
            $rules['country_id'] = $this->config->item('FAL_user_country_field_validation_register');
        }
            
        //getting user profile custom data
	    if ($this->config->item('FAL_create_user_profile')==TRUE)
		{	
		    $data = $this->freakauth_light->_buildUserProfileFieldsRules();
		    $rules_profile= $data['rules'];
		    $fields = $data['fields']; 
		    
		    $this->validation->set_rules($rules_profile);
		    
		}
        
        $this->validation->set_message('is_numeric', 'must be numeric');
        
        $this->validation->set_rules($rules);
        
        //id field needed for validation
        $fields['id'] = 'id';
        $fields['role'] = 'role';
        
        $this->validation->set_fields($fields);
        
		$data['role_options'] = array_keys($this->config->item('FAL_roles'));
    	//this avoid 1 extra query if validation doesn't return true
        if ($id!='')
        {	
        	//gets values for the edit form
        	$query = $this->usermodel->getUserById($id);
        
		
	       	foreach ($query->result() as $row)
		        	{
		        		$data['user']['id']= $row->id;
		        		$data['user']['user_name']= $row->user_name;
		        		$data['user']['email']= $row->email;
		        		$data['user']['country_id']= $row->country_id;
		        		$data['user']['role']= $row->role;
		        		$data['user']['banned']= $row->banned;
		        	}
		        	
		    $query->free_result();
		    
		    

		    if ($this->config->item('FAL_create_user_profile')==TRUE)
			{
				$data['user_prof']= $this->freakauth_light->_getUserProfile($id);
		    	$data['f_r'] = $this->freakauth_light->_buildUserProfileFieldsRules();
		    	$data['fields'] = $data['f_r']['fields']; 
			}
		    
	    }

	    //$countries = null;            
	    if ($this->config->item('FAL_use_country'))
	    {
	    	$this->load->model('country'); 
	        	
	    	//SELECT * FROM country
	        $data['countries'] = $this->country->getCountriesForSelect();
	    }
	    	  	
		if ($this->validation->run() == FALSE)
        {
               	$data['heading'] = 'Admin management';
	        	$data['action'] = 'Edit admin';
	        	$data['controller'] = 'admins';
	        	$data ['page'] = 'FreakAuth_light/template_admin/users/edit';

	        	$this->load->vars($data);

	        	$this->load->view($this->config->item('FAL_template_dir').'template_admin/container');
	        	
	        	//$this->output->enable_profiler(TRUE);

        }
    	
		//if everything ok
		else
		{			
			//get form values
			$values=$this->_get_form_values();
			
			$id = $values['user']['id'];
			
			//update data in DB
			$where=array('id' => $id);
        	$this->usermodel->updateUser($where, $values['user']);
        	
        	//if we want the user profile as well
	        if($this->config->item('FAL_create_user_profile'))
	        {	
	              //let's get the last insert id
	              $this->load->model('Userprofile');
	              $this->Userprofile->updateUserProfile($id, $values['user_profile']);
	        }
        	//set a flash message
			$msg = $this->db->affected_rows().$this->lang->line('FAL_user_edited');
        	$this->session->set_flashdata('flashMessage', $msg, 1);
			
			//redirect to list
			redirect('admin/users', 'location');
		}
        
    }
    
        // --------------------------------------------------------------------
    
    /**
     * Displays the registration form.
     * 
     * @access private
     *
     */
    function del($id)
    {
        //CHECK IF ADMIN#1 OR ONLY ONE ADMIN LEFT
    	$fields = 'id';
        $query=$this->usermodel->getAdmins($fields);

    	//first system admin   	
    	if ($id==1)
    	{
    		//set a flash message
        	$msg = "It's not allowed to delete the system administrator #1";
        
    	}
    	//last admin left
    	elseif ($id!=1 AND $query->num_rows()<1)
    	{
    		//set a flash message
        	$msg = "It's not allowed to delete the last system administrator left!";        
    	}
    	else 
    	{
	    	$this->usermodel->deleteUser($id);
    	
	    	if ($this->config->item('FAL_create_user_profile')==TRUE)
			{
				$this->load->model('Userprofile');
				$this->Userprofile->deleteUserProfile($id);
			}
	        //set a flash message
			$msg = $this->db->affected_rows().$this->lang->line('FAL_user_deleted');
	        $this->session->set_flashdata('flashMessage', $msg, 1);
	        $this->usermodel->deleteAdmin($id) ;
	    	//set a flash message
	        $msg = $this->db->affected_rows().' administrator successfully deleted!';
	        redirect('admin/admins', 'location');     
	    }
	        
    }
    
    // -------------------------------------------------------------------- 
    
    /**
     * Checks if form $_POST data are set and valid
     * assigns the $_POST data to an array and returns it
     *
     * @return array of form values
     */
    function _get_form_values()
    {
        if (isset($_POST['id'])) 
        {
        	//for edit record
        	$values['user']['id']=$_POST['id']; 
        }

        $values['user']['user_name'] = $this->input->post('user_name', TRUE);
        $values['user']['password'] = $this->input->post('password');
        $values['user']['email'] = $this->input->post('email');
        $values['user']['country_id'] = $this->input->post('country_id');
		$values['user']['banned'] = $this->input->post('banned');
		$values['user']['role'] = $this->input->post('role');
		
		//let's get the custom user profile  values
		if ($this->config->item('FAL_create_user_profile')==TRUE)
		{	
		    $this->load->model('Userprofile', 'userprofile');
		    
		    //array of fields
  			$db_fields=$this->userprofile->getTableFields();

  			//number of DB fields -1
  			//I put a -1 because I must subtract the 'id' field
  			$num_db_fields=count($db_fields) - 1;
  		
  			//I use 'for' instead of 'foreach' because I have to escape the 'id' field that has key=0 in my array
	  		for ($i=1; $i<=$num_db_fields;  $i++)
			{
				$values['user_profile'][$db_fields[$i]]=$this->input->post($db_fields[$i]);
			}
		 }
		
        //let's treat our banned yes/no checkbox
        if (isset($_POST['banned']) AND $_POST['banned'] =='') 
        {
        	//let's assign value zero (not banned)
        	$values['user']['banned']=0; 
        }

        if (($values['user']['user_name'] != false) && ($values['user']['email'] != false))
        {
            //necessary if password is not reset in edit()
        	if ($values['user']['password'] !='')  
            {
	        	$password = $values['user']['password'];
	        	//encrypts the password (md5)
	        	$values['user']['password'] = $this->freakauth_light->_encode($password);
            }
            else 
            {
            	unset($values['user']['password']);
            }

        	return $values;
        }
        
        return false;
    }
    
    // --------------------------------------------------------------------
        
    /**
     * RULES HELPER FUNCTION
     *
     * @param form value $value
     * @return boolean
     */
	function _password_check($value)
	{	
		if ($value='' AND isset($_POST['id']))
		{
			$callback = '_password_check';
			return $this->_is_valid_text($callback, $value, $this->config->item('FAL_user_password_min'), $this->config->item('FAL_user_password_max'));
		}
	   
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
     * User name duplicate validation callback for validation against duplicate username in DB
     *
     * @access private
     * @param varchar $value
     * @return boolean
     */
    function _username_duplicate_check($value)
	{
		//checks if the request comes from add or edit actions
        $fields='id';
		isset($_POST['id']) ? $where = array('id !='=> $_POST['id'], 'user_name'=>$value) : $where=null;
        $query = $this->usermodel->getUsers($fields, $limit=null, $where);
        
        //checks if the request comes from add or edit actions
        //query in temporary user table (users waiting for activation)
        $this->load->model('FreakAuth_light/UserTemp');
        $fields='id';
        isset($_POST['id']) ? $where = array('id !='=> $_POST['id'], 'user_name'=>$value) : $where=null;
        $query_temp = $this->UserTemp->getUserTemp($fields, $limit=null, $where);
        

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
	    //checks if the request comes from add or edit actions
		//query in main user table (users already activated)
	    $fields='id';
		isset($_POST['id']) ? $where = array('id !='=> $_POST['id'], 'email'=>$value) : $where=null;
        $query = $this->usermodel->getUsers($fields, $limit=null, $where);

        
        //query in temporary user table (users waiting for activation)
        //checks if the request comes from add or edit actions
        //query in main user table (users already activated)
	     $this->load->model('FreakAuth_light/UserTemp');
	     $fields='id';
		 isset($_POST['id']) ? $where = array('id !='=> $_POST['id'], 'email'=>$value) : $where=null;
         $query_temp = $this->UserTemp->getUserTemp($fields, $limit=null, $where);

		
        if (($query != null) && ($query->num_rows() > 0))
	    {
	        $this->validation->set_message('_email_duplicate_check', 'A user with this e-mail has already registered. If you have forgotten your login details you can get them here');
		    
	        $query->free_result();
	        return false;
		}
		
		 if (($query_temp != null) && ($query_temp->num_rows() > 0))
	    {
	        $this->validation->set_message('_email_duplicate_check', 'A user with this e-mail has already registered and is waiting for activation. If this is your e-mail address please check your e-mail inbox and activate your ');
		    
	        $query_temp->free_result();
	        return false;
		}
		
		
		//return true;
	}
	
	
	// --------------------------------------------------------------------
	
    /**
     * RULES HELPER FUNCTION
     * Checks if a country has been chosen in the select country form element
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
    	    	$this->validation->set_message('_country_check', 'Please specify a country');
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