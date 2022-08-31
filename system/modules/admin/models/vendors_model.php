<?php

class Vendors_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Vendors_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of vendor data for the specified vendor ID
    *
    * @access   public
    * @return   array
    */
   function get_vendor_data($vendor_id)
   {
      $sql = 'SELECT * '.
             'FROM adm_vendor '.
             'WHERE ID = "'.$vendor_id.'"';

      $query = $this->read_db->query($sql);
      $vendor = $query->row_array();
      
      return $vendor;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of all vendor records
    *
    * @access   public
    * @return   array
    */
   function get_vendors()
   {
      $sql = 'SELECT * FROM adm_vendor '.
             'ORDER BY VendorName';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
      return $vendors;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of vendor records that are assigned to a service.
    *
    * @access   public
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_vendors_in_service()
   {
      $this->CI =& get_instance();
      $this->CI->load->model('Site_domains');
      
      $sql = 'SELECT v.ID, v.VendorName, vs.ID AS ServiceID, sv.Status, '.
               'COUNT(sv.Status) AS Clients '.
             'FROM adm_vendor AS v '.
             'LEFT JOIN ( '.
               'SELECT VendorID, ServiceID '.
               'FROM adm_site_vendor '.
               ') AS vsl '.
               'ON v.ID = vsl.VendorID '.
             'LEFT JOIN adm_vendor_service AS vs '.
               'ON vs.ID = vsl.ServiceID '.
             'LEFT JOIN ( '.
               'SELECT DISTINCT VendorID, ServiceID, Status '.
               'FROM adm_site_vendor '.
               'WHERE Status = "current" '.
               ') AS sv '.
               'ON (sv.VendorID = vsl.VendorID '.
               'AND sv.ServiceID = vsl.ServiceID) '.
             'WHERE vsl.ServiceID IS NOT NULL '.
             'AND v.Hidden = 0 '.
             'GROUP BY v.ID, v.VendorName, vs.ID, sv.Status '.
             'ORDER BY v.VendorName ASC';
      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
//      echo '<pre>'; print_r($vendors); echo '</pre>'; exit;
      
      return $vendors;
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

   // --------------------------------------------------------------------

   /**
    * Inserts a new vendor record
    *
    * @access   public
    * @return   array
    */
   function insert_vendor($values)
   {
      $this->write_db->insert('adm_vendor', $values);
      $id = $this->write_db->insert_id();
      
      return $id;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an existing vendor record
    *
    * @access   public
    * @return   array
    */
   function update_vendor($vendor_id, $values)
   {
      $this->write_db->where('ID', $vendor_id);
      $this->write_db->update('adm_vendor', $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a vendor record
    *
    * @access   public
    * @return   array
    */
   function delete_vendor($vendor_id)
   {
      $this->write_db->where('ID', $vendor_id);
      $this->write_db->limit(1);
      $this->write_db->delete('adm_vendor');

      return TRUE;
   }

}

?>