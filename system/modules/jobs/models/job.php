<?php

class Job extends Model {

   function Job()
   {
      parent::Model();
      $this->load->database('write');
   }

   // --------------------------------------------------------------------

   /**
    * Returns job data record
    *
    * @access   public
    * @param    mixed     can supply either a job ID or JobNum
    * @return   array
    */
   function get_job_data($code)
   {
      if ( ! preg_match('/^RX/', $code))
      {
         $sql = 'SELECT * FROM jobs '.
                'WHERE jobs.ID = '.$code;
      }
      else
      {
         $sql = 'SELECT * FROM jobs '.
                'WHERE jobs.JobNum = \''.$code.'\'';
      }

      $query = $this->db->query($sql);
      $job = $query->row_array();
      
      return $job;
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
      $sql = 'SELECT ID, LocationName '.
             'FROM jobs_location '.
             'WHERE Country = \'United States\' '.
             'AND Status = 1 '.
             'ORDER BY LocationName';

      $query = $this->db->query($sql);
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
    * Returns category list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_category_list()
   {
      $sql = 'SELECT ID, CategoryName '.
             'FROM jobs_category '.
             'WHERE Status = 1 '.
             'ORDER BY CategoryName';

      $query = $this->db->query($sql);
      $categories = $query->result_array();
         
      $results = array(''=>'-- Choose a category --');
      for ($i=0; $i<count($categories); $i++)
      {
         $results[$categories[$i]['ID']] = $categories[$i]['CategoryName'];
      }

      return $results;
   }

}

?>