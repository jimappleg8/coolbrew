<?php

class Rtags extends Controller {

   function Rtags()
   {
      parent::Controller();
      $this->load->library('session');
   }
   
    //-------------------------------------------------------------------------
   
   /**
    * Wrapper for main search remote tag.
    *
    */
   function search()
   {
      $this->load->library('Rtag');

      // (string) the collection to use for search results
      $collection = $this->rtag->param('collection');
      
      // (string) the file that the form should point to
      $action = $this->rtag->param('action', 'search.html');

      $post_array = array();
      $get_vars = '';
      $ch = '';
      $output = '';
      $rtag_url = 'http://172.16.10.9/search';
   
      $config = array();
      $config['site'] = $collection;
      $config['client'] = "rtag_frontend";
      $config['output'] = "xml_no_dtd";
      $config['proxystylesheet'] = "rtag_frontend";
      $config['proxyreload'] = "1";
   
      foreach ($config AS $key => $value)
      {
         $post_array[] = $key.'='.$value;
         unset($_POST[$key]);
      }
   
      foreach ($_POST AS $key => $value)
         $post_array[] = $key.'='.urlencode($value);

      $get_vars = implode('&', $post_array);
   
      $url = $rtag_url.'?'.$get_vars;

      $ch = curl_init($rtag_url);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($ch);
      curl_close($ch);
      
      $output = str_replace('search.html', $action, $output);
      
      echo $output;
   }
   
   
}

/* End of file rtags.php */
/* Location: ./system/modules/search/controllers/v1/rtags.php */