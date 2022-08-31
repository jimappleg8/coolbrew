<?php

/*
This model contains much of the logic behind the various data points.
There should be a function for each data point defined in the data point
table that understands how to deal with that data.
*/

class Report_data_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   var $CI;

   // --------------------------------------------------------------------

   function Report_data_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
      $this->CI =& get_instance();
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array with the requested datum
    */
   function get_report_data_data($data_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE id = '.$data_id;
      $query = $this->read_db->query($sql);
      $data = $query->row_array();
      
      return $data;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts or updates a data record. If the record exists and has not
    *  been manually overridden, then it will update it; if it does not
    *  yet exist, it will create it.
    */
   function insert_data($values)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$values['report_id'].' '.
             'AND data_point_id = "'.$values['data_point_id'].'" '.
             'AND site_id = "'.$values['site_id'].'"';
      $query = $this->read_db->query($sql);
      $data = $query->row_array();
      
      if ( ! empty($data))
      {
         if ($data['overridden'] == 0)
         {
            $this->write_db->where('id', $data['id']);
            $this->write_db->update('mon_report_data', $values);
         }
         // else leave it alone
      }
      else
      {
         $this->write_db->insert('mon_report_data', $values);
      }
   }

   // --------------------------------------------------------------------
   // sites_in_report
   // --------------------------------------------------------------------

   function get_sites_in_report($report_id)
   {
      // create data record
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'sites_in_report';
      $new_data['site_id'] = 'all';
      $new_data['amount'] = '0';
      $this->insert_data($new_data);
      
      return 'Collected sites_in_report data from CoolBrew.';
   }

   // --------------------------------------------------------------------

   function calc_sites_in_report($report_id)
   {
      $this->CI->load->model('Reports');
      $report = $this->CI->Reports->get_report_data($report_id);
      
      $amount = count($report['sites']);

      // update data record
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'sites_in_report';
      $new_data['site_id'] = 'all';
      $new_data['amount'] = $amount;
      $this->insert_data($new_data);
   }

   // --------------------------------------------------------------------

   function rpt_sites_in_report($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "sites_in_report"';
      $query = $this->read_db->query($sql);
      $data = $query->row_array();
      
      return $data['amount'];
   }

   // --------------------------------------------------------------------
   // sites_by_region
   // --------------------------------------------------------------------

   function get_sites_by_region($report_id)
   {
      // data is not stored in the report data table
       return 'Collected sites_by_region data from CoolBrew.';
   }

   // --------------------------------------------------------------------

   function calc_sites_by_region($report_id)
   {
      // no calculations are needed.
   }

   // --------------------------------------------------------------------

   function rpt_sites_by_region($report_id)
   {
      $sql = 'SELECT Region, COUNT(Region) AS CountSites '.
             'FROM `adm_site` '.
             'WHERE Type = "Branded" '.
             'GROUP BY Region '.
             'ORDER BY CountSites DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      return $data;
   }

   // --------------------------------------------------------------------
   // site_average_visit_duration
   // --------------------------------------------------------------------

   function get_site_average_visit_duration($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_average_visit_duration($report_id);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_average_visit_duration($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_average_visit_duration($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_average_visit_duration" '.
             'ORDER BY amount * 1 DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_bounce_rate
   // --------------------------------------------------------------------

   function get_site_bounce_rate($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_bounce_rate($report_id);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_bounce_rate($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_bounce_rate($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_bounce_rate" '.
             'ORDER BY amount * 1 DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_pageviews
   // --------------------------------------------------------------------

   function get_site_pageviews($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_pageviews($report_id);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_pageviews($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_pageviews" '.
             'AND site_id != "#total"';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $total = 0;
      foreach ($data AS $datum)
      {
         $total += (int)$datum['amount'];
      }
      
      // create data point for total
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'site_pageviews';
      $new_data['site_id'] = '#total';
      $new_data['amount'] = $total;
      $this->insert_data($new_data);      
   }

   // --------------------------------------------------------------------

   function rpt_site_pageviews($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_pageviews" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_pageviews_per_visit
   // --------------------------------------------------------------------

   function get_site_pageviews_per_visit($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_pageviews_per_visit($report_id);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_pageviews_per_visit($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_pageviews_per_visit($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_pageviews_per_visit" '.
             'ORDER BY amount * 1 DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_percent_new_visitors
   // --------------------------------------------------------------------

   function get_site_percent_new_visitors($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_percent_new_visitors($report_id);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_percent_new_visitors($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_percent_new_visitors" '.
             'AND site_id != "#total"';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $total = 0;
      $divisor = 0;
      foreach ($data AS $datum)
      {
         if ((int)$datum['amount'] > 0)
         {
            $total += (int)$datum['amount'];
            $divisor++;
         }
      }
      // get the average, excluding zero values
      $total = $total / $divisor;
      
      // create data point for total
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'site_percent_new_visitors';
      $new_data['site_id'] = '#total';
      $new_data['amount'] = $total;
      $this->insert_data($new_data);      
   }

   // --------------------------------------------------------------------

   function rpt_site_percent_new_visitors($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_percent_new_visitors" '.
             'ORDER BY amount * 1 DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_unique_visitors
   // --------------------------------------------------------------------

   function get_site_unique_visitors($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_unique_visitors($report_id);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_unique_visitors($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_unique_visitors" '.
             'AND site_id != "#total"';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $total = 0;
      foreach ($data AS $datum)
      {
         $total += (int)$datum['amount'];
      }
      
      // create data point for total
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'site_unique_visitors';
      $new_data['site_id'] = '#total';
      $new_data['amount'] = $total;
      $this->insert_data($new_data);      
   }

   // --------------------------------------------------------------------

   function rpt_site_unique_visitors($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_unique_visitors" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_visits
   // --------------------------------------------------------------------

   function get_site_visits($report_id)
   {
      $this->CI->load->model('Google_analytics');

      $notice = $this->CI->Google_analytics->get_dp_site_visits($report_id);
      
      // create data point for total
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'site_visits';
      $new_data['site_id'] = '#total';
      $new_data['amount'] = '0';
      $this->insert_data($new_data);
      
      return $notice;
   }

   // --------------------------------------------------------------------

   function calc_site_visits($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_visits" '.
             'AND site_id != "#total"';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $total = 0;
      foreach ($data AS $datum)
      {
         $total += (int)$datum['amount'];
      }
      
      // create data point for total
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'site_visits';
      $new_data['site_id'] = '#total';
      $new_data['amount'] = $total;
      $this->insert_data($new_data);
      
      // calculate the daily average
      $sql = 'SELECT DATEDIFF(end_date,start_date) AS days '.
             'FROM mon_report '.
             'WHERE id = '.$report_id;
      $query = $this->read_db->query($sql);
      $data = $query->row_array();
      $average_daily = round($total/(int)$data['days']);
        
      // create data point for total
      $new_data = array();
      $new_data['report_id'] = $report_id;
      $new_data['data_point_id'] = 'site_visits';
      $new_data['site_id'] = '#average_daily';
      $new_data['amount'] = $average_daily;
      $this->insert_data($new_data);
      
   }

   // --------------------------------------------------------------------

   function rpt_site_visits($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_visits" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_contact_us_complaint
   // --------------------------------------------------------------------

   function get_site_contact_us_complaint($report_id)
   {
      $this->CI->load->model('Report_site_link');
      
      $sites = $this->CI->Report_site_link->get_assigned($report_id);
      foreach ($sites AS $site)
      {
         // create data record
         $new_data = array();
         $new_data['report_id'] = $report_id;
         $new_data['data_point_id'] = 'site_contact_us_complaint';
         $new_data['site_id'] = $site['site_id'];
         $new_data['amount'] = '0';
         $this->insert_data($new_data);
      }
      
      return 'Set up site_contact_us_complaint data for input.';
   }

   // --------------------------------------------------------------------

   function calc_site_contact_us_complaint($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_contact_us_complaint($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_contact_us_complaint" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_contact_us_inquiry
   // --------------------------------------------------------------------

   function get_site_contact_us_inquiry($report_id)
   {
      $this->CI->load->model('Report_site_link');
      
      $sites = $this->CI->Report_site_link->get_assigned($report_id);
      foreach ($sites AS $site)
      {
         // create data record
         $new_data = array();
         $new_data['report_id'] = $report_id;
         $new_data['data_point_id'] = 'site_contact_us_inquiry';
         $new_data['site_id'] = $site['site_id'];
         $new_data['amount'] = '0';
         $this->insert_data($new_data);
      }
      
      return 'Set up site_contact_us_inquiry data for input.';
   }

   // --------------------------------------------------------------------

   function calc_site_contact_us_inquiry($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_contact_us_inquiry($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_contact_us_inquiry" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_contact_us_praise
   // --------------------------------------------------------------------

   function get_site_contact_us_praise($report_id)
   {
      $this->CI->load->model('Report_site_link');
      
      $sites = $this->CI->Report_site_link->get_assigned($report_id);
      foreach ($sites AS $site)
      {
         // create data record
         $new_data = array();
         $new_data['report_id'] = $report_id;
         $new_data['data_point_id'] = 'site_contact_us_praise';
         $new_data['site_id'] = $site['site_id'];
         $new_data['amount'] = '0';
         $this->insert_data($new_data);
      }
      
      return 'Set up site_contact_us_praise data for input.';
   }

   // --------------------------------------------------------------------

   function calc_site_contact_us_praise($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_contact_us_praise($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_contact_us_praise" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }

   // --------------------------------------------------------------------
   // site_contact_us_suggestion
   // --------------------------------------------------------------------

   function get_site_contact_us_suggestion($report_id)
   {
      $this->CI->load->model('Report_site_link');
      
      $sites = $this->CI->Report_site_link->get_assigned($report_id);
      foreach ($sites AS $site)
      {
         // create data record
         $new_data = array();
         $new_data['report_id'] = $report_id;
         $new_data['data_point_id'] = 'site_contact_us_suggestion';
         $new_data['site_id'] = $site['site_id'];
         $new_data['amount'] = '0';
         $this->insert_data($new_data);
      }
      
      return 'Set up site_contact_us_suggestion data for input.';
   }

   // --------------------------------------------------------------------

   function calc_site_contact_us_suggestion($report_id)
   {
   }

   // --------------------------------------------------------------------

   function rpt_site_contact_us_suggestion($report_id)
   {
      $sql = 'SELECT * '.
             'FROM mon_report_data '.
             'WHERE report_id = '.$report_id.' '.
             'AND data_point_id = "site_contact_us_suggestion" '.
             'ORDER BY CAST(amount AS SIGNED) DESC';
      $query = $this->read_db->query($sql);
      $data = $query->result_array();
      
      $new_data = array();
      foreach ($data AS $datum)
      {
         $new_data[$datum['site_id']] = $datum;
      }
      return $new_data;
   }


}

/* End of file Report_data_model.php */
/* Location: ./system/modules/monitor/models/Report_data_model.php */