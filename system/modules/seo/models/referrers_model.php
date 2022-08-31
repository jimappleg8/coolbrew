<?php

class Referrers_model extends Model {

   function Referrers_model()
   {
      parent::Model();
      $this->load->database('read');
   }

   // -----------------------------------------------------------------------

   /**
    * Retrieves the list of links on the specified page
    * 
    */
   function get_page_links($link_id)
   {
      $sql = 'SELECT il.ID, il.URL, il.NewURL, il.Title '.
             'FROM seo_referrer AS r, seo_index_link AS il '.
             'WHERE r.ReferrerID = '.$link_id.' '.
             'AND r.LinkID = il.ID';
      $query = $this->db->query($sql);
      $results = $query->result_array();
      
      return $results;
   }

   // -----------------------------------------------------------------------

   /**
    * Retrieves the list of links that link to the specified page
    * 
    */
   function get_page_referrers($link_id)
   {
      $sql = 'SELECT il.ID, il.URL, il.NewURL, il.Title '.
             'FROM seo_referrer AS r, seo_index_link AS il '.
             'WHERE r.LinkID = '.$link_id.' '.
             'AND r.ReferrerID = il.ID';
      $query = $this->db->query($sql);
      $results = $query->result_array();
      
      return $results;
   }



}

?>