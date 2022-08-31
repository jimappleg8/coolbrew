<?php

class Sites extends Controller {

   function Sites()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'products'));
   }
   
   // --------------------------------------------------------------------

   /**
    * AJAX Adds a site to a product
    *
    */
   function add($product_id, $site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->model('Product_sites');
      
      $this->Product_sites->insert_product_site($product_id, $site_id);
   }
   
   // --------------------------------------------------------------------

   /**
    * AJAX Removes a site from a product
    *
    */
   function remove($product_id, $site_id) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Product_sites');
      
      $this->Product_sites->delete_product_site($product_id, $site_id);
   }


   
}
?>