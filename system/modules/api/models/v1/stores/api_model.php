<?php

class Api_model extends Model {

   function Api_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      // we use the "write" database because it points to a specific server
      // where the "read" database should stay "localhost" to balance load.
      $this->read_db = $this->load->database($level.'-write', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns the Google maps API key for the current site.
    *
    */
   function get_map_key($site_id = '', $server_level = '')
   {
      $site_id = ($site_id != '') ? $site_id : SITE_ID;
      $server_level = ($server_level != '') ? $server_level : SERVER_LEVEL;
      
      $sql = 'SELECT APIKey FROM api_key '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ServerLevel = "'.$server_level.'"';
      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
      $key = ($query->num_rows() < 1) ? '' : $result['APIKey'];
      
      return $key;
   }

}

/* End of file api_model.php */
/* Location: ./system/modules/api/models/v1/api_model.php */