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
 * Cool Brew Input Module
 *
 * This class contains functions that access the CI input class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/input.html
 */
class Input extends Controller {

   function Input()
   {
      parent::Controller();	
   }
   
   // --------------------------------------------------------------------

   /**
    * Fetch an item from the POST array
    *
    * @access   public
    * @return   string
    */
   function post()
   {
      // (string) the POST variable to return
      $index = $this->tag->param(1, '');
      
      // (bool) whether to run the value through xss_clean()
      $xss_clean = $this->tag->param(2, FALSE);

      return $this->input->post($index, $xss_clean);
   }
   // END post()
   
   // --------------------------------------------------------------------
   
   /**
    * Fetch an item from the COOKIE array
    *
    * @access   public
    * @return   string
    */
   function cookie()
   {
      // (string) the COOKIE variable to return
      $index = $this->tag->param(1, '');
      
      // (bool) whether to run the value through xss_clean()
      $xss_clean = $this->tag->param(2, FALSE);

      return $this->input->cookie($index, $xss_clean);
   }
   // END cookie()

   // --------------------------------------------------------------------
   
   /**
    * Fetch an item from the SERVER array
    *
    * @access   public
    * @return   string
    */
   function server()
   {      
      // (string) the SERVER variable to return
      $index = $this->tag->param(1, '');
      
      // (bool) whether to run the value through xss_clean()
      $xss_clean = $this->tag->param(2, FALSE);

      return $this->input->server($index, $xss_clean);
   }
   // END server()
   
   // --------------------------------------------------------------------
   
   /**
    * Fetch the IP Address
    *
    * @access   public
    * @return   string
    */
   function ip_address()
   {
      return $this->input->ip_address();
   }
   // END ip_address()
   
   // --------------------------------------------------------------------
   
   /**
    * User Agent
    *
    * @access   public
    * @return   string
    */
   function user_agent()
   {
      return $this->input->user_agent();
   }
   // END user_agent()

}
?>