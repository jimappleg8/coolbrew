<?php

class Report_data_point_link_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Report_data_point_link_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of data points assigned to the given report.
    */
   function get_assigned($report_id)
   {
      $sql = 'SELECT l.data_point_id, d.source, d.model, '.
               'd.name, d.description '.
             'FROM mon_report_data_point_link AS l '.
             'INNER JOIN mon_data_point as d '.
               'ON d.id = l.data_point_id '.
             'WHERE report_id = '.$report_id;
      $query = $this->read_db->query($sql);
      $data_points = $query->result_array();
      
      return $data_points;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes all site links for the given report
    */
   function delete_report_links($report_id)
   {
      $this->write_db->where('report_id', $report_id);
      $this->write_db->delete('mon_report_data_point_link');
   }

   // --------------------------------------------------------------------

   /**
    * Inserts new link record
    */
   function insert_link($report_id, $data_point_id)
   {
      $new_link = array();
      $new_link['report_id'] = $report_id;
      $new_link['data_point_id'] = $data_point_id;
      $this->write_db->insert('mon_report_data_point_link', $new_link);
   }

}

/* End of file Report_data_point_link_model.php */
/* Location: ./system/modules/monitor/models/Report_data_point_link_model.php */