<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * System Front Controller
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Front-controller
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

// CodeIgniter Version
if ( ! defined('CI_VERSION'))
   define('CI_VERSION', '1.6.0');

// CoolBrew Version
if ( ! defined('CB_VERSION'))
   define('CB_VERSION', '1.3.0');

//global $OBJ, $CI;
global $COOLBREW, $action, $result, $local_modules;


/*
 * ------------------------------------------------------
 *  Start the timer... tick tock tick tock...
 * ------------------------------------------------------
 */
	$BM =& load_class('Benchmark');
	$BM->mark('total_execution_time_start');
	$BM->mark('loading_time_base_classes_start');

/*
 * ------------------------------------------------------
 *  Log a message about the get() call
 * ------------------------------------------------------
 */
	log_message('debug', 'get() called from '.$_SERVER['PHP_SELF'].': BASEPATH: '.BASEPATH.' APPPATH: '.APPPATH().' action: '.$action.' class: '.$cb_class.' method: '.$cb_method);

/*
 * ------------------------------------------------------
 *  Instantiate the hooks class
 * ------------------------------------------------------
 */
	$EXT =& load_class('Hooks');

/*
 * ------------------------------------------------------
 *  Is there a "pre_system" hook?
 * ------------------------------------------------------
 */
	$EXT->_call_hook('pre_system');

/*
 * ------------------------------------------------------
 *  Instantiate the config class
 * ------------------------------------------------------
 */
	$CFG =& load_class('Config');

/*
 * ------------------------------------------------------
 *  Instantiate the UTF-8 class
 * ------------------------------------------------------
 *
 * Note: Order here is rather important as the UTF-8
 * class needs to be used very early on, but it cannot
 * properly determine if UTf-8 can be supported until
 * after the Config class is instantiated.
 *
 */
	$UNI =& load_class('Utf8');

/*
 * ------------------------------------------------------
 *  Instantiate the URI class
 * ------------------------------------------------------
 */
	$URI =& load_class('URI', TRUE, TRUE);

/*
 * ------------------------------------------------------
 *  Instantiate the routing class and set the routing
 * ------------------------------------------------------
 */
	$RTR =& load_class('Router', TRUE, TRUE);

if ( ! $COOLBREW['use_uri'])
{
   /* 
    |----------------------------------------------------
    | To fool the system into thinking that each call to this 
    | function is a page load, we modify the $_SERVER['PATH_INFO']
    | variable so that the URI changes accordingly and segments 
    | still work. This is done by adding the class and method 
    | names to the URI as is expected by CodeIgniter.
    |
    | NOTE: This does not currently support application tags mixed
    | with regular tags if the controller is in a sub-directory.
    |
    | The first attempt now looks for REQUEST_URI...
    |----------------------------------------------------
    */
	$URI->_fetch_uri_string();
	$uri_string = $URI->uri_string();
	
   // process the uri_string so we know that our added
   // parts will make sense
   foreach (explode("/", preg_replace("|/*(.+?)/*$|", "\\1", $uri_string)) as $val)
   {
      $val = trim($val);
      if ($val != '')
      {
         $uri_array[] = $val;
      }
   }
   if (isset($uri_array))
   {
//      echo "<pre>"; print_r($uri_array); echo "</pre>";
      if ($COOLBREW['uri_is_complete'])
      {
         // we are supplied a full URI for one application tag on the page, 
         // but this tag has multiple segments, so we need to ignore the
         // supplied class and method in the URI.
         //
         // This needs to be modified to deal with applications with 
         // controllers in subdirectories. In those cases, there will be 
         // three segments that should be unset.
         unset($uri_array[0]);
         unset($uri_array[1]);
      }
      if (count($uri_array) > 0) 
      {
         $uri_string = "/".implode("/", $uri_array)."/";
      }
      else
      {
         $uri_string = "/";
      }
//      echo $uri_string;
//      exit;
   }
   else
   {
      $uri_string = "/";
   }
   $path_info = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : '';
   $_SERVER['PATH_INFO'] = '/'.$cb_class.'/'.$cb_method.$uri_string;
   
   // force the Router class to use $_SERVER['PATH_INFO'] to get the uri
   $CFG->set_item('uri_protocol', 'PATH_INFO');
   $RTR->_set_routing();
   
   // finish by cleaning up
   if ($path_info != '')
   {
      $_SERVER['PATH_INFO'] = $path_info;
   }
   else
   {
      unset($_SERVER['PATH_INFO']);
   }
   unset($uri_string);
   unset($path_info);
}
else
{
   $RTR->_set_routing();
}

/*
 * ------------------------------------------------------
 *  Instantiate the output class
 * ------------------------------------------------------
 */
	$OUT =& load_class('Output', TRUE, TRUE);

/*
 * ------------------------------------------------------
 *	Is there a valid cache file?  If so, we're done...
 * ------------------------------------------------------
 */
	if ($EXT->_call_hook('cache_override') === FALSE)
	{
		if ($OUT->_display_cache($CFG, $RTR) == TRUE)
		{
			return;
		}
	}

/*
 * -----------------------------------------------------
 * Load the security class for xss and csrf support
 * -----------------------------------------------------
 */
	$SEC =& load_class('Security');

