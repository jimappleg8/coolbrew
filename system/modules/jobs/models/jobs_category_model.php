<?php

class Jobs_category_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Jobs_category_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
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

      $query = $this->read_db->query($sql);
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