<?php

class Settings_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Settings_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the Vendor ID and name of the primary registrar
    *
    * @access   public
    * @return   array
    */
   function get_primary_registrar()
   {
      $sql = 'SELECT adm_vendor.ID, adm_vendor.VendorName '.
             'FROM adm_settings, adm_vendor '.
             'WHERE adm_settings.Setting = "PrimaryRegistrar" '.
             'AND adm_vendor.ID = adm_settings.Value';

      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the Vendor ID and name of the primary DNS Vendor
    *
    * @access   public
    * @return   array
    */
   function get_primary_dns_vendor()
   {
      $sql = 'SELECT adm_vendor.ID, adm_vendor.VendorName '.
             'FROM adm_settings, adm_vendor '.
             'WHERE adm_settings.Setting = "PrimaryDNSVendor" '.
             'AND adm_vendor.ID = adm_settings.Value';

      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      return $result;
   }

}

?>