<?php

class Links_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Links_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of links for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_links($site_id)
   {
      $sql = 'SELECT * FROM adm_link '.
             'WHERE SiteID = "'.$site_id.'" '.
             'ORDER BY Sort';

      $query = $this->read_db->query($sql);
      $links = $query->result_array();
      
      return $links;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of quick links for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_quick_links($site_id)
   {
      $sql = 'SELECT * FROM adm_link '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND Dashboard = 1 '.
             'ORDER BY Sort';

      $query = $this->read_db->query($sql);
      $links = $query->result_array();
      
      return $links;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of link data for the specified link ID
    *
    * @access   public
    * @return   array
    */
   function get_link_data($link_id)
   {
      $sql = 'SELECT * '.
             'FROM adm_link '.
             'WHERE ID = "'.$link_id.'"';

      $query = $this->read_db->query($sql);
      $link = $query->row_array();
      
      return $link;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new links record
    *
    * @access   public
    * @return   array
    */
   function insert_link($values)
   {
      $this->write_db->insert('adm_link', $values);
      $id = $this->write_db->insert_id();
      
      return $id;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an existing links record
    *
    * @access   public
    * @return   array
    */
   function update_link($link_id, $values)
   {
      $this->write_db->where('ID', $link_id);
      $this->write_db->update('adm_link', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a links record
    *
    * @access   public
    * @return   array
    */
   function delete_link($link_id)
   {
      $this->write_db->where('ID', $link_id);
      $this->write_db->limit(1);
      $this->write_db->delete('adm_link');

      return TRUE;
   }

}

?>