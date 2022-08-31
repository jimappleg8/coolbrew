<?php

/*
This version of the model uses a service account. I am not able to use this method
until I upgrade to PHP 5.3, so I have stored this here to use as a reference once
it is possible to use a service account.
*/

/*
This model attempts to make the process of authenticating and interacting with
the Google API as invisible as possible. The goal is to make it so that you just
initialize the model and request data like any model pulling data from a database.
*/

require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/src/Google_Client.php';
require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/src/contrib/Google_AnalyticsService.php';

class Google_analytics_model extends Model {

   /**
    * The Google API client ID
    */
   var $gapi_client_id = '322182483029-venbejesf73q1bk5reeorfm0eavpaulr.apps.googleusercontent.com';
   
   /**
    * The Google API email
    */
   var $gapi_email = '322182483029-venbejesf73q1bk5reeorfm0eavpaulr@developer.gserviceaccount.com';

   /**
    * The Google API application name
    */
   var $gapi_application_name = 'Google Analytics Dashboard';

   /**
    * The Google API scopes
    */
   var $gapi_scopes = array(
      'https://www.googleapis.com/auth/analytics.readonly',
   );

   /**
    * The Google API client object
    */
   var $gapi_client;

   
   // --------------------------------------------------------------------

   function Google_analytics_model()
   {
      parent::Model();
      $this->load->library('session');
      
//      $this->set_appliction_name($params['application_name']);
      $this->set_private_key();
      
      // create the client object
      $this->gapi_client = new Google_Client();
      $this->gapi_client->setApplicationName($this->gapi_application_name);
      
      if ($this->session->userdata('token') != '')
      {
         $this->gapi_client->setAccessToken($this->session->userdata('token'));
      }
      
      $this->gapi_client->setClientId($this->gapi_client_id);
      
      // Returns objects from the Analytics Service
      // instead of associative arrays.
      $this->gapi_client->setUseObjects(true);
      
      $assertion = new Google_AssertionCredentials($this->gapi_email, $this->gapi_scopes, $this->gapi_private_key);
      
      // set assertion credentials
      $this->gapi_client->setAssertionCredentials($assertion);

      $this->gapi_client->setAccessType('offline_access');  // this may be unnecessary?

   }

   // --------------------------------------------------------------------

   function set_private_key()
   {
      $path = BASEPATH.'modules/monitor/models/86a59f051d9407381853efc99e661bae267d05f8-privatekey.p12';
      $this->gapi_private_key = file_get_contents($path);
   }

   // --------------------------------------------------------------------

   function set_application_name($application_name)
   {
      $this->gapi_application_name = $application_name;
   }

   // --------------------------------------------------------------------

   function get_client()
   {
      return $this->gapi_client;
   }

   // --------------------------------------------------------------------

   function get_all_sites_summary($startDate, $endDate)
   {
      $service = new Google_AnalyticsService($this->gapi_client);
//      $service->data_ga->get($ids, $startDate, $endDate, $metrics, $optParams);
      
      $optParams = array(
        'dimensions' => 'ga:source,ga:keyword',
        'sort' => '-ga:visits,ga:keyword',
        'filters' => 'ga:medium==organic',
        'max-results' => '25');

      $result = $service->data_ga->get(
        'ga:5084868',
        $startDate,
        $endDate,
        'ga:visits',
        $optParams);
      
      // We want to update the cached access token after each call to the API.
      if ($this->gapi_client->getAccessToken())
      {
         $this->session->set_userdata('token', $this->gapi_client->getAccessToken());
      }
      
      return $result;
   }



}

/* End of file google_analytics_model.php */
/* Location: ./system/modules/monitor/models/google_analytics_model.php */