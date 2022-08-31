<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once 'HTTP/Request.php';

class Scrape {

   var $html = '';
   var $error = '';
   
   // --------------------------------------------------------------------

   /**
    * Class contructor
    */
   function Scrape()
   {
      
   }

   // --------------------------------------------------------------------

   /**
    * Gets the requested web page
    *
    * @access   private
    * @param    string   the url to retrieve
    * @return   void
    */
   function get_url($url, $proxy = '', $proxy_port = '')
   {
      $r = new HTTP_Request($url);

      if ($proxy != "")
      {
         $r->setProxy($proxy, $proxy_port);
      }

      $response = $r->sendRequest();

      if (!PEAR::isError($response))
      {
         $this->html = $r->getResponseBody();
         return $this->html;
      }
      else
      {
         $this->error = $response->getMessage();
         return FALSE;
      }
   }

   // --------------------------------------------------------------------

   /**
    * Gets the requested web page using CURL
    *
    * @access   private
    * @param    string   the url to retrieve
    * @return   void
    */
   function get_curl($url, $proxy = '', $proxy_port = '')
   {
      $ch = curl_init($url);
      
      $headers = array(
         'Accept: application/xml',
         'Content-Type: application/xml',
      );

      if ($proxy != "")
      {
         curl_setopt($ch, CURLOPT_PROXY, $proxy.':'.$proxy_port);
      }
      curl_setopt($ch, CURLOPT_USERPWD, 'japplega:kl33nex');
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

      $page = curl_exec($ch);
      curl_close($ch);

      if ($page)
      {
         $this->html = $page;
         return $this->html;
      }
      else
      {
         return FALSE;
      }
   }

}

?>