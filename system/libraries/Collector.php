<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cool Brew
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		Cool Brew
 * @author		Jim Applegate
 * @copyright	Copyright (c) 2007, The Hain Celestial Group, Inc.
 * @license		http://www.coolbrewcms.com/user_guide/license.html
 * @link		http://www.coolbrewcms.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Collector Class
 *
 * Supports the aggregation of JavaScript and CSS information
 *
 * @package		Cool Brew
 * @subpackage	Libraries
 * @category	Collector
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/libraries/collector.html
 */
class CI_Collector {

   var $js;
   var $css;

   /**
    * Constructor
    *
    * @access   public
    */      
   function CI_Collector()
   {
      log_message('debug', "Collector Class Initialized");
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Add file contents to JavaScript collection.
    *
    * @access   public
    * @param    string
    * @return   bool
    */
   function append_js_file($file)
   {
      return $this->_append_file('js', $file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add file contents to CSS collection.
    *
    * @access   public
    * @param    string
    * @return   bool
    */
   function append_css_file($file)
   {
      return $this->_append_file('css', $file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add file contents to CSS collection.
    *
    * @access   public
    * @param    string
    * @return   bool
    */
   function prepend_css_file($file)
   {
      return $this->_prepend_file('css', $file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add file contents to a collection.
    *
    * Modifies the class variables by appending the file specified.
    *
    * The method looks first in DOCPATH for the file, and then 
    * BASEPATH and lastly in APPPATH. It returns FALSE if the 
    * file is not found.
    * 
    * @access   private
    * @param    string   The kind of file being appended
    * @param    string   The filename without the extension
    * @return   bool
    */
   function _append_file($type, $file)
   {
      $dir = $type."/";
      $file = $file.".".$type;
      
      $file_path = $this->_set_resource_path($dir, $file);

      if ($file_path)
      {
         $this->$type .= "\n" . $this->_read_file($file_path.$file);
         return TRUE;
      }
      else
      {
         return FALSE;
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Add file contents to a collection.
    *
    * Modifies the class variables by prepending the file specified.
    *
    * The method looks first in DOCPATH for the file, and then 
    * BASEPATH and lastly in APPPATH. It returns FALSE if the 
    * file is not found.
    * 
    * @access   private
    * @param    string   The kind of file being prepended
    * @param    string   The filename without the extension
    * @return   bool
    */
   function _prepend_file($type, $file)
   {
      $dir = $type."/";
      $file = $file.".".$type;
      
      $file_path = $this->_set_resource_path($dir, $file);

      if ($file_path)
      {
         $this->$type = $this->_read_file($file_path.$file) . "\n" . $this->$type;
         return TRUE;
      }
      else
      {
         return FALSE;
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the contents of a javascript file.
    *
    * The method looks first in DOCPATH for the file, and then 
    * BASEPATH and lastly in APPPATH. It returns FALSE if the 
    * file is not found.
    *
    * @access   public
    * @param    string   The filename without the extension
    * @return   bool
    */
   function get_js_file($file)
   {
      $dir = "js/";
      $file = $file.".js";
      
      $file_path = $this->_set_resource_path($dir, $file);

      if ($file_path)
      {
         return $this->_read_file($file_path.$file);
      }
      else
      {
         return FALSE;
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Add supplied code to the JavaScript collection
    *
    * @access   public
    * @param    string   The js code
    * @return   void
    */
   function append_js_code($code)
   {
      $this->js .= "\n" . $code;
   
   }

   // --------------------------------------------------------------------
   
   /**
    * Add supplied code to the CSS collection
    *
    * @access   public
    * @param    string   The css code
    * @return   void
    */
   function append_css_code($code)
   {
      $this->css .= "\n" . $code;
   
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the JS collection
    *
    * @access   public
    * @return   string
    */
   function get_js()
   {
      return $this->js;
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the CSS collection
    *
    * @access   public
    * @return   string
    */
   function get_css()
   {
      return $this->css;
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the JS collection wrapped in HTML JavaScript tags
    *
    * The default file is wrapper.js which is a view file with the 
    * variable $javascript in the middle.
    *
    * @access   public
    * @param    string   The wrapper file without the extension
    * @return   string
    */
   function wrap_js($wrapper = "wrapper")
   {
      if ($this->js != '')
      {
         $dir = "js/";
         $file = $wrapper.".js";
      
         $file_path = $this->_set_resource_path($dir, $file);

         if ($file_path)
         {
            $results = $this->_read_file($file_path.$file);
            $results = str_replace('{javascript}', $this->js, $results);
            return $results;
         }
         else
         {
            return FALSE;
         }
      }      
      return $this->js;
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the CSS collection wrapped in HTML Script tags
    *
    * The default file is wrapper.css which is a view file with the 
    * variable $css in the middle.
    *
    * @access   public
    * @param    string   The wrapper file without the extension
    * @return   string
    */
   function wrap_css($media = "all", $wrapper = "wrapper")
   {
      if ($this->css != '')
      {
         $dir = "css/";
         $file = $wrapper.".css";
      
         $file_path = $this->_set_resource_path($dir, $file);

         if ($file_path)
         {
            $results = $this->_read_file($file_path.$file);
            $results = str_replace('{css}', $this->css, $results);
            $results = str_replace('{media}', $media, $results);
            return $results;
         }
         else
         {
            return FALSE;
         }
      }      
      return $this->css;
   }

   // --------------------------------------------------------------------
   
   /**
    * Read File
    *
    * Opens the file specfied in the path and returns it as a string.
    *
    * @access	private
    * @param	string	path to file
    * @return	string
    */	
   function _read_file($file)
   {
      if ( ! file_exists($file))
      {
         return FALSE;
      }
   
      if (function_exists('file_get_contents'))
      {
         return file_get_contents($file);      
      }

      if ( ! $fp = @fopen($file, 'rb'))
      {
         return FALSE;
      }
      
      flock($fp, LOCK_SH);
   
      $data = '';
      if (filesize($file) > 0)
      {
         $data =& fread($fp, filesize($file));
      }

      flock($fp, LOCK_UN);
      fclose($fp);

      return $data;
   }
   
   // --------------------------------------------------------------------
   
   /**
   * Determines what path to set
   *
   * This searches for a file in three possible locations in the appropriate 
   * order and returns the path of the first instance it finds. The order 
   * is document-level, system-level, module-level.
   *
   * @access   private
   * @param    string   the sub-path of the resource
   * @param    string   the filename of the resource
   * @return   string
   */
   function _set_resource_path($path, $resource)
   {
      if (file_exists(DOCPATH.$path.$resource))
      {
         return DOCPATH.$path;
      }
      elseif (file_exists(BASEPATH.'public/'.$path.$resource))
      {
         return BASEPATH.'public/'.$path;
      }
      elseif (file_exists(APPPATH().'public/'.$path.$resource))
      {
         return APPPATH().'public/'.$path;
      }
      else
      {
         return FALSE;
      }
   }

}
// END Collector Class
?>