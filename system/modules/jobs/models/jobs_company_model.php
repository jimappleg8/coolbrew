<?php

class Jobs_company_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Jobs_company_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns company list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_company_list()
   {
      $sql = 'SELECT ID, CompanyName '.
             'FROM jobs_company '.
             'WHERE Status = 1 '.
             'ORDER BY CompanyName';

      $query = $this->read_db->query($sql);
      $companies = $query->result_array();
         
      $results = array(''=>'-- Choose a company --');
      for ($i=0; $i<count($companies); $i++)
      {
         $results[$companies[$i]['ID']] = $companies[$i]['CompanyName'];
      }

      return $results;
   }
	
}

?>