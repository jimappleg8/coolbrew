<?php

class Messages_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   var $live_db;   // database object for the production database

   // --------------------------------------------------------------------

   function Messages_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
      // this model is used primarily by the admin interface
      // which has to pull data from the production database
      $this->live_db = $this->load->database('production', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Connects to the message database and returns the results.
    *
    */
   function get_message_data($message_id)
   {
      $sql = 'SELECT * '.
             'FROM stores_message '.
             'WHERE ID = '.$message_id;
      
      $query = $this->live_db->query($sql);
      $message = $query->row_array();
      
      return $message;
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of all messages that are not closed
    *
    */
   function get_open_messages($filter, $offset = 0, $limit = 20)
   {
      $sql = 'SELECT * '.
             'FROM stores_message '.
             'WHERE Status != "closed" ';
      if ($filter != '')
      {
         $sql .= 'AND Message LIKE "%'.$filter.'%" ';
      }
      $sql .= 'ORDER BY DateSent DESC '.
              'LIMIT '.$offset.', '.$limit;
      
      $query = $this->live_db->query($sql);
      $messages = $query->result_array();
      
      return $messages;
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of all open messages tied to a store ID.
    *
    * @return  array  array of other messages
    */
   function get_open_messages_by_store_id($store_id, $message_id = '')
   {
      if ($store_id == 0)
         return array();
        
      $messages = array();
         
      $message_sql = '';
      if ($message_id != '')
      {
         $message_sql = 'AND ID != '.$message_id.' ';
      }
      
      $sql = 'SELECT * '.
             'FROM stores_message '.
             'WHERE Status != "closed" '.
             'AND StoreID = '.$store_id.' '.
             $message_sql.
             'ORDER BY DateSent DESC ';
      
      $query = $this->live_db->query($sql);
      $messages = $query->result_array();
      
      return $messages;
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of all messages that are not closed
    *
    */
   function get_store_open_message_count($store_id)
   {
      $sql = 'SELECT ID '.
             'FROM stores_message '.
             'WHERE StoreID = '.$store_id.' '.
             'AND Status != "closed"';
      
      $query = $this->live_db->query($sql);
      
      return $query->num_rows();
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of all messages that are not closed
    *
    */
   function get_open_message_count($filter)
   {
      $sql = 'SELECT ID '.
             'FROM stores_message '.
             'WHERE Status != "closed" ';
      if ($filter != '')
      {
         $sql .= 'AND Message LIKE "%'.$filter.'%" ';
      }
      
      $query = $this->live_db->query($sql);
      
      return $query->num_rows();
   }

   // --------------------------------------------------------------------

   /**
    * Sets the messages sent about Nielsen data to closed.
    *
    */
   function close_nielsen_messages()
   {
      $sql = 'UPDATE stores_message '.
             'SET Status = "closed", StatusNotes = "Cannot update this data; Nielsen should auto-update." '.
             'WHERE StoreID = 0 '.
             'AND Status = "unread"';
      
      $query = $this->live_db->query($sql);
      
      return $this->live_db->affected_rows();
   }

   // --------------------------------------------------------------------

   /**
    * Update the message record
    *
    */
   function update_message($message_id, $values)
   {
      $this->live_db->where('ID', $message_id);
      $this->live_db->update('stores_message', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Insert a message record
    *
    */
   function insert_message($values)
   {
      $this->live_db->insert('stores_message', $values);
      
      return TRUE;
   }

}

?>