<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class FeedModel extends Model {

   function FeedModel()
   {
      parent::Model();
      $this->load->database('read');
   }

   // ----------------------------------------------------------------------
   
   /**
    *
    *
    */
   function get_feed_update_urls()
   {
      $this->db->select('ID, FeedURL');
      $rs = $this->db->get('rss_feed');
      $feeds = array();
      if ($rs->num_rows() > 0)
      {
         foreach ($rs->result_array() as $row)
         {
            $feeds[$row['ID']] = $row['FeedURL'];
         }
      }
      return $feeds;
   }
   
}

?>