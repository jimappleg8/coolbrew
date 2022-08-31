<?php

class Categories_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $category_fields = array(
      'Name',
      'FaqCode AS Code',
      'Description',
      'SiteID',
      'ID',
      'Status',
      'ParentID',
      'Sort',
   );

   // --------------------------------------------------------------------

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
      // we use the "write" database because it points to a specific server
      // where the "read" database should stay "localhost" to balance load.
      $this->read_db = $this->load->database($level.'-write', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified category code
    *
    * @access   public
    * @return   array
    */
   function get_category_id_by_code($category_code, $site_id)
   {
      $sql = 'SELECT ID FROM faqs_category ' .
             'WHERE FaqCode = "'.$category_code.'" '.
             'AND SiteID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      $result = $query->row_array();

      if ( ! isset($result['ID']))
      {
         return FALSE;
      }

      return $result['ID'];     
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
      $field_list = implode($this->category_fields, ', ');

      $sql = 'SELECT '.$field_list.', Lft, Rgt '.
             'FROM faqs_category '.
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
    * Returns data for the specified list ID
    *
    * @access   public
    * @return   array
    */
   function get_category_data($category_id)
   {
      $field_list = implode($this->category_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM faqs_category '.
             'WHERE ID = '.$category_id;
      
      $query = $this->read_db->query($sql);
      $category = $query->row_array();

      return $category;
   }


}

/* End of file categories_model.php */
/* Location: ./system/modules/api/models/v1/faqs/categories_model.php */