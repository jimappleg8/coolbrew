<?php

class Sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Sites_model()
   {
      parent::Model();
      $this->load->library('session');
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
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
      $sql = 'SELECT s.ID AS SiteID, s.Description, s.Status, s.DevVendorURL, '.
               's.DevVendorName, s.DevURL, s.StageURL, s.LiveURL, sd.Domain, '.
               'sd.ID AS DomainID, b.Name AS BrandName, b.ID AS BrandID '.
             'FROM adm_site AS s, adm_site_domain AS sd, '.
               'adm_site_brand AS sb, adm_brand AS b ' .
             'WHERE s.ID = \''.$site_id.'\' '.
             'AND s.ID = sd.SiteID '.
             'AND s.ID = sb.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1';
      
      $query = $this->read_db->query($sql);
      $site = $query->row_array();

      return $site;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand name for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_brand_name($site_id)
   {
      $site = $this->get_site_data($site_id);
      return $site['BrandName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand ID for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_brand_id($site_id)
   {
      $site = $this->get_site_data($site_id);
      return $site['BrandID'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns multi-dimensional array of sites ordered by the brand for 
    *   the current user. If the user is an admin the full list is returned.
    *
    * @access   public
    * @return   array
    */
   function get_sites_by_brand()
   {
      $group = $this->session->userdata('group');
      $usercode = $this->session->userdata('usercode');

      $sql = 'SELECT s.ID AS SiteID, s.DevURL, s.StageURL, s.LiveURL, '.
                'sd.Domain, b.Name, b.ID AS BrandID '.
             'FROM adm_site AS s, adm_brand AS b, adm_site_domain AS sd, '.
               'adm_site_brand AS sb '.
             'WHERE s.ID = sb.SiteID '.
             'AND s.Status = "active" '.
             'AND s.ID = sd.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1 '.
             'ORDER BY b.Name, sd.Domain';
 
 /*
      // this restricts the list according to access rights
      
      $sql = 'SELECT s.ID AS SiteID, sd.Domain, b.Name, b.ID AS BrandID '.
             'FROM adm_site AS s, adm_brand AS b, adm_site_domain AS sd, '.
               'adm_site_brand AS sb, adm_member AS m, adm_action AS a, '.
               'adm_permission AS pm, adm_resource AS r '.
             'WHERE s.ID = sb.SiteID '.
             'AND s.ID = sd.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1 '.
             'AND m.Name = "'.$usercode.'" '.
             'AND m.ID = pm.MemberID '.
             'AND r.Name = CONCAT(s.ID,"-site") '.
             'AND r.ID = pm.ResourceID '.
             'AND a.Name = "view" '.
             'AND a.ID = pm.ActionID '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND m.Enabled = 1 '.
             'AND pm.Enabled = 1 '.
             'AND pm.Access = 1 '.
             'ORDER BY b.Name, sd.Domain';
*/

      $query = $this->read_db->query($sql);
      $brands = $query->result_array();
       
//      echo "$sql<pre>"; print_r($brands); echo "</pre>";
//      exit;

      return $brands;
   }

   // --------------------------------------------------------------------

   /**
    * Returns multi-dimensional array of sites in the same brand as Site ID
    *
    * @access   public
    * @return   array
    */
   function get_sites_in_same_brand($site_id)
   {
      $brand_id = $this->get_brand_id($site_id);

      $sql = 'SELECT adm_site.ID AS SiteID, adm_site_domain.Domain, '.
               'adm_brand.Name, adm_brand.ID AS BrandID '.
             'FROM adm_site, adm_brand, adm_site_domain, adm_site_brand '.
             'WHERE adm_brand.ID = "'.$brand_id.'" '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID '.
             'AND adm_site_domain.PrimaryDomain = 1 '.
             'ORDER BY adm_brand.Name, adm_site_domain.Domain';
      
      $query = $this->read_db->query($sql);
      $brands = $query->result_array();
       
//      echo "$sql<pre>"; print_r($brands); echo "</pre>";
//      exit;

      return $brands;
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
   
}

?>