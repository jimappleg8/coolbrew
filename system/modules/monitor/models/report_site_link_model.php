<?php

class Report_site_link_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Report_site_link_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of sites assigned to the given report.
    */
   function get_assigned($report_id)
   {
      $sql = 'SELECT l.site_id, d.Domain AS site_name '.
             'FROM (mon_report_site_link AS l '.
             'INNER JOIN adm_site AS s '.
               'ON l.site_id = s.ID) '.
             'INNER JOIN adm_site_domain as d '.
               'ON s.ID = d.SiteID '.
             'WHERE l.report_id = '.$report_id.' '.
             'AND d.PrimaryDomain = 1 '.
             'ORDER BY d.Domain';
      $query = $this->read_db->query($sql);
      $sites = $query->result_array();
      
      return $sites;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of sites for use in the report edit page.
    */
   function get_report_ga_accounts($report_id)
   {
      $sql = 'SELECT a.* '.
             'FROM mon_report_site_link AS s '.
             'LEFT JOIN mon_analytics_source as a '.
             'ON s.site_id = a.site_id '.
             'WHERE s.report_id = '.$report_id;
      $query = $this->read_db->query($sql);
      $sites = $query->result_array();
      
      return $sites;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes all site links for the given report
    */
   function delete_report_links($report_id)
   {
      $this->write_db->where('report_id', $report_id);
      $this->write_db->delete('mon_report_site_link');
   }

   // --------------------------------------------------------------------

   /**
    * Inserts new link record
    */
   function insert_link($report_id, $site_id)
   {
      $new_link = array();
      $new_link['report_id'] = $report_id;
      $new_link['site_id'] = $site_id;
      $this->write_db->insert('mon_report_site_link', $new_link);
   }

}

/* End of file Report_site_link_model.php */
/* Location: ./system/modules/monitor/models/Report_site_link_model.php */