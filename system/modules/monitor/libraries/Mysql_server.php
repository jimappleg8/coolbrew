<?php

class Mysql_server {

   // --------------------------------------------------------------------
   
   /**
    * CodeIgniter/Coolbrew object to access CoolBrew libraries
    */
   var $CI;

   /**
    * The database connection for this instance
    */
   var $mysql_db;
   

   // --------------------------------------------------------------------

   /**
    * Constructor
    *
    */
   function Mysql_server()
   {
      $this->CI =& get_instance();
   }
   
   // --------------------------------------------------------------------

   /**
    * Sets up the connection for whatever function needs it.
    *
    */
   function _establish_connection($server)
   {
      $var_name = str_replace(' ', '', strtolower($server['name']));

      $config['hostname'] = $server['hostname'];
      $config['username'] = $server['username'];
      $config['password'] = $server['password'];
      $config['dbdriver'] = "mysql";
      $config['dbprefix'] = "";
      $config['active_r'] = TRUE;
      $config['pconnect'] = TRUE;
      $config['db_debug'] = TRUE;
      $config['cache_on'] = FALSE;
      $config['cachedir'] = ""; 
      $config['char_set'] = "utf8";
      $config['dbcollat'] = "utf8_general_ci";
      $this->$var_name = $this->CI->load->database($config, TRUE);
      
      return $var_name;
   }

   // --------------------------------------------------------------------

   /**
    * Adds server status data to the server record provided.
    *
    */
   function get_server_info(&$server)
   {
      $server['status'] = $this->_get_status($server);

   }

   // --------------------------------------------------------------------

   /**
    * Adds server status data to the server record provided.
    *
    */
   function _get_status($server)
   {
      $var_name = $this->_establish_connection($server);

      $sql = 'SHOW STATUS';
      $query = $this->$var_name->query($sql);
      $statuses = $query->result_array();
      
      $new_status = array();
      foreach ($statuses AS $status)
      {
         $new_status[$status['Variable_name']] = $status['Value'];
      }
      
      return $new_status;
   }

}