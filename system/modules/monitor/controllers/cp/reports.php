<?php

class Reports extends Controller {

   // --------------------------------------------------------------------

   function Reports()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'monitor'));
      $this->load->helper(array('url', 'menu'));
   }

   // --------------------------------------------------------------------

   /**
    * List of reports
    *
    */
   function index()
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
      
      $this->load->model('Reports');
      $this->load->model('Report_types');

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $reports = $this->Reports->get_reports();
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Reports');
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['reports'] = $reports;
      $data['report_types'] = $this->Report_types->get_report_types();

      $this->load->vars($data);

      return $this->load->view('cp/reports/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * View a single report
    *
    */
   function view($report_id)
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
      
      $this->load->model('Data_points');
      $this->load->model('Reports');
      $this->load->model('Report_types');
      $this->load->model('Report_data');
      $this->load->model('Report_data_point_link');
      $this->load->model('Sites');
      
      $report = $this->Reports->get_report_data($report_id);
      
      $tpl = 'view-'.$report['report_type_id'];

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Reports');
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['report'] = $report;
      $data['report_types'] = $this->Report_types->get_report_types();
      $data['sites_lookup'] = $this->Sites->get_sites_lookup();
      
      // pull in the data points for this report
//      $data_points = $this->Report_data_point_link->get_assigned($report_id);
      // for now, I'm going to assume all the data should be available to all reports
      $data_points = $this->Data_points->get_all_data_points();
      
      foreach ($data_points AS $data_point)
      {
         $method = 'rpt_'.$data_point['data_point_id'];
         $data[$data_point['data_point_id']] = call_user_func_array(array($this->Report_data, $method), array($report_id));
      }
      
//      echo '<pre>'; print_r($data); echo '</pre>'; exit;

      $this->load->vars($data);

      return $this->load->view('cp/reports/'.$tpl, NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Runs the auto-update process and pulls any data that is missing.
    
      The idea is that this function will run though each data point, 
      decide if it needs to be retrieved, then retrieve it if needed.
      
      To do that, we will need a list of all data points, a list of all 
      sites, and information about how to get the data. The last point
      is supposed to be built into the logic of the get_ function for
      each data point, but it may vary for each site and for a particular
      set of date ranges. 
    *
    */
   function auto_update()
   {
      // allows me to turn off GA connection on local site
      $include_ga_connector = TRUE;

      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
      
      // allows you to reset the session variable if you need to.
      if ($action == 'reset')
      {
         $this->session->unset_userdata('accessToken');
      }
      
      $code = (isset($_GET['code'])) ? $_GET['code'] : '';

      $this->load->model('Reports');
      $this->load->model('Report_data_point_link');
      $this->load->model('Report_data');
      $this->load->model('Sites');

      if ($include_ga_connector)
      {
         $this->load->model('Google_analytics');
         $this->Google_analytics->set_redirect_uri('http://webadmin.hcgweb.net/admin/monitor.php/cp/reports/update_ga_data');
         $this->Google_analytics->set_application_name('Google Analytics Dashboard');
         $this->Google_analytics->initialize();

         if ($action == 'revoke')
         {
            $this->Google_analytics->revoke_token();
         }
         elseif ($action == 'auth' || $code != '')
         {
            $this->Google_analytics->authenticate();
         }

         $this->Google_analytics->set_access_token_from_session();
      }
      
      // Keep things pretty. Removes the auth code from the URL.
      if ($code != '')
      {
         redirect('cp/reports/update_ga_data');
      }

      // get all data points that are defined
      $data_points = $this->Data_points->get_all_data_points();
      
      // go through each data point and run the correct model/method
      $notices = array();
      foreach ($data_points AS $data_point)
      {
         $method = 'get_'.$data_point['data_point_id'];
         $notices[] = call_user_func_array(array($this->Report_data, $method), array($values['Report']));
      }

      redirect('cp/reports/index');
   }

  // --------------------------------------------------------------------

   /**
    * Runs all the calculations for the specified report
    *
    */
   function calculate($report_id)
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
      
      $this->load->model('Reports');
      $this->load->model('Report_types');
      $this->load->model('Report_data');
      $this->load->model('Report_data_point_link');
      $this->load->model('Sites');
      
      $report = $this->Reports->get_report_data($report_id);
      
      // pull in the data points for this report
      $data_points = $this->Report_data_point_link->get_assigned($report_id);
      
      foreach ($data_points AS $data_point)
      {
         $method = 'calc_'.$data_point['data_point_id'];
         $data[$data_point['data_point_id']] = call_user_func_array(array($this->Report_data, $method), array($report_id));
      }

      redirect('cp/reports/update_data/'.$report_id);
   }

   // --------------------------------------------------------------------

   /**
    * Makes a copy of an existing report
    *
    */
   function duplicate($report_id)
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
      
      $this->load->model('Reports');
      $this->load->model('Report_site_link');
      $this->load->model('Report_data_point_link');
      
      $report = $this->Reports->get_report_data($report_id);
      
      $values = array();
      $values['report_type_id'] = $report['report_type_id'];
      $values['name'] = $report['name'];
      $values['start_date'] = $report['start_date'];
      $values['end_date'] = $report['end_date'];

      $new_report_id = $this->Reports->insert_report($values);
      
      foreach ($report['sites'] AS $site)
      {
         $this->Report_site_link->insert_link($new_report_id, $site['site_id']);
      }
      
      foreach ($report['data_points'] AS $data_point)
      {
         $this->Report_data_point_link->insert_link($new_report_id, $data_point['data_point_id']);
      }

      redirect('cp/reports/index/');
   }

   // --------------------------------------------------------------------

   /**
    * Adds a report
    *
    */
   function add($this_action) 
   {
      $this->load->helper(array('form', 'text'));
      $this->load->model('Reports');
      $this->load->model('Report_types');
      $this->load->library('validation');
      
      $rules['report_type_id'] = 'trim|required';
      $rules['start_date'] = 'trim|required';
      $rules['end_date'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['report_type_id'] = 'Report Type';
      $fields['start_date'] = 'Start Date';
      $fields['end_date'] = 'End Date';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('monitor');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
         $data['submenu'] = get_cp_submenu('Reports');
         $data['report_types'] = $this->Report_types->get_report_types();

         $this->load->vars($data);
         return $this->load->view('cp/reports/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add();
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add link form
    *
    */
   function _add()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
            
      $this->Reports->insert_report($values);

      $this->session->set_userdata('monitor_message', 'The new report has been added.');

      redirect("cp/reports/index");
   }
   
   // --------------------------------------------------------------------

   /**
    * Edits the specified link
    *
    */
   function edit($report_id, $this_action) 
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Data_points');
      $this->load->model('Reports');
      $this->load->model('Report_types');
      $this->load->model('Report_data_point_link');
      $this->load->model('Report_site_link');
      $this->load->model('Sites');
      
      $report = $this->Reports->get_report_data($report_id);
      
      $site_list = $this->Sites->get_sites();
      $data_point_list = $this->Data_points->get_data_points();
      
      $rules['report_type_id'] = 'trim|required';
      $rules['start_date'] = 'trim|required';
      $rules['end_date'] = 'trim|required';
      
      $fields['report_type_id'] = 'Report Type';
      $fields['start_date'] = 'Start Date';
      $fields['end_date'] = 'End Date';

      $defaults = $report;
      
      foreach ($site_list AS $site)
      {
         $rules['site'.$site['ID']] = 'trim';
         $fields['site'.$site['ID']] = 'Site #'.$site['ID'];
         $defaults['site'.$site['ID']] = 0;
      }

      foreach ($data_point_list AS $data_point)
      {
         $rules['data'.$data_point['id']] = 'trim';
         $fields['data'.$data_point['id']] = 'Data Point #'.$site['ID'];
         $defaults['data'.$data_point['id']] = 0;
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);
      
      $assigned_sites = $this->Report_site_link->get_assigned($report_id);
      foreach ($assigned_sites AS $site)
      {
         $defaults['site'.$site['site_id']] = 1;
      }
      
      $assigned_data = $this->Report_data_point_link->get_assigned($report_id);
      foreach ($assigned_data AS $data_point)
      {
         $defaults['data'.$data_point['data_point_id']] = 1;
      }

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('monitor');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
         $data['submenu'] = get_cp_submenu('Reports');
         $data['report_id'] = $report_id;
         $data['site_list'] = $site_list;
         $data['data_point_list'] = $data_point_list;
         $data['report_types'] = $this->Report_types->get_report_types();

         $this->load->vars($data);
         return $this->load->view('cp/reports/edit', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($report_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit site_link form
    *
    */
   function _edit($report_id)
   {
      $fields = $this->validation->_fields;
      
      $sites = array();
      $data_points = array();
      
      foreach ($fields AS $key => $value)
      {
         if (substr($key, 0, 4) == "site")
         {
            $sites[substr($key, 4)] = $this->input->post($key);
         }
         elseif (substr($key, 0, 4) == "data")
         {
            $data_points[substr($key, 4)] = $this->input->post($key);
         }
         else
         {
            $values[$key] = $this->input->post($key);
         }
      }
      
//      echo '<pre>'; print_r($sites); print_r($data_points); echo '</pre>'; exit;
      
      // first, update the main report record
      $report['report_type_id'] = $values['report_type_id'];
      $report['start_date'] = $values['start_date'];
      $report['end_date'] = $values['end_date'];
      $this->Reports->update_report($report_id, $report);
      
      // Then, update the report-site links by deleting the existing
      //  ones and then inserting all selected items
      $this->Report_site_link->delete_report_links($report_id);
      foreach ($sites AS $key => $value)
      {
         if ($value == 1)
         {
            $this->Report_site_link->insert_link($report_id, $key);
         }
      }
    
      // Do the same for the data points
      $this->Report_data_point_link->delete_report_links($report_id);
      foreach ($data_points AS $key => $value)
      {
         if ($value == 1)
         {
            $this->Report_data_point_link->insert_link($report_id, $key);
         }
      }

      $this->session->set_userdata('monitor_message', 'The report settings have been updated.');

      redirect('cp/reports/index');
   }   

   // --------------------------------------------------------------------

   /**
    * Runs all the "get metric" functions for the report to populate the
    *  data that is defined in the report.
    *
    */
   function update_data($report_id, $action = '')
   {
      // allows me to turn off GA connection on local site
      $include_ga_connector = TRUE;
      
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
      
      // allows you to reset the session variable if you need to.
      if ($action == 'reset')
      {
         $this->session->unset_userdata('accessToken');
      }
      
      $code = (isset($_GET['code'])) ? $_GET['code'] : '';

      $this->load->model('Reports');
      $this->load->model('Report_data_point_link');
      $this->load->model('Report_data');
      $this->load->model('Sites');

      if ($include_ga_connector)
      {
         $this->load->model('Google_analytics');
         $this->Google_analytics->set_redirect_uri('http://webadmin.hcgweb.net/admin/monitor.php/cp/reports/update_ga_data');
         $this->Google_analytics->set_application_name('Google Analytics Dashboard');
         $this->Google_analytics->initialize();

         if ($action == 'revoke')
         {
            $this->Google_analytics->revoke_token();
         }
         elseif ($action == 'auth' || $code != '')
         {
            $this->Google_analytics->authenticate();
         }

         $this->Google_analytics->set_access_token_from_session();
      }
      
      // Keep things pretty. Removes the auth code from the URL.
      if ($code != '')
      {
         redirect('cp/reports/update_ga_data');
      }

      $this->load->helper(array('form', 'text'));    
      $this->load->library('validation');
      
      $rules['Report'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['Report'] = 'Report ID';

      $this->validation->set_fields($fields);
      
      $defaults['Report'] = $report_id;
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $data['summary'] = array();

      if ($this->validation->run() == TRUE)
      {
         $data['notices'] = $this->_update_data();
      }

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Reports');
      $data['report_id'] = $report_id;
      $data['report'] = $this->Reports->get_report_data($report_id);
      $data['is_authorized'] = ($include_ga_connector) ? $this->Google_analytics->is_authorized(): FALSE;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['errors'] = ($include_ga_connector) ? htmlspecialchars($this->Google_analytics->get_error(), ENT_NOQUOTES) : '';
      $data['reports'] = $this->Reports->get_report_list();
      $data['sites_lookup'] = $this->Sites->get_sites_lookup();
      
      // pull in the data points for this report
      $data_points = $this->Report_data_point_link->get_assigned($report_id);
      
      foreach ($data_points AS $data_point)
      {
         $method = 'rpt_'.$data_point['data_point_id'];
         $data[$data_point['data_point_id']] = call_user_func_array(array($this->Report_data, $method), array($report_id));
      }
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/reports/update-data', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Processes the form and gathers the data
    *
    */
   function _update_data()
   {
      $this->load->model('Report_data_point_link');

      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // get the list of data points for this report
      $data_points = $this->Report_data_point_link->get_assigned($values['Report']);

      // go through each data point and run the correct model/method
      $notices = array();
      foreach ($data_points AS $data_point)
      {
         $method = 'get_'.$data_point['data_point_id'];
         $notices[] = call_user_func_array(array($this->Report_data, $method), array($values['Report']));
      }

      return $notices;
   }


}