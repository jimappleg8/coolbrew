<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		Rick Ellis
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://www.codeignitor.com/user_guide/license.html
 * @link		http://www.codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter XML Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Rick Ellis
 * @link		http://www.codeigniter.com/user_guide/helpers/xml_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Convert Reserved XML characters to Entities
 *
 * @access	public
 * @param	string
 * @return	string
 */	
function xml_convert($str)
{
   $temp = '__TEMP_AMPERSANDS__';

   // Replace entities to temporary markers so that 
   // ampersands won't get messed up
   $str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
   $str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);
	
   $str = str_replace(array("&","<",">","\"", "'", "-"),
                      array("&amp;", "&lt;", "&gt;", "&quot;", "&#39;", "&#45;"),
                      $str);

   // Decode the temp markers back to entities		
   $str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
   $str = preg_replace("/$temp(\w+);/","&\\1;", $str);

   return $str;
}

// ------------------------------------------------------------------------
   
/**
 * Try to ensure that the content is XML compliant
 *
 * @access	public
 * @param  mixed   the element to process
 * @return mixed
 */
function process_xml_element($element)
{
   // make sure html entities are UTF-8
//   $element = htmlentities(html_entity_decode($element), ENT_COMPAT, 'utf-8');

   $element = xml_convert($element);

   if (is_string($element) && $element != '')
   {
      $element = '<![CDATA['.$element.']]>';
   }
   $element = str_replace('*', '&#42;', $element);
   if ($element == '&#13;')
   {
      $element = '';
   }
   return $element;
}

// ------------------------------------------------------------------------

/**
 * Try to ensure that the content is XML compliant
 *
 * @param  array   the elements to process
 * @return array
 */
function process_xml_array($elements)
{
   if ( ! is_array($elements))
   {
      return process_xml_element($elements);
   }
   
   foreach ($elements AS $key => $value)
   {
      $elements[$key] = process_xml_element($value);
   }
   return $elements;
}


?>