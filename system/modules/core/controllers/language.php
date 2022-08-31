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
 * Cool Brew Language Module
 *
 * This class contains functions that access the CI language class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/language.html
 */
class Language extends Controller {

   function Language()
   {
      parent::Controller();	
   }
	
   // --------------------------------------------------------------------

   /**
    * Fetch a language line
    *
    * @access  public
    * @return  string
    */
   function line()
   {
      // (mixed) the name of the language file to be loaded. Can be an array
      $filename = $this->tag->param(1);

      // (string) the language (english, etc.)
      $language = $this->tag->param(2, '');

      // (string) the key to the item to be returned
      $language_key = $this->tag->param(3, '');

      if ( ! $filename)
      {
         show_error("core.language.line: no filename was specified.");
      }

      $this->load->class('Language');
      $this->lang->load($filename, $language);
      
      return $this->lang->line($language_key);
   }
   // END line()

   // --------------------------------------------------------------------

   /**
    * Fetch a language file
    *
    * @access  public
    * @return  array
    */
   function file()
   {
      // (mixed) the name of the language file to be loaded. Can be an array
      $filename = $this->tag->param(1);

      // (string) the language (english, etc.)
      $language = $this->tag->param(2, '');

      if ( ! $filename)
      {
         show_error("core.language.file: no filename was specified.");
      }

      $this->load->class('Language');
      
      return $this->lang->load($filename, $language, TRUE);
   }
   // END file()

}
?>