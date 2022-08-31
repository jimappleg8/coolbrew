<?php

class Resources_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Resources_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns resource records for a given resource ID or all records
    *
    * @access   public
    * @param    int      The project ID
    * @return   array
    */
   function get_resource_data($resrc = "all")
   {
      if ($resrc == "all")
      {
         $sql = 'SELECT ID, FirstName, LastName, HoursPerDay '.
                'FROM projects_resource';
         $query = $this->read_db->query($sql);
         return $query->result_array();
      }
      else
      {
         $sql = 'SELECT ID, FirstName, LastName, HoursPerDay '.
                'FROM projects_resource '.
                'WHERE ID = \''.$resrc.'\'';
         $query = $this->read_db->query($sql);
         return $query->row_array();
      }
   }

   // --------------------------------------------------------------------

   /**
    * Returns resource list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_resource_list()
   {
      $sql = 'SELECT ID, FirstName, LastName '.
             'FROM projects_resource '.
             'ORDER BY LastName';

      $query = $this->read_db->query($sql);
      $resources = $query->result_array();
         
      $results = array(''=>'');
      for ($i=0; $i<count($resources); $i++)
      {
         $results[$resources[$i]['ID']] = $resources[$i]['FirstName'].' '.$resources[$i]['LastName'];
      }
      
      return $results;
   }


}

?>