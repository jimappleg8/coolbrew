<?php

class Blocks_model extends Model {

   var $CI;

   var $read_db;
   var $write_db;

   // --------------------------------------------------------------------

   function Blocks_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of blocks for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_blocks($site_id)
   {
      $sql = 'SELECT * FROM blocks ' .
             'WHERE SiteID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      $blocks = $query->result_array();

      return $blocks;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified block ID
    *
    * @access   public
    * @return   array
    */
   function get_block_data($block_id)
   {
      $sql = 'SELECT * FROM blocks ' .
             'WHERE ID = '.$block_id;
      
      $query = $this->read_db->query($sql);
      $block = $query->row_array();

      return $block;
   }
   
   

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified block name
    *
    * @access   public
    * @return   array
    */
   function get_block_by_code($block_name, $site_id, $language)
   {
      $sql = 'SELECT * '.
             'FROM blocks '.
             'WHERE Name = "'.$block_name.'" '.
             'AND SiteID = "'.$site_id.'" '.
             'AND Language = "'.$language.'"';
      
      $query = $this->read_db->query($sql);
      $block = $query->row_array();

      return $block;
   }
   
   // --------------------------------------------------------------------

   /**
    * Inserts a new block
    *
    * @access   public
    * @return   array
    */
   function insert_block($values)
   {
      $this->CI =& get_instance();

      $this->CI->load->library('auditor');

      $this->write_db->insert('blocks', $values);

      $block_id = $this->write_db->insert_id();
      
      $this->CI->auditor->audit_insert('blocks', '', $values);
      
      return $block_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an existing block record
    *
    * @access   public
    * @param    integer   The block ID
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_block($block_id, $values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');
      
      $old_values = $this->get_block_data($block_id);

      // first, update the product record
      $this->write_db->where('ID', $block_id);
      $this->write_db->update('blocks', $values);
      
      $this->auditor->audit_update('blocks', $this->write_db->ar_where, $old_values, $values);

      return TRUE;
   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes the specified block
    *
    * @access   public
    * @return   array
    */
   function delete_block($block_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // first, delete any references in blocks_page
      $this->write_db->where('BlockID', $block_id);
      $this->write_db->delete('blocks_page');
      
      // should add auditing of bocks_page deletions
      
      // get a copy of the existing record
      $block = $this->get_block_data($block_id);
      
      // then, delete the block record itself
      $this->write_db->where('ID', $block_id);
      $this->write_db->delete('blocks');

      $this->auditor->audit_delete('blocks', $this->write_db->ar_where, $block);

      return $block;
   }

}

?>