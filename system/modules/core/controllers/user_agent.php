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
 * Cool Brew User Agent Module
 *
 * This class contains functions that access the CI user agent class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/user_agent.html
 */
class User_agent extends Controller {

   function User_agent()
   {
      parent::Controller();

      $this->load->library('user_agent');
   }

   // --------------------------------------------------------------------

   /**
    * Is Browser
    *
    * @access   public
    * @return   bool
    */      
   function is_browser()
   {
      return $this->agent->is_browser();
   }

   // --------------------------------------------------------------------
   
   /**
    * Is Robot
    *
    * @access   public
    * @return   bool
    */      
   function is_robot()
   {
      return $this->agent->is_robot();
   }

   // --------------------------------------------------------------------
   
   /**
    * Is Mobile
    *
    * @access   public
    * @return   bool
    */      
   function is_mobile()
   {
      return $this->agent->is_mobile();
   }   

   // --------------------------------------------------------------------
   
   /**
    * Is this a referral from another site?
    *
    * @access   public
    * @return   bool
    */         
   function is_referral()
   {
      return $this->agent->is_referral();
   }

   // --------------------------------------------------------------------
   
   /**
    * Agent String
    *
    * @access   public
    * @return   string
    */         
   function agent_string()
   {
      return $this->agent->agent_string();
   }

   // --------------------------------------------------------------------
   
   /**
    * Get Platform
    *
    * @access   public
    * @return   string
    */         
   function platform()
   {
      return $this->agent->platform();
   }

   // --------------------------------------------------------------------
   
   /**
    * Get Browser Name
    *
    * @access   public
    * @return   string
    */         
   function browser()
   {
      return $this->agent->browser();
   }

   // --------------------------------------------------------------------
   
   /**
    * Get the Browser Version
    *
    * @access   public
    * @return   string
    */         
   function version()
   {
      return $this->agent->version();
   }

   // --------------------------------------------------------------------
   
   /**
    * Get The Robot Name
    *
    * @access   public
    * @return   string
    */            
   function robot()
   {
      return $this->agent->robot();
   }
   // --------------------------------------------------------------------
   
   /**
    * Get the Mobile Device
    *
    * @access   public
    * @return   string
    */         
   function mobile()
   {
      return $this->agent->mobile();
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Get the referrer
    *
    * @access   public
    * @return   bool
    */         
   function referrer()
   {
      return $this->agent->referrer();
   }

   // --------------------------------------------------------------------
   
   /**
    * Get the accepted languages
    *
    * @access   public
    * @return   array
    */         
   function languages()
   {
      return $this->agent->languages();
   }

   // --------------------------------------------------------------------
   
   /**
    * Get the accepted Character Sets
    *
    * @access   public
    * @return   array
    */         
   function charsets()
   {
      return $this->agent->charsets();
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Test for a particular language
    *
    * @access   public
    * @return   bool
    */         
   function accept_lang()
   {
      // (string) language to test for
      $lang = $this->tag->param(1, 'en');
      
      return $this->agent->accept_lang($lang);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Test for a particular character set
    *
    * @access   public
    * @return   bool
    */         
   function accept_charset()
   {
      // (string) character set to test for
      $charset = $this->tag->param(1, 'utf-8');

      return $this->agent->accept_charset($charset);
   }

}
?>