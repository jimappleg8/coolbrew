<?php

class Heat extends Model {

   function Heat()
   {
      parent::Model();
      $this->load->database('heat');
   }


   // --------------------------------------------------------------------

   /**
    * Returns facility list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_facility_list()
   {
      $sql = 'SELECT Facility '.
             'FROM Facilities '.
             'ORDER BY Facility';

      $query = $this->db->query($sql);
      $facilities = $query->result_array();
         
      $results = array(''=>'');
      for ($i=0; $i<count($facilities); $i++)
      {
         $results[$facilities[$i]['Facility']] = $facilities[$i]['Facility'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns employee status list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_employee_status_list()
   {
      $sql = 'SELECT Status '.
             'FROM EmployeeStatus '.
             'ORDER BY Status';

      $query = $this->db->query($sql);
      $status = $query->result_array();
         
      $results = array(''=>'');
      for ($i=0; $i<count($status); $i++)
      {
         $results[$status[$i]['Status']] = $status[$i]['Status'];
      }
      
      return $results;
   }

}

?>