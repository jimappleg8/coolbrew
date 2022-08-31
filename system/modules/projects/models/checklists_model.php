<?php

class Checklists_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Checklists_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns project list with added status information
    *
    * @access   public
    * @param    int      The projects array
    * @return   array
    */
   function add_status_info($project_list)
   {
      // get status information for each project
      for ($i=0, $num_projects=count($project_list); $i<$num_projects; $i++)
      {
         $sql = 'SELECT cl.ParentID, cl.ItemName '. 
                'FROM (projects_checklist AS cl, projects_type AS t, '.
                  'projects_type_default AS td) '. 
                'LEFT JOIN projects_checked AS c '.
                'ON cl.ID = c.ChecklistID '.
                'AND c.ProjectID = '.$project_list[$i]['ID'].' '.
                'WHERE cl.ID = td.PropertyID '. 
                'AND td.Property = "checklist" '.
                'AND t.ID = td.ProjectTypeID '.
                'AND t.ID = '.$project_list[$i]['ProjectTypeID'].' '.
                'AND c.Status is null '.
                'ORDER BY cl.Sort';

         $query = $this->db->query($sql);
         $tasks_array = $query->result_array();
         
         if ($query->num_rows() > 0)
         {
            $project_list[$i]['Task'] = $tasks_array[0]['ItemName'];
            $sql = 'SELECT ItemName, Color, BgColor '.
                   'FROM projects_checklist '.
                   'WHERE ID = '.$tasks_array[0]['ParentID'];

            $query = $this->db->query($sql);
            $phase = $query->row_array();

            $project_list[$i]['Phase'] = $phase['ItemName'];
            $project_list[$i]['Color'] = $phase['Color'];
            $project_list[$i]['BgColor'] = $phase['BgColor'];
         }
         else
         {
            $project_list[$i]['Task'] = 'complete';
            $project_list[$i]['Phase'] = '';
            $project_list[$i]['Color'] = '';
            $project_list[$i]['BgColor'] = '';
         }
      }
      return $project_list;
   }


}

?>