<?php

class Apache_server {

   // --------------------------------------------------------------------

   /**
    * Constructor
    *
    */
   function Apache_server()
   {
   
   }
   
   // --------------------------------------------------------------------

   /**
    * Adds server data to the server record provided.
    *
    */
   function get_server_info(&$server)
   {
      $ch = curl_init($server['status']);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
      curl_setopt($ch, CURLOPT_HEADER, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $server_data = curl_exec($ch);
      curl_close($ch);

      $server['up'] = ($server_data) ? TRUE : FALSE;

      $server['percent-disk-used'] = 'unknown';
      $server['percent-memory-used'] = 'unknown';
      $server['percent-cpu-used'] = 'unknown';

      if (isset($server['server-stats']))
      {
         $ch = curl_init($server['server-stats']);
         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
         curl_setopt($ch, CURLOPT_HEADER, 1);
         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
         $server_data = curl_exec($ch);
         curl_close($ch);
         
         $lines = explode('|', $server_data);
         $disk = explode(':', $lines[3]);
         $memory = explode(':', $lines[4]);
         $cpu = explode(':', $lines[5]);

         $server['percent-disk-used'] = $disk[1];
         $server['percent-memory-used'] = $memory[1];
         $server['percent-cpu-used'] = $cpu[1];
      }
      
   }

}