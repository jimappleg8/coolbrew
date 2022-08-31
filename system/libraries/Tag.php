<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cool Brew
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CoolBrew
 * @author		Jim Applegate
 * @copyright	Copyright (c) 2007, The Hain Celestial Group, Inc.
 * @license		http://www.coolbrewcms.com/user_guide/license.html
 * @link		http://www.coolbrewcms.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Tag Class
 *
 * Parses tag parameters
 *
 * @package		CoolBrew
 * @subpackage	Libraries
 * @category	Tag
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/libraries/tag.html
 */
class CI_Tag {

	var $params = array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */		
	function CI_Tag()
	{
		log_message('debug', "Tag Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a Tag Parameter
	 *
	 * This function returns the tag perameter based on the number provided.
	 *
	 * @access	public
	 * @param	integer
	 * @param	bool
	 * @return	string
	 */
	function param($n, $no_result = FALSE)
	{
		return ( ! isset($this->params[$n])) ? $no_result : $this->params[$n];
	}

	// --------------------------------------------------------------------
	
	/**
	 * Parameter Array
	 *
	 * @access	public
	 * @return	array
	 */
	function param_array()
	{
		return $this->params;
	}

	// --------------------------------------------------------------------
	
	/**
	 * Total number of parameters
	 *
	 * @access	public
	 * @return	integer
	 */
	function total_params()
	{
		return count($this->params);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Sets the parameters array.
	 *
	 * This function re-indexes the $this->params array so that it
	 * starts at 1 rather then 0.  Doing so makes it simpler to
	 * use functions like $this->uri->param(n) since there is
	 * a 1:1 relationship between the param array and the actual segments.
	 *
	 * @access	public
	 * @return	integer
	 */
	function set_params($params)
	{
		$i = 1;
		foreach ($params as $val)
		{
			$this->params[$i++] = $val;
		}
	}


}
// END Tag Class
?>