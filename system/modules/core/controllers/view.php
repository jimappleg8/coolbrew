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
 * Cool Brew View Module
 *
 * This class contains a function that accesses the CI_Loader::view methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/view.html
 */
class View extends Controller {

   function View()
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
   function load()
   {
      // (string) The name of the "view" file to be included
      $view = $this->tag->param(1);
      
      // (array) An associative array of data to be extracted for use in the view.
      $vars = $this->tag->param(2, array());
      
      // (bool) Whether to return the data or load it.
      $return = $this->tag->param(3, FALSE);

      // (bool) Whether to return the data or load it.
      $helpers = $this->tag->param(4, '');

      if ( ! $view)
      {
         show_error('core.view.load: the file to view was not specified.');
      }
      
      if ($helpers != '')
      {
         $this->load->helper($helpers);
      }
            
      $this->load->view($view, $vars, $return);

   }
   // END load()

}
?>