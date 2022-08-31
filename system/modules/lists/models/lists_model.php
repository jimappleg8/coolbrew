<?php

class Lists_model extends Model {

   function Lists_model()
   {
      parent::Model();
      $this->load->database('read');
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of lists for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_lists($site_id)
   {
      $sql = 'SELECT * FROM lists ' .
             'WHERE SiteID = \''.$site_id.'\'';
      
      $query = $this->db->query($sql);
      $lists = $query->result_array();

      return $lists;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified list ID
    *
    * @access   public
    * @return   array
    */
   function get_list_data($list_id)
   {
      $sql = 'SELECT * FROM lists ' .
             'WHERE ID = '.$list_id;
      
      $query = $this->db->query($sql);
      $list = $query->row_array();

      return $list;
   }


}

?>