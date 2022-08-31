<?php

class Vendors_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Vendors_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of vendor records
    *
    * @access   public
    * @return   array
    */
   function get_vendors($site_id)
   {
      $sql = 'SELECT * FROM adm_vendor '.
             'ORDER BY VendorName';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
      return $vendors;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of vendors for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_site_vendors($site_id)
   {
      $sql = 'SELECT s.ID, s.Service, s.ServiceDesc, s.URL, '.
               'v.VendorName, v.Address, v.VendorURL '.
             'FROM adm_site_vendor AS s, adm_vendor AS v '.
             'WHERE s.VendorID = v.ID '.
             'AND s.SiteID = "'.$site_id.'" '.
             'ORDER BY v.VendorName';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
      return $vendors;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array o vendor data for the specified vendor ID
    *
    * @access   public
    * @return   array
    */
   function get_site_vendor_data($vendor_id)
   {
      $sql = 'SELECT * '.
             'FROM adm_site_vendor '.
             'WHERE ID = "'.$vendor_id.'"';

      $query = $this->read_db->query($sql);
      $vendor = $query->row_array();
      
      return $vendor;
   }

   // --------------------------------------------------------------------

   /**
    * Returns vendor list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_vendors_list()
   {
      $sql = 'SELECT ID, VendorName FROM adm_vendor '.
             'ORDER BY VendorName';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();

      $results = array(''=>'-- choose a vendor --');
      for ($i=0; $i<count($vendors); $i++)
      {
         $results[$vendors[$i]['ID']] = $vendors[$i]['VendorName'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the ID of the "Unknown Vendor" record
    *
    * @access   public
    * @return   array
    */
   function get_unknown_vendor_id()
   {
      $sql = 'SELECT ID FROM adm_vendor '.
             'WHERE VendorName = "Unknown Vendor"';

      $query = $this->read_db->query($sql);
      $vendor = $query->row_array();

      return $vendor['ID'];
   }

}

?>