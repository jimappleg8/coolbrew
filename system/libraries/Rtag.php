<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cool Brew
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CoolBrew
 * @author		Jim Applegate
 * @copyright	Copyright (c) 2010, The Hain Celestial Group, Inc.
 * @license		http://www.coolbrewcms.com/user_guide/license.html
 * @link		http://www.coolbrewcms.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Rtag Class
 *
 * Parses remote tag parameters by pulling them from the $_POST array
 *
 * @package		CoolBrew
 * @subpackage	Libraries
 * @category	Tag
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/libraries/rtag.html
 */
class CI_Rtag {
	
	/**
	 * Constructor
	 *
	 * @access	public
	 */		
	function CI_Rtag()
	{	
		log_message('debug', "Rtag Class Initialized");
	}
	

	// --------------------------------------------------------------------
	
	/**
	 * Fetch a Parameter from the POST variable
	 *
	 * This function returns the parameter based on the index provided.
	 * It also removes the index from the $_POST array.
	 *
	 * It treats a blank value the same as if the variable were not defined.
	 *
	 * @access	public
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	function param($index = '', $no_result = FALSE)
	{
		$value = $no_result;
		
		if (isset($_POST[$index]))
		{
			if ($_POST[$index] != '')
			{
				$value = $_POST[$index];
			}
			unset($_POST[$index]);
		}
		return $value;
	}

}
// END My_Input class
?>