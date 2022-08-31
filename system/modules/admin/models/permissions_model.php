<?php

class Permissions_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Permissions_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);

      $this->CI =& get_instance();
      $options = array('db' => 'write', 'prefix' => 'adm');
      $this->CI->load->library('tacl', $options);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of all sites the specified user has access to
    *
    * @access   public
    * @param    str      the username to search for
    * @return   array
    */
   function get_sites($username)
   {
      $this->CI->load->model('People');

      $usercode = $this->CI->People->get_usercode($username);
      
      // get a list of sites the user currently has access to
      $sites = $this->tacl->authorizations('member', $usercode);
      
      $domains = $this->get_site_domains();
      
      $cnt = 0;
      $new_sites = array();
      foreach ($sites AS $site)
      {
         list($site_id, $ident) = explode('-', $site['ResourceName']);
         if ($ident == 'site' && $site['ActionName'] == 'view')
         {
            $new_sites[$cnt] = $site;
            $new_sites[$cnt]['SiteID'] = $site_id;
            $new_sites[$cnt]['Domain'] = $domains[$site_id];
            $cnt++;
         }
      }
      
      // sort the array by domain (domain list is already in sort order)
      $sorted_sites = array();
      foreach ($domains AS $site_id => $domain)
      {
         foreach ($new_sites AS $site)
         {
            if ($site['Domain'] == $domain)
            {
               $sorted_sites[] = $site;
               $continue;
            }
         }
      }
      return $sorted_sites;
   }

   // --------------------------------------------------------------------

   /**
    * Adds view privileges for the specified site and user
    *
    * @access   public
    * @param    str      the username
    * @param    str      the site ID
    * @return   array
    */
   function add_permissions($username, $site_id)
   {
      $this->CI->load->model('People');

      $usercode = $this->CI->People->get_usercode($username);
      $resource = $site_id.'-site';
      $action = 'view';
      $this->tacl->add_permission('member', $usercode, $resource, $action);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes view privileges for the spcified site and user
    *
    * @access   public
    * @param    str      the username
    * @param    str      the site ID
    * @return   array
    */
   function delete_permissions($username, $site_id)
   {
      $this->CI->load->model('People');

      $usercode = $this->CI->People->get_usercode($username);
      $resource = $site_id.'-site';
      $action = 'view';
      $this->tacl->remove_permission('member', $usercode, $resource, $action);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of primary domains for a form
    *
    * If the username is supplied, it list all domains that the user
    *  doesn't already have access to.
    *
    * @access   public
    * @param    str      the username
    * @param    str      the site ID
    * @return   array
    */
   function get_site_domains($username = '')
   {
      $sql = 'SELECT SiteID, Domain '.
             'FROM adm_site_domain '.
             'WHERE PrimaryDomain = 1 '.
             'ORDER BY Domain';
      $query = $this->read_db->query($sql);
      $results = $query->result_array();
      
      $domains = array();
      foreach ($results AS $result)
      {
         $domains[$result['SiteID']] = $result['Domain'];
      }
      
      if ($username != '')
      {
         $this->CI->load->model('People');
         
         $usercode = $this->CI->People->get_usercode($username);
      
         // get a list of sites the user currently has access to
         $sites = $this->tacl->authorizations('member', $usercode);
      
         foreach ($sites AS $site)
         {
            list($site_id, $ident) = explode('-', $site['ResourceName']);
            unset($domains[$site_id]);
         }
      }
      return $domains;
   }

}

?>