<?php

class Keys_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   var $error_msg = '';
   
   // --------------------------------------------------------------------

   function Keys_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('production', TRUE);
      $this->write_db = $this->load->database('production', TRUE);
      
      $this->load->helper('v1/resources');
   }

   // --------------------------------------------------------------------

   /**
    * Checks to see if the supplied key is valid. If so, it returns the
    *   server level; if not, if returns false.
    *
    * @access   public
    * @return   string
    */
   function valid_key($key, $is_error)
   {
      // see if an error has already been thrown
      if ($is_error == TRUE)
      {
         return 'live';
      }
      
      if ($key == '')
      {
         $this->error_msg = 'error: the key is missing.';
         return FALSE;
      }

      // check first to see if the Key exists in the database
      $sql = 'SELECT * FROM api_assigned_key '.
             'WHERE APIKey = "'.$key.'"';
      $query = $this->read_db->query($sql);
      $client = $query->row_array();

      if ( ! empty($client))
      {
         // check to make sure the key matches the URL
         // At this point, there does not seem to be a reliable
         // way to get the referrer except by using Javascript
         // like Google does. So, I am skipping this step for now.
         $level = $client['ServerLevel'];
         return $level;
      }
      else
      {
         $this->error_msg = 'error: the supplied key is not valid.';
         return FALSE;
      }
   }

   // --------------------------------------------------------------------

   /**
    * Generates a unique API key and returns it. If the URL already has
    *   a key assigned to it, it returns the assigned key.
    *
    * @access   public
    * @return   string
    */
   function generate_key($values)
   {
      $unique = FALSE;

      // check first to see if the URL/level is already in the database
      $sql = 'SELECT * FROM api_assigned_key '.
             'WHERE ValidURL = "'.$values['ValidURL'].'" '.
             'AND ServerLevel = "'.$values['ServerLevel'].'"';
      $query = $this->read_db->query($sql);

      if ($query->num_rows() == 0)
      {
         while ($unique == FALSE)
         {
            $key = $this->_propose_key(12, 12);
         
            $sql = 'SELECT * FROM api_assigned_key '.
                   'WHERE APIKey = "'.$key.'"';
            $query = $this->read_db->query($sql);

            $unique = ($query->num_rows() == 0) ? TRUE : FALSE;
         }
      }
      else
      {
         $values = $query->row_array();
         $key = $values['Key'];
      }
      
      return $key;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a randomly generated string of length between $minlength 
    *  and $maxlength inclusively.
    *
    * @access   private
    * @param    string    The minimum length
    * @param    string    The maximum length
    * @return   string
    */
   function _propose_key($minlength, $maxlength)
   {
      $key = '';
      
      $charset = "abcdefghijklmnopqrstuvwxyz";
      $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
      $charset .= "0123456789";

      if ($minlength > $maxlength) 
      {
         $length = mt_rand ($maxlength, $minlength);
      }
      else
      {
         $length = mt_rand ($minlength, $maxlength);
      }

      for ($i=0; $i<$length; $i++) 
      {
         $key .= $charset[(mt_rand(0,(strlen($charset)-1)))];
      }
      return $key;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new key into the database
    *
    * @access   public
    * @param    string    The record values
    * @return   array
    */
   function insert_key($values)
   {
      // check first to see if the URL/level is already in the database
      $sql = 'SELECT * FROM api_assigned_key '.
             'WHERE ValidURL = "'.$values['ValidURL'].'" '.
             'AND ServerLevel = "'.$values['ServerLevel'].'"';
      $query = $this->read_db->query($sql);

      if ($query->num_rows() == 0)
      {
         $this->write_db->insert('api_assigned_key', $values);
      }
      return $values;
   }
   

}

/* End of file keys_model.php */
/* Location: ./system/modules/api/models/v1/keys_model.php */