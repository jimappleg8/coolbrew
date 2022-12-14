<?php
class Installer extends Controller {
	
   function Installer()
    {
        parent::Controller();
        
        //loads necessary libraries
        $this->lang->load('freakauth');
        $this->load->library('validation');

		//sets the necessary form fields
		$fields['user_name'] = $this->lang->line('FAL_user_name_label');
        $fields['password'] = $this->lang->line('FAL_user_password_label');
        
        //additionalFields($fields);
        
        $this->validation->set_fields($fields);
    }
// --------------------------------------------------------------------
        
	
	function index()
	{		
		$data['heading'] = 'FreakAuth';
        
        $data['installer'] = $this->_init();
			
         $this->db->where('role', 'superadmin');
         $query = $this->db->get($this->config->item('FAL_table_prefix').'user');
         $how_many_superadmins = $query->num_rows();
         
        if (!in_array('0', $data['installer']['check']) AND $how_many_superadmins<1)
		{
        	$data['superadmin'] = $this->_insert_SuperAdmin();
        	$data['ins_superadmin']=TRUE;
        	$data['superadmin_msg'] = 'Enter the data for the system administrator. <img src="'.base_url().'public/css/images/error.png"> Take note of your username and password!';        	
		}
		elseif (!in_array('0', $data['installer']['check']) AND $how_many_superadmins>0)
		{	$data['superadmin'] = FALSE;
			$data['ins_superadmin']=FALSE;
			$data['superadmin_msg'] = '<img src="'.base_url().'public/css/images/tick.png"> there is a superadmin in DB';
		}
		else 
		{
			$data['superadmin'] = FALSE;
			$data['ins_superadmin']=FALSE;
			$data['superadmin_msg'] = '<img src="'.base_url().'public/css/images/error.png"> When all settings will be OK, a form to enter the superadmin will be displayed';
		}
		
		
		$data['message']='<h1>FrekAuth_light installer</h1>';
		$data['message'].='<p>We assume that:</p>'."\n";
		$data['message'].='<ul>'."\n";
		$data['message'].='  <li>you have read the included installation.txt file</li>'."\n";
		$data['message'].='  <li>you have created a DB</li>'."\n";
		$data['message'].='  <li> you have created the necessary DB tables for FreakAuth_light&copy; to work</li>'."\n";
		$data['message'].='  <li>you have unzipped and uploaded all the necessary files of FreakAuth_light&copy;</li>'."\n";
		$data['message'].='</ul>';
		$data['message'].='<p>After creating you DB, and the necessary FreakAuth_light DB tables, here we check that everything is ok for FreakAuth_light to work properly. </p><p class="important">After creating the superadmin #1 REMOVE THIS FILE: (system/application/controllers/installer.php)</p><p> and login:</p>';
		$data['message'].="<span class=\"important\">For logging in you must register as user ".anchor('auth/register', 'here')."</span>";
		$data['message'].="<ul><li>".anchor('auth/login', 'login here')."</li></ul><hr>";
		
        
		$this->load->vars($data);

		$this->load->view($this->config->item('FAL_template_dir').'template/container');
		
		//$this->output->enable_profiler(TRUE);
	}
// --------------------------------------------------------------------
	
