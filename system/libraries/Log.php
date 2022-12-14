<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @author		Jim Applegate - Cool Brew changes and additions
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Logging Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Logging
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/general/errors.html
 */
class CI_Log {

	var $log_path;
	var $_threshold	= 1;
	var $_date_fmt	= 'Y-m-d H:i:s';
	var $_enabled	= TRUE;
	var $_levels	= array('ERROR' => '1', 'DEBUG' => '2',  'INFO' => '3', 'ALL' => '4');

	/**
	 * Constructor
	 *
	 * @access	public
	 * @param	string	the log file path
	 * @param	string	the error threshold
	 * @param	string	the date formatting codes
	 */
	function CI_Log()
	{
		$config =& get_config();
		
		// Change, Begin - coolbrew: add the document-level log folder into the mix
		/*
		// Original
		$this->log_path = ($config['log_path'] != '') ? $config['log_path'] : BASEPATH.'logs/';

		if ( ! is_dir($this->log_path) OR ! is_really_writable($this->log_path))
		{
			$this->_enabled = FALSE;
		}
		// Original
		*/
		if ($config['log_path'] != '')
		{
			$this->log_path = $config['log_path'];
			if ( ! is_dir($this->log_path) OR ! is_writable($this->log_path))
			{
				$this->_enabled = FALSE;
			}
		}
		elseif (is_dir(DOCPATH.'logs/') && is_writable(DOCPATH.'logs/'))
		{
			$this->log_path = DOCPATH.'logs/';
		}
		elseif (is_dir(BASEPATH.'logs/') && is_writable(BASEPATH.'logs/'))
		{
			$this->log_path = BASEPATH.'logs/';
		}
		else
		{
			$this->log_path = BASEPATH.'logs/';
			$this->_enabled = FALSE;
		}
		// Change, End
		
		if (is_numeric($config['log_threshold']))
		{
			$this->_threshold = $config['log_threshold'];
		}
			
		if ($config['log_date_format'] != '')
		{
			$this->_date_fmt = $config['log_date_format'];
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Write Log File
	 *
	 * Generally this function will be called using the global log_message() function
	 *
	 * @access	public
	 * @param	string	the error level
	 * @param	string	the error message
	 * @param	bool	whether the error is a native PHP error
	 * @return	bool
	 */		
	function write_log($level = 'error', $msg, $php_error = FALSE)
	{		
		if ($this->_enabled === FALSE)
		{
			return FALSE;
		}
	
		$level = strtoupper($level);
		
		if ( ! isset($this->_levels[$level]) OR ($this->_levels[$level] > $this->_threshold))
		{
			return FALSE;
		}
	
		$filepath = $this->log_path.'log-'.date('Y-m-d').EXT;
		$message  = '';
		
		if ( ! file_exists($filepath))
		{
			$message .= "<"."?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?".">\n\n";
		}
			
		if ( ! $fp = @fopen($filepath, "a"))
		{
			return FALSE;
		}
		
		// Change, Begin - coolbrew: add SITE_ID to log entries...
		/*
		// Original
		$message .= $level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
		// Original
		*/
		$message .= SITE_ID.': '.$level.' '.(($level == 'INFO') ? ' -' : '-').' '.date($this->_date_fmt). ' --> '.$msg."\n";
		// Change, End
		
		flock($fp, LOCK_EX);	
		fwrite($fp, $message);
		flock($fp, LOCK_UN);
		fclose($fp);
	
		@chmod($filepath, 0666); 		
		return TRUE;
	}

}
// END Log Class
?>