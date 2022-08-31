<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CONFICURATION ARRAY FOR THE FreakAuth_light library
 * 
 * @package     FreakAuth_light
 * @subpackage  Config
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link 		http://4webby.com/FreakAuth
 * @version 	1.0.2-Beta
 */
 
 global $COOLBREW;

//------------------------------------
// MAIN CONFIGURATION
//------------------------------------

$config['FAL_website_name']	= $COOLBREW['base_url'];

$config['FAL_user_support'] = 'japplega@hain-celestial.com'; 

$config['FAL'] = TRUE;  				  //TRUE/FALSE (boolean).  Whether the FreakAuth system is turned on.

$config['FAL_create_user_profile'] = FALSE;  	//TRUE/FALSE (boolean).  Whether to use custom user profile

$config['FAL_use_security_code_login'] = FALSE; //Whether to use CAPTCHA (security code) functionality for login.

$config['FAL_use_security_code_forgot_password'] = FALSE; //Whether to use CAPTCHA (security code) functionality for forgotten password.

//------------------------------------
// DATABASE CONFIGURATION
//------------------------------------

$config['FAL_table_prefix'] = 'fa_';  //the table prefix for the database tables needed by FrekAuth_light
											 
											//		!!!!!!!!WARNING!!!!!!!
											//NB: the table name for the session library is set in config.php
											//    and won't be affected by this 'FAL_table_prefix' configuration
											//		!!!!!!!!WARNING!!!!!!!

//------------------------------------
// REGISTRATION SETTINGS
//------------------------------------
$config['FAL_allow_user_registration'] = TRUE;  	//TRUE/FALSE (boolean).  Whether users are allowed to register by themself

$config['FAL_use_country'] = FALSE; 	  //Whether to use country listing for registration

$config['FAL_use_security_code_register'] = FALSE; //Whether to use CAPTCHA (security code) functionality for registration.

$config['FAL_temporary_users_expiration'] = 3600*24;  //the time that an new registered user for activation (default 1 day)

//------------------------------------
// USERNAME SETTINGS
//------------------------------------
$config['FAL_user_name_min'] = 4;		//min username length
$config['FAL_user_name_max'] = 16;		//max username length


//------------------------------------
// PASSWORD SETTINGS
//------------------------------------
$config['FAL_user_password_min'] = 6;		//min password length
$config['FAL_user_password_max'] = 16;		//max password length

//------------------------------------
// CAPTCHA SETTINGS
//------------------------------------
$config['FAL_security_code_case_sensitive'] = TRUE; //set to TRUE if you want case sensitive CAPTCHA, to FALSE otherwhise
$config['FAL_security_code_upper_lower_case'] = FALSE; //set to TRUE if you want CAPTCHA using both upper and lower case, to FALSE for using just lower case
$config['FAL_security_code_use_numbers'] = FALSE;     //set to TRUE if you want CAPTCHA to use numbers in the displayed string
$config['FAL_security_code_use_specials'] = FALSE;		//set to TRUE if you want CAPTCHA to use special characters (i.e. ) in the displayed string
																//NOTE: if you set use_specials to TRUE you should use a font that support it
$config['FAL_security_image_library'] = 'GD2';		//the image library you use to make the CAPTCHA image
$config['FAL_security_code_min'] = 5;		//min captcha length
$config['FAL_security_code_max'] = 5;		//max captcha length
$config['FAL_security_code_image_font'] = './system/fonts/Jester.ttf'; //captcha font location
$config['FAL_security_code_image_font_size'] = 20;					//Captcha font size
$config['FAL_security_code_image_font_color'] = '33CC33';			//Captcha font color
$config['FAL_security_code_image_base_image'] = 'base_image.jpg';	//Base image name for captcha (if you wanna change it change the file images\captcha\base_image.jpg)
$config['FAL_security_code_base_image_path'] = 'public/images/captcha/';					//Folder of the Base image needed to generate Captcha
$config['FAL_security_code_image_path'] = 'tmp/';					//Folder to save the captcha background image (relative to the folder in which the index.php resides)
																					//this folder must be writable																					
