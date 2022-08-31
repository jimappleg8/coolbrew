<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Helper for the FreakAuth library
 * 
 * @package     FreakAuth_light
 * @subpackage  Helpers
 * @category    Authentication
 * @author      Daniel Vecchiato (danfreak)
 * @copyright   Copyright (c) 2007, 4webby.com
 * @license		http://www.gnu.org/licenses/gpl.html
 * @link 		http://4webby.com/FreakAuth
 * @version 	1.0.2-Beta
 */
 
// ------------------------------------------------------------------------

/**
 * FreakAuth Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Security
 * @author      Chris Schletter
 * @copyright   Copyright (c) 2006, thZero.com
 * @license     see FreakAuth License.txt included with the package
 */

// ------------------------------------------------------------------------
//
// Returns the currently logged on user's name.
// Returns an empty string if no user is logged in.
//
function getUserName()
{
    $obj =& get_instance();
    return $obj->freakauth_light->getUserName();
}

// ------------------------------------------------------------------------
//
// Checks to see if a user is an administrator.  
// Returns true if FreakAuth system is not activated.
// Returns true if admin or superadmin, otherwise false.
//
function isAdmin()
{
    $obj =& get_instance();
    return $obj->freakauth_light->isAdmin();
}

// ------------------------------------------------------------------------
//
// Checks to see if a user is logged in.  
// Returns true if FreakAuth system is not activated.
// Returns the user_id if valid, otherwise false.
//
function isValidUser()
{
    $obj =& get_instance();
    return $obj->freakauth_light->isValidUser();
}

// ------------------------------------------------------------------------
	/**
	* Function used to used to check if a logged in members belongs to the custom role (group) specified in the first parameter
     * it requires 2 optional parameters
     * The first parameter specifies the user groups as a comma separated string(NB: just comma separated WITHOUT SPACES->'user,admin'<--RIGHT 'user,admin'<--WRONG)
     * The second parameter specifies whether we want to check to the specified groups ONLY or for AT LEAST those group membership in the hierarchy
     * (returns true also if the logged user belongs to a group higher in the hierarchy)
     * 
     * example usage in a view to echo something depending on it's role (it can be a menu option for example)
     * 
     * 1) <?=belongsToGroup() ? $display-this : $display_that;?> //displays-this if the visitor is logged in and he is AT LEAST an user, $display_that otherwise
     * 2) <?=belongsToGroup('user,editor')? $display-this : $display_that;?>  //displays-this if the visitor is logged in and he is AT LEAST an user or an editor (therefore it displays-this also if he belongs to user-groups higher in the hierarchy (i.e. superadmin), $display_that otherwise
     * 3) <?=belongsToGroup('admin', true)? $display-this : $display_that;?>  //displays-this if the visitor is logged in and is an 'admin' ONLY, $display_that otherwise 
     * 
     * @param string containing comma separated user groups i.e. "user, editor, moderator"
     * @param boolean $_only
     * @return true/false
     */
function belongsToGroup($group=null, $only=null)
{
    $obj =& get_instance();
    return $obj->freakauth_light->belongsToGroup($group, $only);
}
// ------------------------------------------------------------------------
//
function loginAnchor($logout_attributes = null, $login_attributes = null)
{
    $obj =& get_instance();
	$obj->lang->load('freakauth');
	return (belongsToGroup() ? $obj->lang->line('FAL_welcome')." ".getUserName()." / ".anchor('auth/logout', $obj->lang->line('FAL_logout_label'), $logout_attributes) : "welcome Guest / ".anchor('auth/index', $obj->lang->line('FAL_login_label'), $login_attributes));
}

// ------------------------------------------------------------------------
//
function loginAnchorAdmin($logout_attributes = null, $login_attributes = null)
{
    $obj =& get_instance();
	$obj->lang->load('freakauth');
	return (isAdmin() ? "welcome ".getUserName()." [ ".anchor('auth/logout', $obj->lang->line('FAL_logout_label'), $logout_attributes)." ]" : "welcome Guest / ".anchor('auth/index', $obj->lang->line('FAL_login_label'), $login_attributes));
}

// ------------------------------------------------------------------------
//
function displayLoginForm()
{
	$obj =& get_instance();
	$obj->lang->load('freakauth');
    return $obj->freakauth_light->getLoginForm();
}
?>
