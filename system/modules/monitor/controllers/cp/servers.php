<?php

class Servers extends Controller {

   // --------------------------------------------------------------------

   /**
    * Array of Linux servers
    */
   var $lamps = array();

   /**
    * Array of Apache web servers
    */
   var $webs = array();

   /**
    * Array of MySQL database servers
    */
   var $dbs = array();

   
   // --------------------------------------------------------------------

   function Servers()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'monitor'));
      $this->load->helper(array('url', 'menu', 'date'));

      $this->lamps = array(
         0 => array(
            'name' => 'bolwebapp1',
            'server-stats' => 'http://bolwebapp1/tests/server-stats.php',
         ),
         1 => array(
            'name' => 'bolwebapp2',
            'server-stats' => 'http://bolwebapp2/tests/server-stats.php',
         ),
         2 => array(
            'name' => 'bolwebserv1',
            'server-stats' => 'http://bolwebserv1/tests/server-stats.php',
         ),
         3 => array(
            'name' => 'bolwebserv2',
            'server-stats' => 'http://bolwebserv2/tests/server-stats.php',
         ),
         4 => array(
            'name' => 'bolwebdb1',
            'server-stats' => 'http://bolwebdb1/tests/server-stats.php',
         ),
         5 => array(
            'name' => 'bolwebdb2',
            'server-stats' => 'http://bolwebdb2/tests/server-stats.php',
         ),
         6 => array(
            'name' => 'bolwebdb3',
            'server-stats' => 'http://bolwebdb3/tests/server-stats.php',
         ),
         7 => array(
            'name' => 'bolwebadmin1',
            'server-stats' => 'http://bolwebadmin1/tests/server-stats.php',
         ),
      );

      $this->webs = array(
         0 => array(
            'name' => 'WebApp1',
            'status' => 'http://bolwebapp1/server-status',
            'php-info' => 'http://bolwebapp1/phpinfo.php',
         ),
         1 => array(
            'name' => 'WebApp2',
            'status' => 'http://bolwebapp2/server-status',
            'php-info' => 'http://bolwebapp2/phpinfo.php',
         ),
         2 => array(
            'name' => 'WebServ1',
            'status' => 'http://bolwebserv1/server-status',
            'php-info' => 'http://bolwebserv1/phpinfo.php',
         ),
         3 => array(
            'name' => 'WebServ2',
            'status' => 'http://bolwebserv2/server-status',
            'php-info' => 'http://bolwebserv2/phpinfo.php',
         ),
         4 => array(
            'name' => 'WebDB1',
            'status' => 'http://bolwebdb1/server-status',
            'php-info' => 'http://bolwebdb1/phpinfo.php',
         ),
         5 => array(
            'name' => 'WebDB2',
            'status' => 'http://bolwebdb2/server-status',
            'php-info' => 'http://bolwebdb2/phpinfo.php',
         ),
         6 => array(
            'name' => 'WebDB3',
            'status' => 'http://bolwebdb3/server-status',
            'php-info' => 'http://bolwebdb3/phpinfo.php',
         ),
         7 => array(
            'name' => 'WebAdmin1',
            'status' => 'http://bolwebadmin1/server-status',
            'php-info' => 'http://bolwebadmin1/phpinfo.php',
         ),
      );

      $this->dbs = array(
         0 => array(
            'name' => 'WebDB1 Port 3306',
            'hostname' => 'bolwebdb1:3306',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
         1 => array(
            'name' => 'WebDB1 Port 3307',
            'hostname' => 'bolwebdb1:3307',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
         2 => array(
            'name' => 'WebDB2 Port 3306',
            'hostname' => 'bolwebdb2:3306',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
         3 => array(
            'name' => 'WebDB2 Port 3307',
            'hostname' => 'bolwebdb2:3307',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
 /*        4 => array(
            'name' => 'WebDB3 Port 3306',
            'hostname' => 'bolwebdb3:3306',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
         5 => array(
            'name' => 'WebDB3 Port 3307',
            'hostname' => 'bolwebdb3:3307',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
         6 => array(
            'name' => 'Intranet1',
            'hostname' => 'bolintranet1:3306',
            'username' => 'root',
            'password' => 'tr33Cr0w',
         ),
*/
      );
   }
   
   // --------------------------------------------------------------------

   /**
    * Main monitoring page
    *
    */
   function index()
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
         
      $this->load->library('Lamp_server');
      $this->load->library('Apache_server');
      $this->load->library('Mysql_server');
      
      // get the Linux server information
//      for ($i=0; $i<count($this->lamps); $i++)
//      {
//         $this->lamp_server->get_server_info($this->lamps[$i]);
//      }

      // get the Apache server information
      for ($i=0; $i<count($this->webs); $i++)
      {
         $this->apache_server->get_server_info($this->webs[$i]);
      }

      // get the MySQL server information
      for ($i=0; $i<count($this->dbs); $i++)
      {
         $this->mysql_server->get_server_info($this->dbs[$i]);
      }

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Servers');
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['lamps'] = $this->lamps;
      $data['webs'] = $this->webs;
      $data['dbs'] = $this->dbs;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/servers/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * AJAX function to return the stats on a LAMP server
    *
    */
   function ajax_lamp($server_id)
   {
      $this->load->library('Lamp_server');
      
      // get the Linux server information
      $this->lamp_server->get_server_info($this->lamps[$server_id]);

      $data['lamp'] = $this->lamps[$server_id];
      
      $this->load->vars($data);
   	
      echo $this->load->view('cp/servers/lamp', NULL, TRUE);
      exit;
   }

}