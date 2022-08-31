<?php

class Sender_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Sender_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new sender record
    *
    * @access   public
    * @return   integer   the new tell ID
    */
   function insert_sender($values)
   {
      $this->write_db->insert('plugs_sender', $values);
      
      $friend_id = $this->write_db->insert_id();

      return $friend_id;
   }
   

}

?>