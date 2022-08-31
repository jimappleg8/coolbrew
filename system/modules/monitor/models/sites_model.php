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

   /**
    * Returns a list of sites for use in the report edit page.
    */
   function get_sites()
   {
      $sql = 'SELECT s.ID, d.Domain, s.Status, a.id AS SourceID '.
             'FROM (adm_site AS s '.
             'INNER JOIN adm_site_domain as d '.
               'ON s.ID = d.SiteID) '.
             'LEFT JOIN mon_analytics_source as a '.
             'ON s.ID = a.site_id '.
             'WHERE d.PrimaryDomain = 1 '.
             'ORDER BY d.Domain';
      $query = $this->read_db->query($sql);
      $sites = $query->result_array();
      
      return $sites;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns a list of sites for use in the report edit page.
    */
   function get_sites_lookup()
   {
      $sql = 'SELECT s.ID, d.Domain '.
             'FROM adm_site AS s '.
             'INNER JOIN adm_site_domain as d '.
               'ON s.ID = d.SiteID '.
             'WHERE d.PrimaryDomain = 1 '.
             'ORDER BY d.Domain';
      $query = $this->read_db->query($sql);
      $sites = $query->result_array();
      
      $sites_lookup = array();
      foreach ($sites AS $site)
      {
         $sites_lookup[$site['ID']] = $site['Domain'];
      }
      
      return $sites_lookup;
   }
   
}

/* End of file Sites_model.php */
/* Location: ./system/modules/monitor/models/Sites_model.php */