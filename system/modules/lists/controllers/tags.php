<?php

class Lists_Tags extends Controller {

	function Lists_Tags()
	{
		parent::Controller();	
	}
	
   //-------------------------------------------------------------------------
   
   /**
    * Return all items in a list
    *
    */
   function complete()
   {
      // (string) The view name in case we want to override the default
      $list_code = $this->tag->param(1, '');

      // (int) The level that should be considered the root
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(4, $list_code.'-complete');
      
      $this->load->helper('url');
      $this->load->model('Items');

      $list = $this->Items->get_list_items_by_code($list_code, $site_id);

      $data['list'] = $list;
   	
      echo $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------
   
   /**
    * Returns a random selection from the list
    * 
    */
   function random()
   {
      // (string) The view name in case we want to override the default
      $list_code = $this->tag->param(1, 'default');

      // (int) The level that should be considered the root
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(4, $list_code.'-random');
      
      $this->load->helper('url');
      $this->load->model('Items');

      $list = $this->Items->get_list_items_by_code($list_code, $site_id);
      
      $max = count($list) - 1;
      $listing = $list[mt_rand(0, $max)];

      $data['listing'] = $listing;
   	
      echo $this->load->view($tpl, $data, TRUE);
   }

}
?>