<?php

class Contests_model extends Model {

   function Contests_model()
   {
      parent::Model();
      $this->load->database('write');
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified contest ID
    *
    * @access   public
    * @return   array
    */
   function get_contests($site_id)
   {
      $sql = 'SELECT * FROM contests ' .
             'WHERE SiteID = \''.$site_id.'\'';
      
      $query = $this->db->query($sql);
      $contests = $query->result_array();

      return $contests;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified contest ID
    *
    * @access   public
    * @return   array
    */
   function get_contest_by_id($contest_id)
   {
      $sql = 'SELECT * FROM contests ' .
             'WHERE ID = \''.$contest_id.'\'';
      
      $query = $this->db->query($sql);
      $contest = $query->row_array();

      return $contest;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified contest ID
    *
    * @access   public
    * @return   array
    */
   function get_contest_by_name($site_id, $name, $language)
   {
      $sql = 'SELECT * FROM contests ' .
             'WHERE SiteID = \''.$site_id.'\' '.
             'AND ContestName = \''.$name.'\' '.
             'AND Language = \''.$language.'\'';
      
      $query = $this->db->query($sql);
      $contest = $query->row_array();

      return $contest;
   }

   // --------------------------------------------------------------------

   /**
    * Returns friend entry action list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_friend_entry_action_list()
   {
      $results = array(
         'none'   => 'do nothing',
         'extras' => 'give teller an extra entry'
      );      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns friend entry action list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_entry_frequencies_list()
   {
      $results = array(
         'unlimited' => 'unlimited',
         'once'      => 'once per email address',
      );      
      return $results;
   }

}

?>