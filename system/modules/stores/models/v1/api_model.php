<?php

require 'HTTP/Request.php';

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
    * Returns the Google maps API key for the specified site.
    *
    */
   function get_map_key($site_id, $level, $host)
   {
      $url = 'http://'.$host;
      
      $sql = 'SELECT APIKey '.
             'FROM api_key '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ServerLevel = "'.$level.'" '.
             'AND ValidURL LIKE "'.$url.'%" '.
             'AND Status = "active"';
      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      $key = ($query->num_rows() < 1) ? '' : $result['APIKey'];
      
      return $key;
   }

}

/* End of file api_model.php */
/* Location: ./system/modules/stores/models/api_model.php */