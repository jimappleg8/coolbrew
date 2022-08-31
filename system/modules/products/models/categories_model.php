<?php

class Categories_model extends Model {

   var $category_tree = array();
   
   var $cb_read_db;  // database object for coolbrew tables
   var $cb_write_db;  // database object for coolbrew tables
   var $hcg_write_db;  // database object for hcg_public tables

   function Categories_model()
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
    * Returns the category data given a category ID (code or number)
    *
    * @access   public
    * @param    int       The category ID (code or number)
    * @param    string    The site ID
    * @return   array
    */
   function get_category_data($cat_id, $site_id)
   {
      if ($cat_id == '')
      {
         return array();
      }
      
      if ( ! is_numeric($cat_id))
      {
         $cat_id = $this->get_category_id($cat_id, $site_id);
      }

      $sql = 'SELECT * '.
             'FROM pr_category '.
             'WHERE CategoryID = '.$cat_id;
      $query = $this->cb_read_db->query($sql);
      $category = $query->row_array();
      
      return $category;
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
   function get_category_id($cat_code, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      $sql = 'SELECT CategoryID '.
             'FROM pr_category '.
             'WHERE CategoryCode LIKE "'.$cat_code.'" '.
             'AND SiteID LIKE "'.$site_id.'"';
      $query = $this->cb_read_db->query($sql);
      $result = $query->row_array();

      return $result['CategoryID'];     
   }

   // --------------------------------------------------------------------

   /**
    * Returns category code for a category given it's ID
    *
    * @access   public
    * @param    int      The category ID
    * @return   string
    */
   function get_category_code($cat_id)
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      $sql = 'SELECT CategoryCode FROM pr_category '.
             'WHERE CategoryID = '.$cat_id;
      $query = $this->cb_read_db->query($sql);
      $result = $query->row_array();

      return $result['CategoryCode'];     
   }

   // --------------------------------------------------------------------

