<?php

class Jobs_location_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Jobs_location_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns location data for the given location ID
    *
    * @access   public
    * $param    int     the location ID
    * @return   array
    */
   function get_location_data($location_id)
   {
      $sql = 'SELECT * '.
             'FROM jobs_location '.
             'WHERE ID = '.$location_id;

      $query = $this->read_db->query($sql);
      $location = $query->row_array();
         
      return $location;
   }

   // --------------------------------------------------------------------

   /**
    * Returns location list
    *
    * @access   public
    * @return   array
    */
   function get_location_array()
   {
      $sql = 'SELECT ID, LocationName '.
             'FROM jobs_location '.
             'WHERE Country = "United States" '.
             'AND Status = 1 '.
             'ORDER BY LocationName';

      $query = $this->read_db->query($sql);
      $locations = $query->result_array();
         
      $results = array();
      for ($i=0; $i<count($locations); $i++)
      {
         $results[$locations[$i]['ID']] = $locations[$i]['LocationName'];
      }

      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns location list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_location_list()
   {
      $sql = 'SELECT ID, LocationName, ContactEmail '.
             'FROM jobs_location '.
             'WHERE Country = "United States" '.
             'AND Status = 1 '.
             'ORDER BY LocationName';

      $query = $this->read_db->query($sql);
      $locations = $query->result_array();
         
      $results = array(''=>'-- Choose a location --');
      for ($i=0; $i<count($locations); $i++)
      {
         $results[$locations[$i]['ID']] = $locations[$i]['LocationName'].' -> '.$locations[$i]['ContactEmail'];
      }

      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns location list for use in forms. It will return just the 
    * oldest record if there are multiple records with the same 
    * LocationName.
    *
    * @access   public
    * @return   array
    */
   function get_distinct_location_list()
   {
      $sql = 'SELECT ID, LocationName '.
             'FROM jobs_location AS j1 '.
             'WHERE j1.ID IN ('.
               'SELECT min(j2.ID) '.
               'FROM jobs_location AS j2 '.
               'WHERE j2.LocationName = j1.LocationName'.
             ') '.
             'AND Country = "United States" '.
             'AND j1.Status = 1 '.
             'ORDER BY j1.LocationName';

      $query = $this->read_db->query($sql);
      $locations = $query->result_array();
         
      $results = array(''=>'-- Choose a location --');
      for ($i=0; $i<count($locations); $i++)
      {
         $results[$locations[$i]['ID']] = $locations[$i]['LocationName'];
      }

      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns email addresses for the given location name
    *
    * @access   public
    * $param    int     the location ID
    * @return   array
    */
   function get_emails($location_name)
   {
      $sql = 'SELECT ContactEmail '.
             'FROM jobs_location '.
             'WHERE LocationName = "'.$location_name.'" '.
             'AND Status = 1';

      $query = $this->read_db->query($sql);
      $locations = $query->result_array();
      
      $email_array = array();
      foreach ($locations AS $location)
      {
         $email_array[] = $location['ContactEmail'];
      }
//      $emails = implode(', ', $email_array);
         
      return $email_array;
   }

}

?>