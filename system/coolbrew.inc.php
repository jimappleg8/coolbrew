<?php

/*
|---------------------------------------------------------------
| PHP ERROR REPORTING LEVEL
|---------------------------------------------------------------
|
| By default Cool Brew runs with error reporting set to ALL. For security
| reasons you are encouraged to change this when your site goes live.
| For more info visit:  http://www.php.net/error_reporting
|
*/
	error_reporting(E_ALL);

/*
|--------------------------------------------------------------------------
| Sites
|--------------------------------------------------------------------------
|
| List of domains managed by the system
|
| List the domains in the array without the "www". The system will strip
| that out before comparing to this list. That way, you don't have to 
| list both www.domain.com and domain.com if you are serving both. Subdomains
| other than "www" should be fully listed.
|
*/
$COOLBREW['sites'] = array (

   // Different setups allow you to point to different databases.
   // array(
   //   site_id
   //   document_root
   //   
   // )
   // 
   // 'example:8888'                  => array('ex','exdocs','local','dev','en_US'),
   // 'dev.example.com'               => array('ex','exdocs','dev','dev','en_US'),
   // 'stage.example.com'             => array('ex','exdocs','stage','stage','en_US'),
   // 'example.com'                   => array('ex','exdocs','live','live','en_US'),

   'coolbrewdemo:8888'             => array('cbdemo','cbdemodocs','local','dev','en_US'),

   'hcgweb:8888'                   => array('hcgweb','hcgwebdocs','local','dev','en_US'),

   'api-hcgweb:8888'               => array('hcgweb','hcgwebdocs/api','local','local','en_US'),

   'resources-hcgweb:8888'         => array('hcgweb','hcgwebdocs/resources','local','dev','en_US'),

   'rtags-hcgweb:8888'             => array('hcgweb','hcgwebdocs/rtags','local','dev','en_US'),

   'webadmin:8888'                 => array('aa','aadocs','local','dev','en_US'),

);

