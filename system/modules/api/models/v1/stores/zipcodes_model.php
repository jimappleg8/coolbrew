<?php

class Zipcodes_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Zipcodes_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      // we use the "write" database because it points to a specific server
      // where the "read" database should stay "localhost" to balance load.
      $this->read_db = $this->load->database($level.'-write', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns yes or no as to whether the zip code is in the database.
    *
    */
   function zipcode_exists($zip)
   {
      $this->load->database('read');

      $sql = 'SELECT zipcode FROM zipcodes_us '.
             'WHERE zipcode = "'.$zip.'"';

      $query = $this->read_db->query($sql);

      return $query->num_rows();
   }

}

?>