<?php

class Messages_model extends Model {

   function Messages_model()
   {
      parent::Model();
      $this->live_db = $this->load->database('production', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Insert a new message record
    *
    */
   function insert_message($values, $mode)
   {
      if (strtolower($mode) != 'test')
      {
         $this->live_db->insert('stores_message', $values);
      
         $message_id = $this->live_db->insert_id();
      }
      else
      {
         $message_id = "test... not saved";
      }
      
      return $message_id;
   }


}

?>