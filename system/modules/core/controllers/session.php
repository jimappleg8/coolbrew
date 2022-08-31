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
 * Cool Brew Session Module
 *
 * This class contains functions that access the CI session class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/session.html
 */
class Session extends Controller {

   function Session()
   {
      parent::Controller();
      
      $this->load->database('write');
      $this->load->library('session');
   }

   // --------------------------------------------------------------------

   /**
    * Fetch a specific item from the session array
    *
    * @access   public
    * @return   string
    */      
   function userdata()
   {
      // (string) the item to be retrieved from the session
      $item = $this->tag->param(1, '');

      return $this->session->userdata($item);
   }
   // END userdata()
   
   // --------------------------------------------------------------------
   
   /**
    * Add or change data in the "userdata" array
    *
    * @access   public
    * @return   void
    */      
   function set_userdata()
   {
      // (mixed) the item to be added/changed in the session array
      $newdata = $this->tag->param(1, array());

      // (string) the new value of that item
      $newval = $this->tag->param(2, '');

      return $this->session->set_userdata($newdata, $newval);
   }
   // END set_userdata()
   
   // --------------------------------------------------------------------
   
   /**
    * Delete a session variable from the "userdata" array
    *
    * @access   public
    * @return   void
    */      
   function unset_userdata()
   {
      // (mixed) the item to be removed from the session array
      $newdata = $this->tag->param(1, array());

      return $this->session->unset_userdata($newdata);
   }
   // END unset_userdata()

   // --------------------------------------------------------------------

   /**
    * Sets flash data which will be available only in next request.
    *
    * @access   public
    * @return   void
    */
   function set_flashdata()
   {
      // (string) the item to be added to the flash data
      $key = $this->tag->param(1);

      // (string) the value of that item
      $value = $this->tag->param(2);

      if ( ! $key)
      {
         show_error("core.session.set_flashdata: no key was specified.");
      }
      if ( ! $value)
      {
         show_error("core.session.set_flashdata: no value was specified.");
      }

      return $this->session->set_flashdata($key, $value);
   }    
   // END set_flashdata()
   
   // --------------------------------------------------------------------

   /**
    * Keeps existing "flash" data available to next request.
    *
    * @access   public
    * @return   void
    */
   function keep_flashdata()
   {
      // (string) the item to be kept in the flash data
      $key = $this->tag->param(1);

      if ( ! $key)
      {
         show_error("core.session.keep_flashdata: no key was specified.");
      }

      return $this->session->keep_flashdata($key);
   }

   // --------------------------------------------------------------------

   /**
    * Returns "flash" data for the given key.
    *
    * @access   public
    * @return   void
    */
   function flashdata()
   {
      // (string) the item to be kept in the flash data
      $key = $this->tag->param(1);

      if ( ! $key)
      {
         show_error("core.session.flashdata: no key was specified.");
      }

      return $this->session->flashdata($key);
   }

}
?>