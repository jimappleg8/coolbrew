<?php

class Sites_model extends Model {

   function Sites_model()
   {
      parent::Model();

      $this->load->database('read');
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
      $sql = 'SELECT adm_site.ID AS SiteID, adm_site.Description, adm_site_domain.Domain, adm_site_domain.ID AS DomainID, adm_brand.Name AS BrandName '.
             'FROM adm_site, adm_site_domain, adm_site_brand, adm_brand ' .
             'WHERE adm_site.ID = \''.$site_id.'\' '.
             'AND adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID '.
             'AND adm_site_domain.PrimaryDomain = 1';
      
      $query = $this->db->query($sql);
      $site = $query->row_array();

      return $site;
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
      $query = $this->db->query($sql);
      $brand = $query->row_array();
      
      $brand_name = (isset($brand['Name'])) ? $brand['Name'] : 'unknown';
      
      return $brand_name;
   }


}

/* End of file sites_model.php */
/* Location: ./system/modules/products/models/sites_model.php */