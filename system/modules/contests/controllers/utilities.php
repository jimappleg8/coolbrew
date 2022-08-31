<?php

class Utilities extends Controller {

   var $read_db;
   var $write_db;
   var $prod_db;
   
   function Utilities()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'contests'));
      $this->load->helper(array('url', 'menu', 'text'));

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
      $this->prod_db = $this->load->database('production', TRUE);
      
      $this->position = 0;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Reads in all the entries for a contest and saves them to a new 
    *  table with no duplication. The basic version uses the Email field
    *  to determine if the entry is a duplicate. Only the first entry for
    *  for a given email is saved in the new table.
    *
    * The function is designed to read from the production database, but 
    *  write the new table to the dev database so it won't bog down the 
    *  production server with all the queries it makes. The function 
    *  assumes that you have created the needed table to write to.
    *
    */
   function dedupe_contest()
   {
      $tableA = 'jn_sunbrellas';
      $tableB = 'jn_sunbrellas_deduped';
      $field = 'Email';
      
      set_time_limit(0);
      ob_start();
      
      $start = 0;
      $total_count = 0;
      do
      {
         $sql = 'SELECT * '.
                'FROM '.$tableA.' '.
                'LIMIT '.$start.', 500';       
         $query1 = $this->prod_db->query($sql);
         $entries = $query1->result_array();
      
         $saved_count = 0;
         foreach ($entries AS $entry)
         {
            $sql = 'SELECT '.$field.' '.
                   'FROM '.$tableB.' '.
                   'WHERE '.$field.' LIKE "'.$entry[$field].'"';
            $query = $this->write_db->query($sql);

            if ($query->num_rows() == 0)
            {
               unset($entry['ID']);
               $this->write_db->insert($tableB, $entry);
               $saved_count++;
            }
         }

         $start = $start + 500;
         $total_count = $total_count + $saved_count;
         echo $total_count.' unique entries saved from '.$start.' total records.<br />';
         ob_flush();
         flush();
      }
      while ($query1->num_rows() > 0);

      echo "Script completed.<br />";
      exit;
   }
   
}
?>
