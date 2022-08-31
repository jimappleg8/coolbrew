<?php

// Demo of Google Analytics functionality from the Google API site
// converted to CoolBrew/Codeigniter

class Demo extends Controller {

   // --------------------------------------------------------------------

   function Demo()
   {
      parent::Controller();
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * This is the main file for the Google Analytics API PHP demo.
    * This file serves as the controller for all the demos. Based on various
    * query parameters, this file will take users through the authorization
    * process as well as load and run the appropriate demo.
    * Usage:
    *   You need to download and have the google-api-php client library.
    *   You must register for a project in the Google APIs console. Here:
    *     https://code.google.com/apis/console/?api=analytics
    *   From the console you must:
    *   - enable Analytics API access
    *   - Register for OAuth2.0
    *   - The Redirect URL must be the same as defined in the constant below.
    *     this value should be the exact location of this script.
    *   Once complete you must copy the Client ID and Client Secret values from
    *   the APIs console into the constants below.
    *
    * If successful, you should be able to run the Hello Analytics API sample.
    * @author Nick Mihailovski <api.nickm@gmail.com>
    */
   function index()
   {
      require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/src/Google_Client.php';
      require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/src/contrib/Google_AnalyticsService.php';
      
      $demoErrors = null;
      $thisPage = '/admin/monitor.php/cp/demo/index';

      $authUrl = $thisPage.'?action=auth';
      $revokeUrl = $thisPage.'?action=revoke';

      $helloAnalyticsDemoUrl = $thisPage.'?demo=hello';
      $mgmtApiDemoUrl = $thisPage.'?demo=mgmt';
      $coreReportingDemoUrl = $thisPage.'?demo=reporting';

      // make sure parameters are defined
      $action = (isset($_GET['action'])) ? $_GET['action'] : '';
      $code = (isset($_GET['code'])) ? $_GET['code'] : '';
      $demo = (isset($_GET['demo'])) ? $_GET['demo'] : '';
      $tableId = (isset($_GET['tableId'])) ? $_GET['tableId'] : null;
      $htmlOutput = '';
      $demoError = '';
      
      // allows you to reset the session variable if you need to.
      if ($action == 'reset')
      {
         $this->session->unset_userdata('demoToken');
      }

      // Build a new client object to work with authorization.
      $client = new Google_Client();
      $client->setClientId('322182483029.apps.googleusercontent.com');
      $client->setClientSecret('tLglwN6BmMU7-N930qgYTZpZ');
      $client->setRedirectUri('http://webadmin.hcgweb.net/admin/monitor.php/cp/demo/index');
      $client->setApplicationName('Google Analytics Sample Application');
      $client->setScopes(array(
         'https://www.googleapis.com/auth/analytics.readonly'
      ));

      // Magic. Returns objects from the Analytics Service
      // instead of associative arrays.
      $client->setUseObjects(true);

      $this->load->library('Authhelper');
      $this->authhelper->setClient($client);
      $this->authhelper->setControllerUrl($thisPage);
      
      // Main controller logic.

      if ($action == 'revoke')
      {
         $this->authhelper->revokeToken();
      }
      else if ($action == 'auth' || $code != '')
      {
         $this->authhelper->authenticate();
      }
      else
      {
         $this->authhelper->setTokenFromStorage();

         if ($this->authhelper->isAuthorized())
         {
            $analytics = new Google_AnalyticsService($client);

            if ($demo == 'hello')
            {
               // Hello Analytics API Demo.
               require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/examples/analytics/demo/helloAnalyticsApi.php';

               $demo = new HelloAnalyticsApi($analytics);
               $htmlOutput = $demo->getHtmlOutput();
               $demoError = $demo->getError();
            }
            else if ($demo == 'mgmt')
            {
               // Management API Reference Demo.
               require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/examples/analytics/demo/managementApiReference.php';

               $demo = new ManagementApiReference($analytics);
               $htmlOutput = $demo->getHtmlOutput();
               $demoError = $demo->getError();
            }
            else if ($demo == 'reporting')
            {
               // Core Reporting API Reference Demo.
               require_once BASEPATH.'modules/monitor/libraries/google-api-php-client/examples/analytics/demo/coreReportingApiReference.php';

               $demo = new coreReportingApiReference($analytics, $thisPage);
               $htmlOutput = $demo->getHtmlOutput($tableId);
               $demoError = $demo->getError();
            }
         }

         // The PHP library will try to update the access token
         // (via the refresh token) when an API request is made.
         // So the actual token in apiClient can be different after
         // a require through Google_AnalyticsService is made. Here we
         // make sure whatever the valid token in $service is also
         // persisted into storage.
         $this->session->set_userdata('demoToken', $client->getAccessToken());
      }

      // Consolidate errors and make sure they are safe to write.
      $errors = $demoError ? $demoError : $this->authhelper->getError();
      $errors = htmlspecialchars($errors, ENT_NOQUOTES);

      $data['authorized'] = $this->authhelper->isAuthorized();
      $data['revoke_url'] = $revokeUrl;
      $data['auth_url'] = $authUrl;
      $data['hello_url'] = $helloAnalyticsDemoUrl;
      $data['mgmt_url'] = $mgmtApiDemoUrl;
      $data['report_url'] = $coreReportingDemoUrl;
      $data['errors'] = $errors;
      $data['html_output'] = $htmlOutput;

      $this->load->vars($data);
   	
      return $this->load->view('cp/demo/home', NULL, TRUE);
   }

}