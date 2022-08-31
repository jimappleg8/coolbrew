<?php

class Search_Tags extends Controller {

   function Search_Tags()
   {
      parent::Controller();
      $this->load->helper('url');
      $this->load->library('session');
   }
   
    //-------------------------------------------------------------------------
   
   /**
    * Wrapper for main search tag.
    *
    */
   function search()
   {
      // (string) the collection to use for search results
      $collection = $this->tag->param(1);
      
      // (string) the file that the form should point to
      $action = $this->tag->param(2, $_SERVER['PHP_SELF']);
      
      // (string) the file that the form should point to
      $help = $this->tag->param(3, '/user_help.html');
      
      // reinstate the $_GET variable
      parse_str($_SERVER['QUERY_STRING'], $_GET);
      
//      echo '<pre>'; print_r($_GET); echo '</pre>';

      $get_array = array();
      $get_vars = '';
      $ch = '';
      $output = '';
      $search_url = 'http://172.16.10.9/search';
   
      $config = array();
      $config['site'] = $collection;
      $config['client'] = "rtag_frontend";
      $config['output'] = "xml_no_dtd";
      $config['proxystylesheet'] = "rtag_frontend";
      $config['proxyreload'] = "1";
   
      foreach ($config AS $key => $value)
      {
         $get_array[] = $key.'='.$value;
         unset($_GET[$key]);
      }
   
      foreach ($_GET AS $key => $value)
         $get_array[] = $key.'='.urlencode($value);

      $get_vars = implode('&', $get_array);
   
      $url = $search_url.'?'.$get_vars;

      $ch = curl_init($search_url);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($ch);
      curl_close($ch);
      
      $output = str_replace('search.html', $action, $output);
      $output = str_replace('/user_help.html', $help, $output);
   
      return $output;
   }
   
    //-------------------------------------------------------------------------
   
   /**
    * Get the Search Help page from the Google Mini
    *
    */
   function search_help()
   {
      $help = $_SERVER['PHP_SELF'];
      $get_array = array();
      $get_vars = '';
      $ch = '';
      $output = '';
      $search_url = 'http://172.16.10.9/user_help.html';
   
      $ch = curl_init($search_url);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($ch);
      curl_close($ch);

      $output = str_replace('user_help.html', $help, $output);
      $output = substr($output, strpos($output, '<!----------body------>'));
      $output = str_replace('</BODY></HTML>', '', $output);
      $output = str_replace('<BLOCKQUOTE>', '', $output);
      $output = str_replace('</BLOCKQUOTE>', '', $output);
      
      return $output;
   }

}

/* End of file tags.php */
/* Location: ./system/modules/search/controllers/tags.php */