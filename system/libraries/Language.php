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
 * Language Class
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @category	Language
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/libraries/language.html
 */
class CI_Language {

	var $language	= array();
	var $is_loaded	= array();

	/**
	 * Constructor
	 *
	 * @access	public
	 */	
	function CI_Language()
	{
		log_message('debug', "Language Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @return	void
	 */
	function load($langfile = '', $idiom = '', $return = FALSE)
	{	
		$langfile = str_replace(EXT, '', str_replace('_lang.', '', $langfile)).'_lang'.EXT;
		
		if (in_array($langfile, $this->is_loaded, TRUE))
		{
			return;
		}
		
		if ($idiom == '')
		{
			$CI =& get_instance();
			$deft_lang = $CI->config->item('language');
			// Change, Begin - coolbrew: change default to en_US
			/*
			// Original
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
			// Original
			*/
			$idiom = ($deft_lang == '') ? 'en_US' : $deft_lang;
			// Change, End
		}
	
		// Determine where the language file is and load it
		// Change, Begin - coolbrew: APPPATH can't be a constant; add the
		// DOCPATH to the search path.
		/*
		// Original
		if (file_exists(APPPATH.'language/'.$idiom.'/'.$langfile))
		{
			include(APPPATH.'language/'.$idiom.'/'.$langfile);
		}
		// Original
		*/
		if (file_exists(DOCPATH.'language/'.$idiom.'/'.$langfile))
		{
			include(DOCPATH.'language/'.$idiom.'/'.$langfile);
		}
		elseif (file_exists(APPPATH().'language/'.$idiom.'/'.$langfile))
		{
			include(APPPATH().'language/'.$idiom.'/'.$langfile);
		}
		// Change, End
		else
		{		
			if (file_exists(BASEPATH.'language/'.$idiom.'/'.$langfile))
			{
				include(BASEPATH.'language/'.$idiom.'/'.$langfile);
			}
			else
			{
				show_error('Unable to load the requested language file: language/'.$langfile);
			}
		}

		
		if ( ! isset($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}
		
		if ($return == TRUE)
		{
			return $lang;
		}
		
		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);
		
		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Fetch a single line of text from the language array
	 *
	 * @access	public
	 * @param	string	the language line
	 * @return	string
	 */
	function line($line = '')
	{
		return ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];
	}

}
// END Language Class
?>