<?php

class Blocks_Tags extends Controller {

	function Blocks_Tags()
	{
		parent::Controller();	
	}
	
   //-------------------------------------------------------------------------
   
   /**
    * Return the contents of the specified block
    *
    */
   function insert()
   {
      // (string) The view name in case we want to override the default
      $block_name = $this->tag->param(1, '');

      // (string) The site from which the block should be pulled
      $site_id = $this->tag->param(2, SITE_ID);
            
      // (string) The language code for this block
      $language = $this->tag->param(3, 'en_US');

      $this->load->helper('url');
      $this->load->model('Blocks');

      $block = $this->Blocks->get_block_by_code($block_name, $site_id, $language);

      echo $block['Block'];
   }

}
?>