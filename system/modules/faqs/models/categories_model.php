<?php

class Categories_model extends Model {

   var $category_tree = array();
   
   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   // --------------------------------------------------------------------

   function Categories_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified list ID
    *
    * @access   public
    * @return   array
    */
   function get_faq_category_data($category_id)
   {
      $sql = 'SELECT * FROM faqs_category ' .
             'WHERE ID = '.$category_id;
      
      $query = $this->read_db->query($sql);
      $category = $query->row_array();

      return $category;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified category code
    *
    * @access   public
    * @return   array
    */
   function get_faq_category_data_by_code($site_id, $category_code)
   {
      $sql = 'SELECT * FROM faqs_category ' .
             'WHERE FaqCode = "'.$category_code.'" '.
             'AND SiteID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      $category = $query->row_array();

      return $category;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of lists for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_faq_categories($site_id)
   {
      $sql = 'SELECT * FROM faqs_category ' .
             'WHERE SiteID = \''.$site_id.'\' '.
             'ORDER BY Name';
      
      $query = $this->read_db->query($sql);
      $categories = $query->result_array();

      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of categories for the specified Site ID for use in forms
    *
    */
   function get_faq_category_list($site_id)
   {
      $sql = 'SELECT ID, Name '.
             'FROM faqs_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'ORDER BY Name';

      $query = $this->read_db->query($sql);
      $category_array = $query->result_array();
      
      foreach ($category_array AS $list)
      {
         $results[$category['ID']] = $category['Name'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns all Category IDs found for a given FAQ ID.
    *
    * @access   public
    * @param    int      The FAQ ID
    * @return   int
    */
   function get_all_category_ids($faq_id, $answer_id = 0)
   {
      $sql = 'SELECT CategoryID FROM faqs_item_category '.
             'WHERE FaqID = '.$faq_id.' '.
             'AND AnswerID = '.$answer_id;

      $query = $this->read_db->query($sql);
      $categories = $query->result_array();
      
      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Indicates whether the root node of a site exists
    *
    * @access   public
    * @param    string      The site ID
    * @return   boolean
    */
   function root_node_exists($site_id)
   {
      $sql = 'SELECT ID FROM faqs_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND FaqCode = "root"'; 

      $query = $this->read_db->query($sql);
      $row = $query->row_array();
      
      return ($query->num_rows() > 0);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the root node of a site's FAQ categories
    *
    * If a root node does not exist for the given site, it is created.
    *
    * @access   public
    * @param    string      The site ID
    * @return   array
    */
   function get_category_root($site_id)
   {
      $sql = 'SELECT ID FROM faqs_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND FaqCode = "root"'; 

      $query = $this->read_db->query($sql);
      $row = $query->row_array();
      
      if ($query->num_rows() > 0)
      {
         $root_id = $row['ID'];
      }
      else
      {
         // create the node. The assumption is that
         // there are no pre-existing categories where the root
         // node needs to be designated as the ParentID.
         $category['SiteID'] = $site_id;
         $category['FaqCode'] = 'root';
         $category['Name'] = '';
         $category['Description'] = '';
         $category['Status'] = 'active';
         $category['ParentID'] = 0;
         $category['Sort'] = 0;
         $this->write_db->insert('faqs_category', $category);
         $root_id = $this->write_db->insert_id();
      }
      
      return $root_id;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of the requested menu subtree
    *
    * @access   public
    * @param    int      The site ID
    * @return   array
    */
   function get_category_tree($site_id, $cat_code = 'root')
   {      
      // retrieve the left and right value of the "root" node
      $sql = 'SELECT Lft, Rgt FROM faqs_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND FaqCode = "'.$cat_code.'"'; 

      $query = $this->read_db->query($sql);
      $row = $query->row_array();
      
      if (empty($row))
      {
         return array();
      }

      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      $sql = 'SELECT * FROM faqs_category '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'ORDER BY Lft ASC';

      $query = $this->read_db->query($sql);
      $result = $query->result_array();

      // display each row
      for($i=0, $cnt=count($result); $i<$cnt; $i++)
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
    * In other words, it takes the ID, ParentID and Sort fields
    * from the category database and populates the Lft and Rgt fields
    * with data that makes it easier to retrieve hierarchical data.
    * 
    *  | Based on an article by Gijs Van Tulder:
    *  | "Storing Hierarchical Data in a Database"
    *  | http://www.sitepoint.com/article/hierarchical-data-database
    * 
    */
   function rebuild_tree($site_id, $parent, $left)
   {
      // the right value of this node is the left value + 1
      $right = $left + 1;

      // get all children of this node
      $sql = 'SELECT ID FROM faqs_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$parent.' '.
             'ORDER BY Sort';

      $query = $this->read_db->query($sql);
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
      $this->write_db->update('faqs_category', $category);

      // return the right value of this node + 1
      return $right + 1;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of potential parent categories for use in forms
    *
    */
   function get_parent_list($site_id, $catagory_id)
   {
      $sql = 'SELECT ID, Name, FaqCode '.
             'FROM faqs_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ID != '.$catagory_id.' '.
             'ORDER BY ParentID, Sort';

      $query = $this->read_db->query($sql);
      $parent_array = $query->result_array();
      
      foreach ($parent_array AS $parent)
      {
         if ($parent['Name'] == '')
         {
            $results[$parent['ID']] = $parent['FaqCode'];
         }
         else
         {
            $results[$parent['ID']] = $parent['Name'];
         }
      }
      
      return $results;
   }

   // --------------------------------------------------------------------
   
   /**
    * Changes the sort order of a category by moving it up or down
    *
    * @access   public
    * @param    array     the submitted form values
    * @return   boolean
    */
   function move_category($cat_id, $direction)
   {
      // get information about this category
      $category = $this->get_faq_category_data($cat_id);
         
      // determine how many children are on this level
      $sql = 'SELECT ID FROM faqs_category '.
             'WHERE ParentID = '.$category['ParentID'].' '.
             'AND SiteID = \''.$category['SiteID'].'\'';
   
      $query = $this->read_db->query($sql);

      $children = $query->num_rows();
      
      if ($direction == "dn" && $category['Sort'] < $children)
      {
         // move the category below one level up
         $sql = 'UPDATE faqs_category '.
                'SET Sort = '.$category['Sort'].' '.
                'WHERE Sort = '.($category['Sort'] + 1).' '.
                'AND ParentID = '.$category['ParentID'].' '.
                'AND SiteID = "'.$category['SiteID'].'"';

         $query = $this->write_db->query($sql);

         // and move our category one level down
         $sql = 'UPDATE faqs_category '.
                'SET Sort = '.($category['Sort'] + 1).' '.
                'WHERE ID = '.$cat_id;

            $query = $this->write_db->query($sql);
      }
      elseif ($direction == "up" && $category['Sort'] > 1)
      {
         // move the category above one level down
         $sql = 'UPDATE faqs_category '.
                'SET Sort = '.$category['Sort'].' '.
                'WHERE Sort = '.($category['Sort'] - 1).' '.
                'AND ParentID = '.$category['ParentID'].' '.
                'AND SiteID = "'.$category['SiteID'].'"';

         $query = $this->write_db->query($sql);

         // and move our category one level up
         $sql = 'UPDATE faqs_category '.
                'SET Sort = '.($category['Sort'] - 1).' '.
                'WHERE ID = '.$cat_id;

         $query = $this->write_db->query($sql);
      }

      // And rebuild the tree so it is up-to-date
      $root = $this->get_category_root($category['SiteID']);
      $this->rebuild_tree($category['SiteID'], $root, 1);
      
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Inserts a new category record
    *
    * @access   public
    * @param    array     the submitted form values
    * @return   boolean
    */
   function insert_category($values)
   {
      // first, open up a spot for the new record in the sort order
      $sql = 'UPDATE faqs_category '.
             'SET Sort = Sort + 1 '.
             'WHERE SiteID = "'.$values['SiteID'].'" '.
             'AND ParentID = '.$values['ParentID'].' '.
             'AND Sort >= '.$values['Sort'];

      $query = $this->write_db->query($sql);
      
      // now insert the new record
      $this->write_db->insert('faqs_category', $values);

      // and rebuild the tree so it is up-to-date
      $root = $this->get_category_root($values['SiteID']);
      $this->rebuild_tree($values['SiteID'], $root, 1);

      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an existing category record
    *
    * @access   public
    * @param    integer   the ID of the category being updated
    * @param    array     the submitted form values
    * @return   boolean
    */
   function update_category($category_id, $values)
   {
      $category = $this->get_faq_category_data($category_id);

      // first, find out if the parent ID changed
      if ($category['ParentID'] != $values['ParentID'])
      {
         // if so, then get a count of the children of the new parent 
         // add one to get the new Sort for this category
         $sql = 'SELECT ID FROM faqs_category '.
                'WHERE ParentID = '.$values['ParentID'];

         $query = $this->read_db->query($sql);

         $values['Sort'] = $query->num_rows() + 1;
      
         // close up the hole in the sort order in the old parent
         $sql = 'UPDATE faqs_category '.
                'SET Sort = Sort - 1 '.
                'WHERE SiteID = "'.$category['SiteID'].'" '.
                'AND ParentID = '.$category['ParentID'].' '.
                'AND Sort > '.$category['Sort'];

         $query = $this->write_db->query($sql);
      }

      // now update the edited category
      $this->write_db->where('ID', $category_id);
      $this->write_db->update('faqs_category', $values);
      
      // Since we can change the Sort, rebuild the tree so it is up-to-date
      $root = $this->get_category_root($category['SiteID']);
      $this->rebuild_tree($category['SiteID'], $root, 1);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes the specified list ID
    *
    * @access   public
    * @return   boolean
    */
   function delete_faq_category($category_id)
   {
      // delete all references to this category in faqs_item_category
      $sql = 'DELETE FROM faqs_item_category '. 
             'WHERE CategoryID = '.$category_id;
      $this->write_db->query($sql);

      $category = $this->get_faq_category_data($category_id);

      // delete the category record itself
      $this->write_db->where('ID', $category_id);
      $this->write_db->delete('faqs_category');

      // get a list of this category's children
      $sql = 'SELECT ID FROM faqs_category '.
             'WHERE ParentID = '.$category['ID'].' '.
             'ORDER BY Sort';
      $query = $this->read_db->query($sql);
      $children = $query->result_array();

      // get a list of categories whose Sort will need to be adjusted
      $sql = 'SELECT ID, Sort FROM faqs_category '.
             'WHERE SiteID = "'.$category['SiteID'].'" '.
             'AND Sort > '.$category['Sort'].' '.
             'AND ParentID = '.$category['ParentID'];
      $query = $this->read_db->query($sql);
      $belows = $query->result_array();
      
      // change the parent IDs of children to this category's parent ID
      for ($i=0; $i<count($children); $i++)
      {
         $values = array();
         $values['ParentID'] = $category['ParentID'];
         $values['Sort'] = $category['Sort'] + $i;
         $this->write_db->where('ID', $children[$i]['ID']);
         $this->write_db->update('faqs_category', $values);
      }
      
      $offset = count($children) - 1;
      foreach ($belows AS $below)
      {
         $values = array();
         $values['Sort'] = $below['Sort'] + $offset;
         $this->write_db->where('ID', $below['ID']);
         $this->write_db->update('faqs_category', $values);
      }

      // and rebuild the tree so it is up-to-date
      $root = $this->get_category_root($category['SiteID']);
      $this->rebuild_tree($category['SiteID'], $root, 1);

      return TRUE;
   }



}

?>