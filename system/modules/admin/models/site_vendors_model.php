<?php

class Site_vendors_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Site_vendors_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of vendors for the specified Site ID
    *
    * @access   public
    * @param    str      The Site ID
    * @return   array
    */
   function get_site_vendors($site_id)
   {
      $sql = 'SELECT s.ID, vs.Name AS ServiceName, s.ServiceDesc, s.URL, '.
               's.Status, v.VendorName, v.Address, v.VendorURL '.
             'FROM adm_site_vendor AS s '.
             'LEFT JOIN adm_vendor AS v '.
               'ON s.VendorID = v.ID '.
             'LEFT JOIN adm_vendor_service AS vs '.
               'ON s.ServiceID = vs.ID '.
             'WHERE s.SiteID = "'.$site_id.'" '.
             'ORDER BY v.VendorName';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
      return $vendors;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of site_vendors records for the specified vendor
    *  where the vendor is marked as current
    *
    * @access   public
    * @param    int      the Vendor ID
    * @param    str      Site ID of site to exclude from list
    * @return   array
    */
   function get_current_vendor_sites($vendor_id, $exclude = '')
   {
      $sql = 'SELECT sv.ID, sv.SiteID, vs.Name AS ServiceName, '.
               'sv.ServiceDesc, sv.URL, sd.Domain '.
             'FROM adm_site_vendor AS sv '.
             'LEFT JOIN adm_site AS s '.
               'ON sv.SiteID = s.ID '.
             'LEFT JOIN adm_site_domain AS sd '.
               'ON s.ID = sd.SiteID '.
             'LEFT JOIN adm_vendor_service AS vs '.
               'ON sv.ServiceID = vs.ID '.
             'WHERE sv.VendorID = '.$vendor_id.' '.
             'AND sv.Status = "current" '.
             'AND sd.PrimaryDomain = 1 ';
      if ($exclude != '')
      {
         $sql .= 'AND sv.SiteID != "'.$exclude.'" ';
      }
      $sql .= 'ORDER BY sd.Domain';

      $query = $this->read_db->query($sql);
      $clients = $query->result_array();
      
      return $clients;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of site_vendors records for the specified vendor
    *  where the vendor is marked as former
    *
    * @access   public
    * @param    int      the Vendor ID
    * @param    str      Site ID of site to exclude from list
    * @return   array
    */
   function get_former_vendor_sites($vendor_id, $exclude = '')
   {
      $sql = 'SELECT sv.ID, sv.SiteID, vs.Name AS ServiceName, '.
               'sv.ServiceDesc, sv.URL, sd.Domain '.
             'FROM adm_site_vendor AS sv '.
             'LEFT JOIN adm_site AS s '.
               'ON sv.SiteID = s.ID '.
             'LEFT JOIN adm_site_domain AS sd '.
               'ON s.ID = sd.SiteID '.
             'LEFT JOIN adm_vendor_service AS vs '.
               'ON sv.ServiceID = vs.ID '.
             'WHERE sv.VendorID = '.$vendor_id.' '.
             'AND sv.Status = "former" '.
             'AND sd.PrimaryDomain = 1 ';
      if ($exclude != '')
      {
         $sql .= 'AND sv.SiteID != "'.$exclude.'" ';
      }
      $sql .= 'ORDER BY sd.Domain';

      $query = $this->read_db->query($sql);
      $clients = $query->result_array();
      
      return $clients;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of site vendor data for the specified vendor ID
    *
    * @access   public
    * @return   array
    */
   function get_site_vendor_data($vendor_id)
   {
      $sql = 'SELECT sv.*, vs.Name AS ServiceName '.
             'FROM adm_site_vendor AS sv '.
             'LEFT JOIN adm_vendor_service AS vs '.
               'ON sv.ServiceID = vs.ID '.
             'WHERE sv.ID = "'.$vendor_id.'"';

      $query = $this->read_db->query($sql);
      $vendor = $query->row_array();
      
      return $vendor;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new site vendor record
    *
    * @access   public
    * @return   array
    */
   function insert_site_vendor($values)
   {
      $this->write_db->insert('adm_site_vendor', $values);
      $id = $this->write_db->insert_id();
      
      return $id;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an existing site vendor record
    *
    * @access   public
    * @return   array
    */
   function update_site_vendor($vendor_id, $values)
   {
      $this->write_db->where('ID', $vendor_id);
      $this->write_db->update('adm_site_vendor', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a site vendor record
    *
    * @access   public
    * @return   array
    */
   function delete_site_vendor($vendor_id)
   {
      $this->write_db->where('ID', $vendor_id);
      $this->write_db->limit(1);
      $this->write_db->delete('adm_site_vendor');

      return TRUE;
   }

}
