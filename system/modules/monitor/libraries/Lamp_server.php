<?php

class Lamp_server {

   // --------------------------------------------------------------------

   /**
    * Constructor
    *
    */
   function Lamp_server()
   {
   
   }
   
   // --------------------------------------------------------------------

   /**
    * Adds server data to the server record provided.
    *
    */
   function get_server_info(&$server)
   {
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
         
         if ($server_data)
         {
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

}