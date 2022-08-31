<?php
/**
 * Cool Brew Core Modules
 *
 * A module for accessing the core CodeIgniter class methods via tags
 *
 * @package		Cool Brew
 * @author		Jim Applegate
 * @copyright	Copyright (c) 2007, The Hain Celestial Group, Inc.
 * @license		http://www.coolbrewcms.com/user_guide/license.html
 * @link		http://www.coolbrewcms.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Cool Brew URI Module
 *
 * This class contains functions that access the CI uri class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/uri.html
 */
class Uri extends Controller {

   function Uri()
   {
      parent::Controller();	
   }
   
   // --------------------------------------------------------------------

   /**
    * Fetch a URI Segment
    *
    * This function returns the URI segment based on the number provided.
    *
    * @access   public
    * @return   string
    */
   function segment()
   {
      // (integer) the URI segment number
      $n = $this->tag->param(1);
      
      // (bool) the value to return if the segment doesn't exist
      $no_result = $this->tag->param(2, FALSE);

      if ( ! $n)
      {
         show_error('core.uri.segment: the segment number was not specified.');
      }

      return $this->uri->segment($n, $no_result);
   }

   // --------------------------------------------------------------------
   
   /**
    * Fetch a URI "routed" Segment
    *
    * This function returns the re-routed URI segment (assuming routing rules are used)
    * based on the number provided.  If there is no routing this function returns the
    * same result as $this->segment()
    *
    * @access   public
    * @return   string
    */
   function rsegment()
   {
      // (integer) the URI segment number
      $n = $this->tag->param(1);
      
      // (bool) the value to return if the segment doesn't exist
      $no_result = $this->tag->param(2, FALSE);

      if ( ! $n)
      {
         show_error('core.uri.rsegment: the segment number was not specified.');
      }

      return $this->uri->rsegment($n, $no_result);
   }

   // --------------------------------------------------------------------
   
   /**
    * Generate a key value pair from the URI string
    *
    * @access   public
    * @param   integer   the starting segment number
    * @param   array   an array of default values
    * @return   array
    */
   function uri_to_assoc()
   {
      // (integer) the starting segment number
      $n = $this->tag->param(1, 3);
      
      // (array) an array of default values
      $default = $this->tag->param(2, array());

      return $this->uri->uri_to_assoc($n, $default);
   }

   // --------------------------------------------------------------------
   
   /**
    * Identical to above only it uses the re-routed segment array
    *
    */
   function ruri_to_assoc()
   {
      // (integer) the starting segment number
      $n = $this->tag->param(1, 3);
      
      // (array) an array of default values
      $default = $this->tag->param(2, array());

      return $this->uri->ruri_to_assoc($n, $default);
   }

   // --------------------------------------------------------------------
   
   /**
    * Generate a URI string from an associative array
    *
    * @access   public
    * @return   array
    */
   function assoc_to_uri()
   {   
      // (array) an associative array of key/values
      $array = $this->tag->param(1);
      
      return $this->uri->assoc_to_uri($array);
   }

   // --------------------------------------------------------------------
   
   /**
    * Fetch a URI Segment and add a trailing slash
    *
    * @access   public
    * @return   string
    */
   function slash_segment()
   {
      // (integer)
      $n = $this->tag->param(1);
      
      // (string)
      $where = $this->tag->param(2, 'trailing');

      return $this->uri->slash_segment($n, $where);
   }

   // --------------------------------------------------------------------
   
   /**
    * Fetch a URI Segment and add a trailing slash
    *
    * @access   public
    * @return   string
    */
   function slash_rsegment()
   {
      // (integer)
      $n = $this->tag->param(1);
      
      // (string)
      $where = $this->tag->param(2, 'trailing');

      return $this->uri->slash_rsegment($n, $where);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Segment Array
    *
    * @access   public
    * @return   array
    */
   function segment_array()
   {
      return $this->uri->segment_array();
   }

   // --------------------------------------------------------------------
   
   /**
    * Routed Segment Array
    *
    * @access   public
    * @return   array
    */
   function rsegment_array()
   {
      return $this->uri->rsegment_array();
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Total number of segments
    *
    * @access   public
    * @return   integer
    */
   function total_segments()
   {
      return $this->uri->total_segments();
   }

   // --------------------------------------------------------------------
   
   /**
    * Total number of routed segments
    *
    * @access   public
    * @return   integer
    */
   function total_rsegments()
   {
      return $this->uri->total_rsegments();
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Fetch the entire URI string
    *
    * @access   public
    * @return   string
    */
   function uri_string()
   {
      return $this->uri->uri_string();
   }

   
   // --------------------------------------------------------------------
   
   /**
    * Fetch the entire Re-routed URI string
    *
    * @access   public
    * @return   string
    */
   function ruri_string()
   {
      return $this->uri->ruri_string();
   }
   
}
?>