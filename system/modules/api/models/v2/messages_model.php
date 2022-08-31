<?php

class Messages_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';
   
   // --------------------------------------------------------------------

   function Messages_model()
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
      $this->read_db = $this->load->database($level.'-read', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Insert a new message record
    *
    */
   function insert_message($values, $test)
   {
      if (strtolower($test) != 'true')
      {
         $this->write_db->insert('stores_message', $values);
      
         $message_id = $this->write_db->insert_id();
      }
      else
      {
         $message_id = "test... not saved";
      }
      
      return $message_id;
   }


}

?>