   /**
    * Returns the first category found for a given ProductID and SiteID.
    *
    * @access   public
    * @param    int      The product ID
    * @param    string   The site ID
    * @return   array
    */
   function get_first_category($prod_id, $site_id)
   {
      $sql = 'SELECT c.* '.
             'FROM pr_product_category AS pc, pr_category AS c ' .
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = '.$prod_id.' '.
             'AND c.SiteID LIKE "'.$site_id.'"';
      $query = $this->cb_read_db->query($sql);
      $result = $query->row_array();
      
      if ($query->num_rows() < 1)
         return FALSE;
      
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns all Category IDs found for a given ProductID and SiteID.
    *
    * @access   public
    * @param    int      The product ID
    * @param    string   The site ID
    * @return   int
    */
   function get_all_category_ids($prod_id, $site_id)
   {
      $sql = 'SELECT pc.CategoryID '.
             'FROM pr_product_category AS pc, pr_category AS c '.
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = '.$prod_id.' '.
             'AND c.SiteID LIKE "'.$site_id.'"';
      $query = $this->cb_read_db->query($sql);
      $categories = $query->result_array();
      
      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns category data found for a given ProductID and SiteID.
    *
    * @access   public
    * @param    int      The product ID
    * @param    string   The site ID
    * @return   array
    */
   function get_all_categories($prod_id, $site_id)
   {
      $sql = 'SELECT c.* '.
             'FROM pr_product_category AS pc, pr_category AS c '.
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = '.$prod_id.' '.
             'AND c.SiteID LIKE "'.$site_id.'"';
      $query = $this->cb_read_db->query($sql);
      $categories = $query->result_array();
   
      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns parent and sibling data for the given Category
    *
    * @access   public
    * @param    int      The product ID
    * @param    string   The site ID
    * @return   array
    */
   function get_immediate_family($cat_id, $site_id)
   {
      if ($cat_id == '')
      {
         return array();
      }
      
      if ( ! is_numeric($cat_id))
      {
         $cat_id = $this->get_category_id($cat_id, $site_id);
      }

      $sql = 'SELECT * '.
             'FROM ('.
               'SELECT CategoryParentID AS ParentID '.
               'FROM pr_category '.
               'WHERE CategoryID = '.$cat_id.
             ') AS c, pr_category AS cc '.
             'WHERE (cc.CategoryParentID = c.ParentID '.
             'OR cc.CategoryID = c.ParentID) '.
             'AND status = "active" '.
             'ORDER BY cc.CategoryOrder';
      $query = $this->cb_read_db->query($sql);
      $cats = $query->result_array();
      
      $categories = array();
      $children = array();
      foreach ($cats AS $cat)
      {
         if ($cat['ParentID'] == $cat['CategoryID'])
         {
         	$categories[0] = $cat;
         }
         else
         {
         	$children[] = $cat;
         }
      }
      $categories[0]['Children'] = $children;
      
      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the root node of a site's product categories
    *
    * If a root node does not exist for the given site, it is created.
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
      $query = $this->cb_read_db->query($sql);
      $row = $query->row_array();
      
//      echo "$sql<pre>"; print_r($row); echo "</pre>";
      
      if ($query->num_rows() > 0)
      {
         $root_id = $row['CategoryID'];
      }
      else
      {
         // create the node
         $category['SiteID'] = $site_id;
         $category['CategoryCode'] = 'root';
         $category['CategoryName'] = '';
         $category['CategoryDescription'] = '';
         $category['CategoryType'] = '';
         $category['Status'] = 'active';
         $category['CategoryParentID'] = 0;
         $category['CategoryOrder'] = 0;
         $category['CategoryText'] = '';
         $category['SESFilename'] = '';
         $category['MetaTitle'] = '';
         $category['MetaMisc'] = '';
         $category['MetaDescription'] = '';
         $category['MetaKeywords'] = '';
         $category['Language'] = '';
         $this->cb_write_db->insert('pr_category', $category);
         $this->hcg_write_db->insert('pr_category', $category);
         $root_id = $this->cb_write_db->insert_id();
      }
      
      return $root_id;
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
      $query = $this->cb_read_db->query($sql);
      $row = $query->row_array();

      // start with an empty $right stack
      $right = array();
      
      // handle the cases where there are no categories
      if ( ! isset($row['Lft']))
         return array();

      // now, retrieve all descendants of the $root node
      
      $sql = 'SELECT * FROM pr_category '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'ORDER BY Lft ASC';
      $query = $this->cb_read_db->query($sql);
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
    * Returns an array of the requested menu subtree
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_active_category_tree($site_id, $cat_code = 'root')
   {      
      // retrieve the left and right value of the "root" node
      $sql = 'SELECT Lft, Rgt FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "'.$cat_code.'"'; 
      $query = $this->cb_read_db->query($sql);
      $row = $query->row_array();

      // start with an empty $right stack
      $right = array();
      
      // handle the cases where there are no categories
      if ( ! isset($row['Lft']))
         return array();

      // now, retrieve all descendants of the $root node
      
      $sql = 'SELECT * FROM pr_category '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'AND Status = "active" '.
             'ORDER BY Lft ASC';
      $query = $this->cb_read_db->query($sql);
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

   /*
    * Creates a modified preorder traversal table from an adjacency list.
    *
    * In other words, it takes the CategoryID, CategoryParentID and
    * CategoryOrder fields from the category database and populates the 
    * Lft and Rgt fields with data that makes it easier to retrieve 
    * hierarchical data.
    * 
    *  | Based on an article by Gijs Van Tulder:
    *  | "Storing Hierarchical Data in a Database"
    *  | http://www.sitepoint.com/article/hierarchical-data-database
    * 
    */
   function rebuild_tree($site_id, $parent, $left)
   {
      // the right value of this node is the left value + 1
      $right = $left+1;

      // get all children of this node
      $sql = 'SELECT CategoryID FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryParentID = '.$parent.' '.
             'ORDER BY CategoryOrder';

      $query = $this->cb_read_db->query($sql);
      $result = $query->result_array();

      for ($i=0; $i<count($result); $i++)
      {
         // recursive execution of this function for each child of this node
         // $right is the current right value, which is incremented by
         // the rebuild_tree function
         $right = $this->rebuild_tree($site_id, $result[$i]['CategoryID'], $right);
      }

      // we've got the left value, and now that we've processed
      // the children of this node we also know the right value
      $category['Lft'] = $left;
      $category['Rgt'] = $right;
      $this->cb_write_db->where('CategoryID', $parent);
      $this->cb_write_db->update('pr_category', $category);
      $this->hcg_write_db->where('CategoryID', $parent);
      $this->hcg_write_db->update('pr_category', $category);

      // return the right value of this node + 1
      return $right+1;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of ProductIDs for all products in a category.
    *
    * It excludes products with a status of "discontinued" or "pending".
    * It also ensures that products have the same SiteID as the category.
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_prod_ids_in_category($cat_id)
   {
      $sql = 'SELECT pc.ProductID '.
             'FROM pr_product_category AS pc, pr_product AS p, '.
               'pr_category AS c, pr_product_site AS s '.
             'WHERE pc.CategoryID LIKE "'.$cat_id.'" '.
             'AND pc.ProductID = p.ProductID '.
             'AND p.ProductID = s.ProductID '.
             'AND c.CategoryID = pc.CategoryID '.
             'AND c.SiteID = s.SiteID '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "pending" '.
             'AND p.Status NOT LIKE "inactive" '.
             'AND (p.ProductGroup LIKE "none" '.
             'OR p.ProductGroup LIKE "master") '.
             'ORDER BY p.ProductName ASC';   
      $query = $this->cb_read_db->query($sql);
      $raw_list = $query->result_array();

      $id_list = array();
      for ($i=0; $i<count($raw_list); $i++)
      {
         $id_list[$i] = $raw_list[$i]['ProductID'];
      }

      return $id_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of potential parent categories for use in forms
    *
    */
   function get_parent_list($site_id, $this_cat)
   {
      $sql = 'SELECT CategoryID, CategoryName, CategoryCode '.
             'FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryID != '.$this_cat.' '.
             'ORDER BY CategoryParentID, CategoryOrder';
      $query = $this->cb_read_db->query($sql);
      $parent_array = $query->result_array();
      
      foreach ($parent_array AS $parent)
      {
         if ($parent['CategoryName'] == '')
         {
            $results[$parent['CategoryID']] = $parent['CategoryCode'];
         }
         else
         {
            $results[$parent['CategoryID']] = $parent['CategoryName'];
         }
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the path of a product's category page
    *
    * This accesses the "pages" table directly.
    *
    * @access   public
    * @return   array
    */
   function get_category_page_path($cat_code, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;

      $page = $this->get_page_info($cat_code, $site_id);

      $sql = 'SELECT PageName, MenuText, URL FROM pages '.
             'WHERE Lft < '.$page['Lft'].' '.
             'AND Rgt > '.$page['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'ORDER BY Lft ASC';
      $query = $this->cb_read_db->query($sql);
      $paths = $query->result_array();

      foreach ($paths AS $path)
         $result[] = $path;
      
      $next = count($result);

      $result[$next]['PageName'] = $page['PageName'];
      $result[$next]['MenuText'] = $page['MenuText'];
      $result[$next]['URL'] = $page['URL'];
      
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the Pages record of the current page based on the PageName
    *
    * @access   public
    * @return   array
    */
   function get_page_info($page_name, $site_id)
   {
      // search the pages database for that URL
      $sql = 'SELECT * FROM pages '.
             'WHERE PageName LIKE "'.$page_name.'" '.
             'AND SiteID = "'.$site_id.'" '; 
      $query = $this->cb_read_db->query($sql);
      $page = $query->row_array();
      
      return $page;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new category record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   int       The newly generated Category ID
    */
   function insert_category($values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // first, insert the main product record
      $this->cb_write_db->insert('pr_category', $values);
      $this->hcg_write_db->insert('pr_category', $values);

      $category_id = $this->cb_write_db->insert_id();
      
      $this->CI->auditor->audit_insert('pr_category', '', $values);
      
      return $category_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an existing category record
    *
    * @access   public
    * @param    int       The category ID of the record being updated
    * @param    array     The values to be inserted
    * @param    array     The values of the existing record
    * @return   boolean
    */
   function update_category($category_id, $values, $old_values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // update both databases
      $tmp = $this->cb_write_db->where('CategoryID', $category_id);
      $this->cb_write_db->update('pr_category', $values);
      $this->hcg_write_db->where('CategoryID', $category_id);
      $this->hcg_write_db->update('pr_category', $values);

      $this->CI->auditor->audit_update('pr_category', $tmp->ar_where, $old_values, $values);
      
      return TRUE;
   }


}

/* End of file categories_model.php */
/* Location: ./system/modules/products/models/categories_model.php */