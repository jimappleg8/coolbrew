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
 * Administrator Class
 *
 * Provides tools for CoolBrew admin controllers
 *
 * @package		Cool Brew
 * @subpackage	Libraries
 * @category	Administrator
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/libraries/administrator.html
 */
class CI_Administrator {

   var $CI;
   var $module_id;
   var $site_id = '';
   
   /**
    * Constructor
    *
    * @access   public
    */      
   function CI_Administrator($params)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('session');

      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->CI->load->library('tacl', $options);

      $this->module_id = $params['module_id'];
      $this->site_id = SITE_ID;
      
      log_message('debug', "Administrator Class Initialized");
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
    * Determines whether the specified module is activated for this site
    *
    */
   function is_active_module($site_id, $module_id)
   {
      $this->CI->load->database('read');
      
      $sql = 'SELECT * FROM adm_site_module '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ModuleID = "'.$module_id.'"';
      $query = $this->CI->db->query($sql);

      return ($query->num_rows() > 0) ? TRUE : FALSE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if access is allowed
    *
    * DEPRECATED
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
         header('Location: http://webadmin.ctea.com/admin.php/login/login_user');
         exit;
      }
      
      $this->CI->load->database('read');

      // if logged in, first check if person has rights to this module
      if ($section != '')
      {
         $sql = 'SELECT adm_member.Name AS Username '.
                'FROM adm_member, adm_member_module '.
                'WHERE adm_member.Name = adm_member_module.Username '.
                'AND adm_member_module.ModuleID = \''.$this->module_id.'\' '.
                'AND adm_member_module.SiteID = \''.$this->site_id.'\'';

         $query = $this->CI->db->query($sql);
      
         if ($query->num_rows() == 0)
         {
            header('Location: http://webadmin.ctea.com/admin.php/login/sorry');
            exit;
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
            header('Location: http://webadmin.ctea.com/admin.php/login/sorry');
            exit;
         }
      }
      return 'approved';
   }

   // --------------------------------------------------------------------
   
   /**
    * Set a "last_allowed" session variable so we can create useful links 
    * if we have to display a "access denied" page later.
    *
    */
   function set_last_allowed()
   {
      $this->CI->session->set_userdata('last_allowed', $this->CI->uri->uri_string());
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * I've placed this here because it is needed by all modules.
    *
    * @access   public
    * @return   array
    */
   function get_site_data($site_id)
   {
      $this->CI->load->database('read');

      $sql = 'SELECT adm_site.ID AS SiteID, adm_site.Description, adm_site_domain.Domain, adm_site_domain.ID AS DomainID, adm_brand.Name AS BrandName '.
             'FROM adm_site, adm_site_domain, adm_site_brand, adm_brand ' .
             'WHERE adm_site.ID = \''.$site_id.'\' '.
             'AND adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID '.
             'AND adm_site_domain.PrimaryDomain = 1';
      
      $query = $this->CI->db->query($sql);
      $site = $query->row_array();

      return $site;
   }

   // --------------------------------------------------------------------
   
   /**
    * Set a "last_allowed" session variable so we can create useful links 
    * if we have to display a "access denied" page later.
    *
    */
   function get_admin_base_path()
   {
      $this->CI->load->database('read');

      $sql = 'SELECT * FROM adm_settings';
      $query = $this->CI->db->query($sql);
      $setting_array = $query->result_array();

      foreach ($setting_array AS $setting)
         $settings[$setting['Setting']] = $setting['Value'];
      
      return $settings['AdminBasePath'];
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if access is allowed using the access control list
    * If the user is a member of the admin group, access is always allowed.
    *
    * @param   string    the resource name
    * @param   string    the action name
    * @param   boolean   should we force the person to log in?
    * @return  boolean
    *
    * DEPRECATED
    */
   function acl_check($resource = '', $action = '', $force_login = FALSE)
   {
      $this->CI->load->helper('url');

      $base = $this->get_admin_base_path();
      $auth = FALSE;
      $usercode = $this->CI->session->userdata('usercode');

      // first check if user is logged in
      if ($usercode == '' && $force_login)
      {
         $this->CI->session->set_flashdata('return_url', $this->CI->uri->uri_string());
         header('Location:'.base_url().'/login.php');
         exit;
      }

      if ($usercode != '' && $resource == '' && $action == '')
      {
         $auth = TRUE;
      }

      // if logged in, check the access control list
      if ($auth == FALSE)
      {
         $auth = $this->CI->tacl->authorized_member($usercode, $action, $resource);
      }
      
      // if still denied, check if user is a member of the admin group
      if ($auth == FALSE)
      {
         $groups = $this->CI->tacl->memberships($usercode);
         if ($groups)
         {
            foreach ($groups AS $id => $name)
            {
              if ($name == 'admin')
              {
                 $auth = TRUE;
              }
            }
         }
      }

      // since access is allowed, set the last allowed session variable
      if ($auth == TRUE)
      {
         $this->set_last_allowed();
      }

      return $auth;
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if the user is logged in
    *
    * @return  mixed   the username or false
    */
   function check_login()
   {
      $this->CI->load->helper('url');

      $usercode = $this->CI->session->userdata('usercode');

      if ($usercode == '')
      {
         $this->CI->session->set_flashdata('return_url', $this->CI->uri->uri_string());
         header('Location:'.base_url().'/login.php');
         exit;
      }
      return $usercode;
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if user is a member of the specified group
    * If the user is a member of the admin group, access is always allowed.
    *
    * @param   string    the resource name
    * @param   string    the action name
    * @param   boolean   should we force the person to log in?
    * @return  boolean
    */
   function check_group($group_name = 'admin')
   {
      $this->CI->load->helper('url');

      $base = $this->get_admin_base_path();
      $auth = FALSE;
      $usercode = $this->check_login();

      $groups = $this->CI->tacl->memberships($usercode);
      if ($groups)
      {
         foreach ($groups AS $id => $name)
         {
           if ($name == $group_name)
           {
              $auth = TRUE;
           }
         }
      }

      // since access is allowed, set the last allowed session variable
      if ($auth == TRUE)
      {
         $this->set_last_allowed();
      }

      return $auth;
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if access is allowed using the access control list
    * If the user is a member of the admin group, access is always allowed.
    *
    * @param   string    the resource name
    * @param   string    the action name
    * @return  boolean
    */
   function check_acl($resource = '', $action = '', $usercode = '')
   {
      $this->CI->load->helper('url');

      $base = $this->get_admin_base_path();
      $auth = FALSE;
      if ($usercode == '')
      {
         $usercode = $this->check_login();
      }

      if ($resource == '' && $action == '')
      {
         $auth = TRUE;
      }

      // if logged in, check the access control list
      if ($auth == FALSE)
      {
         $auth = $this->CI->tacl->authorized_member($usercode, $action, $resource);
      }
      
      // if still denied, check if user is a member of the admin group
      if ($auth == FALSE)
      {
         $groups = $this->CI->tacl->memberships($usercode);
         if ($groups)
         {
            foreach ($groups AS $id => $name)
            {
              if ($name == 'admin')
              {
                 $auth = TRUE;
              }
            }
         }
      }

      // since access is allowed, set the last allowed session variable
      if ($auth == TRUE)
      {
         $this->set_last_allowed();
      }

      return $auth;
   }

   // --------------------------------------------------------------------

   /**
    * Get array for the top level tabs
    *
    */
   function get_main_tabs($this_tab = '')
   {
      $this->CI->load->helper('url');

      $base = $this->get_admin_base_path();

      $result[] = array('Link' => base_url().$base.'/cp/sites/index',
                           'LinkText' => 'Sites',
                           'Position' => 'left');

      $result[] = array('Link' => base_url().$base.'/cp/links/index',
                           'LinkText' => 'Links',
                           'Position' => 'left');

      $this->CI->load->database('read');

      $sql = 'SELECT ID, Name, BasePath, DefaultPage '.
             'FROM adm_module '.
             'WHERE Extends = "admin" '.
             'ORDER BY Sort ASC';

      $query = $this->CI->db->query($sql);
      $modules = $query->result_array();
      
      foreach ($modules AS $module)
      {
         $result[] = array('Link' => base_url().$module['BasePath']. '/'.$module['DefaultPage'],
                           'LinkText' => $module['Name'],
                           'Position' => 'left',
                          );
      }
      
      $result[] = array('Link' => base_url().$base.'/cp/people/index',
                           'LinkText' => 'All People',
                           'Position' => 'right');

      $result[] = array('Link' => base_url().$base.'/cp/vendors/index',
                           'LinkText' => 'All Vendors',
                           'Position' => 'right');

      if ($this->check_group('admin'))
         $result[] = array('Link' => base_url().$base.'/cp/modules/index',
                           'LinkText' => 'Modules',
                           'Position' => 'right');

      if ($this->check_group('admin'))
         $result[] = array('Link' => base_url().$base.'/cp/settings/index',
                           'LinkText' => 'Settings',
                           'Position' => 'right');

      for ($i=0; $i<count($result); $i++)
      {
         if ($result[$i]['LinkText'] == $this_tab)
         {
            $result[$i]['Selected'] = TRUE;
         }
         else
         {
            $result[$i]['Selected'] = FALSE;
         }
      }

      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of tab data
    *
    * Much of the data is hard-coded in this method and parts are gathered
    * from the adm_site_module table.
    *
    */
   function get_site_tabs($site_id, $this_tab = '')
   {
      $username = $this->CI->session->userdata('username');
      $last_action = $this->CI->session->userdata('last_action');
      if ($last_action == '')
      {
         $last_action = 1;
      }
      
      $this->CI->load->helper('url');

      $base = $this->get_admin_base_path();
      
      $result[] = array('Link' => base_url().$base.'/sites/dashboards/index/'.$site_id,
                        'LinkText' => 'Dashboard',
                        'Position' => 'left',
                       );
      $result[] = array('Link' => base_url().$base.'/sites/links/index/'.$site_id,
                        'LinkText' => 'Links',
                        'Position' => 'left',
                       );
      
      $this->CI->load->database('read');

      $sql = 'SELECT m.ID, m.Name, m.BasePath, '.
                'm.DefaultPage, m.Restricted '.
             'FROM adm_site_module AS sm, adm_module AS m '.
             'WHERE sm.ModuleID = m.ID '.
             'AND sm.SiteID = \''.$site_id.'\'';

      $query = $this->CI->db->query($sql);
      $modules = $query->result_array();
      
      foreach ($modules AS $module)
      {
         if ($this->check_acl($site_id.'-site', 'view') || $module['Restricted'] == 0)
         {
            $result[] = array('Link' => base_url().$module['BasePath']. '/'.$module['DefaultPage'].'/'.$site_id.'/',
                              'LinkText' => $module['Name'],
                              'Position' => 'left',
                             );
         }
      }
      
      $result[] = array('Link' => base_url().$base.'/sites/people/index/'.$site_id,
                        'LinkText' => 'People &amp; Permissions',
                        'Position' => 'right',
                       );
      $result[] = array('Link' => base_url().$base.'/sites/vendors/index/'.$site_id,
                        'LinkText' => 'Vendors',
                        'Position' => 'right',
                       );
      if ($this->check_group('admin'))
         $result[] = array('Link' => base_url().$base.'/sites/modules/index/'.$site_id,
                           'LinkText' => 'Modules',
                           'Position' => 'right',
                          );
      if ($this->check_group('admin'))
         $result[] = array('Link' => base_url().$base.'/sites/settings/index/'.$site_id.'/'.$last_action,
                           'LinkText' => 'Settings',
                           'Position' => 'right',
                          );

      for ($i=0; $i<count($result); $i++)
      {
         if ($result[$i]['LinkText'] == $this_tab)
         {
            $result[$i]['Selected'] = TRUE;
         }
         else
         {
            $result[$i]['Selected'] = FALSE;
         }
      }
     
      return $result;
   }

}
// END Access Class
?>