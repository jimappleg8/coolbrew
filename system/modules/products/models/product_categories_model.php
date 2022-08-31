<?php

class Product_categories_model extends Model {

   var $cb_read_db;  // database object for coolbrew tables
   var $cb_write_db;  // database object for coolbrew tables
   var $hcg_write_db;  // database object for hcg_public tables

   function Product_categories_model()
   {
      parent::Model();
      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_read_db = $this->load->database('read', TRUE);
      $this->cb_write_db = $this->load->database('write', TRUE);
      $this->hcg_write_db = $this->load->database('hcg_write', TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new Product_category record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function insert_product_category($values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // first, insert the main product record
      $this->cb_write_db->insert('pr_product_category', $values);
      $this->hcg_write_db->insert('pr_product_category', $values);

      $this->CI->auditor->audit_insert('pr_product_category', '', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Deletes all links to the given category
    *
    * @access   public
    * @param    int       The category ID
    * @return   boolean
    */
   function delete_category_links($cat_id)
   {
      // delete all references to this category in pr_products_category
      $this->cb_write_db->where('CategoryID', $cat_id);
      $this->cb_write_db->delete('pr_product_category');
      $this->hcg_write_db->where('CategoryID', $cat_id);
      $this->hcg_write_db->delete('pr_product_category');

      return TRUE;
   }

}

/* End of file product_categories_model.php */
/* Location: ./system/modules/products/models/product_categories_model.php */