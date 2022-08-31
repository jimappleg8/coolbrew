<?php

class Categories_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Categories_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
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
      $sql = "SELECT * FROM rcp_category " .
             "WHERE ID = ".$cat_id;
      $query = $this->read_db->query($sql);
      $category = $query->row_array();
      
      $category = $this->get_category_image($category);
      
      return $category;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the category name for the given category id.
    *
    * @access   public
    * @param    int      The category ID
    * @return   array
    */
   function get_category_name($cat_id)
   {
      $sql = "SELECT CategoryName FROM rcp_category " .
             "WHERE ID = ".$cat_id;
      $query = $this->read_db->query($sql);
      $category = $query->row_array();
      
      return $category['CategoryName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns the category data given a site id and category code.
    *
    * @access   public
    * @param    string   The site_id
    * @param    string   The category code
    * @return   array
    */
   function get_category_data_by_code($site_id, $category_code)
   {
      $sql = 'SELECT * FROM rcp_category '.
             'WHERE CategoryCode = "'.$category_code.'" '.
             'AND SiteID = "'.$site_id.'"';
      $query = $this->read_db->query($sql);
      $category = $query->row_array();
      
      $category = $this->get_category_image($category);

      return $category;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the category data with corrected ImageFile info.
    *
    * @access   public
    * @param    array   The category array
    * @return   array
    */
   function get_category_image($category)
   {
      if ($category['ImageFile'] != '')
      {
         return $category;
      }
      
      if (file_exists(SERVERPATH.'/hcgwebdocs/resources/'. $category['SiteID'].'/recipes/categories/default.jpg'))
      {
         $category['ImageFile'] = $category['SiteID']. '/recipes/categories/default.jpg';
         return $category;
      }

      return $category;
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
      $sql = 'SELECT ID FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$parent.' '.
             'ORDER BY Sort';

      $query = $this->write_db->query($sql);
      $result = $query->result_array();

      for ($i=0; $i<count($result); $i++)
      {
         // recursive execution of this function for each child of this node
         // $right is the current right value, which is incremented by
         // the rebuild_tree function
         $right = $this->rebuild_tree($site_id, $result[$i]['ID'], $right);
      }

      // we've got the left value, and now that we've processed
      // the children of this node we also know the right value
      $category['Lft'] = $left;
      $category['Rgt'] = $right;
      $this->write_db->where('ID', $parent);
      $this->write_db->update('rcp_category', $category);

      // return the right value of this node + 1
      return $right+1;
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
      $sql = 'SELECT ID FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "root"'; 
      $query = $this->write_db->query($sql);
      $row = $query->row_array();
      
//      echo "$sql<pre>"; print_r($row); echo "</pre>";
      
      if ($query->num_rows() > 0)
      {
         $root_id = $row['ID'];
      }
      else
      {
         // create the node
         $category['SiteID'] = $site_id;
         $category['ParentID'] = 0;
         $category['CategoryCode'] = 'root';
         $category['CategoryName'] = '';
         $category['Status'] = 'active';
         $category['Sort'] = 0;
         $category['Language'] = '';
         $this->write_db->insert('rcp_category', $category);
         $root_id = $this->write_db->insert_id();
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
      $sql = 'SELECT Lft, Rgt FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "'.$cat_code.'"'; 
      $query = $this->read_db->query($sql);
      $row = $query->row_array();

      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      
      $sql = 'SELECT * FROM rcp_category '.
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
    * Returns a lookup array for all categories in a site
    *
    * @access   public
    * @param    int      The site ID
    * @return   int
    */
   function get_category_lookup($site_id)
   {
      $sql = "SELECT * FROM rcp_category " .
             'WHERE SiteID = "'.$site_id.'" '.
             'ORDER BY ID';
      $query = $this->read_db->query($sql);
      $categories = $query->result_array();
      
      foreach ($categories AS $cat)
      {
         $new_cat[$cat['ID']] = $cat;
      }
      
      return $new_cat;
   }

   // --------------------------------------------------------------------

   /**
    * Returns all Category IDs found for a given RecipeID.
    *
    * @access   public
    * @param    int      The recipe ID
    * @return   int
    */
   function get_all_category_ids($recipe_id)
   {
      $sql = 'SELECT CategoryID '.
             'FROM rcp_recipe_category '.
             'WHERE RecipeID = '.$recipe_id;
      $query = $this->read_db->query($sql);
      $categories = $query->result_array();
      
      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of potential parent categories for use in forms
    *
    */
   function get_parent_list($site_id, $this_cat = '')
   {
      $root = $this->get_category_root($site_id);
      
      $sql = 'SELECT ID, CategoryName, CategoryCode '.
             'FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" ';
      if ($this_cat != '')
         $sql .= 'AND ID != '.$this_cat.' ';
      $sql .= 'AND (ParentID = '.$root.' '.
              'OR ParentID = 0) '.
              'ORDER BY ParentID, Sort';
      $query = $this->read_db->query($sql);
      $parent_array = $query->result_array();
      
      foreach ($parent_array AS $parent)
      {
         if ($parent['CategoryName'] == '')
         {
            $results[$parent['ID']] = $parent['CategoryCode'];
         }
         else
         {
            $results[$parent['ID']] = $parent['CategoryName'];
         }
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of categories for this site
    *
    * @access   public
    * @return   array
    */
   function get_categories($site_id)
   {
      $root = $this->get_category_root($site_id);
      
      // start by getting the main categories
      $sql = 'SELECT * '.
             'FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$root.' '.
             'AND Status = "active" '.
             'ORDER BY Sort';

      $query = $this->read_db->query($sql);
      $cats = $query->result_array();
      
      return $cats;
   }   

   // --------------------------------------------------------------------

   /**
    * Returns array of categories for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_category_lists($site_id)
   {
      $root = $this->get_category_root($site_id);
      
      // start by getting the main categories
      $sql = 'SELECT * '.
             'FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$root.' '.
             'AND Status = "active" '.
             'ORDER BY Sort';

      $query = $this->read_db->query($sql);
      $cats = $query->result_array();
      
      $list = array();
      
      // get the subcategories for each category
      foreach ($cats AS $cat)
      {
         $result = array();
         $sql = 'SELECT * '.
                'FROM rcp_category '.
                'WHERE ParentID = '.$cat['ID'].' '.
                'ORDER BY Lft';
         $query = $this->read_db->query($sql);
         $subcats = $query->result_array();
         
         $list[$cat['CategoryName']]['Name'] = $cat['CategoryName'];
         $list[$cat['CategoryName']]['Code'] = $cat['CategoryCode'];
         
         $result[''] = "All";
         foreach ($subcats AS $subcat)
         {
            $result[$subcat['ID']] = $subcat['CategoryName'];
         }

         $list[$cat['CategoryName']]['List'] = $result;

      }
      return $list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of recipes in the specified category
    *
    * @access   public
    * @param    int      the category ID to search for
    * @return   array
    */
   function get_recipes_by_category($category_id)
   {
      $sql = 'SELECT RecipeID '.
             'FROM rcp_recipe_category '.
             'WHERE CategoryID = '.$category_id;
      $query = $this->read_db->query($sql);
      $cat_matches = $query->result_array();

      return $cat_matches;
   }

   // --------------------------------------------------------------------
   
   /**
    * Inserts a recipe category record
    *
    * @access   public
    * @return   null
    */
   function insert_recipe_category($site_id, $values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $this->write_db->insert('rcp_category', $values);
      $category_id = $this->write_db->insert_id();
      
      $this->CI->auditor->audit_insert('rcp_category', '', $values);
      
      // And rebuild the tree so it is up-to-date
      $root = $this->get_category_root($site_id);
      $this->rebuild_tree($site_id, $root, 1);

      return $category_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a recipe category record
    *
    * @access   public
    * @return   null
    */
   function update_recipe_category($site_id, $category_id, $values, $old_values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $tmp = $this->write_db->where('ID', $category_id);
      $this->write_db->update('rcp_category', $values);

      $this->CI->auditor->audit_update('rcp_category', $tmp->ar_where, $old_values, $values);

      // And rebuild the tree so it is up-to-date
      $root = $this->get_category_root($site_id);
      $this->rebuild_tree($site_id, $root, 1);

      return TRUE;
   }


}

?>
