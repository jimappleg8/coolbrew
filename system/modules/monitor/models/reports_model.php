<?php

class Reports_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   var $CI;

   // --------------------------------------------------------------------

   function Reports_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of report data for the specified report record.
    *
    * @returns array  list of reports
    */
   function get_report_data($report_id)
   {
      $this->CI =& get_instance();
      
      $sql = 'SELECT * '.
             'FROM mon_report '.
             'WHERE id = '.$report_id;
      $query = $this->read_db->query($sql);
      $report = $query->row_array();
      
      // get the sites list
      $this->CI->load->model('Report_site_link');
      $report['sites'] = $this->CI->Report_site_link->get_assigned($report_id);
      
      // get the data points list
      $this->CI->load->model('Report_data_point_link');
      $report['data_points'] = $this->CI->Report_data_point_link->get_assigned($report_id);
      
      return $report;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of reports
    *
    * @returns array  list of reports
    */
   function get_reports()
   {
      $sql = 'SELECT * '.
             'FROM mon_report '.
             'ORDER BY start_date';
      $query = $this->read_db->query($sql);
      $reports = $query->result_array();
      
      return $reports;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of reports for use in selects
    *
    * @returns array  list of reports
    */
   function get_report_list()
   {
      $sql = 'SELECT r.*, rt.name '.
             'FROM mon_report AS r '.
             'LEFT JOIN mon_report_type as rt '.
             '  ON r.report_type_id = rt.id '.
             'ORDER BY start_date';
      $query = $this->read_db->query($sql);
      $reports = $query->result_array();
      
      $report_list[''] = '-- Choose a report --';
      foreach ($reports AS $report)
      {
         $report_list[$report['id']] = $report['name'].' ('.$report['start_date'].' - '.$report['end_date'].')';
      }
      
      return $report_list;
   }

   // --------------------------------------------------------------------

   /**
    * Creates a new source record
    *
    * @param   array  values to insert
    * @returns int    the record id of new record
    */
   function insert_report($values)
   {
      $this->write_db->insert('mon_report', $values);
      $report_id = $this->write_db->insert_id();
      
      return $report_id;
   }

   // --------------------------------------------------------------------

   function update_report($report_id, $values)
   {
      $this->write_db->where('id', $report_id);
      $this->write_db->update('mon_report', $values);
      
      return TRUE;
   }


}

/* End of file Reports_model.php */
/* Location: ./system/modules/monitor/models/Reports_model.php */