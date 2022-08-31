<?php
/**
 * Cool Brew Core Modules
 *
 * A module for accessing the core CodeIgniter class methods via tags
 *
 * @package		Cool Brew
 * @author		Jim Applegate
 * @copyright	Copyright (c) 2007, The Hain Celestial Group, Inc.
 * @license		http://www.coolbrewcms.com/user_guide/cb-license.html
 * @link		http://www.coolbrewcms.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Cool Brew Calendar Module
 *
 * This class contains functions that access the CI calendar class methods
 *
 * @package		Cool Brew
 * @subpackage	Modules
 * @category	Modules
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/modules/calendar.html
 */
class Calendar extends Controller {

   function Calendar()
   {
      parent::Controller();	
   }
	
   // --------------------------------------------------------------------

   /**
    * Generate a calendar
    *
    * @access  public
    * @return  string
    */		
   function generate()
   {
      // (integer) the year
      $year = $this->tag->param(1, '');
      
      // (integer) the month
      $month = $this->tag->param(2, '');
      
      // (array) the data to be shown in the calendar cells
      $data = $this->tag->param(3, array());
      
      // (array) preferences used to control various aspects of the calendar
      $prefs = $this->tag->param(4, array());

      $this->load->library('Calendar', $prefs);
		
      return $this->calendar->generate($year, $month, $data);
   }
   // END generate()

}
?>