<?php

class Nleas_model extends Model {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables

   function Nleas_model()
   {
      parent::Model();
      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('write', TRUE);
      $this->hcg_db = $this->load->database('hcg_write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the nlea data given a product ID. If the NLEA record
    * doesn't exist, it is created.
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_nlea_data($product_id)
   {
      $sql = "SELECT * FROM pr_nlea " .
             "WHERE ProductID = ".$product_id;
      $query = $this->cb_db->query($sql);
      $nlea = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         // create a new record for this product
         $sql = 'SELECT ProductID, SiteID, ProductName '.
                'FROM pr_product '.
                'WHERE ProductID = '.$product_id;
         $query = $this->cb_db->query($sql);
         $values = $query->row_array();
         $values['TYPE'] = '0';

         $this->cb_db->insert('pr_nlea', $values);
         $this->hcg_db->insert('pr_nlea', $values);

         $sql = "SELECT * FROM pr_nlea " .
                "WHERE ProductID = ".$product_id;
         $query = $this->cb_db->query($sql);
         $nlea = $query->row_array();
      }
      return $nlea;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products for use in online forms
    *
    */
   function get_product_list($site_id)
   {
     $sql = 'SELECT p.ProductName, p.ProductID, '.
               'p.PackageSize, p.ProductGroup '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID LIKE "'.$site_id.'" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" '.
             'ORDER BY p.ProductName';
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
      
      $list = array();
      foreach ($products AS $product)
      {
         if ($product['ProductGroup'] == 'master')
         {
            $list[$product['ProductID']] = $product['ProductName'] . ' - master';
         }
         elseif ($product['ProductGroup'] != 'none')
         {
            $list[$product['ProductID']] = $product['ProductName'] . ' - ' . $product['PackageSize'];
         }
         else
         {
            $list[$product['ProductID']] = $product['ProductName'];
         }

      }

      return $list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the HTML Nutrition Facts for the given Product ID.
    *
    */
   function nutrition_facts($prod_id, $display_hd = false)
   {
      $this->load->helper('nlea');
     
      $nutfacts = $this->get_nlea_data($prod_id);
            
      // calculate the total number of table rows
      // for now, we just set it at 100 and it works
      $nutfacts['total_rows'] = 100;
      
      // see if the info is overridden
      if ($nutfacts['TYPE'] == 8)
      {
         return $nutfacts['OverrideHTML'];
      }
      
      // build copy for STMT1 if applicable
      if (strtoupper($nutfacts['STMT1']) == "YES")
      {
         $nutfacts['STMT1Q'] = build_stmt1($nutfacts['STMT1Q']);
      }
      
      $nutfacts['display_hd'] = $display_hd;
   
      $tpl = "nutrition_facts_" . $nutfacts['TYPE'];
      
      $data['nutfacts'] = $nutfacts;
      
      return $this->load->view($tpl, $data, TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an NLEA record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_nlea($product_id, $old_values, $values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $tmp = $this->cb_db->where('ProductID', $product_id);
      $this->cb_db->update('pr_nlea', $values);
      $this->hcg_db->where('ProductID', $product_id);
      $this->hcg_db->update('pr_nlea', $values);
      
      $this->CI->auditor->audit_update('pr_nlea', $tmp->ar_where, $old_values, $values);
      
      return TRUE;
   }

}

/* End of file nleas_model.php */
/* Location: ./system/modules/products/models/nleas_model.php */