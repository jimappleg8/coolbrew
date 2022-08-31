<?php

class Utilities extends Controller {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Utilities()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'recipes'));
      $this->load->helper(array('url', 'menu'));

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Generates a search index from existing database entries
    *
    */
   function generate_index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Indexes');
      
      // get a list of all recipes for this site
      $sql = 'SELECT ID FROM rcp_recipe '.
             'WHERE SiteID = "'.$site_id.'"';
      $query = $this->read_db->query($sql);
      $recipes = $query->result_array();
      
      // go through each recipe, including ingredients and index
      foreach ($recipes AS $recipe)
      {
         echo "Indexing recipe ID = ".$recipe['ID']."... ";
         $this->Indexes->update_search_index($recipe['ID']);
         echo "done.<br>";
      }
   }

}
?>
