<?php

/** 
 * Remote File Helper
 *
 */

// ----------------------------------------------------------------------

/*
 * @return boolean
 * @param  string $link
 * @desc   function for testing a website/URI for availability (HTTP-Code: 200)
 */
function url_exists($url)
{       
   $url_parts = @parse_url($url);

   if (empty($url_parts['host']))
   {
      return FALSE;
   }

   if ( ! empty($url_parts['path']))
   {
      $documentpath = $url_parts['path'];
   }
   else
   {
      $documentpath = '/';
   }

   if ( ! empty($url_parts['query']))
   {
      $documentpath .= '?'.$url_parts['query'];
   }

   $host = $url_parts['host'];
   $port = (isset($url_parts['port'])) ? $url_parts['port'] : '';
   // Now (HTTP-)GET $documentpath at $host;

   if (empty($port))
   {
      $port = '80';
   }
  
   $socket = @fsockopen( $host, $port, $errno, $errstr, 30 );
   if ( ! $socket)
   {
      return FALSE;
   }
   else
   {
      fwrite ($socket, 'HEAD '.$documentpath." HTTP/1.0\r\nHost: $host\r\n\r\n");
      $http_response = fgets($socket, 22);
           
      if (ereg('200 OK', $http_response, $regs))
      {
         fclose($socket);
         return TRUE;
      }
      else
      {
         //echo 'HTTP-Response: '.$http_response.'<br>';
         return FALSE;
      }
   }
}

?>