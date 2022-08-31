<?php

class Jobs_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Jobs_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
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

      $query = $this->read_db->query($sql);
      $job = $query->row_array();
      
      return $job;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the next job number
    *
    * @access   public
    * @return   string
    */
   function generate_job_number()
   {
      $prefix = 'RX'.date('y');

      $sql = 'SELECT JobNum FROM jobs '.
             'WHERE JobNum LIKE "'.$prefix.'%" '.
             'ORDER BY JobNum DESC';

      $query = $this->db->query($sql);
      $row = $query->row_array();
      
      // deal with circumstance when this is the first of the year
      $row['JobNum'] = (isset($row['JobNum'])) ? $row['JobNum'] : 0;

      $nextnum = (int) substr($row['JobNum'], 4) + 1;
      return $prefix.str_pad($nextnum, 4, '0', STR_PAD_LEFT);
   }

   // --------------------------------------------------------------------

   /**
    * Creates a new job record
    *
    * @access   public
    * @param    array   The data to insert
    * @return   int     The Job ID for the inserted record
    */
   function insert_job($values)
   {
      $this->write_db->insert('jobs', $values);
      $job_id = $this->write_db->insert_id();
      
      return $job_id;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an existing job record
    *
    * @access   public
    * @param    array   The record ID to update
    * @param    array   The data to update
    * @return   null
    */
   function update_job($job_id, $values)
   {
      $this->write_db->where('ID', $job_id);
      $this->write_db->update('jobs', $values);
      
      return $job_id;
   }

}

?>