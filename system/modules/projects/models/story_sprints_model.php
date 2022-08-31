<?php

class Story_sprints_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Story_sprints_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the sprint record for the specified story ID. If there
    *   are multiple records, it will return the one with the newest
    *   StartDate.
    *
    * @access   public
    * @return   array
    */
   function get_newest_sprint($story_id)
   {
      $sql = 'SELECT s.*, ss.Status, ss.EstimatedHours '.
             'FROM projects_sprint AS s, projects_story_sprint AS ss '.
             'WHERE s.ID = ss.SprintID '.
             'AND ss.StoryID = '.$story_id.' '.
             'ORDER BY StartDate DESC';

      $query = $this->read_db->query($sql);
      $sprint = $query->row_array();

      return ($sprint != FALSE) ? $sprint : array();
   }

   // --------------------------------------------------------------------

   /**
    * Returns the sprint record for the specified story ID and Sprint ID
    *
    * @access   public
    * @return   array
    */
   function get_story_sprint_data($story_id, $sprint_id)
   {
      $sql = 'SELECT s.*, ss.Status, ss.EstimatedHours '.
             'FROM projects_sprint AS s, projects_story_sprint AS ss '.
             'WHERE s.ID = ss.SprintID '.
             'AND ss.StoryID = '.$story_id.' '.
             'AND ss.SprintID = '.$sprint_id;

      $query = $this->read_db->query($sql);
      $sprint = $query->row_array();
      
      return $sprint;
   }

   // --------------------------------------------------------------------

   /**
    * Gets the data for the specified Story and Sprint
    *
    * @access   public
    * @return   array
    */
   function get_link_data($story_id, $sprint_id)
   {
      $link['StoryID'] = $story_id;
      $link['SprintID'] = $sprint_id;

      $this->write_db->where($link);
      $query = $this->write_db->get('projects_story_sprint');
      $link = $query->row_array();

      return $link;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a link between the specified Story and Sprint
    *
    * @access   public
    * @return   array
    */
   function insert_link($values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');

      $this->write_db->insert('projects_story_sprint', $values);

      $this->CI->auditor->audit_insert('projects_story_sprint', '', $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Updates the status for the specified Story and Sprint
    *
    * @access   public
    * @return   array
    */
   function update_link($story_id, $sprint_id, $values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');
      
      $old_values = $this->get_link_data($story_id, $sprint_id);
      
      $link['StoryID'] = $story_id;
      $link['SprintID'] = $sprint_id;

      $tmp = $this->write_db->where($link);
      $this->write_db->update('projects_story_sprint', $values);

      $this->auditor->audit_update('projects_story_sprint', $this->write_db->ar_where, $old_values, $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a record from the table
    *
    * @access   public
    * @return   array
    */
   function delete_link($story_id, $sprint_id)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');
      
      $old_values = $this->get_link_data($story_id, $sprint_id);
      
      $link['StoryID'] = $story_id;
      $link['SprintID'] = $sprint_id;

      $tmp = $this->write_db->where($link);
      $this->write_db->delete('projects_story_sprint');
      
      $this->CI->auditor->audit_delete('projects_story_sprint', $tmp->ar_where, $old_values);
      
      // delete all hours records associated with this story
      $this->write_db->where($link);
      $query = $this->write_db->get('projects_hour');
      $hours = $query->result_array();
      foreach ($hours AS $hour)
      {
         $tmp2 = $this->write_db->where('ID', $hour['ID']);
         $this->write_db->delete('projects_hour');
      
         $this->CI->auditor->audit_delete('projects_hour', $tmp2->ar_where, $hour);
      }

      return TRUE;
   }



}

?>