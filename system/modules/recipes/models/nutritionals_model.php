<?php

class Nutritionals_model extends Model {

   function Nutritionals_model()
   {
      parent::Model();

      $this->load->database('read');
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
   function get_nutrition_data($recipe_id)
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


}

?>
