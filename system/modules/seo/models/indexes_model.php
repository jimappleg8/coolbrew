<?php

class Indexes_model extends Model {

   function Indexes_model()
   {
      parent::Model();
      $this->load->database('read');
   }

   // -----------------------------------------------------------------------

   /**
    * Retrieves the index from the database
    * 
    */
   function get_site_index($site_id)
   {
      $sql = 'SELECT * FROM seo_index '.
             'WHERE SiteID = "'.$site_id.'"';
      $query = $this->db->query($sql);
      $results = $query->row_array();
      
      $sql = 'SELECT * FROM seo_index_link '.
             'WHERE IndexID = '.$results['ID'].' '.
             'ORDER BY Location DESC, Type DESC, URL ASC';
      $query = $this->db->query($sql);
      $links = $query->result_array();

      $results['links'] = $links;
      
      $results['number_parsed'] = count($links);

      return $results;
   }

   // -----------------------------------------------------------------------

   /**
    * Saves the index results to the database
    * 
    */
   function get_index_config($site_id)
   {
      $sql = 'SELECT * FROM seo_index '.
             'WHERE SiteID = "'.$site_id.'"';
      $query = $this->db->query($sql);
      $results = $query->row_array();

      unset($results['ID']);
      unset($results['IndexedDate']);
      unset($results['IndexedBy']);

      return $results;
   }

   // -----------------------------------------------------------------------

   /**
    * Checks if an index is currently saved.
    * 
    */
   function index_exists($site_id)
   {
      $sql = 'SELECT * FROM seo_index '.
             'WHERE SiteID = "'.$site_id.'"';
      $query = $this->db->query($sql);
      
      return ($query->num_rows() > 0) ? TRUE : FALSE;
   }

   // -----------------------------------------------------------------------

   /**
    * Retrieves the link data
    * 
    */
   function get_link_data($link_id)
   {
      $sql = 'SELECT * FROM seo_index_link '.
             'WHERE ID = '.$link_id;
      $query = $this->db->query($sql);
      $results = $query->row_array();
      
      return $results;
   }

   // -----------------------------------------------------------------------

   /**
    * Retrieves the link data and the index data
    * 
    */
   function get_all_link_data($link_id)
   {
      $sql = 'SELECT * '.
             'FROM seo_index_link AS il, seo_index AS i '.
             'WHERE i.ID = il.IndexID '.
             'AND il.ID = '.$link_id;
      $query = $this->db->query($sql);
      $results = $query->row_array();
      
      return $results;
   }


}

?>