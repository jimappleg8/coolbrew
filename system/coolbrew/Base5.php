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
 * @since		Version 1.3
 * @filesource
 */


// ------------------------------------------------------------------------

/**
 * CI_BASE - For PHP 5
 *
 * This file contains some code used only when CodeIgniter is being
 * run under PHP 5.  It allows us to manage the CI super object more
 * gracefully than what is possible with PHP 4.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	front-controller
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

class CI_Base {

	private static $instance;

	public function CI_Base()
	{
		self::$instance =& $this;
	}

	public static function &get_instance()
	{
		return self::$instance;
	}

	/**
	 * COOLBREW: resets loader for each tag
	 */
	public static function unset_instance()
	{
		self::$instance->load->_ci_ob_level = '';
		self::$instance->load->_ci_view_path = '';
		self::$instance->load->_ci_is_instance = FALSE;
		self::$instance->load->_ci_cached_vars = array();
		self::$instance->load->_ci_classes = array();
		self::$instance->load->_ci_init_classes = array();
		self::$instance->load->_ci_models = array();
		self::$instance->load->_ci_helpers = array();
		self::$instance->load->_ci_plugins = array();
		self::$instance->load->_ci_scripts = array();
		self::$instance->load->_ci_varmap = array();
	}
}

function &get_instance()
{
	return CI_Base::get_instance();
}



/* End of file Base5.php */
/* Location: ./system/codeigniter/Base5.php */