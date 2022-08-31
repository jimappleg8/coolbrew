<?php

class Dbupload extends Controller {

   function Dbupload()
   {
      parent::Controller();
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * Log out of the system
    *
    */
   function logout()
   {
      $this->load->helper('url');
      $this->session->unset_userdata('username');
      $this->session->unset_userdata('name');
      redirect('');
   }

   // --------------------------------------------------------------------
   
   /**
    * Display the "Sorry you don't have access" page.
    *
    */
   function sorry()
   {
      $this->collector->append_css_file('admin_adm');
      $this->collector->append_css_file('login');
      
      return $this->load->view('login/sorry', NULL, TRUE);
   }

}
?>