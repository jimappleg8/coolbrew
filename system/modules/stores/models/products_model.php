<?php

class Products_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Products_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the product name for the supplied product code
    *
    * @access   public
    * @param    string    The product code to search for
    * @return   array
    */
   function get_product_by_locator_code($code)
   {
      $sql = 'SELECT ProductName FROM pr_product '.
             'WHERE LocatorCode = "'.$code.'" '.
             'AND (ProductGroup LIKE "master" '.
             'OR ProductGroup LIKE "none")';
      $query = $this->read_db->query($sql);
      $product = $query->row_array();
      
      return $product['ProductName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns the product name for the supplied UPC
    *
    * @access   public
    * @param    string    The UPC to search for
    * @return   array
    */
   function get_product_by_upc($upc)
   {
      // strip out the extra initial 0 (added to match the Nielsen list)
      $upc = substr($upc, 1);
      
      $sql = 'SELECT ProductName, PackageSize, ProductGroup '.
             'FROM pr_product '.
             'WHERE UPC = "'.$upc.'" '.
             'AND Status NOT LIKE "discontinued" '.
             'AND Status NOT LIKE "inactive" '.
             'AND ProductGroup != "master"';
      $query = $this->read_db->query($sql);
      $product = $query->row_array();
      
      if ($product['ProductGroup'] != 'none')
      {
         $product['ProductName'] = $product['ProductName'].' '.$product['PackageSize'];
      }

      return $product['ProductName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns the product record for the supplied UPC
    *
    * @access   public
    * @param    string    The UPC to search for
    * @return   array
    */
   function get_product_data_by_upc($upc)
   {
      $sql = 'SELECT * '.
             'FROM pr_product '.
             'WHERE UPC = "'.$upc.'" '.
             'AND Status NOT LIKE "discontinued" '.
             'AND Status NOT LIKE "inactive" '.
             'AND ProductGroup != "master"';
      $query = $this->read_db->query($sql);
      $product = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         $query->free_result();  // free up the memory used by this query
         return array();
      }
      
      if ($product['ProductGroup'] != 'none')
      {
         $product['ProductName'] = $product['ProductName'].' '.$product['PackageSize'];
      }

      $query->free_result();  // free up the memory used by this query
      return $product;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products for use in online forms
    *
    * @access   public
    * @param    string     The site ID
    * @return   array
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
      $query = $this->read_db->query($sql);
      $products = $query->result_array();
      
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $products[$i]['UPC'] = '0'.$products[$i]['UPC'];
         if ($products[$i]['ProductGroup'] != 'none')
         {
            $products[$i]['ProductName'] = $products[$i]['ProductName'].' '.$products[$i]['PackageSize'];
         }
      }
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products for use in online forms. This version
    *   respects the product groups
    *
    * @access   public
    * @param    string     The site ID
    * @return   array
    */
   function get_product_list_with_groups($site_id)
   {
      $sql = 'SELECT p.ProductName, p.LocatorCode '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID LIKE "'.$site_id.'" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" '.
             'AND p.LocatorCode NOT LIKE "none" '.
             'AND (p.ProductGroup LIKE "master" '.
             'OR p.ProductGroup LIKE "none") '.
             'ORDER BY p.ProductName';
      $query = $this->read_db->query($sql);
      $products = $query->result_array();
      
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products by category for use in online forms
    *
    * @access   public
    * @param    string     The site ID
    * @param    boolean    Whether to group by product group
    * @return   array
    */
   function get_product_category_list($site_id, $with_groups = FALSE)
   {
      $cat_list = $this->get_category_tree($site_id);
      $flat_cats = array();
         
      // flatten the categories
      $cat_hds = array();
      for($i=0; $i<count($cat_list); $i++)
      {
         $id = $cat_list[$i]['CategoryID'];
         if ($cat_list[$i]['level'] == 1)
         {
            $cat_hds = array();
            $cat_hds[0] = $cat_list[$i]['CategoryName'];
            $flat_cats[$id]['CategoryName'] = $cat_hds[0];
         }
         elseif ($cat_list[$i]['level'] == 2)
         {
            $cat_hds[1] = $cat_list[$i]['CategoryName'];
            $flat_cats[$id]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[1];
         }
         elseif ($cat_list[$i]['level'] == 3)
         {
            $cat_hds[2] = $cat_list[$i]['CategoryName'];
            $flat_cats[$id]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[1].' - '.$cat_hds[2];
         }
         $flat_cats[$id]['CategoryID'] = $cat_list[$i]['CategoryID'];
         $flat_cats[$id]['Products'] = array();
      }
   
      if ($with_groups == TRUE)
      {
         $products = $this->get_products_categories_with_groups($site_id);
      }
      else
      {
         $products = $this->get_products_categories($site_id);
      }
   
      foreach($products AS $product)
      {
         $flat_cats[$product['CategoryID']]['Products'][] = $product;
      }
   
      // convert to a numeric index
      $cats = array();
      foreach($flat_cats AS $cat)
      {
         $cats[] = $cat;
      }
      
      return $cats;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a multi-dimensional array of products by category
    *
    * @access   public
    * @param    string      The site ID
    * @return   array
    */
   function get_products_categories($site_id)
   {
      $sql = 'SELECT p.ProductName, p.LocatorCode, p.UPC, p.ProductID, '.
               'p.PackageSize, p.ProductGroup, c.CategoryID, c.CategoryName '.
             'FROM pr_product AS p, '.
                  'pr_category AS c, '.
                  'pr_product_category AS pc '.
             'WHERE c.SiteID = "'.$site_id.'" '.
             'AND pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND c.Status LIKE "active" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" '.
             'AND p.LocatorCode NOT LIKE "none" '.
             'AND p.ProductGroup NOT LIKE "master" '.
             "ORDER BY p.ProductName";
      $query = $this->read_db->query($sql);
      $products = $query->result_array();
      
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $products[$i]['UPC'] = '0'.$products[$i]['UPC'];
         if ($products[$i]['ProductGroup'] != 'none')
         {
            $products[$i]['ProductName'] = $products[$i]['ProductName'].' '.$products[$i]['PackageSize'];
         }
      }
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a multi-dimensional array of products by category. This
    *   version respects the product groups
    *
    * @access   public
    * @param    string      The site ID
    * @return   array
    */
   function get_products_categories_with_groups($site_id)
   {
      $sql = 'SELECT p.ProductName, p.LocatorCode, c.CategoryID, '.
               'c.CategoryName '.
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
             'AND (p.ProductGroup = "master" '.
             'OR p.ProductGroup = "none") '.
             "ORDER BY p.ProductName";
      $query = $this->read_db->query($sql);
      $products = $query->result_array();

      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the root node of a site's product categories
    *
    * @access   public
    * @param    string      The site ID
    * @return   array
    */
   function get_category_root($site_id)
   {
      $sql = 'SELECT CategoryID FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "root"'; 
      $query = $this->read_db->query($sql);
      $row = $query->row_array();
      
      if ($query->num_rows() > 0)
      {
         return $row['CategoryID'];
      }
      return FALSE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of the requested menu subtree
    *
    * @access   public
    * @param    int      The product ID
    * @param    string   The node from which to build the tree
    * @return   array
    */
   function get_category_tree($site_id, $cat_code = 'root')
   {      
      // retrieve the left and right value of the "root" node
      $sql = 'SELECT Lft, Rgt FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "'.$cat_code.'"'; 
      $query = $this->read_db->query($sql);
      $row = $query->row_array();
      
      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      
      $sql = 'SELECT * FROM pr_category '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'ORDER BY Lft ASC';
      $query = $this->read_db->query($sql);
      $result = $query->result_array();

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
         $result[$i]['level'] = count($right);
         $result[$i]['next_child'] = (($result[$i]['Rgt'] - $result[$i]['Lft'] - 1) / 2) + 1;

         // add this node to the stack
         $right[] = $result[$i]['Rgt'];
      }
      
      return $result;
   }

}

/* End of file products_model.php */
/* Location: ./system/modules/stores/models/products_model.php */