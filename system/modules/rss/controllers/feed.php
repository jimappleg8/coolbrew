<?php

class Feed extends Controller {

   var $items_per_page = 20;

   function Feed()
   {
      parent::Controller();
      $this->load->helper('url');
   }

   // -------------------------------------------------------------------

   function index()
   {
      $this->view_feed();
   }

   // -------------------------------------------------------------------

   function view_feed($offset = 0)
   {
      if (is_numeric($offset))
      {
         $offset = floor($offset);
      }
      else
      {
         $offset = 0;
      }
      $this->load->model('ItemModel');

      $data = $this->ItemModel->get_items($offset, $this->items_per_page);
      $data['per_page'] = $this->items_per_page;

      $this->load->view('feed/view', $data);
   }
   
   // -------------------------------------------------------------------

   function update_all()
   {
      $this->load->library('simplepie');

      $this->simplepie->cache_location = BASEPATH .'cache';
      
      $this->load->library('HTMLPurifier');
      $purifier_config = HTMLPurifier_Config::createDefault();
      $purifier_config->set('Cache', 'SerializerPath', BASEPATH.'cache');

      $this->load->model('FeedModel');
      $this->load->model('ItemModel');

      $feeds = $this->FeedModel->get_feed_update_urls();

      foreach ($feeds as $feed_id => $feed_url)
      {
         $this->simplepie->set_feed_url($feed_url);
         $this->simplepie->init();
         $items = $this->simplepie->get_items();
         foreach ($items as $item)
         {
            $this->ItemModel->load($feed_id, md5($item->get_id()));

            $permalink = $item->get_permalink();
            $scheme = @parse_url($permalink, PHP_URL_SCHEME);
            $allowed_schemes = array('http', 'https', 'ftp');
            if ($scheme === FALSE || ! in_array($scheme, $allowed_schemes))
            {
               $permalink = '';
            }
            $this->ItemModel->link = $permalink;

            $this->ItemModel->title = html_entity_decode($item->get_title());
            $this->ItemModel->text = $this->htmlpurifier->purify($item->get_content(), $purifier_config);
            $this->ItemModel->save();
         }
      }
   }
   
}
?>