/*
 * ------------------------------------------------------
 *  Load the Input class and sanitize globals
 * ------------------------------------------------------
 */
	$IN =& load_class('Input');
	
/*
 * ------------------------------------------------------
 *  Load the Tag class and initiate the parameters
 * ------------------------------------------------------
 */
	$TAG =& load_class('Tag', TRUE, TRUE);
	$TAG->set_params($COOLBREW['params']);

/*
 * ------------------------------------------------------
 *  Load the Collector class
 * ------------------------------------------------------
 */
	$COL =& load_class('Collector');

/*
 * ------------------------------------------------------
 *  Load the Language class
 * ------------------------------------------------------
 */
	$LANG =& load_class('Language');

/*
 * ------------------------------------------------------
 *  Load the local application controller
 * ------------------------------------------------------
 *
 *  Note: The Router class automatically validates the 
 *  controller path. If this include fails it means that 
 *  the default controller in the Routes.php file is not 
 *  resolving to something valid.
 *
 *  This section checks to see if the requested controller
 *  is the local controller. If it is, it does not load
 *  any file and assumes the controller is already loaded.
 *
 */
if ( ! in_array($RTR->fetch_class(), $local_modules))
{
	if ( ! file_exists(APPPATH().'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT))
	{
		show_error('Unable to load your default controller.  Please make sure the controller specified in your Routes.php file is valid.');
	}

	include_once(APPPATH().'controllers/'.$RTR->fetch_directory().$RTR->fetch_class().EXT);
}

// Set a mark point for benchmarking
$BM->mark('loading_time_base_classes_end');

/*
 * ------------------------------------------------------
 *  Is this a "tags" controller?
 * ------------------------------------------------------
 */
$class  = $RTR->fetch_class();
if ($class == 'tags')
{
   $RTR->set_class($cb_module.'_tags');
   $class = $RTR->fetch_class();
}

/*
 * ------------------------------------------------------
 *  Security check
 * ------------------------------------------------------
 *
 *  None of the functions in the app controller or the
 *  loader class can be called via the URI, nor can
 *  controller functions that begin with an underscore
 */
$method = $RTR->fetch_method();

if ( ! class_exists($class)
	OR $method == 'controller'
	OR substr($method, 0, 1) == '_'
	OR in_array($method, get_class_methods('Controller'), TRUE)
	)
{
	log_message('debug', "class (".$class.") or method (".$method.") didn't pass security check.");
	show_404();
}

/*
 * ------------------------------------------------------
 *  Is there a "pre_controller" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('pre_controller');

/*
 * ------------------------------------------------------
 *  Instantiate the controller and call requested method
 * ------------------------------------------------------
 */

// Mark a start point so we can benchmark the controller
$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_start');

$GLOBALS['CI'] = new $class();

// Is this a scaffolding request?
if ($RTR->scaffolding_request === TRUE)
{
	if ($EXT->_call_hook('scaffolding_override') === FALSE)
	{
		$GLOBALS['CI']->_ci_scaffolding();
	}
}
else
{
	/*
	 * ------------------------------------------------------
	 *  Is there a "post_controller_constructor" hook?
	 * ------------------------------------------------------
	 */
	$EXT->_call_hook('post_controller_constructor');
	
	// Is there a "remap" function?
	if (method_exists($GLOBALS['CI'], '_remap'))
	{
		$GLOBALS['CI']->_remap($method, array_slice($URI->rsegments, 2));
	}
	else
	{
		if ( ! method_exists($GLOBALS['CI'], $method))
		{
			show_404();
		}

		// Call the requested method.
		// Any URI segments present (besides the class/function) will be passed to the method for convenience
		$result = call_user_func_array(array(&$GLOBALS['CI'], $method), array_slice($URI->rsegments, 2));		
	}
}

// Mark a benchmark end point
$BM->mark('controller_execution_time_( '.$class.' / '.$method.' )_end');

/*
 * ------------------------------------------------------
 *  Is there a "post_controller" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('post_controller');

/*
 * ------------------------------------------------------
 *  Send the final rendered output to the browser
 * ------------------------------------------------------
 */

if ($EXT->_call_hook('display_override') === FALSE)
{
	$OUT->_display();
}

/*
 * ------------------------------------------------------
 *  Is there a "post_system" hook?
 * ------------------------------------------------------
 */
$EXT->_call_hook('post_system');

/*
 * ------------------------------------------------------
 *  Close the DB connection if one exists
 * ------------------------------------------------------
 */
if (class_exists('CI_DB') AND isset($GLOBALS['CI']->db))
{
	$GLOBALS['CI']->db->close();
}

/*
 * ------------------------------------------------------
 *  Unset the includes array so is reset for the next tag.
 * ------------------------------------------------------
 */
unset($COOLBREW['includes']);

/*
 * ------------------------------------------------------
 *  Unset the main object for this tag so everything is
 *  reset for the next tag.
 * ------------------------------------------------------
 */
unset($GLOBALS['CI']);
// check for $OBJ for users of PHP4
if (isset($GLOBALS['OBJ']))
{
   if (is_object($GLOBALS['OBJ']))
   {
      unset($GLOBALS['OBJ']);
   }
}
else   // we must be using PHP 5
{
   CI_Base::unset_instance();
}

/* End of file CoolBrew.php */
/* Location: ./system/coolbrew/CoolBrew.php */