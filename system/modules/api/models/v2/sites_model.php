<?php

class Sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';
   
   // --------------------------------------------------------------------

   function Sites_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      $this->read_db = $this->load->database($level.'-read', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_site_data($site_id)
   {
      $sql = 'SELECT adm_site.ID AS SiteID, adm_site.Description, '.
               'adm_site_domain.Domain, adm_brand.Name AS BrandName '.
             'FROM adm_site ' .
             'LEFT JOIN adm_site_domain ON adm_site.ID = adm_site_domain.SiteID '.
             'LEFT JOIN adm_site_brand ON adm_site.ID = adm_site_brand.SiteID '.
             'LEFT JOIN adm_brand ON adm_brand.ID = adm_site_brand.BrandID '.
             'WHERE adm_site.ID = \''.$site_id.'\' '.
             'AND adm_site_domain.PrimaryDomain = 1';
      
      $query = $this->read_db->query($sql);
      $site = $query->row_array();
      
      return $site;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function valid_site_id($site_id, $is_error)
   {
      // see if an error has already been thrown
      if ($is_error == TRUE)
      {
         return TRUE;
      }
      
      if ($site_id == '')
      {
         $this->http_code = 400;
         $this->error_dev_msg = 'No site-id was supplied.';
         $this->error_usr_msg = 'No site-id was supplied.';
         $this->error_more_info = 'http://www.hcgweb.net/docs/site-codes-reference.php';
         return FALSE;
      }
      
      $sql = 'SELECT s.ID AS SiteID '.
             'FROM adm_site AS s '.
             'WHERE s.ID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      
      if ($query->num_rows() == 0)
      {
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$site_id.' is invalid for site-id.';
         $this->error_usr_msg = 'The supplied site-id parameter is invalid.';
         $this->error_more_info = 'http://www.hcgweb.net/docs/site-codes-reference.php';
         return FALSE;
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand name for a given site ID.
    *
    */
   function get_brand_name($site_id)
   {
      $this->load->database('read');
      
      $sql = 'SELECT adm_brand.Name '.
             'FROM adm_site, adm_brand, adm_site_brand '.
             'WHERE adm_site.ID LIKE "'.$site_id.'" '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID';
      $query = $this->db->query($sql);
      $brand = $query->row_array();
      
      if ( ! isset($brand['Name']))
         $name = "this brand's";
      else
         $name = $brand['Name'];
      
      return $name;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of sites that have a store locator
    *
    */
   function get_sites_list()
   {
      $this->load->database('read');

      $sql = 'SELECT adm_site.ID AS SiteID, adm_site.Description, '.
               'adm_site_domain.Domain, adm_brand.Name AS BrandName '.
             'FROM adm_site ' .
             'LEFT JOIN adm_site_domain ON adm_site.ID = adm_site_domain.SiteID '.
             'LEFT JOIN adm_site_brand ON adm_site.ID = adm_site_brand.SiteID '.
             'LEFT JOIN adm_brand ON adm_brand.ID = adm_site_brand.BrandID '.
             'WHERE adm_site_domain.PrimaryDomain = 1 '.
             'ORDER BY adm_site.ID ASC';
      $query = $this->db->query($sql);
      $sites = $query->result_array();
      
      return $sites;
   }

   // --------------------------------------------------------------------

   /**
    * Returns boolean indicating whether module name exists.
    *
    * @access   public
    * @return   boolean
    */
   function valid_module_id($module_id, $is_error)
   {
      // see if an error has already been thrown
      if ($is_error == TRUE)
      {
         return TRUE;
      }
      
      // the module id is an optional parameter so if it's blank, all is well.
      if ($module_id == '')
      {
         return TRUE;
      }
      
      $sql = 'SELECT m.ID AS ModuleID '.
             'FROM adm_module AS m '.
             'WHERE m.ID = "'.$module_id.'"';
      
      $query = $this->read_db->query($sql);
      
      if ($query->num_rows() == 0)
      {
         $this->http_code = 400;
         $this->error_dev_msg = 'The value '.$module_id.' is invalid for module id.';
         $this->error_usr_msg = 'The supplied module id parameter is invalid.';
         return FALSE;
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of sites that have a store locator
    *
    */
   function get_sites_list_by_module($module_name)
   {
      $this->load->database('read');

      $sql = 'SELECT adm_site.ID AS SiteID, adm_site.Description, '.
               'adm_site_domain.Domain, adm_brand.Name AS BrandName '.
             'FROM adm_site ' .
             'LEFT JOIN adm_site_domain ON adm_site.ID = adm_site_domain.SiteID '.
             'LEFT JOIN adm_site_brand ON adm_site.ID = adm_site_brand.SiteID '.
             'LEFT JOIN adm_brand ON adm_brand.ID = adm_site_brand.BrandID '.
             'LEFT JOIN adm_site_module ON adm_site_module.SiteID = adm_site_brand.SiteID '.
             'WHERE adm_site_domain.PrimaryDomain = 1 '.
             'AND adm_site_module.ModuleID = "'.$module_name.'" '.
             'ORDER BY adm_site.ID ASC';
      $query = $this->db->query($sql);
      $sites = $query->result_array();
      
      return $sites;
   }

}

/* End of file sites_model.php */
/* Location: ./system/modules/api/models/v1/sites_model.php */