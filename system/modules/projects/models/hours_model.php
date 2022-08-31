<?php

class Hours_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Hours_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns hour record for the specified hours ID
    *
    * @access   public
    * @return   array
    */
   function get_hours_data($hour_id)
   {
      $sql = 'SELECT * '.
             'FROM projects_hour '.
             'WHERE ID = '.$hour_id;
             
      $query = $this->read_db->query($sql);
      $hours = $query->row_array();

      return $hours;
   }

   // --------------------------------------------------------------------

   /**
    * Returns all hour records for the specified story and sprint
    *
    * @access   public
    * @return   array
    */
   function get_hours($story_id, $sprint_id)
   {
      $sql = 'SELECT * '.
             'FROM projects_hour '.
             'WHERE StoryID = '.$story_id.' '.
             'AND SprintID = '.$sprint_id;
             
      $query = $this->read_db->query($sql);
      $hours = $query->result_array();

      return $hours;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a link between the specified Story and Sprint
    *
    * @access   public
    * @return   array
    */
   function insert_hours($values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');

      $this->write_db->insert('projects_hour', $values);

      $this->CI->auditor->audit_insert('projects_hour', '', $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Updates the specified Hours record
    *
    * @access   public
    * @return   array
    */
   function update_hours($hour_id, $values, $old_values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');

      $tmp = $this->write_db->where('ID', $hour_id);
      $this->write_db->update('projects_hour', $values);

      $this->CI->auditor->audit_update('projects_hour', $tmp->ar_where, $old_values, $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a record from the table
    *
    * @access   public
    * @return   array
    */
   function delete_hours($hour_id, $old_values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');

      $tmp = $this->write_db->where('ID', $hour_id);
      $this->write_db->delete('projects_hour');
      
      $this->CI->auditor->audit_delete('projects_hour', $tmp->ar_where, $old_values);

      return TRUE;
   }



}

?>