	function _init()
	{
		$data['installer']=TRUE;
		
		$prefix= $this->config->item('FAL_table_prefix');
		//check if DB tables in database
		$tables[] = $this->db->list_tables();
		
		$necessary_tables = array('cb_sessions', $prefix.'country', $prefix.'user', $prefix.'user_profile', $prefix.'user_temp');

		foreach ($necessary_tables as $key=>$table)
		{
			in_array($table, $tables[0]) ? $data['missing_tb'][$table]='<img src="'.base_url().'public/css/images/tick.png"> ok' : $data['missing_tb'][$table]='<img src="'.base_url().'public/css/images/error.png">missing' ;

		}

		if (in_array('<img src="'.base_url().'public/css/images/error.png">missing', $data['missing_tb'])==FALSE)
		{
			$data['tables']='<img src="'.base_url().'public/css/images/tick.png"> ok';
			$data['check'][1] = 1;
		}
		else
		{ 
			$data['tables']='<span class="error">Some necessary DB table is missing. Please install database_schema.sql</span>';
			$data['check'][1] = 0;
		}
		
		//check if FreakAuth turned on in config
		if ($this->config->item('sess_use_database'))
		{
			$data['DB_session'] = '<img src="'.base_url().'public/css/images/tick.png"> DB_session ON';
			$data['check'][2] = 1;
		}
		else 
		{
			$data['DB_session'] = '<span class="error">DB_session OFF</span>';
			$data['check'][2] = 0;
		}
		
		//check if FreakAuth turned on in config
		if ($this->config->item('FAL'))
		{
			$data['system_on'] = '<img src="'.base_url().'public/css/images/tick.png"> FreakAuth_light ON';
			$data['check'][3] = 1;
		}
		else 
		{
			$data['system_on'] = '<span class="error">FreakAuth_light OFF</span>';
			$data['check'][3] = 0;
		}
		
		//check if $config['encryption-key'] is set and !=ciao
		if (strlen($this->config->item('encryption_key')) AND  $this->config->item('encryption_key')!='ciao')
		{
			$data['enc_key']='<img src="'.base_url().'public/css/images/tick.png"> encryption key OK';
			$data['check'][4] = 1;
		}
		else 
		{
			$data['enc_key']='<span class="error">Please set your <b>$config[encryption_key]</b> or change it from <b>ciao</b> in config.php</span>';
			$data['check'][4] = 0;
		}
		
		//check if website name set
		if ($this->config->item('FAL_website_name')!='' AND $this->config->item('FAL_website_name')!='YOUR_DOMAIN.com')
		{
			$data['w_name'] = '<img src="'.base_url().'public/css/images/tick.png"> OK -> '.$this->config->item('FAL_website_name');
			$data['check'][5] = 1;
		}
		else 
		{
			$data['w_name'] = '<span class="error">please define your website name in config/freakauth_light.php</span>';
			$data['check'][5] = 0;
		}

		
		//check if admin e_mail set
		if ($this->config->item('FAL_user_support')!='' AND $this->config->item('FAL_user_support')!='you@your-email.com')
		{
			$data['email'] = '<img src="'.base_url().'public/css/images/tick.png"> email OK-> '.$this->config->item('FAL_user_support');
			$data['check'][6] = 1;
		}
		else 
		{
			$data['email']='<span class="error">please define your email in config/freakauth_light.php</span>';
			$data['check'][6] = 0;
		}
		
		//insert superadmin
		return $data;
	}
// --------------------------------------------------------------------

	
	function _insert_SuperAdmin()
    {      
    	//set validation rules
    	$rules['user_name'] = 'trim|required|xss_clean|callback__admin_name_check';
        $rules['password'] = 'trim|required|xss_clean|callback__password_check';
        $rules['password_confirm'] = "trim|required|xss_clean|matches[password]";
        $rules['email'] = 'trim|required|valid_email|xss_clean';
        
        $this->validation->set_rules($rules);
        
        //set form fields for validation
        $fields['user_name'] = 'superadmin name';
        $fields['password'] = 'password';
        $fields['password_confirm'] = 'password confirm';
        $fields['email'] = 'email';
    	$this->validation->set_fields($fields);
    	
    	$this->validation->set_error_delimiters('<div class="error">', '</div>');
    	
    	//if validation unsuccesfull & data not ok
        if ($this->validation->run() == FALSE)
		{

	    	$data = false;
            				
	      return $data;
		}
		//if everything ok
		else
		{	 
			$values=$this->_get_form_values();
        	$this->load->model('FreakAuth_light/usermodel');
        	//insert data in DB
        	$this->usermodel->insertUser($values);      	
        	
			//set a flash message
			$msg = $this->db->affected_rows().' new administrator successfully added!';
			$this->session->set_flashdata('flashMessage', $msg, 1);
			$data = true;
            				
	        return $data;
		}
        	
    }
// --------------------------------------------------------------------
    
    function _get_form_values()
    {    	
        $values['user_name'] = $this->input->post('user_name', TRUE);
        $values['password'] = $this->input->post('password');
        $values['email'] = $this->input->post('email');
        $values['role'] = 'superadmin';
		

        if (($values['user_name'] != false) && ($values['email'] != false))
        {
            //necessary if password is not reset in edit()
        	if ($values['password'] !='')  
            {
	        	$password = $values['password'];
	        	//encrypts the password (md5)
	        	$values['password'] = $this->freakauth_light->_encode($password);
            }
            else 
            {
            	unset($values['password']);
            }

        	return $values;
        }
        
        return false;
    }
// --------------------------------------------------------------------
    
    function _admin_name_check($value)
	{	
		$callback = '_admin_name_check';
	    return $this->_is_valid_text($callback, $value, $this->config->item('FAL_user_name_min'), $this->config->item('FAL_user_name_max'));
	}
// --------------------------------------------------------------------
	
	function _password_check($value)
	{	
		$callback = '_password_check';
	    return $this->_is_valid_text($callback, $value, $this->config->item('FAL_user_password_min'), $this->config->item('FAL_user_password_max'));
	}
// --------------------------------------------------------------------
	
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