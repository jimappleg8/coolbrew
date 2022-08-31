<?php
/**
 * Cool Brew Core Modules
 *
 * A module for accessing the core CodeIgniter class methods via tags
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
 * Cool Brew Collector Module
 *
 * This class contains functions that access the CB collector class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/collector.html
 */
class Collect extends Controller {

   function Collect()
   {
      parent::Controller();	
   }
	
   // --------------------------------------------------------------------

   /**
    * Add file contents to JavaScript collection.
    *
    * @access   public
    * @return   bool
    */
   function append_js_file()
   {
      // (string) the file to append
      $file = $this->tag->param(1);
      
      if ( ! $file)
      {
         show_error("core.collect.append_js_file: no file was specified.");
      }

      return $this->collector->append_js_file($file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add file contents to CSS collection.
    *
    * @access   public
    * @return   bool
    */
   function append_css_file()
   {
      // (string) the file to append
      $file = $this->tag->param(1);
      
      if ( ! $file)
      {
         show_error("core.collect.append_css_file: no file was specified.");
      }

      return $this->collector->append_css_file($file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add file contents to CSS collection.
    *
    * @access   public
    * @return   bool
    */
   function prepend_css_file()
   {
      // (string) the file to append
      $file = $this->tag->param(1);
      
      if ( ! $file)
      {
         show_error("core.collect.prepend_css_file: no file was specified.");
      }

      return $this->collector->prepend_css_file($file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the contents of a javascript file.
    *
    * @access   public
    * @return   bool
    */
   function get_js_file()
   {
      // (string) the file to be returned
      $file = $this->tag->param(1);
      
      if ( ! $file)
      {
         show_error("core.collect.get_js_file: no file was specified.");
      }

      return $this->collector->get_js_file($file);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add supplied code to the JavaScript collection
    *
    * @access   public
    * @return   void
    */
   function append_js_code()
   {
      // (string) the js code to be appended
      $code = $this->tag->param(1);
      
      if ( ! $code)
      {
         show_error("core.collect.append_js_code: no code was supplied.");
      }

      return $this->collector->append_js_code($code);
   }

   // --------------------------------------------------------------------
   
   /**
    * Add supplied code to the CSS collection
    *
    * @access   public
    * @param    string   The css code
    * @return   void
    */
   function append_css_code()
   {
      // (string) the css code to be appended
      $code = $this->tag->param(1);
      
      if ( ! $code)
      {
         show_error("core.collect.append_css_code: no code was supplied.");
      }

      return $this->collector->append_css_code($code);
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
      return $this->collector->get_js();
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
      return $this->collector->get_css();
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the JS collection wrapped in HTML JavaScript tags
    *
    * @access   public
    * @return   string
    */
   function wrap_js()
   {
      // (string) The wrapper file without the extension
      $wrapper = $this->tag->param(1, "wrapper");
      
      return $this->collector->wrap_js($wrapper);
   }

   // --------------------------------------------------------------------
   
   /**
    * Return the CSS collection wrapped in HTML Script tags
    *
    * @access   public
    * @return   string
    */
   function wrap_css()
   {
      // (string) The media type to be used
      $media = $this->tag->param(1, "all");
      
      // (string) The wrapper file without the extension
      $wrapper = $this->tag->param(2, "wrapper");

      return $this->collector->wrap_css($media, $wrapper);
   }
   
}
?>