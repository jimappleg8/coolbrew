<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Language file needed by the FreakAuth library
 * @package     FreakAuth_light
 * @subpackage  Languages
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/lgpl.html
 * @link 		http://4webby.com/FreakAuth
 * @version 	1.0.2-Beta
 */

//------------------------------------------------------------------
//WEBSITE STUFF
//------------------------------------------------------------------
$lang['FAL_welcome'] = 'Welcome on board ';
$lang['FAL_email_halo_message'] = 'Dear';
$lang['FAL_user_name_label'] = 'User Name';
$lang['FAL_user_password_label'] = 'Password';
$lang['FAL_user_password_confirm_label'] = 'Confirm Password';
$lang['FAL_user_email_label'] = 'Email';
$lang['FAL_user_autologin_label'] = 'remember me';
$lang['FAL_user_security_code_label'] = 'Security Code';
$lang['FAL_user_country_label'] = 'Country';

$lang['FAL_login_label'] = 'Login';
$lang['FAL_logout_label'] = 'Logout';

$lang['FAL_cancel_label'] = 'Cancel';
$lang['FAL_agree_label'] = 'I Agree';
$lang['FAL_continue_label'] = 'Continue';
$lang['FAL_donotagree_label'] = 'I Do Not Agree';
$lang['FAL_forgotten_password_label'] = 'Forgotten Password?';
$lang['FAL_register_label'] = 'Register';
$lang['FAL_submit_label'] = 'Submit';

$lang['FAL_citation_message'] = 'Thank You!';

//------------------------------------------------------------------
//CAPTCHA
//------------------------------------------------------------------
$lang['FAL_captcha_label'] = 'Security code';
$lang['FAL_captcha_message'] = 'Please type the code you see in the image below';

//------------------------------------------------------------------
//REGISTRATION
//------------------------------------------------------------------
$lang['FAL_register_cancel_confirm'] = 'Are you sure you want to decline the Terms of Service? Click Cancel to continue with registration.';

$lang['FAL_register_sucess_message'] = 'Thank You!<BR/>Your registration has been successfully completed.<BR/><BR/>You have just been sent an email containing membership activation instructions.<BR/>';

$lang['FAL_invalid_register_message'] = 'Invalid registration attempt.';

$lang['FAL_terms_of_service_message'] =
'All messages posted at this site express the views of the author, and do not necessarily reflect the views of the owners and administrators of this site.

By registering at this site you agree not to post any messages that are obscene, vulgar, slanderous, hateful, threatening, or that violate any laws.   We will permanently ban all users who do so.

We reserve the right to remove, edit, or move any messages for any reason.
';

//------------------------------------------------------------------
//ACTIVATION
//------------------------------------------------------------------
$lang['FAL_activation_email_subject'] = 'Account activation info';
$lang['FAL_activation_email_body_message'] = 
'Thank you for your new member registration.

To activate your new account, please visit the following URL in the next 24 hours:';

$lang['FAL_activation_login_instruction'] ='After activating your accout you can login as:';
$lang['FAL_activation_keep_data'] ='Keep this e-mail for future reference!';

$lang['FAL_activation_failed_message'] = 'We are sorry, but your activation has failed.  Contact the site administrator for assistance.';
$lang['FAL_activation_sucess_message'] = 'Your activation has been successfully completed.  Feel free to login.';




//------------------------------------------------------------------
//VALIDATION ERROR MESSAGES
//------------------------------------------------------------------
$lang['FAL_invalid_user_message'] = 'Invalid user.';
$lang['FAL_invalid_username_message'] = 'Invalid username';
$lang['FAL_invalid_password_message'] = 'Invalid password';
$lang['FAL_username_first_password_message'] = 'Please enter your username first and then type your password';
$lang['FAL_banned_user_message'] = 'Go away man!';
$lang['FAL_login_message'] = 'You have successfully logged in.';
$lang['FAL_logout_message'] = 'You have successfully logged out.';
$lang['FAL_lenght_validation_message'] = 'must be between %s and %s characters in length.';
$lang['FAL_allowed_characters_validation_message'] = 'Only alpha characters, digits, underline or dash characters allowed.';
$lang['FAL_invalid_validation_message'] = 'The %s is invalid: ';
$lang['FAL_in_use_validation_message'] = 'The %s is already in use.';
$lang['FAL_country_validation_message'] = 'Please choose the country where you live';

//------------------------------------------------------------------
//CHANGE PASSWORD
//------------------------------------------------------------------
$lang['FAL_change_password_success'] = 'Your password has been successfully changed';



//------------------------------------------------------------------
//FORGOTTEN PASSWORD
//------------------------------------------------------------------
//->email
$lang['FAL_forgotten_password_email_subject'] = 'Forgotten password reminder';
$lang['FAL_forgotten_password_email_reset_subject'] = 'New login information';
$lang['FAL_forgotten_password_email_header_message'] = 'To reset your password, please go to the following page:';
$lang['FAL_forgotten_password_email_body_message'] = 
'
You or somebody else requested a password remind about your account.

If You do not remember your password and want to change it, please click on the following link:';

$lang['FAL_forgotten_password_email_body_message2'] ='If You did not do any request please ignore this e-mail.';

$lang['FAL_forgotten_password_reset_email_body_message'] = 'Here is your new login information:';
$lang['FAL_forgotten_password_reset_email_user_label'] = 'Username';
$lang['FAL_forgotten_password_reset_email_password_label'] = 'Password';

$lang['FAL_forgotten_password_email_change_message'] = 'To change this ugly password, please go to the following page:';

//->messages
$lang['FAL_forgotten_password_success_message'] = 'Instructions for resetting your password have just been emailed to you. ';
$lang['FAL_forgotten_password_user_not_found_message'] = 'We are sorry, but a user was not found with the email address you provided';
$lang['FAL_forgotten_password_reset_failed_message'] = 'We are sorry, but your forgotten password reset has failed.  Contact the site administrator for assistance.';
$lang['FAL_forgotten_password_reset_success_message'] = 'Your password has been reset and emailed to you.';

//------------------------------------------------------------------
//E-MAILS (GENERAL)
//------------------------------------------------------------------
$lang['FAL_email_from_name'] = 'Administrator';

//------------------------------------------------------------------
//FLASH MESSAGES
//------------------------------------------------------------------
$lang['FAL_user_added'] = ' new user successfully added!';
$lang['FAL_user_edited'] = ' user edited successfully!';
$lang['FAL_user_deleted'] = ' user successfully deleted!';
$lang['FAL_no_permissions'] = 'You don\'t have the credentials to access this area';
$lang['FAL_no_records'] = 'No records in DB';
?>