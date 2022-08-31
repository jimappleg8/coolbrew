<?php

class Sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $error_msg = '';
   
   // --------------------------------------------------------------------

   function Sites_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function valid_site_id($site_id, $is_error)
   {
      // see if an error has already been thrown
      if ($is_error == TRUE)
      {
         return TRUE;
      }
      
      if ($site_id == '')
      {
         $this->error_msg = 'error: the site id is missing.';
         return FALSE;
      }
      
      $sql = 'SELECT s.ID AS SiteID '.
             'FROM adm_site AS s '.
             'WHERE s.ID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      $site = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         $this->error_msg = 'error: the site id is invalid.';
         return FALSE;
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand name for a given site ID.
    *
    */
   function get_brand_name($site_id)
   {
      $this->load->database('read');
      
      $sql = 'SELECT adm_brand.Name '.
             'FROM adm_site, adm_brand, adm_site_brand '.
             'WHERE adm_site.ID LIKE "'.$site_id.'" '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID';
      $query = $this->db->query($sql);
      $brand = $query->row_array();
      
      if ( ! isset($brand['Name']))
         $name = "this brand's";
      else
         $name = $brand['Name'];
      
      return $name;
   }

}

/* End of file sites_model.php */
/* Location: ./system/modules/api/models/v1/sites_model.php */