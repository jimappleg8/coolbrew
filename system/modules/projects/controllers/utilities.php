<?php

class Utilities extends Controller {

   var $priorities = array(
      'sprint' => 0,
      'support' => 1,
      'very high' => 2,
      'high' => 3,
      'medium' => 4,
      'low' => 5,
      'very low' => 6,
      'unplanned' => 7,
   );

   // --------------------------------------------------------------------

   function Utilities()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper('url');

      $this->load->database('read');
   }
	
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file or Tabbed Text file of project info
    *  and add it to the projects databases.
    */
   function upload_projects()
   {
      $this->load->helper(array('text','url'));
      $this->load->model('Projects');
      
      // For now, I am going to hard-wire the data into this function
      // but we may want to make this something that can be reused by
      // making a form to enter this information.
      $file = '/Users/japplega/Desktop/projects.csv';
      $columns = array('Group', 'ID', 'ProjectName');
      
      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');

      $row = 0;
      $handle = fopen($file, "r");

      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         for ($i=0; $i<count($columns); $i++)
         {
            $projects[$row][$columns[$i]] = $data[$i];
         }
         
         $id = $this->Projects->insert_project($projects[$row]);
         
         echo $id.': '.$projects[$row]['ProjectName'].' created<br />';
         $row++;
      }
      fclose($handle);
      
//      echo '<pre>'; print_r($projects); echo '</pre>';
      echo '<br />'.($row - 1).' records created.';
      exit;
   }
   
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file or Tabbed Text file of story info
    *  and add it to the story databases.
    */
   function upload_stories()
   {
      $this->load->helper(array('text','url'));
      $this->load->model('Stories');
      
      // For now, I am going to hard-wire the data into this function
      // but we may want to make this something that can be reused by
      // making a form to enter this information.
      $file = '/Users/japplega/Desktop/stories.csv';
      $columns = array('HeatID', 'ProjectID', 'Description', 'Client', 'Points', 'Assigned', 'Deadline', 'Sprint', 'SprintRange', 'Priority');
      
      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');

      $row = 0;
      $handle = fopen($file, "r");

      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         for ($i=0; $i<count($columns); $i++)
         {
            $stories[$row][$columns[$i]] = $data[$i];
         }
         $stories[$row]['HeatAssignment'] = NULL;
         if ($stories[$row]['HeatID'] != '')
         {
            if (strpos($stories[$row]['HeatID'], '-') == 0)
            {
               $stories[$row]['HeatID'] = $stories[$row]['HeatID'].'-1';
            }
            list($stories[$row]['HeatID'], $stories[$row]['HeatAssignment']) = explode('-', $stories[$row]['HeatID']);
         }
         $stories[$row]['Priority'] = $this->priorities[$stories[$row]['Priority']];
         
         $id = $this->Stories->insert_story($stories[$row]);
         
         echo $id.': '.$stories[$row]['Description'].' created<br />';
         $row++;
      }
      fclose($handle);
      
      echo '<br />'.($row - 1).' records created.';
      exit;
   }
   
   

} // END Class

?>