/*
|--------------------------------------------------------------------------
| Default Domain
|--------------------------------------------------------------------------
|
| The default domain if the current domain is not listed above. You can
| enter different default domains depending on whether the script being
| run is a web page or a command-line script.
|
*/
if ( ! isset($COOLBREW['command_line_interface']))
{
   $default_domain = 'coolbrewcms.com';
}
else
{
   $_SERVER['HTTP_HOST'] = (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '';
   if (preg_match("/8888/", $_SERVER['HTTP_HOST']))
   {
      $default_domain = 'webadmin';
   }
   else
   {
      $default_domain = 'webadmin.ctea.com';
   }
}

/*
|--------------------------------------------------------------------------
| Server Document Directory
|--------------------------------------------------------------------------
|
| The full server path to the directory containing your document root 
| directories for all your websites. Do not include a trailing slash.
|
*/
$server_doc_dir = str_replace('/system/coolbrew.inc.php', '', __FILE__);

/*
|--------------------------------------------------------------------------
| System Directory
|--------------------------------------------------------------------------
|
| The full server path to the system directory. 
| Do not include a trailing slash. 
|
*/
$system_dir = $server_doc_dir."/system";

/*
|--------------------------------------------------------------------------
| Local Modules
|--------------------------------------------------------------------------
|
| A list of modules where controllers will be local. For more information:
| 
|     http://www.coolbrewcms.com/general/local-controllers.html
|
*/
$local_modules = array('local');

/*
|--------------------------------------------------------------------------
| Modules Directory
|--------------------------------------------------------------------------
|
| The name of the folder containing modules
|
*/
$module_dir = "modules";


/*
|================================================
| END OF USER CONFIGURABLE SETTINGS
|================================================
*/

/*
 * ------------------------------------------------------------
 *  Define Application Variables
 * ---------------------------------------------------------------
 *
 * These are defined in the get() function, but I need to define
 * them here first to avoid notices from PHP.
 */
$COOLBREW['APPPATH'] = '';
$COOLBREW['SELF'] = basename($_SERVER['SCRIPT_NAME']);
$COOLBREW['uri_is_complete'] = FALSE;
$COOLBREW['command_line_interface'] = isset($COOLBREW['command_line_interface']) ? $COOLBREW['command_line_interface'] : FALSE;

/*
 * ------------------------------------------------------
 *  Determine which website is calling this config file
 * ------------------------------------------------------
 */
if ( ! $COOLBREW['command_line_interface'])
{
   $http_host = $_SERVER['HTTP_HOST'];
   $request_uri = $_SERVER['REQUEST_URI'];
   $server_protocol = $_SERVER['SERVER_PROTOCOL'];
}
else
{
   $http_host = '';
   $request_uri = '';
   $server_protocol = '';
}
   
if (preg_match("/www./", $http_host))
{
   $COOLBREW['domain'] = str_replace("www.", "", $http_host);
}
else
{
   $COOLBREW['domain'] = $http_host;
}

if ( ! empty($COOLBREW['sites'][$COOLBREW['domain']][0]))
{
   $COOLBREW['site_id'] = $COOLBREW['sites'][$COOLBREW['domain']][0];
   $COOLBREW['doc_root_base'] = $COOLBREW['sites'][$COOLBREW['domain']][1];
   $COOLBREW['server_level'] = $COOLBREW['sites'][$COOLBREW['domain']][2];
   $COOLBREW['db_server_level'] = $COOLBREW['sites'][$COOLBREW['domain']][3];
   $COOLBREW['default_locale'] = $COOLBREW['sites'][$COOLBREW['domain']][4];
}
else  // set the default
{
   $COOLBREW['site_id'] = $COOLBREW['sites'][$default_domain][0];
   $COOLBREW['doc_root_base'] = $COOLBREW['sites'][$default_domain][1];
   $COOLBREW['server_level'] = $COOLBREW['sites'][$default_domain][2];
   $COOLBREW['db_server_level'] = $COOLBREW['sites'][$default_domain][3];
   $COOLBREW['default_locale'] = $COOLBREW['sites'][$default_domain][4];
}

/*
 * ------------------------------------------------------
 *  Change the system_dir if the site is a staging one
 *  so we can have a stage version of the system.
 * ------------------------------------------------------
 */
if ($COOLBREW['server_level'] == 'stage')
{
   $system_dir = $server_doc_dir."/systemstage";
}

/*
 * ------------------------------------------------------
 *  Set the cookie domain variable. If the domain is
 *  local (e.g. localhost), it needs to be set to FALSE 
 *  or cookies won't work
 * ------------------------------------------------------
 */
if (count(explode('.', $COOLBREW['domain'])) < 2)
{
   $COOLBREW['cookie_domain'] = FALSE;
}
else
{
   $COOLBREW['cookie_domain'] = ".".$COOLBREW['domain'];
   session_set_cookie_params(0, '/', $COOLBREW['cookie_domain']);
}

/*
 * ------------------------------------------------------
 *  Set the Document Root Directory variable
 * ------------------------------------------------------
 */
$COOLBREW['doc_root_dir'] = $server_doc_dir . "/" .
   $COOLBREW['doc_root_base'];

/*
 * ------------------------------------------------------
 *  Set the Base URL variable
 * ------------------------------------------------------
 */   
if (preg_match('/^HTTPS/i', $server_protocol))
{
   $COOLBREW['protocol'] = 'https://';
}
else 
{
   $COOLBREW['protocol'] = 'http://';
}
$COOLBREW['base_url'] = $COOLBREW['protocol'].$http_host;
$COOLBREW['base_url'] = trim($COOLBREW['base_url'], "/");


/*
 * ------------------------------------------------------------
 *  Define Application Constants
 * ---------------------------------------------------------------
 *
 * EXT             - The file extension.  Typically ".php"
 * FCPATH	       - The full server path to THIS file
 * SERVERPATH	   - The full server path to the folder containing doc folders
 * BASEPATH	       - The full server path to the "system" folder
 * DOCPATH         - The full server path to the document root folder
 * DOCROOT         - And alias for DOCPATH
 *
 * SITE_ID         - The ID of the site requesting this page
 * SERVER_LEVEL    - Whether the site is dev, stage, or live
 * DB_SERVER_LEVEL - The server level used to set the database connection
 *
 */
if ( ! defined('EXT'))
   define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
if ( ! defined('FCPATH'))
   define('FCPATH', __FILE__);
if ( ! defined('SERVERPATH'))
   define('SERVERPATH', $server_doc_dir.'/');
if ( ! defined('BASEPATH'))
   define('BASEPATH', $system_dir.'/');
if ( ! defined('DOCPATH'))
   define('DOCPATH', $COOLBREW['doc_root_dir'].'/');
if ( ! defined('DOCROOT'))
   define('DOCROOT', $COOLBREW['doc_root_dir'].'/');

if ( ! defined('SITE_ID'))
   define('SITE_ID', $COOLBREW['site_id']);
if ( ! defined('SERVER_LEVEL'))
   define('SERVER_LEVEL', $COOLBREW['server_level']);
if ( ! defined('DB_SERVER_LEVEL'))
   define('DB_SERVER_LEVEL', $COOLBREW['db_server_level']);

/*
 * ---------------------------------------------------------------
 *  Define E_STRICT
 * ---------------------------------------------------------------
 *
 * Some older versions of PHP don't support the E_STRICT constant
 * so we need to explicitly define it otherwise the Exception class 
 * will generate errors.
 *
*/
if ( ! defined('E_STRICT'))
   define('E_STRICT', 2048);

/*
 * ------------------------------------------------------
 *  Load the global functions
 * ------------------------------------------------------
 */
require_once(BASEPATH.'coolbrew/Common'.EXT);

/*
 * ------------------------------------------------------
 *  Load the compatibility override functions
 * ------------------------------------------------------
 */
require_once(BASEPATH.'coolbrew/Compat'.EXT);
	
/*
 * ------------------------------------------------------
 *  Define a custom error handler so we can log PHP errors
 * ------------------------------------------------------
 */
set_error_handler('_exception_handler');

/*
 * ------------------------------------------------------
 *  Load the app controller
 * ------------------------------------------------------
 *
 *  Note: Due to the poor object handling in PHP 4 we'll
 *  conditionally load different versions of the base
 *  class. Retaining PHP 4 compatibility requires a bit of a hack.
 *
 *  Note: The Loader class needs to be included first
 *
 */
if (floor(phpversion()) < 5)
{
	load_class('Loader', FALSE);
	require_once(BASEPATH.'coolbrew/Base4'.EXT);
}
else
{
	require_once(BASEPATH.'coolbrew/Base5'.EXT);
}

// Load the base controller class
load_class('Controller', FALSE);



/**
 * Routes the requested tag to the correct class and method
 */
function get()
{
   global $CI;
   global $COOLBREW, $action, $cb_class, $cb_method;
   global $local_modules, $module_dir;
      
   /*
   |------------------------------------------------------------
   | PROCESS THE TAG PARAMETERS
   |---------------------------------------------------------------
   |
   | get arguments and separate into action and parameters
   |
   */
   $params = array();
   $COOLBREW['params'] = array();

   $num_params = func_num_args();
   if ($num_params > 0)
   {
      $action = func_get_arg(0);
      if ($num_params > 1)
      {
         for ($i = 1; $i < $num_params; $i++)
         {
            $COOLBREW['params'][$i-1] = func_get_arg($i);
         }
      }
   }
   else
   {
      show_error('get() function requires at least one parameter.');
   }
   
   /*
   |------------------------------------------------------------
   | DETERMINE MODULE, CLASS AND METHOD
   |------------------------------------------------------------
   |
   | If there is only one item specified, it must be the module
   | and we are emulating a CI index.php file.
   |
   | If only two items are specified, assume that the module 
   | and class are the same. I'm not all that thrilled about this
   | assumption since it limits how the tag can be used in order
   | to be backwards compatible. I would like to be able to use
   | 'faqs.admin' for instance to run an admin application.
   |
   */
   $cb_actions = explode(".", $action);

   if (count($cb_actions) == 1)
   {
      $COOLBREW['use_uri'] = true;
      $cb_module = $cb_actions[0];
      $cb_class = "";
      $cb_method = "";
   }
   elseif (count($cb_actions) == 2)
   {
      // look for a tags controller first
      if (file_exists(BASEPATH.$module_dir.'/'.$cb_actions[0].'/controllers/tags.php'))
      {
         $COOLBREW['use_uri'] = false;
         $cb_module = $cb_actions[0];
         $cb_class = 'tags';
         $cb_method = $cb_actions[1];
      }
      // if not found, assume the controller is the same name as the module
      else
      {
         $COOLBREW['use_uri'] = false;
         $cb_module = $cb_actions[0];
         $cb_class = $cb_actions[0];
         $cb_method = $cb_actions[1];
      }
   }
   else
   {
      $COOLBREW['use_uri'] = false;
      $cb_module = $cb_actions[0];
      $cb_class = $cb_actions[1];
      $cb_method = $cb_actions[2];
   }
   
   /*
   |------------------------------------------------------------
   | DEFINE APPLICATION VARIABLES
   |------------------------------------------------------------
   |
   | These are constants in CodeIgniter, but we make them
   | variables in Cool Brew because they must change for each
   | call to the get() function.
   |
   | $COOLBREW['APPPATH'] - The full server path to the "application" folder
   | $COOLBREW['SELF'] - The name of THIS file (typically "index.php)
   |
   */ 
   $COOLBREW['APPPATH'] = BASEPATH.$module_dir.'/'.$cb_module.'/';
   $COOLBREW['MODULE'] = $cb_module;
   
   /*
   |---------------------------------------------------------------
   | LOAD THE FRONT CONTROLLER
   |---------------------------------------------------------------
   |
   | And away we go...
   |
   */
   include BASEPATH.'coolbrew/CoolBrew'.EXT;

   /*
   |------------------------------------------------------
   | RETURN ANY RESULTS FROM THE CALLED CLASS AND METHOD.
   |------------------------------------------------------
   */
   return $result;

}

/* End of file coolbrew.inc.php */
/* Location: ./system/coolbrew.inc.php */