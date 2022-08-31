<?php

class Sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Sites_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

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
               'adm_site_domain.Domain, adm_site_domain.ID AS DomainID, '.
               'adm_brand.Name AS BrandName '.
             'FROM adm_site, adm_site_domain, adm_site_brand, adm_brand ' .
             'WHERE adm_site.ID = \''.$site_id.'\' '.
             'AND adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID '.
             'AND adm_site_domain.PrimaryDomain = 1';
      
      $query = $this->read_db->query($sql);
      $site = $query->row_array();
      
      $site_config = site_config($site_id);
      
      $full_site = array_merge($site, $site_config);

      return $full_site;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand name for a given site ID.
    *
    */
   function get_brand_name($site_id)
   {
      $sql = 'SELECT adm_brand.Name '.
             'FROM adm_site, adm_brand, adm_site_brand '.
             'WHERE adm_site.ID LIKE "'.$site_id.'" '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID';
      $query = $this->read_db->query($sql);
      $brand = $query->row_array();
      
      return $brand['Name'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns sites list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_sites_list($include_multi = FALSE)
   {
      $sql = 'SELECT adm_site.ID, adm_site_domain.Domain '.
             'FROM adm_site, adm_site_domain '.
             'WHERE adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site_domain.PrimaryDomain = 1 '.
             'ORDER BY adm_site_domain.Domain';

      $query = $this->read_db->query($sql);
      $sites = $query->result_array();

      $results = array(''=>'');
      if ($include_multi == TRUE)
      {
         $results['multi'] = 'Multiple Websites';
         $results['all'] = 'All Websites';
      }
      for ($i=0; $i<count($sites); $i++)
      {
         $results[$sites[$i]['ID']] = $sites[$i]['Domain'].' ('.$sites[$i]['ID'].')';
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns sites list for use in forms
    *  This version only returns sites that use the Products module
    *
    * @access   public
    * @return   array
    */
   function get_product_sites_list()
   {
      $sql = 'SELECT s.ID, sd.Domain '.
             'FROM adm_site AS s, adm_site_domain AS sd, '.
               'adm_site_module AS sm '.
             'WHERE s.ID = sd.SiteID '.
             'AND s.ID = sm.SiteID '.
             'AND sd.PrimaryDomain = 1 '.
             'AND sm.ModuleID = "products" '.
             'AND s.Status = "active" '.
             'ORDER BY sd.Domain';

      $query = $this->read_db->query($sql);
      $sites = $query->result_array();

      $results = array(''=>'-- select a branded site --');
      for ($i=0; $i<count($sites); $i++)
      {
         $results[$sites[$i]['ID']] = $sites[$i]['Domain'].' ('.$sites[$i]['ID'].')';
      }
      
      return $results;
   }

}

/* End of file sites_model.php */
/* Location: ./system/modules/stores/models/sites_model.php */