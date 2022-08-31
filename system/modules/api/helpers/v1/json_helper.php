<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/** 
 * JSON Helper
 *
 */

// ------------------------------------------------------------------------
   
/**
 * Try to ensure that the content is JSON compliant
 *
 * This function requires PHP 5.2.0 or higher
 *
 * @access	public
 * @param  mixed   the element to process
 * @return mixed
 */
function process_json_element($element)
{
   // make sure html entities are decoded as a base setting
   $element = html_entity_decode($element);
   
   $element = mb_convert_encoding($element, 'utf-8');
   $element = json_encode($element);
//   $element = preg_replace('/^([^[{].*)$/', '[$1]', $element);

   return $element;
}

// ------------------------------------------------------------------------

/**
 * Try to ensure that the content is JSON compliant
 *
 * @param  array   the elements to process
 * @return array
 */
function process_json_array($elements)
{
   if ( ! is_array($elements))
   {
      return process_json_element($elements);
   }
   
   foreach ($elements AS $key => $value)
   {
      $elements[$key] = process_json_element($value);
   }
   return $elements;
}


?>