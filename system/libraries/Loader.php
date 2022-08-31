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
 * Loader Class
 *
 * Loads views and files
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		ExpressionEngine Dev Team
 * @category	Loader
 * @link		http://codeigniter.com/user_guide/libraries/loader.html
 */
class CI_Loader {

	// All these are set automatically. Don't mess with them.
	var $_ci_ob_level;
	var $_ci_view_path		= '';
	var $_ci_is_php5		= FALSE;
	var $_ci_is_instance 	= FALSE; // Whether we should use $this or $CI =& get_instance()
	var $_ci_cached_vars	= array();
	var $_ci_classes		= array();
	// Addition, Begin - coolbrew: add init check in addition to load check
	var $_ci_init_classes	= array();
	// Addition, End
	var $_ci_models			= array();
	var $_ci_helpers		= array();
	var $_ci_plugins		= array();
	var $_ci_scripts		= array();
	var $_ci_varmap			= array('unit_test' => 'unit', 'user_agent' => 'agent');
	

	/**
	 * Constructor
	 *
	 * Sets the path to the view files and gets the initial output buffering level
	 *
	 * @access	public
	 */
	function CI_Loader()
	{	
		$this->_ci_is_php5 = (floor(phpversion()) >= 5) ? TRUE : FALSE;
		// Change, Begin - coolbrew: APPPATH can't be a constant
		/*
		// Original
		$this->_ci_view_path = APPPATH.'views/';
		// Original
		*/
		$this->_ci_view_path = APPPATH().'views/';
		// Change, End
		$this->_ci_ob_level  = ob_get_level();
				
		log_message('debug', "Loader Class Initialized");
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Class Loader
	 *
	 * This function lets users load and instantiate classes.
	 * It is designed to be called from a user's app controllers.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	mixed	the optional parameters
	 * @return	void
	 */	
	function library($library = '', $params = NULL)
	{		
		if ($library == '')
		{
			return FALSE;
		}

		if (is_array($library))
		{
			foreach ($library as $class)
			{
				$this->_ci_load_class($class, $params);
			}
		}
		else
		{
			$this->_ci_load_class($library, $params);
		}
		
		$this->_ci_assign_to_models();
	}

	// --------------------------------------------------------------------
	
	/**
	 * Model Loader
	 *
	 * This function lets users load and instantiate models.
	 *
	 * @access	public
	 * @param	string	the name of the class
	 * @param	mixed	any initialization parameters
	 * @return	void
	 */	
	function model($model, $name = '', $db_conn = FALSE)
	{		
		if (is_array($model))
		{
			foreach($model as $babe)
			{
				$this->model($babe);	
			}
			return;
		}

		if ($model == '')
		{
			return;
		}
	
		// Is the model in a sub-folder? If so, parse out the filename and path.
		if (strpos($model, '/') === FALSE)
		{
			$path = '';
		}
		else
		{
			$x = explode('/', $model);
			$model = end($x);			
			unset($x[count($x)-1]);
			$path = implode('/', $x).'/';
		}
	
		if ($name == '')
		{
			$name = $model;
		}
		
		if (in_array($name, $this->_ci_models, TRUE))
		{
			return;
		}
		
		$CI =& get_instance();
		if (isset($CI->$name))
		{
			show_error('The model name you are loading is the name of a resource that is already being used: '.$name);
		}
	
		$model = strtolower($model);
		
		// Change, Begin - coolbrew: APPPATH can't be a constant. Also, added
		// the ability to add _model to the model file and class names to avoid
		// name conflicts
		/*
		// Original
		if ( ! file_exists(APPPATH.'models/'.$path.$model.EXT))
		{
			show_error('Unable to locate the model you have specified: '.$model);
		}
		// Original
		*/
		if ( ! file_exists(APPPATH().'models/'.$path.$model.EXT))
		{
			if ( ! file_exists(APPPATH().'models/'.$path.$model.'_model'.EXT))
			{
				show_error('Unable to locate the model you have specified: '.$model);
			}
			else
			{
				$model = $model.'_model';
			}
		}
		// Change, End
				
		if ($db_conn !== FALSE AND ! class_exists('CI_DB'))
		{
			if ($db_conn === TRUE)
				$db_conn = '';
		
			$CI->load->database($db_conn, FALSE, TRUE);
		}
	
		if ( ! class_exists('Model'))
		{
			load_class('Model', FALSE);
		}

		// Change, Begin - coolbrew: APPPATH can't be a constant
		/*
		// Original
		require_once(APPPATH.'models/'.$path.$model.EXT);
		// Original
		*/
		require_once(APPPATH().'models/'.$path.$model.EXT);
		// Change, End

		$model = ucfirst($model);
				
		$CI->$name = new $model();
		$CI->$name->_assign_libraries();
		
		$this->_ci_models[] = $name;	
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Database Loader
	 *
	 * @access	public
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */	
	function database($params = '', $return = FALSE, $active_record = FALSE)
	{
		// Grab the super object
		$CI =& get_instance();
		
		// Do we even need to load the database class?
		if (class_exists('CI_DB') AND $return == FALSE AND $active_record == FALSE AND isset($CI->db) AND is_object($CI->db))
		{
			return FALSE;
		}	
		
		require_once(BASEPATH.'database/DB'.EXT);

		if ($return === TRUE)
		{
			return DB($params, $active_record);
		}

		// Initialize the db variable. Needed to prevent   
		// reference errors with some configurations
		$CI->db = '';
		
		// Load the DB class
		$CI->db =& DB($params, $active_record);	
		
		// Assign the DB object to any existing models
		$this->_ci_assign_to_models();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Load the Utilities Class
	 *
	 * @access	public
	 * @return	string		
	 */		
	function dbutil()
	{
		if ( ! class_exists('CI_DB'))
		{
			$this->database();
		}
		
		$CI =& get_instance();

		// for backwards compatibility, load dbforge so we can extend dbutils off it
		// this use is deprecated and strongly discouraged
		$CI->load->dbforge();
	
		require_once(BASEPATH.'database/DB_utility'.EXT);
		require_once(BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_utility'.EXT);
		$class = 'CI_DB_'.$CI->db->dbdriver.'_utility';

		$CI->dbutil = new $class();

		$CI->load->_ci_assign_to_models();
	}
	
	// --------------------------------------------------------------------

	/**
	 * Load the Database Forge Class
	 *
	 * @access	public
	 * @return	string		
	 */		
	function dbforge()
	{
		if ( ! class_exists('CI_DB'))
		{
			$this->database();
		}
		
		$CI =& get_instance();
	
		require_once(BASEPATH.'database/DB_forge'.EXT);
		require_once(BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_forge'.EXT);
		$class = 'CI_DB_'.$CI->db->dbdriver.'_forge';

		$CI->dbforge = new $class();
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load View
	 *
	 * This function is used to load a "view" file.  It has three parameters:
	 *
	 * 1. The name of the "view" file to be included.
	 * 2. An associative array of data to be extracted for use in the view.
	 * 3. TRUE/FALSE - whether to return the data or load it.  In
	 * some cases it's advantageous to be able to return data so that
	 * a developer can process it in some way.
	 *
	 * @access	public
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function view($view, $vars = array(), $return = FALSE)
	{
		return $this->_ci_load(array('_ci_view' => $view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load File
	 *
	 * This is a generic file loader
	 *
	 * @access	public
	 * @param	string
	 * @param	bool
	 * @return	string
	 */
	function file($path, $return = FALSE)
	{
		return $this->_ci_load(array('_ci_path' => $path, '_ci_return' => $return));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Set Variables
	 *
	 * Once variables are set they become available within
	 * the controller class and its "view" files.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function vars($vars = array())
	{
		$vars = $this->_ci_object_to_array($vars);
	
		if (is_array($vars) AND count($vars) > 0)
		{
			foreach ($vars as $key => $val)
			{
				$this->_ci_cached_vars[$key] = $val;
			}
		}
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Helper
	 *
	 * This function loads the specified helper file.
	 *
	 * @access	public
	 * @param	mixed
	 * @return	void
	 */
	function helper($helpers = array())
	{
		if ( ! is_array($helpers))
		{
			$helpers = array($helpers);
		}
	
		foreach ($helpers as $helper)
		{		
			$helper = strtolower(str_replace(EXT, '', str_replace('_helper', '', $helper)).'_helper');
		
			if (isset($this->_ci_helpers[$helper]))
			{
				continue;
			}
			
			// Change, Begin - coolbrew: APPPATH can't be a constant
			/*
			// Original
			$ext_helper = APPPATH.'helpers/'.config_item('subclass_prefix').$helper.EXT;

			// Is this a helper extension request?			
			if (file_exists($ext_helper))
			{
				$base_helper = BASEPATH.'helpers/'.$helper.EXT;
				
				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: helpers/'.$helper.EXT);
				}
				
				include_once($ext_helper);
				include_once($base_helper);
			}
			elseif (file_exists(APPPATH.'helpers/'.$helper.EXT))
			{ 
				include_once(APPPATH.'helpers/'.$helper.EXT);
			// Original
			*/
			$ext_helper = APPPATH().'helpers/'.config_item('subclass_prefix').$helper.EXT;

			// Is this a helper extension request?			
			if (file_exists($ext_helper))
			{
				$base_helper = BASEPATH.'helpers/'.$helper.EXT;
				
				if ( ! file_exists($base_helper))
				{
					show_error('Unable to load the requested file: helpers/'.$helper.EXT);
				}
				
				include_once($ext_helper);
				include_once($base_helper);
			}
			elseif (file_exists(APPPATH().'helpers/'.$helper.EXT))
			{ 
				include_once(APPPATH().'helpers/'.$helper.EXT);
			// Change, End
			}
			else
			{		
				if (file_exists(BASEPATH.'helpers/'.$helper.EXT))
				{
					// Change, Begin - coolbrew: change include to include_once
					/*
					// Original
					include(BASEPATH.'helpers/'.$helper.EXT);
					// Original
					*/
					include_once(BASEPATH.'helpers/'.$helper.EXT);
					// Change, End
				}
				else
				{
					show_error('Unable to load the requested file: helpers/'.$helper.EXT);
				}
			}

			$this->_ci_helpers[$helper] = TRUE;
			
		}
		
		log_message('debug', 'Helpers loaded: '.implode(', ', $helpers));
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Helpers
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function helpers($helpers = array())
	{
		$this->helper($helpers);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Load Plugin
	 *
	 * This function loads the specified plugin.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function plugin($plugins = array())
	{
		if ( ! is_array($plugins))
		{
			$plugins = array($plugins);
		}
	
		foreach ($plugins as $plugin)
		{	
			$plugin = strtolower(str_replace(EXT, '', str_replace('_pi', '', $plugin)).'_pi');		

			if (isset($this->_ci_plugins[$plugin]))
			{
				continue;
			}

			// Change, Begin - coolbrew: APPPATH can't be a constant
			/*
			// Original
			if (file_exists(APPPATH.'plugins/'.$plugin.EXT))
			{
				include(APPPATH.'plugins/'.$plugin.EXT);	
			// Original
			*/
			if (file_exists(APPPATH().'plugins/'.$plugin.EXT))
			{
				include(APPPATH().'plugins/'.$plugin.EXT);	
			// Change, End
			}
			else
			{
				if (file_exists(BASEPATH.'plugins/'.$plugin.EXT))
				{
					include(BASEPATH.'plugins/'.$plugin.EXT);	
				}
				else
				{
					show_error('Unable to load the requested file: plugins/'.$plugin.EXT);
				}
			}
			
			$this->_ci_plugins[$plugin] = TRUE;
		}
		
		log_message('debug', 'Plugins loaded: '.implode(', ', $plugins));
	}

	// --------------------------------------------------------------------
	
	/**
	 * Load Plugins
	 *
	 * This is simply an alias to the above function in case the
	 * user has written the plural form of this function.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function plugins($plugins = array())
	{
		$this->plugin($plugins);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Load Script
	 *
	 * This function loads the specified include file from the
	 * application/scripts/ folder.
	 *
	 * NOTE:  This feature has been deprecated but it will remain available
	 * for legacy users.
	 *
	 * @access	public
	 * @param	array
	 * @return	void
	 */
	function script($scripts = array())
	{
		if ( ! is_array($scripts))
		{
			$scripts = array($scripts);
		}
	
		foreach ($scripts as $script)
		{	
			$script = strtolower(str_replace(EXT, '', $script));

			if (isset($this->_ci_scripts[$script]))
			{
				continue;
			}
		
			// Change, Begin - coolbrew: APPPATH can't be a constant
			/*
			// Original
			if ( ! file_exists(APPPATH.'scripts/'.$script.EXT))
			// Original
			*/
			if ( ! file_exists(APPPATH().'scripts/'.$script.EXT))
			// Change, End
			{
				show_error('Unable to load the requested script: scripts/'.$script.EXT);
			}
			
		// Change, Begin - coolbrew: APPPATH can't be a constant
		/*
		// Original
			include(APPPATH.'scripts/'.$script.EXT);
		// Original
		*/
			include(APPPATH().'scripts/'.$script.EXT);
		// Change, End
		}
		
		log_message('debug', 'Scripts loaded: '.implode(', ', $scripts));
	}
		
	// --------------------------------------------------------------------
	
	/**
	 * Loads a language file
	 *
	 * @access	public
	 * @param	array
	 * @param	string
	 * @return	void
	 */
	function language($file = array(), $lang = '')
	{
		$CI =& get_instance();

		if ( ! is_array($file))
		{
			$file = array($file);
		}

		foreach ($file as $langfile)
		{	
			$CI->lang->load($langfile, $lang);
		}
	}

	// --------------------------------------------------------------------
	
	/**
	 * Loads language files for scaffolding
	 *
	 * @access	public
	 * @param	string
	 * @return	arra
	 */
	function scaffold_language($file = '', $lang = '', $return = FALSE)
	{
		$CI =& get_instance();
		return $CI->lang->load($file, $lang, $return);
	}
	
	// --------------------------------------------------------------------
	
	/**
	 * Loads a config file
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */
	function config($file = '', $use_sections = FALSE, $fail_gracefully = FALSE)
	{
		$CI =& get_instance();
		$CI->config->load($file, $use_sections, $fail_gracefully);
	}

	// --------------------------------------------------------------------
	
	/**
	 * Scaffolding Loader
	 *
	 * This initializing function works a bit different than the
	 * others. It doesn't load the class.  Instead, it simply
	 * sets a flag indicating that scaffolding is allowed to be
	 * used.  The actual scaffolding function below is
	 * called by the front controller based on whether the
	 * second segment of the URL matches the "secret" scaffolding
	 * word stored in the application/config/routes.php
	 *
	 * @access	public
	 * @param	string
	 * @return	void
	 */	
	function scaffolding($table = '')
	{		
		if ($table === FALSE)
		{
			show_error('You must include the name of the table you would like to access when you initialize scaffolding');
		}
		
		$CI =& get_instance();
		$CI->_ci_scaffolding = TRUE;
		$CI->_ci_scaff_table = $table;
	}

	// --------------------------------------------------------------------

	/**
	 * Loader
	 *
	 * This function is used to load views and files.
	 * Variables are prefixed with _ci_ to avoid symbol collision with
	 * variables made available to view files
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_load($_ci_data)
	{
		// Set the default data variables
		foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val)
		{
			$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
		}

		// Set the path to the requested file
		if ($_ci_path == '')
		{
			$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
			$_ci_file = ($_ci_ext == '') ? $_ci_view.EXT : $_ci_view;

			// Addition, Begin - coolbrew: add ability to look for views in
			// first the document root views folder, then the system views
			// folder. This allows you to override views at the site level 
			// and set system-wide default templates without changing the
			// official defaults.
			$_ci_subpath = (strtolower(MODULE()) == 'core') ? 'views/' : 'views/'.strtolower(MODULE()).'/';

			$orig_ci_view_path = $this->_ci_view_path;
			
			if (file_exists($this->_fix_path(DOCPATH.$_ci_subpath.$_ci_file)))
			{
				$this->_ci_view_path = DOCPATH.$_ci_subpath;
			}
			elseif (file_exists($this->_fix_path(BASEPATH.$_ci_subpath.$_ci_file)))
			{
				$this->_ci_view_path = BASEPATH.$_ci_subpath;
			}
//			else
//			{
//				$this->_ci_view_path = $orig_ci_view_path;
//			}
			// Addition, End
			
			$_ci_path = $this->_fix_path($this->_ci_view_path.$_ci_file);

			$this->_ci_view_path = $orig_ci_view_path;
		}
		else
		{
			$_ci_x = explode('/', $_ci_path);
			$_ci_file = end($_ci_x);
		}

		if ( ! file_exists($_ci_path))
		{
			show_error('Unable to load the requested file: '.$_ci_path);
		}
	
		// This allows anything loaded using $this->load (views, files, etc.)
		// to become accessible from within the Controller and Model functions.
		// Only needed when running PHP 5
		
		if ($this->_ci_is_instance())
		{
			$_ci_CI =& get_instance();
			foreach (get_object_vars($_ci_CI) as $_ci_key => $_ci_var)
			{
				if ( ! isset($this->$_ci_key))
				{
					$this->$_ci_key =& $_ci_CI->$_ci_key;
				}
			}
		}

		/*
		 * Extract and cache variables
		 *
		 * You can either set variables using the dedicated $this->load_vars()
		 * function or via the second parameter of this function. We'll merge
		 * the two types and cache them so that views that are embedded within
		 * other views can have access to these variables.
		 */	
		if (is_array($_ci_vars))
		{
			$this->_ci_cached_vars = array_merge($this->_ci_cached_vars, $_ci_vars);
		}
		extract($this->_ci_cached_vars);
				
		/*
		 * Buffer the output
		 *
		 * We buffer the output for two reasons:
		 * 1. Speed. You get a significant speed boost.
		 * 2. So that the final rendered template can be
		 * post-processed by the output class.  Why do we
		 * need post processing?  For one thing, in order to
		 * show the elapsed page load time.  Unless we
		 * can intercept the content right before it's sent to
		 * the browser and then stop the timer it won't be accurate.
		 */
		ob_start();

		// If the PHP installation does not support short tags we'll
		// do a little string replacement, changing the short tags
		// to standard PHP echo statements.
		
		if ((bool) @ini_get('short_open_tag') === FALSE AND config_item('rewrite_short_tags') == TRUE)
		{
			echo eval('?'.'>'.preg_replace("/;*\s*\?".">/", "; ?".">", str_replace('<?=', '<?php echo ', file_get_contents($_ci_path))).'<?php ');
		}
		else
		{
			include($_ci_path);
		}

		log_message('debug', 'File loaded: '.$_ci_path);

		// Return the file data if requested
		if ($_ci_return === TRUE)
		{
			$buffer = ob_get_contents();
			@ob_end_clean();
			return $buffer;
		}

		/*
		 * Flush the buffer... or buff the flusher?
		 *
		 * In order to permit views to be nested within
		 * other views, we need to flush the content back out whenever
		 * we are beyond the first level of output buffering so that
		 * it can be seen and included properly by the first included
		 * template and any subsequent ones. Oy!
		 *
		 */	
		if (ob_get_level() > $this->_ci_ob_level + 1)
		{
			ob_end_flush();
		}
		else
		{
			// Change, Begin - coolbrew: get "global" object via load_class().
			/*
			// Original
			// PHP 4 requires that we use a global
			global $OUT;
			// Original
			*/
			$OUT =& load_class('Output');
			// Change, End
			$OUT->append_output(ob_get_contents());
			@ob_end_clean();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Load class
	 *
	 * This function loads the requested class.
	 *
	 * @access	private
	 * @param 	string	the item that is being loaded
	 * @param	mixed	any additional parameters
	 * @return 	void
	 */
	function _ci_load_class($class, $params = NULL)
	{	
		// Get the class name
		$class = str_replace(EXT, '', $class);

		// We'll test for both lowercase and capitalized versions of the file name
		foreach (array(ucfirst($class), strtolower($class)) as $class)
		{
			// Change, Begin - coolbrew: APPPATH can't be a constant
			/*
			// Original
			$subclass = APPPATH.'libraries/'.config_item('subclass_prefix').$class.EXT;
			// Original
			*/
			$subclass = APPPATH().'libraries/'.config_item('subclass_prefix').$class.EXT;
			// Change, End

			// Is this a class extension request?			
			if (file_exists($subclass))
			{
				$baseclass = BASEPATH.'libraries/'.ucfirst($class).EXT;

				if ( ! file_exists($baseclass))
				{
					log_message('error', "Unable to load the requested class: ".$class);
					show_error("Unable to load the requested class: ".$class);
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($subclass, $this->_ci_classes))
				{
					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}

				include($baseclass);				
				include($subclass);
				$this->_ci_classes[] = $subclass;

				return $this->_ci_init_class($class, config_item('subclass_prefix'), $params);			
			}

			// Change, Begin - coolbrew: re-add support for init files based 
			// on patch supplied by Greg MacLellan: http://www.codeigniter.com/forums/viewthread/2246/
			// This includes another bug fix as described here:
			// http://www.codeigniter.com/forums/viewthread/2453/
			/*
			// Original

			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;		
			for ($i = 1; $i < 3; $i++)
			{
				$path = ($i % 2) ? APPPATH : BASEPATH;	
				$filepath = $path.'libraries/'.$class.EXT;
				
				// Does the file exist?  No?  Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($filepath, $this->_ci_classes))
				{
					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}

				include($filepath);
				$this->_ci_classes[] = $filepath;
				return $this->_ci_init_class($class, '', $params);
			}

			// Original
			*/

			// First search for an init file - if found, we let the init file create the object
			// For backwards compatibility with CI <= 1.4.1, we look for lowercase filenames as well
			$init_search_path = array(
					APPPATH().'init/init_'.$class.EXT,
					APPPATH().'init/init_'.strtolower($class).EXT,
					BASEPATH.'init/init_'.$class.EXT,
					BASEPATH.'init/init_'.strtolower($class).EXT,
			);
			foreach ($init_search_path as $path)
			{
				if (file_exists($path))
				{
					// found an init file, include it and we're done.
					include($path);
					return;
				}
			}

			// Lets search for the requested library file and load it.
			$is_duplicate = FALSE;
			$lib_search_path = array(
					APPPATH().'libraries/'.$class.EXT,
					BASEPATH.'libraries/'.$class.EXT,
			);
			foreach ($lib_search_path as $filepath)
			{
				// Does the file exist?  No?  Bummer...
				if ( ! file_exists($filepath))
				{
					continue;
				}

				// Safety:  Was the class already loaded by a previous call?
				if (in_array($filepath, $this->_ci_classes))
				{
					$is_duplicate = TRUE;
					log_message('debug', $class." class already loaded. Second attempt ignored.");
					return;
				}
				
				if ($is_duplicate == FALSE)
				{
					include_once($filepath);
					$this->_ci_classes[] = $filepath;
				}

				if ( ! in_array($class, $this->_ci_init_classes))
				{
					return $this->_ci_init_class($class, '', $params);
				}
				else
				{
					return TRUE;
				}
			}

			// Change, End

		} // END FOREACH

		// If we got this far we were unable to find the requested class.
		// We do not issue errors if the load call failed due to a duplicate request
		if ($is_duplicate == FALSE)
		{
			log_message('error', "Unable to load the requested class: ".$class);
			show_error("Unable to load the requested class: ".$class);
		}
	}
	
	// --------------------------------------------------------------------

	/**
	 * Instantiates a class
	 *
	 * @access	private
	 * @param	string
	 * @param	string
	 * @return	null
	 */
	function _ci_init_class($class, $prefix = '', $config = FALSE)
	{	
		$class = strtolower($class);
		
		// Is there an associated config file for this class?
		if ($config === NULL)
		{
			// Change, Begin - coolbrew: APPPATH can't be a constant
			/*
			// Original
			if (file_exists(APPPATH.'config/'.$class.EXT))
			{
				include(APPPATH.'config/'.$class.EXT);
			}
			// Original
			*/
			if (file_exists(APPPATH().'config/'.$class.EXT))
			{
				include(APPPATH().'config/'.$class.EXT);
			}
			// Change, End
		}
		
		if ($prefix == '')
		{
			$name = (class_exists('CI_'.$class)) ? 'CI_'.$class : $class;
		}
		else
		{
			$name = $prefix.$class;
		}
		
		// Set the variable name we will assign the class to	
		$classvar = ( ! isset($this->_ci_varmap[$class])) ? $class : $this->_ci_varmap[$class];
				
		// Instantiate the class		
		$CI =& get_instance();
		if ($config !== NULL)
		{
			$CI->$classvar = new $name($config);
		}
		else
		{		
			$CI->$classvar = new $name;
		}
		// Addition, Begin - coolbrew: record that class is instatiated
		$this->_ci_init_classes[] = $class;
		// Addition, End
	} 	
	
	// --------------------------------------------------------------------
	
	/**
	 * Autoloader
	 *
	 * The config/autoload.php file contains an array that permits sub-systems,
	 * libraries, plugins, and helpers to be loaded automatically.
	 *
	 * @access	private
	 * @param	array
	 * @return	void
	 */
	function _ci_autoloader()
	{	
		// Change, Begin - coolbrew: APPPATH can't be a constant; add system-wide autoload file
		/*
		// Original
		include(APPPATH.'config/autoload'.EXT);

		if ( ! isset($autoload))
		{
			return FALSE;
		}
		// Original
		*/
		include(BASEPATH.'config/autoload'.EXT);
		$sys_autoload = $autoload;
		unset($autoload);

		include(APPPATH().'config/autoload'.EXT);

		if ( ! isset($autoload) && empty($sys_autoload))
		{
			return FALSE;
		}

		// Merge system-wide and module-level autoload arrays. I'm not 
		// worrying about script and core arrays because the sys_autoload 
		// does not include them
		foreach (array('config', 'helper', 'plugin', 'libraries') as $type)
		{
			$autoload[$type] = array_merge($sys_autoload[$type], $autoload[$type]);
		}
		// Change, End

		// Load any custom config files
		if (count($autoload['config']) > 0)
		{			
			$CI =& get_instance();
			foreach ($autoload['config'] as $key => $val)
			{
				$CI->config->load($val);
			}
		}

		// Autoload plugins, helpers, scripts and languages
		foreach (array('helper', 'plugin', 'script', 'language') as $type)
		{			
			if (isset($autoload[$type]) AND count($autoload[$type]) > 0)
			{
				$this->$type($autoload[$type]);
			}		
		}

		// A little tweak to remain backward compatible
		// The $autoload['core'] item was deprecated
		if ( ! isset($autoload['libraries']))
		{
			$autoload['libraries'] = $autoload['core'];
		}
		
		// Load libraries
		if (isset($autoload['libraries']) AND count($autoload['libraries']) > 0)
		{
			// Load the database driver.
			if (in_array('database', $autoload['libraries']))
			{
				$this->database();
				$autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
			}

			// Load scaffolding
			if (in_array('scaffolding', $autoload['libraries']))
			{
				$this->scaffolding();
				$autoload['libraries'] = array_diff($autoload['libraries'], array('scaffolding'));
			}
		
			// Load all other libraries
			foreach ($autoload['libraries'] as $item)
			{
				$this->library($item);
			}
		}

		// Autoload models
		if (isset($autoload['model']))
		{
			$this->model($autoload['model']);
		}

	}
	
	// --------------------------------------------------------------------

	/**
	 * Assign to Models
	 *
	 * Makes sure that anything loaded by the loader class (libraries, plugins, etc.)
	 * will be available to models, if any exist.
	 *
	 * @access	private
	 * @param	object
	 * @return	array
	 */
	function _ci_assign_to_models()
	{
		if (count($this->_ci_models) == 0)
		{
			return;
		}
	
		if ($this->_ci_is_instance())
		{
			$CI =& get_instance();
			foreach ($this->_ci_models as $model)
			{			
				$CI->$model->_assign_libraries();
			}
		}
		else
		{		
			foreach ($this->_ci_models as $model)
			{			
				$this->$model->_assign_libraries();
			}
		}
	}  	

	// --------------------------------------------------------------------

	/**
	 * Object to Array
	 *
	 * Takes an object as input and converts the class variables to array key/vals
	 *
	 * @access	private
	 * @param	object
	 * @return	array
	 */
	function _ci_object_to_array($object)
	{
		return (is_object($object)) ? get_object_vars($object) : $object;
	}

	// --------------------------------------------------------------------

	/**
	 * Determines whether we should use the CI instance or $this
	 *
	 * @access	private
	 * @return	bool
	 */
	function _ci_is_instance()
	{
		if ($this->_ci_is_php5 == TRUE)
		{
			return TRUE;
		}
	
		global $CI;
		return (is_object($CI)) ? TRUE : FALSE;
	}
	
	// --------------------------------------------------------------------

   // Addition, Begin - coolbrew: added the _fix_path function to handle
   // times when you might want to give a relative path name for a view.
   // without it, if any part of the relative path doesn't exist, 
   // file_exists() will fail. I tried realpath(), but it didn't work.
   
	/**
	 * Determines whether we should use the CI instance or $this
	 *
	 * @access	private
	 * @return	bool
	 */
	function _fix_path($path)
	{
		// check if path begins with "/" ie. is absolute
		// if it isn't concat with script path
		if (strpos($path,"/") !== 0)
		{
			$base=dirname($_SERVER['SCRIPT_FILENAME']);
			$path=$base."/".$path;
		}

		// canonicalize
		$path = explode('/', $path);
		$newpath = array();
		for ($i=0; $i<sizeof($path); $i++)
		{
			if ($path[$i]==='' || $path[$i]==='.') continue;
			if ($path[$i]==='..')
			{
				array_pop($newpath);
				continue;
			}
			array_push($newpath, $path[$i]);
		}
		$finalpath="/".implode('/', $newpath);

		return $finalpath;
   }
   // Addition, End

}
?>