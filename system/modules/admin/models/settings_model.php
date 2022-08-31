<?php

class Settings_model extends Model {

   function Settings_model()
   {
      parent::Model();
      $this->load->database('read');
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

      $query = $this->db->query($sql);
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

      $query = $this->db->query($sql);
      $result = $query->row_array();
      
      return $result;
   }

}

?>