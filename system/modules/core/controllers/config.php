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
 * Cool Brew Config Module
 *
 * This class contains functions that access the CI config class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/config.html
 */
class Config extends Controller {

   function Config()
   {
      parent::Controller();	
   }

   // --------------------------------------------------------------------

   /**
    * Fetch a config file item
    *
    * @access  public
    * @return  string
    */
   function item()
   {
      // (string) the config item name
      $item = $this->tag->param(1);

      // (string) the index name
      $index = $this->tag->param(2, '');
      
      if ( ! $item)
      {
         show_error("core.config.item: no item was specified.");
      }
      
      return $this->config->item($item, $index);

   }
   // END item()
   
   // --------------------------------------------------------------------

   /**
    * Site URL
    *
    * @access  public
    * @return  string
    */		
   function site_url()
   {
      // (string) the URI string
      $uri = $this->tag->param(1, '');

      return $this->config->site_url($uri);
   }
   // END site_url()
	
   // --------------------------------------------------------------------

   /**
    * System URL
    *
    * @access  public
    * @return  string
    */		
   function system_url()
   {
      return $this->config->system_url();
   }
   // END system_url()
  	
   // --------------------------------------------------------------------

   /**
    * Set a config file item
    *
    * @access  public
    * @return  void
    */		
   function set_item()
   {
      // (string) the config item key
      $item = $this->tag->param(1);
      
      // (string) the config item value
      $value = $this->tag->param(2);
      
      if ( ! $item)
      {
         show_error("core.config.set_item: no item was specified.");
      }
      if ( ! $value)
      {
         show_error("core.config.set_item: no value was specified.");
      }

      return $this->config->set_item($item, $value);

   }
   // END set_item

}
?>