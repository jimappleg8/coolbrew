<?php

class Sprints_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Sprints_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns sprint record for the current sprint
    *
    * @access   public
    * @return   array
    */
   function get_this_sprint()
   {
      $today = date('Y-m-d');
      
      $sql = 'SELECT * '.
             'FROM projects_sprint '.
             'WHERE StartDate <= "'.$today.'" '.
             'ORDER BY StartDate DESC';

      $query = $this->read_db->query($sql);
      $sprint = $query->row_array();
      
      return $sprint;
   }

   // --------------------------------------------------------------------

   /**
    * Returns sprint record for the sprint following the indicated sprint
    *
    * @access   public
    * @return   array
    */
   function get_next_sprint($sprint_id)
   {
      $sql = 'SELECT StartDate '.
             'FROM projects_sprint AS s '.
             'WHERE ID = '.$sprint_id;

      $query = $this->read_db->query($sql);
      $dates = $query->row_array();
      $date = $dates['StartDate'];

      $sql = 'SELECT * '.
             'FROM projects_sprint '.
             'WHERE StartDate > "'.$date.'" '.
             'ORDER BY StartDate ASC';

      $query = $this->read_db->query($sql);
      $sprint = $query->row_array();
      
      return ($sprint != FALSE) ? $sprint : array();
   }

   // --------------------------------------------------------------------

   /**
    * Returns sprint record for the sprint preceeding the indicated sprint
    *
    * @access   public
    * @return   array
    */
   function get_previous_sprint($sprint_id)
   {
      $sql = 'SELECT StartDate '.
             'FROM projects_sprint AS s '.
             'WHERE ID = '.$sprint_id;

      $query = $this->read_db->query($sql);
      $dates = $query->row_array();
      $date = $dates['StartDate'];

      $sql = 'SELECT * '.
             'FROM projects_sprint '.
             'WHERE StartDate < "'.$date.'" '.
             'ORDER BY StartDate DESC';

      $query = $this->read_db->query($sql);
      $sprint = $query->row_array();
      
      return ($sprint != FALSE) ? $sprint : array();
   }

   // --------------------------------------------------------------------

   /**
    * Returns sprint record for the specified sprint ID
    *
    * @access   public
    * @return   array
    */
   function get_sprint_data($sprint_id)
   {
      $sql = 'SELECT * '.
             'FROM projects_sprint '.
             'WHERE ID = '.$sprint_id;

      $query = $this->read_db->query($sql);
      $sprint = $query->row_array();
      
      return $sprint;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of sprint records for the specified story ID
    *
    * @access   public
    * @return   array
    */
   function get_sprints($story_id)
   {
      $sql = 'SELECT s.*, ss.Status, ss.EstimatedHours '.
             'FROM projects_sprint AS s, projects_story_sprint AS ss '.
             'WHERE s.ID = ss.SprintID '.
             'AND ss.StoryID = '.$story_id.' '.
             'ORDER BY s.StartDate DESC';

      $query = $this->read_db->query($sql);
      $sprints = $query->result_array();
      
      return $sprints;
   }

   // --------------------------------------------------------------------

   /**
    * Returns sprint list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_sprint_list()
   {
      $sql = 'SELECT * '.
             'FROM projects_sprint '.
             'ORDER BY StartDate';

      $query = $this->read_db->query($sql);
      $sprints = $query->result_array();
         
      $results = array(''=>'-- Choose a Sprint --');
      for ($i=0; $i<count($sprints); $i++)
      {
         $results[$sprints[$i]['ID']] = $sprints[$i]['Name'].' ('.date('M. j, Y', strtotime($sprints[$i]['StartDate'])).' - '.date('M. j, Y', strtotime($sprints[$i]['EndDate'])).')';
      }
      
      return $results;
   }


}

?>