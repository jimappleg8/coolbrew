<?php

class Projects_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Projects_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns project record for a given project ID
    *
    * @access   public
    * @param    int      The project ID
    * @return   array
    */
   function get_project_data($project_id)
   {
      $sql = "SELECT * FROM projects " .
             "WHERE ID = ".$project_id;
      
      $query = $this->read_db->query($sql);
      return $query->row_array();
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns list of all active projects
    *
    * @access   public
    * @return   array
    */
   function get_active_projects()
   {
      $sql = 'SELECT p.ID, p.GroupName, p.ProjectName, p.RequestedDueDate, '.
               'p.Status, p.StartPage, p.ProjectTypeID, p.NewRecord '.
             'FROM projects AS p ' .
             'WHERE p.ProjectTypeID = 1 '.
             'AND p.Status = "active" '.
             'ORDER BY p.ProjectName';

      $query = $this->db->query($sql);
      $results = $query->result_array();
            
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all active projects ordered by group
    *
    * The complex SQL statement below is necessary to get a count
    * of active stories in each project.
    *
    * @access   public
    * @return   array
    */
   function get_active_projects_by_group()
   {
      $sql = 'SELECT p.ID, p.GroupName, p.ProjectName, p.RequestedDueDate, '.
             'p.Status, p.StartPage, p.ProjectTypeID, p.NewRecord, '.
             'COUNT(pp.StoryID) AS ActiveStories '.
             'FROM projects AS p '.
             'LEFT JOIN ('.
               'SELECT pr.ID, ss.SprintID, s.ID AS StoryID, ss.Status '.
               'FROM projects AS pr, projects_story AS s '.
               'LEFT JOIN ('.
                 'SELECT * '.
                 'FROM projects_story_sprint '.
                 'ORDER BY SprintID DESC'.
               ') AS ss '.
                 'ON s.ID = ss.StoryID '.
               'WHERE s.ProjectID = pr.ID '.
               'GROUP BY s.ID '.
               'HAVING ss.Status IS NULL '.
                 'OR ss.Status = "Not Started" '.
                 'OR ss.Status = "In Progress" '.
                 'OR ss.Status = "Deferred" '.
                 'OR ss.Status = "Testing"'.
             ') AS pp '.
               'ON pp.ID = p.ID '.
             'WHERE p.ProjectTypeID = 1 '.
             'AND p.Status = "active" '.
             'GROUP BY p.ID '.
             'ORDER BY p.GroupName, p.ProjectName';

      $query = $this->db->query($sql);
      $results = $query->result_array();
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all projects - active, on-hold and archived.
    *
    * @access   public
    * @return   array
    */
   function get_all_projects()
   {
      $sql = 'SELECT * FROM projects '.
             'ORDER BY ProjectName';

      $query = $this->read_db->query($sql);
      $results = $query->result_array();
            
      return $results;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new project record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function insert_project($values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');

      $this->write_db->insert('projects', $values);

      $project_id = $this->write_db->insert_id();
      
      $this->CI->auditor->audit_insert('projects', '', $values);
      
      return $project_id;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates an existing project record
    *
    * @access   public
    * @param    integer   The project ID
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_project($project_id, $values)
   {
      $this->CI =& get_instance();
      $this->CI->load->library('auditor');
      
      $old_values = $this->get_project_data($project_id);

      // first, update the product record
      $this->write_db->where('ID', $project_id);
      $this->write_db->update('projects', $values);
      
      $this->auditor->audit_update('projects', $this->write_db->ar_where, $old_values, $values);

      return TRUE;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns project types list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_project_type_list()
   {
      $sql = 'SELECT ID, TypeName '.
             'FROM projects_type '.
             'ORDER BY TypeName';

      $query = $this->read_db->query($sql);
      $types = $query->result_array();
         
      $results = array(''=>'');
      for ($i=0; $i<count($types); $i++)
      {
         $results[$types[$i]['ID']] = $types[$i]['TypeName'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns start page list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_start_page_list()
   {
      // we may want to pull this from the menu at some point

      $results['dashboard'] = 'Dashboard';
      $results['stories'] = 'Stories';

      return $results;
   }
   
   
}

?>