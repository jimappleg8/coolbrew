<?php

class Pages_model extends Model {

   var $site_id = SITE_ID;
   var $page_url = '';

   function Pages_model()
   {
      parent::Model();
      $this->load->database('read');
      $this->reset_page_url();
   }

   // --------------------------------------------------------------------

   /**
    * Sets the $page_url variable to the current page
    *
    * @access   public
    * @return   array
    */
   function reset_page_url()
   {
      $this->load->helper('url');

      // get the filename
      $filename = '/'.index_page().'/';
      for ($i=3; $i<=$this->uri->total_segments(); $i++)
      {
         $filename .= $this->uri->segment($i).'/';
      }
      $filename = str_replace('//', '/', $filename);
      $filename = rtrim($filename, '/');
      
      $this->page_url = $filename;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified page ID
    *
    * @access   public
    * @return   array
    */
   function get_page_data($page_id)
   {
      $sql = 'SELECT * FROM pages ' .
             'WHERE ID = \''.$page_id.'\'';
      
      $query = $this->db->query($sql);
      $page = $query->row_array();

      return $page;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the root node of a site
    *
    * If a root node does not exist for the given site, it is created.
    *
    * @access   public
    * @param    string      The site ID
    * @return   array
    */
   function get_site_root($site_id)
   {
      $sql = 'SELECT ID FROM pages '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND PageName = "root"'; 
      $query = $this->db->query($sql);
      $row = $query->row_array();
      
      if ($query->num_rows() > 0)
      {
         $root_id = $row['ID'];
      }
      else
      {
         // create the node
         $page['SiteID'] = $site_id;
         $page['ParentID'] = 0;
         $page['Lft'] = 1;
         $page['Rgt'] = 2;
         $page['PageName'] = 'root';
         $page['MenuText'] = '';
         $page['URL'] = '';
         $page['ExternalLink'] = 0;
         $page['NewWindow'] = 0;
         $page['Sort'] = 0;
         $page['CreatedDate'] = date('Y-m-d H:i:s');
         $page['CreatedBy'] = $this->session->userdata('username');
         $this->db->insert('pages', $page);
         $root_id = $this->db->insert_id();
      }
      
      return $root_id;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the path of the current page
    *
    * @access   public
    * @return   array
    */
   function get_page_path($page = '')
   {
      $page = $this->get_page_info();

      $sql = 'SELECT ID FROM pages '.
             'WHERE Lft < '.$page['Lft'].' '.
             'AND Rgt > '.$page['Rgt'].' '.
             'AND SiteID = "'.$this->site_id.'" '.
             'ORDER BY Lft ASC';

      $query = $this->db->query($sql);
      $paths = $query->result_array();

      foreach ($paths AS $path)
         $result[] = $path['ID'];

      $result[] = $page['ID'];
      
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the Pages record of the current page based on the URL
    *
    * @access   public
    * @return   array
    */
   function get_page_info()
   {
      // search the pages database for that URL
      $sql = 'SELECT * FROM pages '.
             'WHERE URL REGEXP "^'.$this->page_url.'(/)*" '.
             'AND SiteID = "'.$this->site_id.'" '; 
      $query = $this->db->query($sql);
      $page = $query->row_array();
      
      return $page;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the PageName of the current page
    *
    * @access   public
    * @return   array
    */
   function get_page_name()
   {
      $page = $this->get_page_info();
      return $page['PageName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the current page
    *
    * @access   public
    * @return   array
    */
   function get_page_id()
   {
      $page = $this->get_page_info();
      return $page['ID'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of the requested page subtree
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_page_tree($site_id, $page_name = 'root')
   {      
      // retrieve the left and right value of the $root node
      $sql = 'SELECT Lft, Rgt FROM pages '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND PageName = "'.$page_name.'"'; 
      $query = $this->db->query($sql);
      $row = $query->row_array();
      
      // if there is no root node, then create one
      if ($page_name == 'root' && ! isset($row['Lft']))
      {
         $this->get_site_root($site_id);
         $row = array();
         $row['Lft'] = 1;
         $row['Rgt'] = 2;
      }
      elseif ($page_name != 'root' && ! isset($row['Lft']))
      {
         show_error('Pages: The requested root page was not found.');
      }

      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      
      $sql = 'SELECT * FROM pages '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'ORDER BY Lft ASC';
      $query = $this->db->query($sql);
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
    * In other words, it takes the ID, ParentID and Sort fields from
    * the pages database and populates the Lft and Rgt fields with data
    * that makes it easier to retrieve hierarchical data.
    * 
    *  | Based on an article by Gijs Van Tulder:
    *  | "Storing Hierarchical Data in a Database"
    *  | http://www.sitepoint.com/article/hierarchical-data-database
    * 
    */
   function rebuild_tree($site_id, $parent, $left)
   {
      $this->load->database('read');

      // the right value of this node is the left value + 1
      $right = $left+1;

      // get all children of this node
      $sql = 'SELECT ID FROM pages '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$parent.' '.
             'ORDER BY Sort';

      $query = $this->db->query($sql);
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
      $page['Lft'] = $left;
      $page['Rgt'] = $right;
      $this->db->where('ID', $parent);
      $this->db->update('pages', $page);

      // return the right value of this node + 1
      return $right+1;
   }

}

?>