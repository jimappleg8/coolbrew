<?php

class Stories_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Stories_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns story record for a given story ID
    *
    * @access   public
    * @param    int      The story ID
    * @return   array
    */
   function get_story_data($story_id)
   {
      $sql = "SELECT * FROM projects_story " .
             "WHERE ID = ".$story_id;
      
      $query = $this->read_db->query($sql);
      return $query->row_array();
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns list of all stories linked to active projects
    *
    * @access   public
    * @return   array
    */
   function get_active_stories()
   {
      $sql = 'SELECT s.* '.
             'FROM projects_story AS s, projects AS p ' .
             'WHERE s.ProjectID = p.ID '.
             'AND p.Status = "active" '.
             'ORDER BY s.Priority, s.ProjectID';

      $query = $this->db->query($sql);
      $results = $query->result_array();
            
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all stories linked to active projects but not 
    *  linked to the current sprint or linked to any other sprint with
    *  a status of Complete or Cancelled.
    *
    * @access   public
    * @return   array
    */
   function get_backlog_stories()
   {
      $this->CI =& get_instance();
      
      $this->CI->load->model('Story_sprints');
      $this->CI->load->model('Sprints');

      $sql = 'SELECT s.* '.
             'FROM projects_story AS s, projects AS p '.
             'WHERE s.ProjectID = p.ID '.
             'AND p.Status = "active" '.
             'ORDER BY s.Priority, s.ProjectID';

      $query = $this->db->query($sql);
      $results = $query->result_array();
      
      $current = $this->CI->Sprints->get_this_sprint();
      
      $stories = array();
      $cnt = 0;
      foreach ($results AS $result)
      {
         $sprint = $this->CI->Story_sprints->get_newest_sprint($result['ID']);
         if ( ! empty($sprint))
         {
            if ($sprint['ID'] != $current['ID'] 
                && $sprint['Status'] != 'Completed' 
                && $sprint['Status'] != 'Cancelled')
            {
               $stories[$cnt] = $result;
               $stories[$cnt]['Sprint'] = $sprint;
               $cnt++;
            }
         }
         else
         {
            $stories[$cnt] = $result;
            $stories[$cnt]['Sprint'] = $sprint;
            $cnt++;
         }
      }
            
      return $stories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all stories linked to the specified sprint
    *
    * @access   public
    * @return   array
    */
   function get_sprint_stories($sprint_id)
   {
      $sql = 'SELECT s.*, sp.Status '.
             'FROM projects_story AS s, projects AS p, '.
               'projects_story_sprint AS sp ' .
             'WHERE sp.StoryID = s.ID '.
             'AND s.ProjectID = p.ID '.
             'AND sp.SprintID = '.$sprint_id.' '.
             'ORDER BY p.GroupName, p.ProjectName, s.ProjectID';

      $query = $this->db->query($sql);
      $results = $query->result_array();
            
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all active stories marked as vendor support
    *
    * @access   public
    * @return   array
    */
   function get_support_stories()
   {
      $sql = 'SELECT s.*, sp.Status '.
             'FROM projects_story AS s '.
             'LEFT JOIN projects_story_sprint AS sp ' .
               'ON sp.StoryID = s.ID '.
             'INNER JOIN projects AS p '.
               'ON s.ProjectID = p.ID '.
             'WHERE s.Priority = 1 '.
             'AND ((sp.Status != "Completed" '.
             'AND sp.Status != "Cancelled") '.
             'OR sp.Status IS NULL) '.
             'ORDER BY p.GroupName, p.ProjectName, s.ProjectID';

      $query = $this->db->query($sql);
      $results = $query->result_array();
      
//      echo $sql;
//      echo '<pre>'; print_r($results); echo '</pre>'; exit;
            
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all stories linked to specified project
    *
    * @access   public
    * @return   array
    */
   function get_project_stories($project_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->model('Story_sprints');

      $sql = 'SELECT * '.
             'FROM projects_story ' .
             'WHERE ProjectID = '.$project_id.' '.
             'ORDER BY Priority, ID DESC';

      $query = $this->db->query($sql);
      $results = $query->result_array();
            
      $stories = array();
      $cnt = 0;
      foreach ($results AS $result)
      {
         $sprint = $this->CI->Story_sprints->get_newest_sprint($result['ID']);
         $stories[$cnt] = $result;
         $stories[$cnt]['Sprint'] = $sprint;
         $cnt++;
      }

      return $stories;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new story record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function insert_story($values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');

      // first, insert the main product record
      $this->write_db->insert('projects_story', $values);

      $story_id = $this->write_db->insert_id();
      
      $this->CI->auditor->audit_insert('projects_story', '', $values);
      
      // then, make sure the project's NewRecord is set to 0.
      $project['NewRecord'] = 0;
      $this->write_db->where('ID', $values['ProjectID']);
      $this->write_db->update('projects', $project);
      
      return $story_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an existing story record
    *
    * @access   public
    * @param    integer   The project ID
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_story($story_id, $values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');
      
      $old_values = $this->get_story_data($story_id);

      // first, update the story record
      $this->write_db->where('ID', $story_id);
      $this->write_db->update('projects_story', $values);
      
      $this->auditor->audit_update('projects_story', $this->write_db->ar_where, $old_values, $values);

      return TRUE;
   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes the specified story
    *
    * @access   public
    * @return   boolean
    */
   function delete_story($story_id, $old_values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');
      
      // delete all references to this story in projects_story_sprint
      $this->write_db->where('StoryID', $story_id);
      $query = $this->write_db->get('projects_story_sprint');
      $records = $query->result_array();
      foreach ($records AS $record)
      {
         $link['StoryID'] = $record['StoryID'];
         $link['SprintID'] = $record['SprintID'];

         $tmp1 = $this->write_db->where($link);
         $this->write_db->delete('projects_story_sprint');

         $this->CI->auditor->audit_delete('projects_story_sprint', $tmp1->ar_where, $record);
         
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
      }
      
      // delete the actual story record
      $tmp3 = $this->write_db->where('ID', $story_id);
      $this->write_db->delete('projects_story');

      $this->CI->auditor->audit_delete('projects_story', $tmp3->ar_where, $old_values);

      return TRUE;
   }


}

?>