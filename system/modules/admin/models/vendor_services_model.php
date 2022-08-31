<?php

class Vendor_services_model extends Model {

   var $service_tree = array();
   
   var $read_db;  // database object for reading
   var $write_db;  // database object for writing

   function Vendor_services_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the service data given a service ID
    *
    * @access   public
    * @param    int       The service ID
    * @return   array
    */
   function get_service_data($cat_id)
   {
      $sql = 'SELECT * '.
             'FROM adm_vendor_service '.
             'WHERE ID = '.$cat_id;

      $query = $this->read_db->query($sql);
      $service = $query->row_array();
      
      return $service;
   }

   // --------------------------------------------------------------------

   /**
    * Returns service data for all services.
    *
    * @access   public
    * @return   array
    */
   function get_all_services()
   {
      $sql = 'SELECT c.* '.
             'FROM adm_vendor_service';

      $query = $this->read_db->query($sql);
      $services = $query->result_array();
   
      return $services;
   }

   // --------------------------------------------------------------------

   /**
    * Returns all service IDs found for a given Vendor ID
    *
    * @access   public
    * @param    int      The vendor ID
    * @return   int
    */
   function get_all_service_ids($vendor_id)
   {
      $sql = 'SELECT vcl.ServiceID '.
             'FROM adm_vendor_service_link AS vcl, adm_vendor_service AS vc '.
             'WHERE vcl.ServiceID = vc.ID '.
             'AND vcl.VendorID = '.$vendor_id;
      $query = $this->read_db->query($sql);
      $services = $query->result_array();
      
      return $services;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the "Hosting" service record
    *
    * @access   public
    * @return   array
    */
   function get_hosting_id()
   {
      $sql = 'SELECT ID FROM adm_vendor_service '.
             'WHERE Name = "Hosting"';

      $query = $this->read_db->query($sql);
      $service = $query->row_array();

      return $service['ID'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of the requested menu subtree
    *
    * @access   public
    * @return   array
    */
   function get_service_tree()
   {      
      $sql = 'SELECT * '.
             'FROM adm_vendor_service '.
             'ORDER BY Lft ASC';

      $query = $this->read_db->query($sql);
      $result = $query->result_array();

      $right = array();
      for ($i=0, $cnt=count($result); $i<$cnt; $i++)
      {
         // only check stack if there is one
         if (count($right) > 0)
         {
            // check if we should remove a node from the stack
            while ($right[count($right)-1] < $result[$i]['Rgt'])
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
    * In other words, it takes the ID, ParentID and Order fields from the
    * service database and populates the Lft and Rgt fields with data 
    * that makes it easier to retrieve hierarchical data.
    * 
    *  | Based on an article by Gijs Van Tulder:
    *  | "Storing Hierarchical Data in a Database"
    *  | http://www.sitepoint.com/article/hierarchical-data-database
    * 
    */
   function rebuild_tree($parent, $left)
   {
      // the right value of this node is the left value + 1
      $right = $left + 1;

      // get all children of this node
      $sql = 'SELECT ID FROM adm_vendor_service '.
             'WHERE ParentID = '.$parent.' '.
             'ORDER BY SortOrder';

      $query = $this->read_db->query($sql);
      $result = $query->result_array();

      for ($i=0; $i<count($result); $i++)
      {
         // recursive execution of this function for each child of this node
         // $right is the current right value, which is incremented by
         // the rebuild_tree function
         $right = $this->rebuild_tree($result[$i]['ID'], $right);
      }

      // we've got the left value, and now that we've processed
      // the children of this node we also know the right value
      $service['Lft'] = $left;
      $service['Rgt'] = $right;
      $this->write_db->where('ID', $parent);
      $this->write_db->update('adm_vendor_service', $service);

      // return the right value of this node + 1
      return $right + 1;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of potential parent services for use in forms
    *
    */
   function get_parent_list($this_cat)
   {
      $sql = 'SELECT ID, Name '.
             'FROM adm_vendor_service '.
             'WHERE ID != '.$this_cat.' '.
             'ORDER BY ParentID, SortOrder';

      $query = $this->read_db->query($sql);
      $parent_array = $query->result_array();
      
      $results = array(0 => 'root');
      foreach ($parent_array AS $parent)
      {
         $results[$parent['ID']] = $parent['Name'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------
   
   /**
    * Deletes an existing service record
    *
    * @access   public
    * @param    array     The service ID to be deleted
    * @return   bool      
    */
   function delete_service($service_id)
   {
      $service = $this->get_service_data($service_id);

      // delete the service record itself
      $this->write_db->where('ID', $service_id);
      $this->write_db->delete('adm_vendor_service');
      
      // get a list of this service's children
      $sql = 'SELECT ID FROM adm_vendor_service '.
             'WHERE ParentID = '.$service['ID'].' '.
             'ORDER BY SortOrder';
      $query = $this->read_db->query($sql);
      $children = $query->result_array();
      
      // get a list of services whose SortOrder will need to be adjusted
      $sql = 'SELECT ID, SortOrder FROM adm_vendor_service '.
             'WHERE SortOrder > '.$service['SortOrder'].' '.
             'AND ParentID = '.$service['ParentID'];
      $query = $this->read_db->query($sql);
      $belows = $query->result_array();
      
      // change the parent IDs of children to this service's parent ID
      for ($i=0; $i<count($children); $i++)
      {
         $values['ParentID'] = $service['ParentID'];
         $values['SortOrder'] = $service['SortOrder'] + $i;
         $this->write_db->where('ID', $children[$i]['ID']);
         $this->write_db->update('adm_vendor_service', $values);
      }
      
      $offset = count($children) - 1;
      foreach ($belows AS $below)
      {
         $values['SortOrder'] = $below['SortOrder'] + $offset;
         $this->write_db->where('ID', $below['ID']);
         $this->write_db->update('adm_vendor_service', $values);
      }

      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new service record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   int       The newly generated Service ID
    */
   function insert_service($values)
   {
      if ( ! isset($values['ParentID']))
      {
         // set it to be at the root level
         $values['ParentID'] = 1;
      }
      
      if ( ! isset($values['SortOrder']))
      {
         // get the last sorted number and place it at the end
         $sql = 'SELECT MAX(SortOrder) AS MaxOrder '.
                'FROM adm_vendor_service';
         $query = $this->read_db->query($sql);
         $result = $query->row_array();
         $values['SortOrder'] = $result['MaxOrder'] + 1;
      }
      
      // update needed sort fields to make room for insert
      $sql = 'SELECT ID, SortOrder FROM adm_vendor_service '.
             'WHERE ParentID = '.$values['ParentID'].' '.
             'AND SortOrder >= '.$values['SortOrder'];

      $query = $this->read_db->query($sql);
      $sort_list = $query->result_array();
      
      if ($query->num_rows() > 0)
      {
         foreach($sort_list AS $item)
         {
            $item['SortOrder'] = $item['SortOrder'] + 1;
            $this->write_db->where('ID', $item['ID']);
            $this->write_db->update('adm_vendor_service', $item);
         }
      }

      $this->write_db->insert('adm_vendor_service', $values);
      $id = $this->write_db->insert_id();
      
      return $id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an existing service record
    *
    * @access   public
    * @param    int       The service ID of the record being updated
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_service($service_id, $values)
   {
      // find out if the parent ID changed
      $service = $this->get_service_data($service_id);
      
      if ($service['ParentID'] != $values['ParentID'])
      {
         // if so, then get a count of the children of the new parent 
         // add one to get the new SortOrder for this service
         $sql = 'SELECT ID FROM adm_vendor_service '.
                'WHERE ParentID = '.$values['ParentID'];
         $query = $this->read_db->query($sql);
         $children = $query->result_array();
         $values['SortOrder'] = count($children) + 1;
      
         // get a list of all services below this one in its old parent
         $sql = 'SELECT ID, SortOrder FROM adm_vendor_service '.
                'WHERE SortOrder > '.$service['SortOrder'].' '.
                'AND ParentID = '.$service['ParentID'];
         $query = $this->read_db->query($sql);
         $belows = $query->result_array();

         // adjust the SortOrder (-1) for each of those services
         foreach ($belows AS $below)
         {
            $item['SortOrder'] = $below['SortOrder'] - 1;
            $this->write_db->where('ID', $below['ID']);
            $this->write_db->update('adm_vendor_service', $item);
         }
      }

      $this->write_db->where('ID', $service_id);
      $this->write_db->update('adm_vendor_service', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates the Sort Order of an existing service record
    *
    * @access   public
    * @param    int       The service ID of the record being updated
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_service_sort_order($cat_id, $direction)
   {
      $sql = 'SELECT ParentID, SortOrder '.
             'FROM adm_vendor_service '.
             'WHERE ID = '.$cat_id;
   
      $query = $this->read_db->query($sql);
      $row = $query->row_array();
         
      // determine how many children are on this level
      $sql = 'SELECT ID '.
             'FROM adm_vendor_service '.
             'WHERE ParentID = '.$row['ParentID'];
   
      $query = $this->read_db->query($sql);
      $parent = $query->result_array();
      $children = $query->num_rows();
      
      if ($direction == "dn" && $row['SortOrder'] < $children)
      {
         $sql = 'UPDATE adm_vendor_service '.
                'SET SortOrder = '.$row['SortOrder'].' '.
                'WHERE SortOrder = '.($row['SortOrder'] + 1).' '.
                'AND ParentID = '.$row['ParentID'];
         $this->write_db->query($sql);

         $sql = 'UPDATE adm_vendor_service '.
                'SET SortOrder = '.($row['SortOrder'] + 1).' '.
                'WHERE ID = '.$cat_id;
         $this->write_db->query($sql);
      }
      elseif ($direction == "up" && $row['SortOrder'] > 1)
      {
         $sql = 'UPDATE adm_vendor_service '.
                'SET SortOrder = '.$row['SortOrder'].' '.
                'WHERE SortOrder = '.($row['SortOrder'] - 1).' '.
                'AND ParentID = '.$row['ParentID'];
         $this->write_db->query($sql);

         $sql = 'UPDATE adm_vendor_service '.
                'SET SortOrder = '.($row['SortOrder'] - 1).' '.
                'WHERE ID = '.$cat_id;
         $this->write_db->query($sql);
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns services list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_services_list()
   {
      $services = $this->get_service_tree();

      $results = array(''=>'-- choose a service --');
      for ($i=0; $i<count($services); $i++)
      {
         if ($services[$i]['Name'] != 'root')
         {
            $results[$services[$i]['ID']] = $services[$i]['Name'];
         }
      }
      
      return $results;
   }



   /* ====================================================================
      Below this line the functions have not been converted.
      ==================================================================== */


   // --------------------------------------------------------------------

   /**
    * Returns an array of IDs for all vendors in a service.
    *
    * It excludes products with a status of "discontinued" or "pending".
    * It also ensures that products have the same SiteID as the service.
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_prod_ids_in_service($cat_id)
   {
      $sql = 'SELECT pc.ProductID '.
             'FROM pr_product_service AS pc, pr_product AS p, '.
               'pr_service AS c, pr_product_site AS s '.
             'WHERE pc.ServiceID LIKE "'.$cat_id.'" '.
             'AND pc.ProductID = p.ProductID '.
             'AND p.ProductID = s.ProductID '.
             'AND c.ServiceID = pc.ServiceID '.
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
    * Returns the path of a product's service page
    *
    * This accesses the "pages" table directly.
    *
    * @access   public
    * @return   array
    */
   function get_service_page_path($cat_code, $site_id = '')
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


}

/* End of file vendor_services_model.php */
/* Location: ./system/modules/admin/models/vendor_services_model.php */