<?php

class Analytics_source_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Analytics_source_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of sources
    *
    * @param   str    optional type string
    * @returns array  list of sources
    */
   function get_active_sources($type = '')
   {
      $sql = 'SELECT * '.
             'FROM mon_analytics_source '.
             'WHERE status = "active" ';
      if ($type != '')
      {
         $sql .= 'AND type = "'.$type.'" ';
      }
      $sql .= 'ORDER BY profile_label';
      $query = $this->read_db->query($sql);
      $sources = $query->result_array();
      
      return $sources;
   }

   // --------------------------------------------------------------------

   /**
    * Creates a new source record
    *
    * @param   array  values to insert
    * @returns int    the record id of new record
    */
   function insert_source($values)
   {
      $this->write_db->insert('mon_analytics_source', $values);
      $source_id = $this->write_db->insert_id();
      
      return $source_id;
   }

   // --------------------------------------------------------------------

   function update_source($source_id, $values)
   {
      $this->write_db->where('id', $source_id);
      $this->write_db->update('mon_analytics_source', $values);
      
      return TRUE;
   }


}

/* End of file analytics_source_model.php */
/* Location: ./system/modules/monitor/models/analytics_source_model.php */