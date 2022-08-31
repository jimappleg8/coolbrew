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
 * Initialize the database
 *
 * @category	Database
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/database/
 */
function &DB($params = '', $active_record_override = FALSE)
{
	// Load the DB config file if a DSN string wasn't passed
	if (is_string($params) AND strpos($params, '://') === FALSE)
	{

		// Change, Begin - coolbrew: APPPATH can't be a constant; load 
		// both system-level and module-level db settings. The system-wide
		// config file is the only one required.
		/*
		// Original
		include(APPPATH.'config/database'.EXT);
		// Original
		*/
		include(BASEPATH.'config/database'.EXT);
		$sys_db = $db;
		unset($db);
		
		if (file_exists(DOCPATH.'config/database'.EXT))
		{
			include(DOCPATH.'config/database'.EXT);
			if (isset($db))
			{
			   $doc_db = $db;
			   unset($db);
			}
			else
			{
			   $doc_db = array();
			}
		}
		else
		{
			$doc_db = array();
		}

		if (file_exists(APPPATH().'config/database'.EXT))
		{
			include(APPPATH().'config/database'.EXT);
			if (isset($db))
			{
			   $mod_db = $db;
			   unset($db);
			}
			else
			{
			   $mod_db = array();
			}
		}
		else
		{
			$mod_db = array();
		}

		$db = array_merge($sys_db, $mod_db, $doc_db);
		// Change, End

		if ( ! isset($db) OR count($db) == 0)
		{
			show_error('No database connection settings were found in the database config file.');
		}
		
		if ($params != '')
		{
			$active_group = $params;
		}
		
		if ( ! isset($active_group) OR ! isset($db[$active_group]))
		{
			show_error('You have specified an invalid database connection group.');
		}
		
		$params = $db[$active_group];			
	}
	
	// No DB specified yet?  Beat them senseless...
	if ( ! isset($params['dbdriver']) OR $params['dbdriver'] == '')
	{
		show_error('You have not selected a database type to connect to.');
	}

	// Load the DB classes.  Note: Since the active record class is optional
	// we need to dynamically create a class that extends proper parent class
	// based on whether we're using the active record class or not.
	// Kudos to Paul for discovering this clever use of eval()
	
	if ($active_record_override == TRUE)
	{
		$active_record = TRUE;
	}
	
	require_once(BASEPATH.'database/DB_driver'.EXT);

	if (! isset($active_record) OR $active_record == TRUE)
	{
		require_once(BASEPATH.'database/DB_active_rec'.EXT);
		
		if ( ! class_exists('CI_DB'))
		{
			eval('class CI_DB extends CI_DB_active_record { }');
		}
	}
	else
	{
		if ( ! class_exists('CI_DB'))
		{
			eval('class CI_DB extends CI_DB_driver { }');
		}
	}
	
	require_once(BASEPATH.'database/drivers/'.$params['dbdriver'].'/'.$params['dbdriver'].'_driver'.EXT);

	// Instantiate the DB adapter
	$driver = 'CI_DB_'.$params['dbdriver'].'_driver';
	$DB = new $driver($params);
	
	if ($DB->autoinit == TRUE)
	{
		$DB->initialize();
	}
	
	return $DB;
}	


?>