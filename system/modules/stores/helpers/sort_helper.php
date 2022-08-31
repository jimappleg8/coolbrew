<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * CoolBrew Sort Helpers
 *
 */

// --------------------------------------------------------------------

/**
 * Sorts a 2-dimensional array and allows you to add in multiple key
 * names, it will then sort them in that order, the purpose is, if the
 * first one is the same throughout, it will jump to the next key.
 *
 * Taken from the PHP sort comments:
 *    http://us3.php.net/manual/en/function.sort.php
 * 
 */
function mu_sort($array, $key_sort) 
{
   if (empty($array))
      return $array;
      
   $n = 0;
   $key_sorta = explode(",", $key_sort); 

   $keys = array_keys($array[0]);

   // sets the $key_sort vars to the first
   for ($m=0; $m<count($key_sorta); $m++)
   {
      $nkeys[$m] = trim($key_sorta[$m]);
   }

   $n += count($key_sorta);   // counter used inside loop

   // this loop is used for gathering the rest of the 
   // key's up and putting them into the $nkeys array
   for ($i=0; $i<count($keys); $i++)
   {
      // quick check to see if key is already used.
      if ( ! in_array($keys[$i], $key_sorta))
      {
         // set the key into $nkeys array
         $nkeys[$n] = $keys[$i];
         // add 1 to the internal counter
         $n += "1"; 
      }
   }
   // this loop is used to group the first array [$array]
   // into it's usual clumps
   $output = array();
   for ($u=0, $array_cnt=count($array); $u<$array_cnt; $u++)
   {
      // set array into var, for easier access.
      $arr = $array[$u];

      // this loop is used for setting all the new keys 
      // and values into the new order
      for ($s=0; $s<count($nkeys); $s++)
      {
         // set key from $nkeys into $k to be passed into multidimensional array
         $k = $nkeys[$s];
         // sets up new multidimensional array with new key ordering
         $output[$u][$k] = $array[$u][$k]; 
      }
   }
   if ( ! empty($output))
   {
      sort($output);
   }
   return $output;
}


?>