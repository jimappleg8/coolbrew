<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// +----------------------------------------------------------------------+
// | Filename: phpZipLocator.php                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) http://www.sanisoft.com                                |
// +----------------------------------------------------------------------+
// | License (c) This software is licensed under LGPL                     |
// +----------------------------------------------------------------------+
// | Description: A simple class for finding distances between two zip    |
// | codes, The distance calculation is based on Zipdy package found      |
// | at http://www.cryptnet.net/fsp/zipdy/ written by V. Alex Brennen     |
// | <vab@cryptnet.net>                                                   |
// | You can also do radius calculations to find all the zipcodes within  |
// | the radius of x miles                                                |
// +----------------------------------------------------------------------+
// | Authors: Dr Tarique Sani <tarique@sanisoft.com>                      |
// |          Girish Nair <girish@sanisoft.com>                           |
// +----------------------------------------------------------------------+
// | Adapted for use with the CoolBrew Framework by Jim Applegate        |
// +----------------------------------------------------------------------+
//
// $Id$

class Ziplocator
{

   var $CI;
    
   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Ziplocator()
   {
      $this->CI =& get_instance();

      $this->read_db = $this->CI->load->database('read', TRUE);
      $this->write_db = $this->CI->load->database('write', TRUE);
   }
    
   // ------------------------------------------------------------------------

   /**
    * Returns the distance in Miles between two zip codes, a zip code and another location (latitude and longitude), or two locations depending on how many parameters are passed.
    * 
    * This method returns the distance in Miles between two zip codes, if either of the zip codes is not found and error is returned
    *
    * @param      zipOne - The first zip code
    * @param      zipTwo - The second zip code
    * @global     none
    * @since      1.0
    * @access     public
    * @return     string
    * @update
    */
   function distance()
   {
      $num_params = func_num_args();
      if ($num_params == 2 || $num_params == 3)
      {
         $zipOne = func_get_arg(0);
         $sql = 'SELECT * FROM zipcodes_us WHERE zipcode='.$zipOne;
         $query = $this->read_db->query($sql);
         $zipOneRec = $query->row_array();
         if (count($zipOneRec) < 1)
         {
            return "First Zip Code not found";
         }
         else
         {
            $lat1 = $zipOneRec["latitude"];
            $lon1 = $zipOneRec["longitude"];
         }
      }
      if ($num_params == 2)
      {
         $zipTwo = func_get_arg(1);
         $sql = 'SELECT * FROM zipcodes_us WHERE zipcode='.$zipTwo;
         $query = $this->read_db->query($sql);
         $zipTwoRec = $query->row_array();
         if (count($zipTwoRec) < 1)
         {
            return "Second Zip Code not found";
         }
         else
         {
            $lat2 = $zipTwoRec["latitude"];
            $lon2 = $zipTwoRec["longitude"];
         }
      }
      if ($num_params == 3)
      {
         $lat2 = func_get_arg(1);
         $lon2 = func_get_arg(2);
      }
      if ($num_params == 4)
      {
         $lat1 = func_get_arg(0);
         $lon1 = func_get_arg(1);
         $lat2 = func_get_arg(2);
         $lon2 = func_get_arg(3);       
      }

      /* Convert all the degrees to radians */
      $lat1 = $this->deg_to_rad($lat1);
      $lon1 = $this->deg_to_rad($lon1);
      $lat2 = $this->deg_to_rad($lat2);
      $lon2 = $this->deg_to_rad($lon2);

      /* Find the deltas */
      $delta_lat = $lat2 - $lat1;
      $delta_lon = $lon2 - $lon1;

      /* Find the Great Circle distance */
      $temp = pow(sin($delta_lat/2.0),2) + cos($lat1) * cos($lat2) * pow(sin($delta_lon/2.0),2);

      $EARTH_RADIUS = 3956;
      $distance = $EARTH_RADIUS * 2 * atan2(sqrt($temp),sqrt(1-$temp));

      return $distance;
   }

   // ------------------------------------------------------------------------

   /**
    * Converts degrees to radians
    *
    * @param      deg - degrees
    * @global     none
    * @since      1.0
    * @access     private
    * @return     void
    * @update
    */
   function deg_to_rad($deg)
   {
      $radians = 0.0;
      $radians = $deg * M_PI/180.0;
      return($radians);
   }

   // ------------------------------------------------------------------------

   /**
    * Returns an array of zipcodes found with the radius supplied
    *
    * This method returns an array of zipcodes found with the radius supplied in miles, if the zip code is invalid an error string is returned
    *
    * @param      zip - The zip code
    * @param      radius - The radius in miles
    * @global     none
    * @since      1.0
    * @access     public
    * @return     array/FALSE
    * @update     date time
    */
   function inradius($zip, $radius)
   {
      $sql = 'SELECT * FROM zipcodes_us WHERE zipcode="'.$zip.'"';
      $query = $this->read_db->query($sql);
      $zipRec = $query->row_array();
      if (count($zipRec) < 1)
      {
         return FALSE;
      }
      else
      {
         $lat = $zipRec["latitude"];
         $lon = $zipRec["longitude"];

         $sql = 'SELECT zipcode FROM zipcodes_us '.
                'WHERE (POW((69.1*(longitude-"'.$lon.'")*'.
                'cos('.$lat.'/57.3)),"2")+'.
                'POW((69.1*(latitude-"'.$lat.'")),"2"))'.
                '<('.$radius.'*'.$radius.')';

         $query = $this->read_db->query($sql);
         $allZips = $query->result_array();
         if (count($allZips) > 0)
         {
            $i = 0;
            foreach ($allZips as $thisZip)
            {
               $zipArray[$i] = $thisZip['zipcode'];
               $i++;
            }
         }
      }
   return $zipArray;
   }

} // end class
?>