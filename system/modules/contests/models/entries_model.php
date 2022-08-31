<?php

class Entries_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Entries_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new entry into the database
    *
    * @access   public
    * @param    string    The table into which to insert the record
    * @param    string    The record values
    * @return   array
    */
   function insert_entry($table, $values)
   {
      if ($this->write_db->insert($table, $values) === FALSE)
      {
         return FALSE;
      }
      else
      {
         return $this->write_db->insert_id();
      }
   }

   // --------------------------------------------------------------------

   /**
    * Checks if the specified string value exists in the specified table 
    *  and field. Can be used, for example, to check if a specific email
    *  address is already in the entry database.
    *
    * NOTE: LIKE is used for strings so the test is not case-sensitive.
    *
    * @access   public
    * @param    string    The table into which to insert the record
    * @param    string    The record values
    * @return   array
    */
   function check_entry_string($table, $field, $value)
   {
      $sql = 'SELECT '.$field.' '.
             'FROM '. $table.' '.
             'WHERE '.$field.' LIKE "'.$value.'"';
      $query = $this->read_db->query($sql);

      return ($query->num_rows() > 0) ? TRUE : FALSE;
   }

}

/* End of file entries_model.php */
/* Location: ./system/modules/contests/models/entries_model.php */