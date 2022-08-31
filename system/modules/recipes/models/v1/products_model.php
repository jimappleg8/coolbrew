<?php

class Products_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Products_model()
   {
      parent::Model();
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
      $this->read_db = $this->load->database($level.'-read', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns the name of the specified product ID
    *
    * @access   public
    * @return   array
    */
   function get_product_name($product_id)
   {
      $sql = 'SELECT ProductName '.
             'FROM pr_product '.
             'WHERE ProductID = '.$product_id;
      $query = $this->read_db->query($sql);
      $product = $query->row_array();
      
      return $product['ProductName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of products used in recipes for use in forms
    * 
    * This method assumes that the SiteID field in the pr_product table
    * defines the brand. This is imperfect, but should work.
    *
    * @access   public
    * @return   array
    */
   function get_product_list($site_id, $by_brand = FALSE)
   {
      $this->CI =& get_instance();
      $this->CI->load->model('Sites');

      $result[''] = "All";
      if ($by_brand)
      {
         $sql = 'SELECT DISTINCT p.ProductID, p.ProductName, p.SiteID '.
                'FROM pr_product AS p, rcp_ingredient AS ri, rcp_recipe AS r, rcp_recipe_site AS rs '.
                'WHERE (ri.ProductOne = p.ProductID '.
                'OR ri.ProductTwo = p.ProductID) '.
                'AND rs.RecipeID = r.ID '.
                'AND rs.SiteID = "'.$site_id.'" '.
                'AND ri.RecipeID = r.ID '.
                'AND r.Language = "en_US" '. 
                'AND p.Status = "active" '.
                'ORDER BY p.SiteID ASC, p.ProductName ASC';

         $query = $this->read_db->query($sql);
         $products = $query->result_array();
      
         $brands = array();
         foreach ($products AS $product)
         {
            if ( ! isset($brands[$product['SiteID']]))
            {
               $brands[$product['SiteID']] = $this->CI->Sites->get_brand_name($product['SiteID']);
               $result[$brands[$product['SiteID']]] = array();
            }
            $result[$brands[$product['SiteID']]][$product['ProductID']] = $product['ProductName'];
         }
      }
      else
      {
         $sql = 'SELECT DISTINCT p.ProductID, p.ProductName '.
                'FROM pr_product AS p, rcp_ingredient AS ri, rcp_recipe AS r, rcp_recipe_site AS rs '.
                'WHERE (ri.ProductOne = p.ProductID '.
                'OR ri.ProductTwo = p.ProductID) '.
                'AND rs.RecipeID = r.ID '.
                'AND rs.SiteID = "'.$site_id.'" '.
                'AND ri.RecipeID = r.ID '.
                'AND r.Language = "en_US" '. 
                'AND p.Status = "active" '.
                'ORDER BY p.ProductName asc';

         $query = $this->read_db->query($sql);
         $products = $query->result_array();

         foreach ($products AS $product)
         {
            $result[$product['ProductID']] = $product['ProductName'];
         }
      }
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of all products for this site for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_all_product_list($site_id)
   {
      $sql = 'SELECT p.ProductID, p.ProductName, p.PackageSize '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial") '.
             'AND (p.ProductGroup LIKE "none" '.
             'OR p.ProductGroup LIKE "master")';
      $query = $this->read_db->query($sql);
      $products = $query->result_array();
   
      $result[''] = "None";
      foreach ($products AS $product)
      {
         $result[$product['ProductID']] = $product['ProductName'];
      }
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products by category for use in forms
    *
    */
   function get_product_category_list($site_id)
   {
      $cat_list = $this->get_category_tree($site_id);
      $flat_cats = array();
   
      // flatten the categories
      $cat_hds = array();
      for($i=0; $i<count($cat_list); $i++)
      {
         $name = $cat_list[$i]['CategoryName'];
         if ($cat_list[$i]['level'] == 1)
         {
            $cat_hds = array();
            $cat_hds[0] = strip_tags($cat_list[$i]['CategoryName']);
            $flat_cats[$name]['CategoryName'] = $cat_hds[0];
         }
         elseif ($cat_list[$i]['level'] == 2)
         {
            $cat_hds[1] = strip_tags($cat_list[$i]['CategoryName']);
            $flat_cats[$name]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[1];
         }
         elseif ($cat_list[$i]['level'] == 3)
         {
            $cat_hds[2] = strip_tags($cat_list[$i]['CategoryName']);
            $flat_cats[$name]['CategoryName'] = $cat_hds[0].' - '.$cat_hds[2];
         }
         $flat_cats[$name]['CategoryID'] = $cat_list[$i]['CategoryID'];
         $flat_cats[$name]['Products'] = array();
      }
   
      $products = $this->get_products_categories($site_id);
   
      foreach($products AS $product)
      {
         $flat_cats[$product['CategoryName']]['Products'][] = $product;
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
    */
   function get_products_categories($site_id)
   {
      $sql = 'SELECT p.ProductName, p.ProductID, c.CategoryName '.
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
    * Returns an array of the requested menu subtree
    *
    * @access   public
    * @param    string   The site ID
    * @param    string   The root category code
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
      
      if ( ! isset($row['Lft']) || ! isset($row['Rgt']))
      {
         return array();
      }

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

   // --------------------------------------------------------------------

   /**
    * Returns an array representing the category levels of this product
    *
    * @access   public
    * @param    string   The site ID
    * @param    int      The product ID
    * @return   array
    */
   function get_category_path($site_id, $prod_id)
   {
      $path = array();
      
      // get all categories assigned to this product on this site
      $sql = 'SELECT c.Lft, c.Rgt '.
             'FROM pr_product_category AS pc, pr_category AS c ' .
             'WHERE c.CategoryID = pc.CategoryID '.
             'AND c.SiteID = "'.$site_id.'" '.
             'AND pc.ProductID = '.$prod_id.' '.
             'ORDER BY c.Lft ASC';
      $query = $this->read_db->query($sql);
      $cat = $query->row_array();
      
      if ($query->num_rows() > 0)
      {
         // now get the path back to the root for this category
         $sql = 'SELECT CategoryID AS ProductCategoryID, '.
                  'CategoryCode AS ProductCategoryCode '.
                'FROM pr_category '.
                'WHERE Lft <= '.$cat['Lft'].' '.
                'AND Rgt >= '.$cat['Rgt'].' '.
                'AND SiteID = "'.$site_id.'" '.
                'AND CategoryCode != "root" '.
                'ORDER BY Lft DESC';
         $query = $this->read_db->query($sql);
         $path = $query->result_array();
      }
//      echo '<pre>'; print_r($path); echo '</pre>'; exit;

      return $path;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the corrected site ID for the specified product
    *  If a product is assigned to the current site, then it can
    *  override the Site ID stored in the recipe database.
    *
    * @access   public
    * @param    string   The site ID
    * @param    int      The product ID
    * @return   array
    */
   function get_site_id($site_id, $prod_site_id, $prod_id)
   {
      // get all categories assigned to this product on this site
      $sql = 'SELECT SiteID '.
             'FROM pr_product_site '.
             'WHERE ProductID =  '.$prod_id.' '.
             'AND SiteID = "'.$site_id.'"';
      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      if ($query->num_rows() > 0)
      {
         $prod_site_id = $result['SiteID'];
      }

      return $prod_site_id;
   }
 
}

?>
