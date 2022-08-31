<?php

class Report_types_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Report_types_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of report types for lists
    *
    * @returns array  list of reports
    */
   function get_report_types()
   {
      $sql = 'SELECT id, name '.
             'FROM mon_report_type '.
             'ORDER BY name';
      $query = $this->read_db->query($sql);
      $types = $query->result_array();
      
      $report_types[''] = '-- Choose a report type --';
      foreach ($types AS $type)
      {
         $report_types[$type['id']] = $type['name'];
      }
      
      return $report_types;
   }


}

/* End of file Report_types_model.php */
/* Location: ./system/modules/monitor/models/Report_types_model.php */