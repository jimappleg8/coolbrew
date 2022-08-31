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
 * Cool Brew Helper Module
 *
 * This class contains a function that accesses the CI Helper functions
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/view.html
 */
class Helper extends Controller {

   function Helper()
   {
      parent::Controller();	
   }
	
   // --------------------------------------------------------------------

   /**
    * Load a view file
    *
    * @access  public
    * @return  void
    */		
   function load_helper()
   {
      // (string) The name of the helper to be loaded
      $helper = $this->tag->param(1);
      
      // (string) The name of the helper function
      $function = $this->tag->param(2);

      $vars = array();
      for ($i=3; $i<=$this->tag->total_params(); $i++)
      {
         $vars[] = $this->tag->param($i);
      }
      
      $this->load->helper($helper);
            
      return call_user_func_array($function, $vars);

   }
   // END load_helper()

}
?>