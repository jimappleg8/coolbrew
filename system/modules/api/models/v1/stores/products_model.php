<?php

class Products_model extends Model {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables
   
   var $category_fields = array(
      'CategoryName',
      'CategoryCode',
      'CategoryDescription',
      'CategoryText',
      'SiteID',
      'CategoryID',
      'Status',
      'CategoryParentID',
      'CategoryOrder',
      'Language',
      'MetaTitle',
      'MetaDescription',
      'MetaKeywords',
      'MetaMisc AS MetaRobots',
   );

   // --------------------------------------------------------------------

   function Products_model()
   {
      parent::Model();
      
      $this->load->helper('v1/resources');
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      // this module is read only, but for simplicity when copying
      // code from any of the other models, I have left the double
      // database loads.
      $this->cb_db = $this->load->database($level.'-read', TRUE);
      $this->hcg_db = $this->load->database($level.'-hcg_read', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of the requested menu subtree
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_category_tree($site_id, $cat_code = 'root')
   {      
      // retrieve the left and right value of the "root" node
      $sql = 'SELECT Lft, Rgt FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "'.$cat_code.'"'; 
      $query = $this->cb_db->query($sql);
      $row = $query->row_array();

      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      if ( ! empty($row))
      {
         $field_list = implode($this->category_fields, ', ');

         $sql = 'SELECT '.$field_list.', Lft, Rgt '.
                'FROM pr_category '.
                'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
                'AND SiteID = "'.$site_id.'" '.
                'ORDER BY Lft ASC';
         $query = $this->cb_db->query($sql);
         $result = $query->result_array();
      }
      else
      {
         $result = array();
      }

      // display each row
      for($i=0; $i<count($result); $i++)
      {
         // only check stack if there is one
         if (count($right) > 0)
         {
            // check if we should remove a node from the stack
            while ($right[count($right) - 1] < $result[$i]['Rgt'])
            {
               array_pop($right);
            }
         }
         // add level information to the data
         $result[$i]['Level'] = count($right);

         // add this node to the stack
         $right[] = $result[$i]['Rgt'];

         unset($result[$i]['Rgt']);
         unset($result[$i]['Lft']);
      }
      
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products for use in online forms
    *
    */
   function get_product_list($site_id)
   {
     $sql = 'SELECT p.ProductName, p.LocatorCode, p.UPC, '.
               'p.PackageSize, p.ProductGroup '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID LIKE "'.$site_id.'" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" '.
             'AND p.LocatorCode NOT LIKE "none" '.
             'AND p.ProductGroup NOT LIKE "master" '.
             'ORDER BY p.ProductName';
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
      
      $results = array();
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $results[$i]['ProductNum'] = '0'.$products[$i]['UPC'];
         if ($products[$i]['ProductGroup'] != 'none')
         {
            $results[$i]['ProductName'] = $products[$i]['ProductName'].' '.$products[$i]['PackageSize'];
         }
         else
         {
            $results[$i]['ProductName'] = $products[$i]['ProductName'];
         }
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products by category for use in online forms
    *
    */
   function get_product_category_list($site_id)
   {
      $cat_list = $this->get_category_tree($site_id);
      $flat_cats = array();
   
      // flatten the categories
      $cat_hds = array();
      for ($i=0; $i<count($cat_list); $i++)
      {
         if ($cat_list[$i]['Level'] == 0)
         {
            continue;
         }
         $id = $cat_list[$i]['CategoryID'];
         $flat_cats[$id]['CategoryID'] = $cat_list[$i]['CategoryID'];
         if ($cat_list[$i]['Level'] == 1)
         {
            $cat_hds = array();
            $cat_hds[0] = $cat_list[$i]['CategoryName'];
            $flat_cats[$id]['CategoryName'] = $cat_hds[0];
         }
         elseif ($cat_list[$i]['Level'] == 2)
         {
            $cat_hds[1] = $cat_list[$i]['CategoryName'];
            $flat_cats[$id]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[1];
         }
         elseif ($cat_list[$i]['Level'] == 3)
         {
            $cat_hds[2] = $cat_list[$i]['CategoryName'];
            $flat_cats[$id]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[2];
         }
         $flat_cats[$id]['Products'] = array();
      }
   
      $products = $this->get_products_categories($site_id);
   
      foreach($products AS $product)
      {
         $flat_cats[$product['CategoryID']]['Products'][] = $product;
      }
   
      // convert to a numeric index
      // while getting rid of any categories that don't have products
      $cats = array();
      foreach($flat_cats AS $cat)
      {
         if ( ! empty($cat['Products']))
         {
            $cats[] = $cat;
         }
      }
      
      return $cats;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a multi-dimensional array of products by category
    *
    */
   function get_products_categories($site_id)
   {
      $sql = 'SELECT p.ProductName, p.LocatorCode, p.UPC, '.
               'p.PackageSize, p.ProductGroup, c.CategoryID, c.CategoryName '.
             'FROM pr_product AS p, '.
                  'pr_category AS c, '.
                  'pr_product_category AS pc '.
             'WHERE c.SiteID = "'.$site_id.'" '.
             'AND pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND c.Status LIKE "active" '.
             'AND p.Status != "discontinued" '.
             'AND p.Status != "inactive" '.
             'AND p.LocatorCode != "none" '.
             'AND p.ProductGroup != "master" '.
             "ORDER BY p.ProductName";
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
      
      $results = array();
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $results[$i]['ProductNum'] = '0'.$products[$i]['UPC'];
         if ($products[$i]['ProductGroup'] != 'none')
         {
            $results[$i]['ProductName'] = $products[$i]['ProductName'].' '.$products[$i]['PackageSize'];
         }
         else
         {
            $results[$i]['ProductName'] = $products[$i]['ProductName'];
         }
         $results[$i]['CategoryName'] = $products[$i]['CategoryName'];
         $results[$i]['CategoryID'] = $products[$i]['CategoryID'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products for use in online forms
    *
    */
   function get_product_name_by_upc($upc)
   {
      // strip out the extra initial 0 (added to match the Nielsen list)
      $upc = substr($upc, 1);
      
      $sql = 'SELECT ProductName, PackageSize, ProductGroup '.
             'FROM pr_product '.
             'WHERE UPC = "'.$upc.'" '.
             'AND (Status = "active" '.
             'OR Status = "partial") '.
             'AND ProductGroup != "master"';
      $query = $this->db->query($sql);
      $product = $query->row_array();
      
      if ($product['ProductGroup'] != 'none')
      {
         $product['ProductName'] = $product['ProductName'].' '.$product['PackageSize'];
      }

      return $product['ProductName'];
   }


}

/* End of file products_model.php */
/* Location: ./system/modules/api/models/v1/products_model.php */