<?php

class Stores_product_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Stores_product_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a stores-product record
    *
    * @access   public
    * @param    int      The Store ID to search for
    * @param    int      The Product ID to search for
    * @return   array
    */
   function get_store_product_data($store_id, $product_id)
   {
      $sql = 'SELECT * '.
             'FROM stores_product '.
             'WHERE ProductID = '.$product_id.' '.
             'AND StoreID = '.$store_id;
      $query = $this->read_db->query($sql);
      $link = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         return FALSE;
      }
      
      return $link;
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
             'AND sp.ProductID = '.$product_id.' '.
             'OR sp.Carries = 1';
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
      return $stores;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products carried by the indicated store
    *
    * @access   public
    * @param    int      The Store ID to search for
    * @return   array
    */
   function get_products($store_id, $carried)
   {
      $sql = 'SELECT ps.SiteID, p.ProductID, p.ProductName, '.
               'p.PackageSize, p.UPC, p.SESFilename, sp.Carried '.
             'FROM pr_product AS p, stores_product AS sp, '.
               'pr_product_site AS ps '.
             'WHERE p.ProductID = sp.ProductID '.
             'AND p.ProductID = ps.ProductID '.
             'AND sp.StoreID = '.$store_id.' '.
             'AND sp.Carried = '.$carried.' '.
             'ORDER BY ps.SiteID';
      $query = $this->read_db->query($sql);
      $products = $query->result_array();
      
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns info about whether a product and store are linked
    *
    * @access   public
    * @param    int      The Store ID to search for
    * @param    int      The Product ID to search for
    * @return   string OR FALSE
    */
   function get_carried_status($store_id, $product_id)
   {
      $link = $this->get_store_product_data($store_id, $product_id);

      if ($link == FALSE)
      {
         return FALSE;
      }
      
      if ($link['Carried'] == 0)
      {
         return 'no';
      }
      else
      {
         return 'yes';
      }
   }

   // --------------------------------------------------------------------

   /**
    * Adds a new Stores_product record
    *
    * The rule that is applied to creating store-product links is simple:
    *  we assume that any new information is the most accurate and delete
    *  and other link information before we insert the new.
    *
    * @access   public
    * @param    array    The link record
    * @return   bool
    */
   function insert_store_product($values)
   {
      // start by deleting any existing link
      $this->delete_store_product($values['StoreID'], $values['ProductID']);

//      $this->CI =& get_instance();
//      $this->CI->load->library('auditor');

      $this->write_db->insert('stores_product', $values);

//      $this->CI->auditor->audit_insert('stores_product', '', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Removes a Stores_product record
    *
    * @access   public
    * @param    int      The store ID
    * @param    int      The product ID
    * @return   bool
    */
   function delete_store_product($store_id, $product_id)
   {
      $this->write_db->where('StoreID', $store_id);
      $this->write_db->where('ProductID', $product_id);
      $this->write_db->delete('stores_product');

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Used by the import script to delete links created from a specific
    *  data source.
    *
    * @access   public
    * @param    int      The source ID that created the link
    * @return   array
    */
   function delete_product_links($source_id)
   {
      $this->write_db->where('Source', $source_id);
      $this->write_db->delete('stores_product');
            
      return TRUE;
   }

}

/* End of file stores_product_model.php */
/* Location: ./system/modules/stores/models/stores_product_model.php */