$config['FAL_security_code_image'] = '';		//name of the generate image (live it blank!!!!!!)

//------------------------------------
// COOKIE (AUTOLOGIN) SETTINGS
//------------------------------------
$config['FAL_auto_login_period'] = 60*60*24*30;		//Time (in seconds) from now that the autologin cookie remains valid.

//------------------------------------
//VALIDATION RULES
//------------------------------------ 
//validation function are in the Freakauth_test.php controller

//  GENERAL RULES
	$config['FAL_country_validation'] = 'trim|required|numeric|xss_clean|callback__country_check';
	
		//PASSWORD
	$config['FAL_password_required_validation'] = 'trim|required|xss_clean|callback__password_check'; //password validation (required)
	$config['FAL_password_required_confirm_validation'] = 'trim|required|xss_clean';	////password confirmation validation (required)				
	
		//USERNAME
	$config['FAL_user_name_duplicate_validation'] = 'trim|required|xss_clean|callback__username_check|callback__username_duplicate_check'; //username validation (required): checks if min-max characters settings are respected
																																//and if username is not already present in DB

	//  LOGIN
$config['FAL_user_name_field_validation_login'] = 'trim|required|xss_clean|callback__username_login_check';          //name in login
$config['FAL_user_password_field_validation_login'] = 'trim|required|xss_clean|callback__password_login_check';  //password in login

	//  REGISTRATION 
$config['FAL_user_name_field_validation_register'] = $config['FAL_user_name_duplicate_validation'];	//registration: name field
$config['FAL_user_password_field_validation_register'] = $config['FAL_password_required_validation'];		//registration: password field 
$config['FAL_user_email_field_validation_register'] = 'trim|required|valid_email|xss_clean|callback__email_duplicate_check';	//registration: e-mail field 
$config['FAL_user_security_code_field_validation_register'] = 'trim|required|xss_clean|callback__securitycode_check';	//registration: captcha field 
$config['FAL_user_country_field_validation_register'] = $config['FAL_country_validation'];	//registration: country selection field

//------------------------------------
// VIEWS SETTINGS
//------------------------------------
$config['FAL_template_dir'] = 'FreakAuth_light/';				//directory of your FreakAuth_light teplates relative to the application/view folder

	 //  LOGIN
$config['FAL_login_view'] = $config['FAL_template_dir'].'content/login';						//The view to display the login form

	//  REGISTRATION
$config['FAL_register_view'] = $config['FAL_template_dir'].'content/register';				//view to display the user registration form
$config['FAL_register_success_view'] = $config['FAL_template_dir'].'content/register_success';				//view to display the successful registration information
$config['FAL_register_activation_success_view'] = $config['FAL_template_dir'].'content/activation_success';	//view to display the successful activation information
$config['FAL_register_activation_failed_view'] = $config['FAL_template_dir'].'content/activation_failed';		//view to display the failed activation information

	//  FORGOTTEN PASSWORD 
$config['FAL_forgotten_password_view'] = $config['FAL_template_dir'].'content/forgotten_password';			//view to display the forgotten password form
$config['FAL_forgotten_password_success_view'] = $config['FAL_template_dir'].'content/forgotten_password_success';				//view to display the successful forgotten password request
$config['FAL_forgotten_password_reset_success_view'] = $config['FAL_template_dir'].'content/forgotten_password_reset_success';	//view to display the successful forgotten password reset
$config['FAL_forgotten_password_reset_failed_view'] = $config['FAL_template_dir'].'content/forgotten_password_reset_failed';		//view to display the failed forgotten password reset

	//  CHANGE PASSWORD 
