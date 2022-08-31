<?php

class Tell_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Tell_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for all tells for this site
    *
    * @access   public
    * @return   array
    */
   function get_tells($site_id)
   {
      $sql = 'SELECT * FROM plugs_tell ' .
             'WHERE SiteID = \''.$site_id.'\'';
      
      $query = $this->read_db->query($sql);
      $tells = $query->result_array();

      return $tells;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified tell ID
    *
    * @access   public
    * @return   array
    */
   function get_tell_by_id($tell_id)
   {
      $sql = 'SELECT * FROM plugs_tell ' .
             'WHERE ID = \''.$tell_id.'\'';
      
      $query = $this->read_db->query($sql);
      $tell = $query->row_array();

      return $tell;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified tell name
    *
    * @access   public
    * @return   array
    */
   function get_tell_by_name($site_id, $name, $language)
   {
      $sql = 'SELECT * FROM plugs_tell ' .
             'WHERE SiteID = \''.$site_id.'\' '.
             'AND TellName = \''.$name.'\' '.
             'AND Language = \''.$language.'\'';
      
      $query = $this->read_db->query($sql);
      $tell = $query->row_array();

      return $tell;
   }

   // --------------------------------------------------------------------

   /**
    * Returns meta data for the specified tell name
    *
    * @access   public
    * @return   array
    */
   function get_meta_data_by_name($site_id, $name, $language)
   {
      $sql = 'SELECT MetaTitle, MetaDescription, MetaKeywords, '.
               'MetaAbstract, MetaRobots '.
             'FROM plugs_tell '.
             'WHERE SiteID = \''.$site_id.'\' '.
             'AND TellName = \''.$name.'\' '.
             'AND Language = \''.$language.'\'';
      
      $query = $this->read_db->query($sql);
      $meta = $query->row_array();

      return $meta;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new tell record
    *
    * @access   public
    * @return   integer   the new tell ID
    */
   function insert_tell($values)
   {
      $this->write_db->insert('plugs_tell', $values);
      
      $tell_id = $this->write_db->insert_id();

      return $tell_id;
   }
   
   // --------------------------------------------------------------------

   /**
    * Updates an existing tell record
    *
    * @access   public
    * @return   boolean
    */
   function update_tell($tell_id, $values)
   {
      $this->write_db->where('ID', $tell_id);
      $this->write_db->update('plugs_tell', $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a tell record
    *
    * @access   public
    * @return   boolean
    */
   function delete_tell($tell_id)
   {
      $this->write_db->where('ID', $tell_id);
      $this->write_db->delete('plugs_tell');

      return TRUE;
   }

}

?>