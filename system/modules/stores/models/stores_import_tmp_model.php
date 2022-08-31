<?php

class Stores_import_tmp_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $store_exists_cache = array();

   // --------------------------------------------------------------------

   function Stores_import_tmp_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   function get_all_stores($import_id)
   {
      $sql = 'SELECT * '.
             'FROM stores_import_tmp '.
             'WHERE ImportID = '.$import_id;
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
      return $stores;
   }

   // --------------------------------------------------------------------

   function get_products_array($store_id)
   {
      $sql = 'SELECT Products '.
             'FROM stores_import_tmp '.
             'WHERE StoreID = '.$store_id;
      $query = $this->read_db->query($sql);
      $store = $query->row_array();
      
      $products = unserialize($store['Products']);
      
      return $products;
   }

   // --------------------------------------------------------------------

   function insert_store($values)
   {
      $this->write_db->insert('stores_import_tmp', $values);
      $store_id = $this->write_db->insert_id();
      
      return $store_id;
   }

   // --------------------------------------------------------------------

   function update_store($store_id, $values)
   {
      $this->write_db->where('StoreID', $store_id);
      $this->write_db->update('stores_import_tmp', $values);
      
//      echo '<br />'.$this->write_db->last_query();
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Checks the database to see if this store already has a record.
    *
    * This is a simple check because it is assumed that the store
    *  records will have been created originally from a data source 
    *  and any subsequent updates will come from the same source and 
    *  have same store name and address format. It uses the 
    *  SourceStoreName and SourceAddress1 fields for that reason.
    *
    * A cache of stores is also kept to reduce calls to the database
    *  in situations where a store is listed multiple times in the
    *  data file, one time for each product.
    *
    * @param   array   the info about the store
    * @return  mixed   the store ID if found, otherwise, FALSE
    *
    */
   function store_exists($store, &$mystore)
   {
      $key = $store['SourceStoreName'] . $store['SourceAddress1'] . $store['City'] . $store['State'] . $store['Zip'];
      
      if (isset($this->store_exists_cache[$key]))
      {
         return $this->store_exists_cache[$key];
      }
      
      $sql = 'SELECT * '.
             'FROM stores_import_tmp '.
             'WHERE SourceStoreName LIKE '.$this->db->escape($store['SourceStoreName']).' '.
             'AND SourceAddress1 LIKE '.$this->db->escape($store['SourceAddress1']).' '.
             'AND City LIKE '.$this->db->escape($store['City']).' '.
             'AND State LIKE '.$this->db->escape($store['State']).' '.
             'AND Zip LIKE '.$this->db->escape($store['Zip']);
      $query = $this->read_db->query($sql);
      $mystore = $query->row_array();
   
      if ($query->num_rows() == 1)
      {
         $this->store_exists_cache[$key] = $mystore['StoreID'];
         $query->free_result();  // free up the memory used by this query
         return $mystore['StoreID'];
      }
      
      $query->free_result();  // free up the memory used by this query
      return FALSE;
   }
   
}

/* End of file stores_import_tmp_model.php */
/* Location: ./system/modules/stores/models/stores_import_tmp_model.php */