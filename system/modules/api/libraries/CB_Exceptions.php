<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------

/**
 * Exceptions Class
 *
 * @package		Coolbrew
 * @subpackage	Libraries
 * @category	Exceptions
 * @author		Jim Applegate
 * @link		http://codeigniter.com/user_guide/libraries/exceptions.html
 */
class CB_Exceptions extends CI_Exceptions {


    public function __construct()
    {
        parent::CI_Exceptions();
    }

	// --------------------------------------------------------------------

	/**
	 * 404 Page Not Found Handler
	 *
	 * @access	private
	 * @param	string
	 * @return	string
	 */
	function show_404($page = '')
	{	
		$heading = "404 Page Not Found";
		$message = "The page you requested was not found.";
		
		$CI =& get_instance();
		
		echo '<pre>'; print_r($CI); echo '</pre>'; exit;

		log_message('error', '404 Page Not Found --> '.$page);
		echo $this->show_error($heading, $message, 'error_404');
		exit;
	}
  	
// END Exceptions Class
?>