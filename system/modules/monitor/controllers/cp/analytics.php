<?php

class Analytics extends Controller {

   // --------------------------------------------------------------------

   function Analytics()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'monitor'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Welcome page for Analytics.
    *
    */
   function index()
   {
      $admin['message'] = '';
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Analytics');
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/analytics/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * This is a Coolbrewed version of their sample script
    *
    */
   function demo()
   {
      require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/src/Google_Client.php';
      require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/src/contrib/Google_AnalyticsService.php';

      $client = new Google_Client();
      $client->setApplicationName("Google Analytics PHP Starter Application");

      // setting from project created at https://code.google.com/apis/console
      $client->setClientId('322182483029.apps.googleusercontent.com');
      $client->setClientSecret('tLglwN6BmMU7-N930qgYTZpZ');
      $client->setRedirectUri('http://webadmin.hcgweb.net/admin/monitor.php/analytics/index');
      $client->setDeveloperKey('AIzaSyD3SWY51V9Dw0YswzRZT2YYvXXVEk7cHE8');

      $service = new Google_AnalyticsService($client);
      
      if (isset($_GET['logout']))
      {
         $this->session->unset_userdata('token');
      }

      if (isset($_GET['code']))
      {
         $client->authenticate();
         $this->session->set_userdata('token', $client->getAccessToken());
         $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
         header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
      }

      if ($this->session->userdata('token') != '')
      {
         $client->setAccessToken($this->session->userdata('token'));
      }

      if ($client->getAccessToken())
      {
         $props = $service->management_webproperties->listManagementWebproperties("~all");
         print "<h1>Web Properties</h1><pre>" . print_r($props, true) . "</pre>";

         $accounts = $service->management_accounts->listManagementAccounts();
         print "<h1>Accounts</h1><pre>" . print_r($accounts, true) . "</pre>";

         $segments = $service->management_segments->listManagementSegments();
         print "<h1>Segments</h1><pre>" . print_r($segments, true) . "</pre>";

         $goals = $service->management_goals->listManagementGoals("~all", "~all", "~all");
         print "<h1>Segments</h1><pre>" . print_r($goals, true) . "</pre>";

         $this->session->set_userdata('token', $client->getAccessToken());
      }
      else
      {
         $authUrl = $client->createAuthUrl();
         print "<a class='login' href='$authUrl'>Connect Me!</a>";
      }
   }

   // --------------------------------------------------------------------

   /**
    * Lists stats for all sites in a table for the given date range.
    *
    */
   function all_sites_summary($action = '')
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
         
      // allows you to reset the session variable if you need to.
      if ($action == 'reset')
      {
         $this->session->unset_userdata('accessToken');
      }
      
      $code = (isset($_GET['code'])) ? $_GET['code'] : '';

      $this->load->model('Google_analytics');
      $this->Google_analytics->set_redirect_uri('http://webadmin.hcgweb.net/admin/monitor.php/cp/analytics/all_sites_summary');
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

      // Keep things pretty. Removes the auth code from the URL.
      if ($code != '')
      {
         redirect('cp/analytics/all_sites_summary');
      }
      
      $this->Google_analytics->set_access_token_from_session();

      $this->load->helper(array('form', 'text'));    
      $this->load->library('validation');
      
      $rules['StartDate'] = 'trim'; //|required';
      $rules['EndDate'] = 'trim'; //|required';

      $this->validation->set_rules($rules);

      $fields['StartDate'] = 'Start of Date Range';
      $fields['EndDate'] = 'End of Date Range';

      $this->validation->set_fields($fields);

      // default range is last 30 days
      $defaults['StartDate'] = date('Y-m-d', time()-2592000);
      $defaults['EndDate'] = date('Y-m-d');
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $data['summary'] = array();

      if ($this->validation->run() == TRUE)
      {
         $data['summary'] = $this->_all_sites_summary();
//         $data['summary'] = $this->_accounts();
      }

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Analytics');
      $data['is_authorized'] = $this->Google_analytics->is_authorized();
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['errors'] = htmlspecialchars($this->Google_analytics->get_error(), ENT_NOQUOTES);

      
      $this->load->vars($data);
   	
      return $this->load->view('cp/analytics/all-sites', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Processes the form and gathers the data
    *
    */
   function _all_sites_summary()
   {
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $data = $this->Google_analytics->get_all_sites_summary($values['StartDate'], $values['EndDate']);
      
      return $data;

   }

   // --------------------------------------------------------------------

   /**
    * Lists stats for all sites in a table for the given date range.
    *
    */
   function all_sites_status($action = '')
   {
      $admin['message'] = $this->session->userdata('monitor_message');
      if ($this->session->userdata('monitor_message') != '')
         $this->session->set_userdata('monitor_message', '');
         
      // allows you to reset the session variable if you need to.
      if ($action == 'reset')
      {
         $this->session->unset_userdata('accessToken');
      }
      
      $code = (isset($_GET['code'])) ? $_GET['code'] : '';

      $this->load->model('Google_analytics');
      $this->Google_analytics->set_redirect_uri('http://webadmin.hcgweb.net/admin/monitor.php/cp/analytics/all_sites_status');
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

      // Keep things pretty. Removes the auth code from the URL.
      if ($code != '')
      {
         redirect('cp/analytics/all_sites_status');
      }
      
      $this->Google_analytics->set_access_token_from_session();

      $data['summary'] = $this->Google_analytics->get_all_sites_status();

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('monitor');
      
      $data['admin'] = $admin;
      $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
      $data['submenu'] = get_cp_submenu('Analytics');
      $data['is_authorized'] = $this->Google_analytics->is_authorized();
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['errors'] = htmlspecialchars($this->Google_analytics->get_error(), ENT_NOQUOTES);

      
      $this->load->vars($data);
   	
      return $this->load->view('cp/analytics/all-status', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Temporary function: gets accounts and puts data in a database table.
    *
    */
   function _accounts()
   {
      $this->load->model('Analytics_source');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $data = $this->Google_analytics->get_accounts();
      
      return $data;

   }

}