<?php

class Stores_product_model extends Model {

   var $db_read;   // database object for reading
   var $db_write;  // database object for writing

   // --------------------------------------------------------------------

   function Stores_product_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of stores that carry the indicated product
    *
    * @access   public
    * @param    int      The Product ID to search for
    * @return   array
    */
   function get_stores($product_id)
   {
      $sql = 'SELECT s.* '.
             'FROM stores AS s, stores_product AS sp '.
             'WHERE s.StoreID = sp.StoreID '.
             'AND sp.ProductID = '.$productID.' '.
             'OR sp.Carries = 1';
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
      return $stores;
   }

   // --------------------------------------------------------------------

   function insert_store_product($values)
   {
//      $this->CI =& get_instance();
//      $this->CI->load->library('auditor');
   
      $this->write_db->insert('stores_product', $values);
      
//      $this->CI->auditor->audit_insert('stores', '', $values);
      
      return TRUE;
   }

}

/* End of file stores_product_model.php */
/* Location: ./system/modules/stores/models/stores_product_model.php */