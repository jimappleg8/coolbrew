<?php

class Data_points_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Data_points_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of data points
    */
   function get_data_points()
   {
      $sql = 'SELECT * '.
             'FROM mon_data_point';
      $query = $this->read_db->query($sql);
      $data_points = $query->result_array();
      
      return $data_points;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of data points in place of the get_assigned function
    */
   function get_all_data_points()
   {
      $sql = 'SELECT id AS data_point_id, source, model, '.
               'name, description '.
             'FROM mon_data_point';
      $query = $this->read_db->query($sql);
      $data_points = $query->result_array();
      
      return $data_points;
   }
  
}

/* End of file Data_points_model.php */
/* Location: ./system/modules/monitor/models/Data_points_model.php */