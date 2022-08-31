<?php

class Categories_model extends Model {

   var $cb_db;  // database object for coolbrew tables
//   var $hcg_db;  // database object for hcg_public tables

   var $category_fields = array(
      'CategoryName',
      'CategoryCode',
      'CategoryDescription',
      'CategoryText',
      'SiteID',
      'CategoryID',
//      'CategoryType',
      'Status',
      'CategoryParentID',
      'CategoryOrder',
//      'SESFilename',
      'Language',
      'MetaTitle',
      'MetaDescription',
      'MetaKeywords',
      'MetaMisc AS MetaRobots',
//      'Lft',
//      'Rgt',
   );

   function Categories_model()
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
      $this->cb_db = $this->load->database($level.'-write', TRUE);
//      $this->hcg_db = $this->load->database($level.'-hcg_write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns all Category IDs and Codes found for a given ProductID.
    *
    * @access   public
    * @param    int      The product ID
    * @return   int
    */
   function get_all_category_ids($prod_id)
   {
      $sql = 'SELECT c.CategoryID, c.CategoryCode '.
             'FROM pr_product_category AS pc, pr_category AS c ' .
             'WHERE pc.ProductID = '.$prod_id.' '.
             'AND pc.CategoryID = c.CategoryID';
      $query = $this->cb_db->query($sql);
      $categories = $query->result_array();
      
      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns category ID for a category given it's code
    *
    * @access   public
    * @param    string    The category code
    * @param    string    The site ID
    * @return   int
    */
   function get_category_id_by_code($cat_code, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      $sql = 'SELECT CategoryID '.
             'FROM pr_category '.
             'WHERE CategoryCode LIKE "'.$cat_code.'" '.
             'AND SiteID LIKE "'.$site_id.'"';
      $query = $this->cb_db->query($sql);
      $result = $query->row_array();
      
      if ( ! isset($result['CategoryID']))
      {
         return FALSE;
      }

      return $result['CategoryID'];     
   }

   // --------------------------------------------------------------------

   /**
    * Returns the category data given a category number.
    *
    * @access   public
    * @param    int      The category ID
    * @return   array
    */
   function get_category_data($cat_id)
   {
      $field_list = implode($this->category_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM pr_category '.
             'WHERE CategoryID = '.$cat_id;

      $query = $this->cb_db->query($sql);
      $category = $query->row_array();
      
      return $category;
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
   function get_products_categories($site_id, $use_groups = FALSE)
   {
      $sql = 'SELECT p.ProductName, p.SESFilename AS ProductCode, '.
               'p.ProductID, p.UPC, p.PackageSize, p.ProductGroup, '.
               'c.CategoryID, c.CategoryName '.
             'FROM pr_product AS p, pr_category AS c, '.
               'pr_product_category AS pc '.
             'WHERE c.SiteID = "'.$site_id.'" '.
             'AND pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND c.Status LIKE "active" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" ';
      if ($use_groups == TRUE)
      {
         $sql .= 'AND (p.ProductGroup LIKE "master" '.
                 'OR p.ProductGroup LIKE "none") ';
      }
      else
      {
         $sql .= 'AND p.ProductGroup NOT LIKE "master" ';
      }
      $sql .= "ORDER BY p.ProductName";
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
      
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         if ($products[$i]['ProductGroup'] != 'none' && $use_groups == FALSE)
         {
            $results[$i]['ProductName'] = $products[$i]['ProductName'].' '.$products[$i]['PackageSize'];
         }
         unset($products[$i]['ProductGroup']);
         unset($products[$i]['PackageSize']);
      }
      
      return $products;
   }


}

?>
