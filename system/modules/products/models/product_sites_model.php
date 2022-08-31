<?php

class Product_sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Product_sites_model()
   {
      parent::Model();

      // this table is used only by CoolBrew, so we don't have to 
      //  mess with hcgPublic tables.
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Inserts a link record
    *
    * @access   public
    * @return   null
    */
   function insert_product_site($product_id, $site_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $values['ProductID'] = $product_id;
      $values['SiteID'] = $site_id;

      $this->write_db->insert('pr_product_site', $values);
      
      $this->CI->auditor->audit_insert('pr_product_site', '', $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a link record
    *
    */
   function delete_product_site($product_id, $site_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // delete the link record
      $values['ProductID'] = $product_id;
      $values['SiteID'] = $site_id;
      $this->write_db->delete('pr_product_site', $values);
      
      $this->CI->auditor->audit_delete('pr_product_site', $this->write_db->ar_where, $values);

      // delete this product from any of this site's categories
      $sql = 'SELECT pc.CategoryID, pc.ProductID '.
             'FROM pr_category AS c, pr_product_category AS pc '.
             'WHERE c.CategoryID = pc.CategoryID '.
             'AND pc.ProductID = '.$product_id.' '.
             'AND c.SiteID = "'.$site_id.'"';
      $query = $this->write_db->query($sql);
      $cats = $query->result_array();
      
      foreach ($cats AS $cat)
      {
         $this->write_db->delete('pr_product_category', $cat);
      
         $this->CI->auditor->audit_delete('pr_product_category', $this->write_db->ar_where, $cat);
      }

      return TRUE;
   }

}

/* End of file product_sites_model.php */
/* Location: ./system/modules/products/models/product_sites_model.php */