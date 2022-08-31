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

class Example extends Controller
{	
	/**
	 * Initialises the controller
	 *
	 * @return Admin
	 */
    function Example()
    {
        parent::Controller();
        
        ////////////////////////////
		//CHECKING FOR PERMISSIONS
		///////////////////////////
		//-------------------------------------------------
        //only 'admin' and 'superadmin' can manage users
        
        $this->freakauth_light->check('admin');
        
        //-------------------------------------------------
        //END CHECKING FOR PERMISSION
    	
    }
	
    	// --------------------------------------------------------------------
	
    /**
     * Do what you want here
     *
     */
    function index()
    {
		$data['heading']='Admin Console home';
		$data['action']='Just an example';
		$data['content']="<p>Do what do You want with this controller!</p>"
						 ."<p>Click <b>".anchor('admin/example/restrict_example', 'here')."</b>: if you are a superadmin you will see something</p>"
						 ."<p>I'm sure You are smart enough!</p>";
		
		$data['page'] = 'FreakAuth_light/template_admin/example/example';
        
        $this->load->vars($data);
	        
	    $this->load->view($this->config->item('FAL_template_dir').'template_admin/container');
        
    }
	
    // --------------------------------------------------------------------
	/**
     * Let's restric a method just to superadmin
     *
     */
    function restrict_example()
    {
		
    	////////////////////////////
		//CHECKING FOR PERMISSIONS
		///////////////////////////
		//-------------------------------------------------
        //only 'admin' and 'superadmin' can manage users
        
        $this->freakauth_light->check('superadmin');
        
        //-------------------------------------------------
        //END CHECKING FOR PERMISSION
        
        $data['heading']='Admin Console home';
		$data['action']='Just an example';
		$data['content']="<p>You can view this because yo are a super admin</p>";

		
		$data['page'] = 'FreakAuth_light/template_admin/example/example';
        
        $this->load->vars($data);
	        
	    $this->load->view($this->config->item('FAL_template_dir').'template_admin/container');
        
    }

}
?>