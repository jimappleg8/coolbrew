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
 * Access Class
 *
 * Supports the user access for Cool Brew admin controllers
 *
 * @package		Cool Brew
 * @subpackage	Libraries
 * @category	Access
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/libraries/access.html
 */
class CI_Access {

   var $CI;
   var $module_id;
   var $site_id = '';
   
   /**
    * Constructor
    *
    * @access   public
    */      
   function CI_Access($params)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('session');
      $this->module_id = $params['module_id'];
      $this->site_id = SITE_ID;
      
      log_message('debug', "Access Class Initialized");
   }
   
    // --------------------------------------------------------------------
   
   /**
    * Set the current module ID
    *
    */
   function set_module_id($module_id)
   {
      $this->module_id = $module_id;
   }

    // --------------------------------------------------------------------
   
   /**
    * Set the current site ID
    *
    */
   function set_site_id($site_id)
   {
      $this->site_id = $site_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if access is allowed
    *
    */
   function check($section = '', $menu_name = 'main')
   {
      $this->CI->load->helper('url');

      $username = $this->CI->session->userdata('username');
      
      // first check if user is logged in
      if ($username == '')
      {
         $this->CI->session->set_flashdata('return_url', $this->CI->uri->uri_string());
         return 'no_login';
      }
      
      $this->CI->load->database('read');

      // if logged in, first check if person has rights to this module
      if ($section != '')
      {
         $sql = 'SELECT adm_people.Username '.
                'FROM adm_people, adm_people_module '.
                'WHERE adm_people.Username = adm_people_module.Username '.
                'AND adm_people_module.ModuleID = \''.$this->module_id.'\' '.
                'AND adm_people_module.SiteID = \''.$this->site_id.'\'';

         $query = $this->CI->db->query($sql);
      
         if ($query->num_rows() == 0)
         {
            return 'no_module';
         }
      }

      // then check if person has rights to the requested section
      if ($section != '')
      {
         $sql = 'SELECT adm_menu.LinkText '.
                'FROM adm_menu LEFT JOIN adm_people_menu '.
                'ON adm_menu.ID = adm_people_menu.MenuID '.
                'WHERE adm_menu.ModuleID = \''.$this->module_id.'\' '.
                'AND adm_menu.MenuName = \''.$menu_name.'\' '.
                'AND adm_people_menu.Username = \''.$username.'\' '.
                'AND adm_people_menu.SiteID = \''.$this->site_id.'\' '.
                'AND adm_menu.LinkText = \''.$section.'\'';

         $query = $this->CI->db->query($sql);
         
         if ($query->num_rows() == 0)
         {
            return 'no_section';
         }
      }
      return 'approved';
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of tab data
    *
    */
   function get_tabs($this_tab = '', $menu_name = 'main')
   {
      $username = $this->CI->session->userdata('username');
      
      $this->CI->load->database('read');

      $sql = 'SELECT adm_menu.Link, adm_menu.LinkText, adm_menu.Position '.
             'FROM adm_menu LEFT JOIN adm_people_menu '.
             'ON adm_menu.ID = adm_people_menu.MenuID '.
             'WHERE adm_menu.ModuleID = \''.$this->module_id.'\' '.
             'AND adm_menu.MenuName = \''.$menu_name.'\' '.
             'AND adm_people_menu.Username = \''.$username.'\' '.
             'AND adm_people_menu.SiteID = \''.$this->site_id.'\' '.
             'ORDER BY adm_menu.Sort';

      $query = $this->CI->db->query($sql);
      $rights = $query->result_array();
      
      for ($i=0; $i<count($rights); $i++)
      {
         if ($rights[$i]['LinkText'] == $this_tab)
         {
            $rights[$i]['Selected'] = TRUE;
         }
         else
         {
            $rights[$i]['Selected'] = FALSE;
         }
      }
      return $rights;
   }

}
// END Access Class
?>