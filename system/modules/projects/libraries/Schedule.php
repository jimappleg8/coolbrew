<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Schedule {

   var $base_cal = array();
   var $cal_rows = array();
   var $cal_data = array();

   var $cal_start = '';
   var $cal_end = '';
   
   // --------------------------------------------------------------------

   /**
    * Class contructor
    */
   function Schedule($params)
   {
      $this->_create_base_schedule($params['months_to_display']);
   }

   // --------------------------------------------------------------------

   /**
    * Creates the basic schedule for the events to be added to.
    *
    * As designed, it will display 12 months maximum.
    *
    * @access   private
    * @param    integer   the number of months to display
    * @return   void
    */
   function _create_base_schedule($months_to_display)
   {
      $year_span = 1;
      $start_month = (int)date('m');
      $end_month = $start_month + $months_to_display - 1;
      
      if ($end_month > 12)
      {
         $end_month = $end_month - 12;
         $year_span = 2;
      }
      
      $today = date('Y-m-d');
      
      for ($y=(int)date('Y'); $y<=(int)date('Y')+$year_span-1; $y++)
      {
         if ($year_span == 2 && $y == date('Y'))
         {
            $this_start_month = $start_month;
            $this_end_month = 12;
         }
         elseif ($year_span == 2)
         {
            $this_start_month = 1;
            $this_end_month = $end_month;
         }
         else
         {
            $this_start_month = $start_month;
            $this_end_month = $end_month;
         }
         for ($m=$this_start_month; $m<=$this_end_month; $m++)
         {
            for ($d=1; $d<=$this->_days_in_month($m, $y); $d++)
            {
               $weekday = date('w', strtotime($y.'-'.$m.'-'.$d));
               $thisday = date('Y-m-d', strtotime($y.'-'.$m.'-'.$d));
               if ($weekday == 0 OR $weekday == 6)
               {
                  $this->base_cal[$y][$m][$d]['class'] = 'weekend';
                  $this->base_cal[$y][$m][$d]['desc'] = 'weekend';
                  $this->base_cal[$y][$m][$d]['color'] = '';
                  $this->base_cal[$y][$m][$d]['bgcolor'] = '';
               }
               elseif ($thisday == $today)
               {
                  $this->base_cal[$y][$m][$d]['class'] = 'today';
                  $this->base_cal[$y][$m][$d]['desc'] = 'today';
                  $this->base_cal[$y][$m][$d]['color'] = '';
                  $this->base_cal[$y][$m][$d]['bgcolor'] = '';
               }
               else
               {
                  $this->base_cal[$y][$m][$d]['class'] = 'blank';
                  $this->base_cal[$y][$m][$d]['desc'] = '';
                  $this->base_cal[$y][$m][$d]['color'] = '';
                  $this->base_cal[$y][$m][$d]['bgcolor'] = '';
               }
            }
            if ($d < 32) {
               for ($i=$d; $i<32; $i++)
               {
                  $this->base_cal[$y][$m][$i]['class'] = 'noday';
                  $this->base_cal[$y][$m][$i]['desc'] = '';
                  $this->base_cal[$y][$m][$d]['color'] = '';
                  $this->base_cal[$y][$m][$d]['bgcolor'] = '';
               }
            }
         }
      }
      // set the start and end dates for database searches
      $this->cal_start = date('Y-m-').'01';
      $this->cal_end = ($y-1).'-'.($m-1).'-'.$this->_days_in_month($m-1, $y-1);
      
   }
   
   // --------------------------------------------------------------------

   /**
    * Adds summary event data to a given calendar row. This emphasizes the
    * primary work time and not the gearing up or cleaning up.
    * The data is assumed to be a multi-dimensional array.
    *
    * @access   public
    * @param    string   the name of the row to be added to
    * @param    array    the data to be added
    * @return   void
    */
   function add_summary_cal_data($row_name, $data)
   {
      $cal_index = $this->get_cal_row_index($row_name);
      
      $proposed_days = array();
      $accepted_days = array();
      $holiday_days = array();
      $vacation_days = array();

      foreach ($data AS $timeblock)
      {
         $days = $this->_date_list($timeblock['StartDate'], $timeblock['EndDate']);
            
         $new_days = array();
         $count = 0;
         foreach ($days AS $day)
         {
            list($yr, $mo, $dy) = explode('-', $day);
            $mo = (int)$mo;
            $dy = (int)$dy;
            if ($day >= $this->cal_start AND $day <= $this->cal_end 
                AND $this->cal_data[$cal_index][$yr][$mo][$dy]['class'] != 'weekend')
            {
               $new_days[$count]['yr'] = $yr;
               $new_days[$count]['mo'] = $mo;
               $new_days[$count]['dy'] = $dy;
               $count++;
            }
         }

         switch ($timeblock['Status'])
         {
            case 'proposed':
               $proposed_days = array_merge($proposed_days, $new_days);
               break;
            case 'accepted':
               $accepted_days = array_merge($accepted_days, $new_days);
               break;
            case 'holiday':
               $holiday_days = array_merge($holiday_days, $new_days);
               break;
            case 'vacation':
               $vacation_days = array_merge($vacation_days, $new_days);
               break;
         }
      }

      foreach ($proposed_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = 'proposed';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = '';
      }

      foreach ($accepted_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = 'accepted';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = '';
      }

      foreach ($holiday_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = 'holiday';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = '';
      }

      foreach ($vacation_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = 'vacation';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = '';
      }

   }
   
   // --------------------------------------------------------------------

   /**
    * Adds detailed event data to a given calendar row. This displays the
    * three main phases of the project: gearing up, primary work time and 
    * cleaning up.
    * The data is assumed to be a multi-dimensional array.
    *
    * @access   public
    * @param    string   the name of the row to be added to
    * @param    array    the data to be added
    * @return   array
    */
   function add_detail_cal_data($row_name, $data)
   {
      $CI =& get_instance();
      
      $CI->load->database('write');
      
      $cal_index = $this->get_cal_row_index($row_name);
      
      $gearup_days = array();
      $primary_days = array();
      $cleanup_days = array();
      $review_days = array();
      
      // get the default colors from the workflow
      $sql = 'SELECT ItemName, Color, BgColor '.
             'FROM projects_checklist '.
             'WHERE ItemName = \'Gearing Up\' '.
             'OR ItemName = \'Primary Work\' '.
             'OR ItemName = \'Cleaning Up\'';

      $query = $CI->db->query($sql);
      $color_list = $query->result_array();

      foreach ($color_list AS $color)
      {
         $colors[$color['ItemName']]['Color'] = $color['Color'];
         $colors[$color['ItemName']]['BgColor'] = $color['BgColor'];
      }

      for ($i=0; $i<count($data); $i++)
      {
         $days = $this->_date_list($data[$i]['StartDate'], $data[$i]['EndDate']);
            
         $new_days = array();
         $count = 0;
         foreach ($days AS $day)
         {
            list($yr, $mo, $dy) = explode('-', $day);
            $mo = (int)$mo;
            $dy = (int)$dy;
            if ($day >= $this->cal_start AND $day <= $this->cal_end 
                AND $this->cal_data[$cal_index][$yr][$mo][$dy]['class'] != 'weekend')
            {
               $new_days[$count]['yr'] = $yr;
               $new_days[$count]['mo'] = $mo;
               $new_days[$count]['dy'] = $dy;
               $count++;
            }
         }

         switch ($data[$i]['BlockName'])
         {
            case 'Gearing up':
               $gearup_days = array_merge($gearup_days, $new_days);
               $data[$i]['color'] = $colors['Gearing Up']['Color'];
               $data[$i]['bgcolor'] = $colors['Gearing Up']['BgColor'];
               break;
            case 'First draft':
            case 'Second draft':
            case 'Final draft':
            case 'Final corrections':
            case 'Go live':
               $primary_days = array_merge($primary_days, $new_days);
               $data[$i]['color'] = $colors['Primary Work']['Color'];
               $data[$i]['bgcolor'] = $colors['Primary Work']['BgColor'];
               break;
            case 'Cleaning up':
               $cleanup_days = array_merge($cleanup_days, $new_days);
               $data[$i]['color'] = $colors['Cleaning Up']['Color'];
               $data[$i]['bgcolor'] = $colors['Cleaning Up']['BgColor'];
               break;
            case 'First review':
            case 'Second review':
            case 'Final review':
               $review_days = array_merge($review_days, $new_days);
               $data[$i]['color'] = '000';
               $data[$i]['bgcolor'] = 'CC9';
               break;
         }
      }

      foreach ($gearup_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = $colors['Gearing Up']['Color'];
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = $colors['Gearing Up']['BgColor'];
      }

      foreach ($primary_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = $colors['Primary Work']['Color'];
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = $colors['Primary Work']['BgColor'];
      }

      foreach ($cleanup_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = $colors['Cleaning Up']['Color'];
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = $colors['Cleaning Up']['BgColor'];
      }

      foreach ($review_days AS $day)
      {
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['class'] = '';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['color'] = '000';
         $this->cal_data[$cal_index][$day['yr']][$day['mo']][$day['dy']]['bgcolor'] = 'CC9';
      }
      return $data;
   }
   
   // --------------------------------------------------------------------

   /**
    * Adds a row to the $cal_row and $cal_data variables
    *
    * @access   public
    * @param    string    the name of the row being added
    * @return   integer
    */
   function add_cal_row($row_name)
   {
      $index = count($this->cal_rows);
      $this->cal_rows[$index] = $row_name;
      $this->cal_data[$index] = $this->base_cal;
      return $index;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the index number for the first calendar row with the given name
    *
    * @access   public
    * @param    string    the name of the row being requested
    * @return   integer
    */
   function get_cal_row_index($row_name)
   {
      for ($i=0; $i<count($this->cal_rows); $i++)
      {
         if ($this->cal_rows[$i] == $row_name)
            return $i;
      }
      return FALSE;
   }

   // --------------------------------------------------------------------

   /**
    * The total number of calendar rows
    *
    * @access   public
    * @return   integer
    */
   function num_cal_rows()
   {
      return count($this->cal_rows);
   }

   // ------------------------------------------------------------------------

   /**
    * Number of days in a month
    *
    * Takes a month/year as input and returns the number of days
    * for the given month/year. Takes leap years into consideration.
    *
    * @access   private
    * @param    integer   a numeric month
    * @param    integer   a numeric year
    * @return   integer
    */   
   function _days_in_month($month = 0, $year = '')
   {
      if ($month < 1 OR $month > 12)
      {
         return 0;
      }
   
      if ( ! is_numeric($year) OR strlen($year) != 4)
      {
         $year = date('Y');
      }
   
      if ($month == 2)
      {
         if ($year % 400 == 0 OR ($year % 4 == 0 AND $year % 100 != 0))
         {
            return 29;
         }
      }

      $days_in_month   = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
      return $days_in_month[$month - 1];
   }

   // --------------------------------------------------------------------

   /**
    * return array of dates given a range
    *
    * @access   private
    * @param    string    a date in the format YYYY-MM-DD
    * @param    string    a date in the format YYYY-MM-DD
    * @return   array
    */
   function _date_list($start_date, $end_date)
   {
      // epoch timestamp for midnight on start day
      list($start_yr, $start_mo, $start_dy) = explode('-', $start_date);
      $start = mktime(0,0,0,$start_mo,$start_dy,$start_yr);

      // epoch timestamp for midnight on end day
      list($end_yr, $end_mo, $end_dy) = explode('-', $end_date);
      $end = mktime(0,1,0,$end_mo,$end_dy,$end_yr);

      $results = array();
      while ($start < $end)
      {
         $results[] = date('Y-m-d', $start);
         $start += 86400;
      }
      return $results;
   }

}

?>