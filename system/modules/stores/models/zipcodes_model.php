<?php

class Zipcodes_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Zipcodes_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns yes or no as to whether the zip code is in the database.
    *
    */
   function zipcode_exists($zip)
   {
      $sql = 'SELECT zipcode FROM zipcodes_us '.
             'WHERE zipcode = "'.$zip.'"';
      $query = $this->read_db->query($sql);

      return $query->num_rows();
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of zip codes in the requested state
    *
    */
   function zipcodes_in_state($state)
   {
      $sql = 'SELECT zipcode FROM zipcodes_us '.
             'WHERE admin_code1 = "'.$state.'"';
      $query = $this->read_db->query($sql);
      
      $zips = $query->result_array();
      
      $clean_zips = array();
      foreach ($zips AS $zip)
      {
         $clean_zips[] = $zip['zipcode'];
      }

      return $clean_zips;
   }

}

?>