$config['FAL_change_password_view'] = $config['FAL_template_dir'].'content/change_password';			//view to display the forgotten password form
$config['FAL_change_password_email'] = $config['FAL_template_dir'].'email/change_password_email';		
//------------------------------------
// ACTIONS SETTINGS
//------------------------------------
$config['FAL_login_success_action'] = 'index.html';		//The action to execute after successful login
$config['FAL_logout_success_action'] = 'login.php';		//The action to execute after successful logout

//------------------------------------
// ADMIN ACTIONS SETTINGS
//------------------------------------
$config['FAL_admin_login_success_action'] = 'admin';		//The action to execute after successful ADMIN login
																//relative to base_url
$config['FAL_admin_logout_success_action'] = '';		//The action to execute after successful ADMIN logout

//------------------------------------
$config['FAL_error_delimiter_open'] = '<div class="error">';	//Opening tag for the validation error messages
$config['FAL_error_delimiter_close'] = '</div>';				//closing tag for the validation error messages
	//------------------------------------
	// CSS SETTINGS
	//to customise the errors of you messages we suggest
	//you tu put this in your CSS file
	/*.error {
		font-weight: bold;
		color:#760000;
		padding-left: 18px;
		background: url(images/error.png) no-repeat left top;
	}*/
//------------------------------------
// E-MAIL CONTENT SETTINGS
//------------------------------------
$config['FAL_activation_email'] = $config['FAL_template_dir'].'email/activation_email';						//The location of the activation email
$config['FAL_forgotten_password_email'] = $config['FAL_template_dir'].'email/forgotten_password_email';		// The location of the forgotten password email
$config['FAL_forgotten_password_reset_email'] = $config['FAL_template_dir'].'email/forgotten_password_reset_email';//The location of the forgotten password reset email
$config['FAL_email_from'] = 'webmaster';	


//------------------------------------
// E-MAIL CONTENT SETTINGS
//------------------------------------
$config['FAL_admin_console_records_per_page'] = 2;  //the number or records per page for the admin user listing 
												//(needed for results PAGINATION)

												
//----------------------------------------------------
// ROLES SETTINGS (don't change them)
//----------------------------------------------------
//roles work by inheritance
//this means that the lower the value of the role, the higher in the hierarchy
//i.e superadmin (value 1) has more rights than admin (value 2)
//i.e editor (value 3) has more rights than user (value 100)
//
//you can also set usergroups with the same hierarchy
//i.e. 
//'editor' => 4,
//'gallery_manager' => 4
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
// WARNING do not set custom groups with value 1 or 2
// !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
$config['FAL_roles'] = array(
							//don't change the two following lines//
							 'superadmin' => 1,
							 'admin' => 2,
							 //end don't change
							 
							 //add your custom roles here
							 //'editor' => 3,
							 //'gallery_manager' => 4
							 //--------------------------
							 
							 //don't change the following line
							 'user' => 100,
							);

//----------------------------------------------------
// CUSTOM USER FIELDS SETTINGS (FA_user_profile table)
//----------------------------------------------------
// you can set how many custom validation fields as you want
// in the DB table they can be of any type (varchar, text, int etc.)
//
// WARNING-> The system will authomatically bring the fields from DB
// if you set less rules/fields here in config, the system will use 
// the table field name as field for the table fields not ruled here 
// in config, and won't set any rule for them you can call your fields 
// in DB as you like. The array keys in this config refer to the name 
// of the fields in DB tables

$config['FAL_user_profile_fields_names']= array(
   'store'=>'store',					
   'contact'=>'contact',
   'phone'=>'phone',
   'favorite'=>'favorite',
);

// set the validation rules for your custom user_profile fields here
// if you need callback validation functions, remember to include them 
// in your controllers

$config['FAL_user_profile_fields_validation_rules']= array(
   'store'=>'trim|required',
   'contact'=>'trim|required',
   'phone'=>'trim|required',
   'favorite'=>'trim|required',
);

?>
