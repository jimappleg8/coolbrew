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
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Common Functions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

// ------------------------------------------------------------------------

global $COOLBREW;

// ------------------------------------------------------------------------

/**
* Determines if the current version of PHP is greater then the supplied value
*
* Since there are a few places where we conditionally test for PHP > 5
* we'll set a static variable.
*
* @access	public
* @param	string
* @return	bool	TRUE if the current version is $version or higher
*/
if ( ! function_exists('is_php'))
{
	function is_php($version = '5.0.0')
	{
		static $_is_php;
		$version = (string)$version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}
}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on.
 *
 * @access	private
 * @return	void
 */
if ( ! function_exists('is_really_writable'))
{
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif ( ! is_file($file) OR ($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}
}

// ------------------------------------------------------------------------

/**
* Returns the APPPATH value
*
* This is a workaround for an issue with what are constants
* in the CodeIgniter framework. Since I'm calling the system
* multiple times, and three of the constants need to change each
* call, I can't use constants. Instead, I've decided to make
* them function calls which gives me the same kind of global
* access and a small footprint.
*
* @access	public
* @return	string
*/
function APPPATH()
{
   global $COOLBREW;
   return $COOLBREW['APPPATH'];
}

// ------------------------------------------------------------------------

/**
* Returns the SELF value
*
* @access	public
* @return	string
*/
function SELF()
{
   global $COOLBREW;
   return $COOLBREW['SELF'];
}

// ------------------------------------------------------------------------

/**
* Returns the MODULE value
*
* @access	public
* @param    string    a new module name
* @return	string
*/
function MODULE($module_id = '')
{
   global $COOLBREW;

   if ($module_id != '')
      $COOLBREW['MODULE'] = $module_id;

   return $COOLBREW['MODULE'];
}

// ------------------------------------------------------------------------

/**
* Returns the Site Config Data array
*
* @access	public
* @param    string    a site id
* @return	string
*/
function site_config($site_id = '')
{
   global $COOLBREW;
   
   $site_id = ($site_id == '') ? SITE_ID : $site_id;
   
   // set defaults in case the requested site is not locally hosted
   $result['ActiveDomain'] = '';
   $result['Protocol'] = $COOLBREW['protocol'];
   $result['SiteID'] = $site_id;
   $result['DocRootDir'] = '';
   $result['ServerLevel'] = '';
   $result['DBServerLevel'] = '';
   $result['DefaultLocale'] = '';
   
   foreach ($COOLBREW['sites'] AS $domain => $values)
   {
      if ($values[0] == $site_id && $values[2] == SERVER_LEVEL)
      {
         $result['ActiveDomain'] = $domain;
         $result['Protocol'] = $COOLBREW['protocol'];
         $result['SiteID'] = $values[0];
         $result['DocRootDir'] = $values[1];
         $result['ServerLevel'] = $values[2];
         $result['DBServerLevel'] = $values[3];
         $result['DefaultLocale'] = $values[4];
      }
   }
   return $result;
}

// ------------------------------------------------------------------------

/**
* Tracks if certain files have been included
*
* There are times when you need a conditional include_once that ends when
* the get() function terminates. This utility is for determining if a file
* was included within the runtime of this particular get() call.
*
* @access	public
* @param	string	the path of the include file
* @return	bool
*/
function is_included($path)
{
   global $COOLBREW;

   if (empty($COOLBREW['includes'][$path]))
   {
      $COOLBREW['includes'][$path] = 1;
      return false;
   }
   else 
   {
      return true;
   }
}

// ------------------------------------------------------------------------

/**
* Class registry
*
* This function acts as a singleton.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has
* previously been instantiated the variable is returned.
*
* @access	public
* @param	string	the class name being requested
* @param	bool	optional flag that lets classes get loaded but not instantiated
* @return	object
*/
function &load_class($class, $instantiate = TRUE, $force = FALSE)
{
	static $objects = array();

	// Does the class exist?  If so, we're done...
	if (isset($objects[$class]) && ! $force)
	{
		return $objects[$class];
	}
			
	// If the requested class does not exist in the application/libraries
	// folder we'll load the native class from the system/libraries folder.	
	if (file_exists(APPPATH().'libraries/'.config_item('subclass_prefix').$class.EXT))
	{
		require_once(BASEPATH.'libraries/'.$class.EXT);	
		require_once(APPPATH().'libraries/'.config_item('subclass_prefix').$class.EXT);
		$is_subclass = TRUE;	
	}
	else
	{
		if (file_exists(APPPATH().'libraries/'.$class.EXT))
		{
			require_once(APPPATH().'libraries/'.$class.EXT);	
			$is_subclass = FALSE;	
		}
		else
		{
			require_once(BASEPATH.'libraries/'.$class.EXT);
			$is_subclass = FALSE;
		}
	}

	if ($instantiate == FALSE)
	{
		$objects[$class] = TRUE;
		return $objects[$class];
	}
		
	if ($is_subclass == TRUE)
	{
		$name = config_item('subclass_prefix').$class;
		$objects[$class] = new $name();
		return $objects[$class];
	}

	$name = ($class != 'Controller') ? 'CI_'.$class : $class;
	
	$objects[$class] = new $name();
	return $objects[$class];
}

// ------------------------------------------------------------------------

/**
* Loads the main config.php file(s)
*
* This function has been modified for Cool Brew in that it now loads both a
* system-wide config file and the local module-level config file and merges
* the two arrays.
*
* @access	private
* @return	array
*/
function &get_config()
{
	static $main_conf;
		
	if ( ! isset($main_conf))
	{
		// get system-wide config file
		if ( ! file_exists(BASEPATH.'config/config'.EXT))
		{
			exit('The system-wide configuration file config'.EXT.' does not exist.');
		}
		
		require(BASEPATH.'config/config'.EXT);
		
		if ( ! isset($config) OR ! is_array($config))
		{
			exit('Your system-wide config file does not appear to be formatted correctly.');
		}
		
		$sys_config = $config;
		unset($config);
		
		// get module-level config file (not required)
		if ( file_exists(APPPATH().'config/config'.EXT))
		{
			require(APPPATH().'config/config'.EXT);

			if ( ! isset($config) OR ! is_array($config))
			{
				exit('Your module-level config file does not appear to be formatted correctly.');
			}
			
			$mod_config = $config;
			unset($config);
		}
		else
		{
			$mod_config = array();
		}
		
		// get document-level config file (not required)
		if ( file_exists(DOCPATH.'config/config'.EXT))
		{
			require(DOCPATH.'config/config'.EXT);

			if ( ! isset($config) OR ! is_array($config))
			{
				exit('Your document-level config file does not appear to be formatted correctly.');
			}
			
			$doc_config = $config;
			unset($config);
		}
		else
		{
			$doc_config = array();
		}

		$main_conf[0] = array_merge($sys_config, $mod_config, $doc_config);
		
	}
	return $main_conf[0];
}

// ------------------------------------------------------------------------

/**
* Gets a config item
*
* @access	public
* @return	mixed
*/
function config_item($item)
{
	static $config_item = array();

	if ( ! isset($config_item[$item]))
	{
		$config =& get_config();
		
		if ( ! isset($config[$item]))
		{
			return FALSE;
		}
		$config_item[$item] = $config[$item];
	}

	return $config_item[$item];
}

// ------------------------------------------------------------------------

/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located
* in application/errors/errors.php
* This function will send the error page directly to the
* browser and exit.
*
* @access	public
* @return	void
*/
function show_error($message)
{
	$error =& load_class('Exceptions');
	echo $error->show_error('An Error Was Encountered', $message);
	exit;
}

// ------------------------------------------------------------------------

/**
* 404 Page Handler
*
* This function is similar to the show_error() function above
* However, instead of the standard error template it displays
* 404 errors.
*
* @access	public
* @return	void
*/
function show_404($page = '')
{
	$error =& load_class('Exceptions');
	$error->show_404($page);
	exit;
}

// ------------------------------------------------------------------------

/**
* Error Logging Interface
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
function log_message($level = 'error', $message, $php_error = FALSE)
{
	static $LOG;
	
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$LOG =& load_class('Log');	
	$LOG->write_log($level, $message, $php_error);
}

// ------------------------------------------------------------------------

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int		the status code
 * @param	string
 * @return	void
 */
if ( ! function_exists('set_status_header'))
{
	function set_status_header($code = 200, $text = '')
	{
		$stati = array(
							200	=> 'OK',
							201	=> 'Created',
							202	=> 'Accepted',
							203	=> 'Non-Authoritative Information',
							204	=> 'No Content',
							205	=> 'Reset Content',
							206	=> 'Partial Content',

							300	=> 'Multiple Choices',
							301	=> 'Moved Permanently',
							302	=> 'Found',
							304	=> 'Not Modified',
							305	=> 'Use Proxy',
							307	=> 'Temporary Redirect',

							400	=> 'Bad Request',
							401	=> 'Unauthorized',
							403	=> 'Forbidden',
							404	=> 'Not Found',
							405	=> 'Method Not Allowed',
							406	=> 'Not Acceptable',
							407	=> 'Proxy Authentication Required',
							408	=> 'Request Timeout',
							409	=> 'Conflict',
							410	=> 'Gone',
							411	=> 'Length Required',
							412	=> 'Precondition Failed',
							413	=> 'Request Entity Too Large',
							414	=> 'Request-URI Too Long',
							415	=> 'Unsupported Media Type',
							416	=> 'Requested Range Not Satisfiable',
							417	=> 'Expectation Failed',

							500	=> 'Internal Server Error',
							501	=> 'Not Implemented',
							502	=> 'Bad Gateway',
							503	=> 'Service Unavailable',
							504	=> 'Gateway Timeout',
							505	=> 'HTTP Version Not Supported'
						);

		if ($code == '' OR ! is_numeric($code))
		{
			show_error('Status codes must be numeric', 500);
		}

		if (isset($stati[$code]) AND $text == '')
		{
			$text = $stati[$code];
		}

		if ($text == '')
		{
			show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi')
		{
			header("Status: {$code} {$text}", TRUE);
		}
		elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
		{
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}
		else
		{
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}
}

// ------------------------------------------------------------------------

/**
* Exception Handler
*
* This is the custom exception handler that is declaired at the top
* of Codeigniter.php.  The main reason we use this is permit
* PHP errors to be logged in our own log files since we may
* not have access to server logs. Since this function
* effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level.
* We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
function _exception_handler($severity, $message, $filepath, $line)
{	
	 // We don't bother with "strict" notices since they will fill up
	 // the log file with information that isn't normally very
	 // helpful.  For example, if you are running PHP 5 and you
	 // use version 4 style class functions (without prefixes
	 // like "public", "private", etc.) you'll get notices telling
	 // you that these have been deprecated.
	
	if ($severity == E_STRICT)
	{
		return;
	}

	$error =& load_class('Exceptions');

	// Should we display the error?
	// We'll get the current error_reporting level and add its bits
	// with the severity bits to find out.
	
	if (($severity & error_reporting()) == $severity)
	{
		$error->show_php_error($severity, $message, $filepath, $line);
	}
	
	// Should we log the error?  No?  We're done...
	$config =& get_config();
	if ($config['log_threshold'] == 0)
	{
		return;
	}

	$error->log_exception($severity, $message, $filepath, $line);
}

// --------------------------------------------------------------------

/**
 * Remove Invisible Characters
 *
 * This prevents sandwiching null characters
 * between ascii characters, like Java\0script.
 *
 * @access	public
 * @param	string
 * @return	string
 */
if ( ! function_exists('remove_invisible_characters'))
{
	function remove_invisible_characters($str, $url_encoded = TRUE)
	{
		$non_displayables = array();
		
		// every control character except newline (dec 10)
		// carriage return (dec 13), and horizontal tab (dec 09)
		
		if ($url_encoded)
		{
			$non_displayables[] = '/%0[0-8bcef]/';	// url encoded 00-08, 11, 12, 14, 15
			$non_displayables[] = '/%1[0-9a-f]/';	// url encoded 16-31
		}
		
		$non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';	// 00-08, 11, 12, 14-31, 127

		do
		{
			$str = preg_replace($non_displayables, '', $str, -1, $count);
		}
		while ($count);

		return $str;
	}
}

/* End of file Common.php */
/* Location: ./system/coolbrew/Common.php */