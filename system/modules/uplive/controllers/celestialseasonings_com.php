<?php

/**
 * Upload script for CelestialSeasonings.com
 *
 * This script encodes everything we have learned about how to make the site
 * live including what files and database tables need to be moved.
 *
 */

class Celestialseasonings_com extends Controller {

   function Celestialseasonings_com()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'uplive'));
      $this->load->helper(array('url', 'menu'));
   }
	
   // -------------------------------------------------------------------------

   function upstage()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('docs');
         
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Uplive');
      $data['submenu'] = get_submenu('Deploy');
      $data['admin'] = $admin; // errors and messages
      
      $data['today'] = date('Ymd');

      $this->load->vars($data);
   	
      return $this->load->view('notes/cs-upstage', NULL, TRUE);
   }
   
   // -------------------------------------------------------------------------

   function uplive()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('docs');
         
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Uplive');
      $data['submenu'] = get_submenu('Deploy');
      $data['admin'] = $admin; // errors and messages
      
      $data['today'] = date('Ymd');

      $this->load->vars($data);
   	
      return $this->load->view('notes/cs-uplive', NULL, TRUE);
   }
   
   // -------------------------------------------------------------------------
   
   function upstage_database()
   {
      $this->load->model('Drupal_celestialseasonings');
      $this->Drupal_celestialseasonings->move_tables('dev', 'stage');
   }
   
   // -------------------------------------------------------------------------
   
   function uplive_database()
   {
      $this->load->model('Drupal_celestialseasonings');
      $this->Drupal_celestialseasonings->move_tables('stage', 'live');
   }

   // -------------------------------------------------------------------------
   
   /**
    *  This function is incomplete
    */
   function _backup_database($server)
   {
//      $this->load->model('Drupal_celestialseasonings');
//      $db_def = $this->Drupal_celestialseasonings->{$server}_db;

      $command = '/usr/bin/mysqldump '.
                   '--opt '.
                   '--host='.$dbhost.' '.
                   '--user='.$dbuser.' '.
                   '--password='.$dbpwd.' '.
                   $dbname.' '.
                   '> '.$dumpfile;
      
//      passthru($command);
   }

   // -------------------------------------------------------------------------

   function _send_query($sql)
   {
      if (FALSE === $result = $this->db->query($sql))
      {
         $msg .= "Unable to query database\n";
         $msg .= mysql_error();
         $subject = "The EB deployment failed";
         send_message($hostname, $subject, $msg);
         echo $msg;
         exit;
      }
      return $result;
   }
   
}

// end of uplive/controllers/earthsbest.php