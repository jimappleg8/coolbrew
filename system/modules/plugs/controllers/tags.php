<?php

class Plugs_Tags extends Controller {

   function Plugs_Tags()
   {
      parent::Controller();   
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of meta data for the specified tell-a-friend widget
    *
    */
   function meta_data()
   {
      // (string) The tell code
      $name = $this->tag->param(1);
      
      // (string) The site ID
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) The language
      $language = $this->tag->param(3, 'en_US');

      $this->load->model('Tell');
      
      return $this->Tell->get_meta_data_by_name($site_id, $name, $language);
   }

}
?>