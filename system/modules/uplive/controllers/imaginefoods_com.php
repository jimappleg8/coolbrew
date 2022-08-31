<?php

/**
 * Upload script for ImagineFoods.com
 *
 * This script encodes everything we have learned about how to make the site
 * live including what files and database tables need to be moved.
 *
 */

class Imaginefoods_com extends Controller {

   function Imaginefoods_com()
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
   	
      return $this->load->view('notes/if-upstage', NULL, TRUE);
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
   	
      return $this->load->view('notes/if-uplive', NULL, TRUE);
   }
   
   // -------------------------------------------------------------------------
   
   function upstage_database()
   {
      $this->load->model('Drupal_imagine');
      $this->Drupal_imagine->move_tables('dev', 'stage');
   }

   // -------------------------------------------------------------------------
   
   function uplive_database()
   {
      $this->load->model('Drupal_imagine');
      $this->Drupal_imagine->move_tables('stage', 'live');
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
   
   // -------------------------------------------------------------------------

   /**
    * 2013-03-08
    * Spammers attacked the IF site and filled the database with thousands of 
    * nodes with span content. This script goes through and tries to remove the 
    * damage as well as deleting the users that caused it. For good measure, it
    * also keeps a record of IP addresses of users that were removed.
    *
    * http://webadmin:8888/admin/uplive.php/imaginefoods_com/clean_database
    *
    */
   function clean_database()
   {
      $this->load->model('Drupal_imagine');
//      $this->Drupal_imagine->remove_recipe_spam();
      $this->Drupal_imagine->remove_recipe_lab_spam();
   }

   // -------------------------------------------------------------------------

   /**
    * 2013-03-08
    * Takes the list of IP addresses saved by the previous script and processes
    * them for use in the blacklist.
    *
    */
   function clean_ips()
   {
      $this->load->model('Drupal_imagine');
      $this->Drupal_imagine->process_ips();
   
   }

}

// end of uplive/controllers/earthsbest.php