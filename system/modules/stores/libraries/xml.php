<?php

// ---------------------------------------------------------------------------
// xml.inc.php
//   written by Jim Applegate
//
// ---------------------------------------------------------------------------

global $_HCG_GLOBAL;

// ------------------------------------------------------------------------
// makeXMLTree()
//   used to process XML feed from IRI
//   Pulled from the php.net documentation comments:
//      http://us3.php.net/manual/en/ref.xml.php
//
// ------------------------------------------------------------------------
function makeXMLTree ($data)
{
   $output = array();

   $parser = xml_parser_create();

   xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
   //xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
   //xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'ISO-8859-1');
   xml_parse_into_struct($parser, $data, $values, $tags);
   xml_parser_free($parser);

   $hash_stack = array();
   
   foreach ($values as $key => $val) {

   switch ($val['type']) {
      case 'open':
         array_push($hash_stack, $val['tag']);
         if (isset($val['attributes']))
            $output = composeArray($output, $hash_stack, $val['attributes']);
         else
            $output = composeArray($output, $hash_stack);
         break;

      case 'close':
         array_pop($hash_stack);
         break;

      case 'complete':
          array_push($hash_stack, $val['tag']);
         $output = composeArray($output, $hash_stack, $val['value']);
         array_pop($hash_stack);

         // handle attributes
         if (isset($val['attributes'])) {
            while(list($a_k,$a_v) = each($val['attributes'])) {
               $hash_stack[] = $val['tag']."_attribute_".$a_k;
               $output = composeArray($output, $hash_stack, $a_v);
               array_pop($hash_stack);
            }
         }
         break;
      }
   }
   return $output;
}


// ------------------------------------------------------------------------
// composeArray()
//   works with makeXMLTree() and helps clean up the array that it creates
//   Pulled from the php.net documentation comments:
//      http://us3.php.net/manual/en/ref.xml.php
//
// ------------------------------------------------------------------------
function &composeArray($array, $elements, $value=array())
{
   global $_HCG_GLOBAL;
   
   $XML_LIST_ELEMENTS = $_HCG_GLOBAL['XML_LIST_ELEMENTS'];

   // get current element
   $element = array_shift($elements);

   // does the current element refer to a list
   if (in_array($element,$XML_LIST_ELEMENTS)) {
      // more elements?
      if (sizeof($elements) > 0) {
         $array[$element][sizeof($array[$element])-1] = &composeArray($array[$element][sizeof($array[$element])-1], $elements, $value);
      } else { // if (is_array($value))
         $array[$element][sizeof($array[$element])] = $value;
      }
   } else {
      // more elements?
      if (sizeof($elements) > 0) {
         $array[$element] = &composeArray($array[$element], $elements, $value);
      } else {
         $array[$element] = $value;
      }
   }
   return $array;
}


// ------------------------------------------------------------------------
// mu_sort()
//   Sorts a 2-dimensional array and allows you to add in multiple key
//   names, it will then sort them in that order, the purpose is, is that 
//   if the first one is the same throughout, it will jump to the next key.
//   Taken from the PHP sort comments:
//      http://us3.php.net/manual/en/function.sort.php
//
// ------------------------------------------------------------------------
function mu_sort($array, $key_sort) 
{
   $key_sorta = explode(",", $key_sort); 

   $keys = array_keys($array[0]);

   // sets the $key_sort vars to the first
   for ($m=0; $m < count($key_sorta); $m++) {
      $nkeys[$m] = trim($key_sorta[$m]);
   }

   $n += count($key_sorta);   // counter used inside loop

   // this loop is used for gathering the rest of the 
   // key's up and putting them into the $nkeys array
   for ($i=0; $i < count($keys); $i++) { // start loop
      // quick check to see if key is already used.
      if(!in_array($keys[$i], $key_sorta)){
         // set the key into $nkeys array
         $nkeys[$n] = $keys[$i];
         // add 1 to the internal counter
         $n += "1"; 
      }
   }
   // this loop is used to group the first array [$array]
   // into it's usual clumps
   for ($u=0;$u<count($array); $u++) { // start loop #1

      // set array into var, for easier access.
      $arr = $array[$u];

      // this loop is used for setting all the new keys 
      // and values into the new order
      for ($s=0; $s<count($nkeys); $s++) {
         // set key from $nkeys into $k to be passed into multidimensional array
         $k = $nkeys[$s];
         // sets up new multidimensional array with new key ordering
         $output[$u][$k] = $array[$u][$k]; 
      }
   }
   if (!empty($output)) {
      sort($output);
   }
   return $output;

}


?>