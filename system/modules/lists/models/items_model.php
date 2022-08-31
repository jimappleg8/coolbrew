<?php

class Items_model extends Model {

   function Items_model()
   {
      parent::Model();
      $this->load->database('read');
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of lists for the specified List ID
    *
    * @access   public
    * @return   array
    */
   function get_list_items($list_id)
   {
      $sql = 'SELECT * FROM lists_item ' .
             'WHERE ListID = '.$list_id.' '.
             'ORDER BY SortKey';
      
      $query = $this->db->query($sql);
      $items = $query->result_array();

      return $items;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of lists for the specified List Code
    *
    * @access   public
    * @return   array
    */
   function get_list_items_by_code($list_code, $site_id = '')
   {
      $site_id = ($site_id == '') ? SITE_ID : $site_id;
      
      $sql = 'SELECT lists_item.* '.
             'FROM lists, lists_item ' .
             'WHERE lists_item.ListID = lists.ID '.
             'AND lists.ListCode = "'.$list_code.'" '.
             'AND lists.SiteID = "'.$site_id.'" '.
             'ORDER BY SortKey';
      
      $query = $this->db->query($sql);
      $items = $query->result_array();

      return $items;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified item ID
    *
    * @access   public
    * @return   array
    */
   function get_item_data($item_id)
   {
      $sql = 'SELECT * FROM lists_item ' .
             'WHERE ID = '.$item_id;
      
      $query = $this->db->query($sql);
      $item = $query->row_array();

      return $item;
   }

}

?>