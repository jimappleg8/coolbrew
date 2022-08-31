<?php

class Api_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Api_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the Google maps API key for the current site.
    *
    */
   function get_map_key()
   {
      $site_id = SITE_ID;
      $server_level = SERVER_LEVEL;

      // this will need to be rethought if we have any SSL sites
      $domain = 'http://'.$_SERVER['HTTP_HOST'];

      $sql = 'SELECT APIKey '.
             'FROM api_key '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ServerLevel = "'.$server_level.'" '.
             'AND ValidURL LIKE "'.$domain.'%" '.
             'AND Status = "active"';
      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      $key = ($query->num_rows() < 1) ? '' : $result['APIKey'];
      
      return $key;
   }

}

/* End of file api_model.php */
/* Location: ./system/modules/stores/models/api_model.php */