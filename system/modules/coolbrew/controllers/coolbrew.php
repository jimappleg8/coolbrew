<?php

/* NOTES

RoR has a db/ folder which contains the database schema in schema.rb. db/migrate contains all the sequence of Migrations for your schema.

Ror has a public/ folder that contains the css/, images/, and js/ folders. In the RoR model, this is where you point your webserver document root.

RoR has a test/ folder that contains all the "unit and functional tests along with fixtures. When using the script/generate scripts, template test files will be generated for you and placed in this directory."

RoR has a script/ folder which contains helper scripts for automation and generation.

scripts:

Creating an empty rails application

rails cookbook
	creates a cookbook subdirectory containing a complete directory tree of folders and files for an empty Rails application. This also seems to create the script/generate script that is used to generate the rest of the items. I'm not sure why they do it this way, but I'm guessing there is a good reason. From my perspective, there's no reason that I can't do the same. These scripts can still pull from the coolbrew module.
	
	  create  
      create  app/controllers
      create  app/helpers
      create  app/models
      create  app/views/layouts
      create  config/environments
      create  config/initializers
      create  db
      create  doc
      create  lib
      create  lib/tasks
      create  log
      create  public/images
      create  public/javascripts
      create  public/stylesheets
      create  script/performance
      create  script/process
      create  test/fixtures
      create  test/functional
      create  test/integration
      create  test/mocks/development
      create  test/mocks/test
      create  test/unit
      create  vendor
      create  vendor/plugins
      create  tmp/sessions
      create  tmp/sockets
      create  tmp/cache
      create  tmp/pids
      create  Rakefile
      create  README
      create  app/controllers/application.rb
      create  app/helpers/application_helper.rb
      create  test/test_helper.rb
      create  config/database.yml
      create  config/routes.rb
      create  public/.htaccess
      create  config/initializers/inflections.rb
      create  config/initializers/mime_types.rb
      create  config/boot.rb
      create  config/environment.rb
      create  config/environments/production.rb
      create  config/environments/development.rb
      create  config/environments/test.rb
      create  script/about
      create  script/console
      create  script/destroy
      create  script/generate
      create  script/performance/benchmarker
      create  script/performance/profiler
      create  script/performance/request
      create  script/process/reaper
      create  script/process/spawner
      create  script/process/inspector
      create  script/runner
      create  script/server
      create  script/plugin
      create  public/dispatch.rb
      create  public/dispatch.cgi
      create  public/dispatch.fcgi
      create  public/404.html
      create  public/422.html
      create  public/500.html
      create  public/index.html
      create  public/favicon.ico
      create  public/robots.txt
      create  public/images/rails.png
      create  public/javascripts/prototype.js
      create  public/javascripts/effects.js
      create  public/javascripts/dragdrop.js
      create  public/javascripts/controls.js
      create  public/javascripts/application.js
      create  doc/README_FOR_APP
      create  log/server.log
      create  log/production.log
      create  log/development.log
      create  log/test.log

*/

// For simplicity sake, the program will create a module in the folder in which
// you run the program, so if you want it in the usual place, you should go to
// the modules folder before running the script.

class Coolbrew extends Controller {

   var $base_path = '';
//   var $base_path = '/var/opt/httpd/system';
//   var $base_path = '/Users/japplega/Desktop/websites/system';
   
   var $data = array();
   
   var $config_files = array(
       'module/config/autoload' => 'config/autoload.php',
       'module/config/config' => 'config/config.php',
       'module/config/database' => 'config/database.php',
       'module/config/hooks' => 'config/hooks.php',
       'module/config/mimes' => 'config/mimes.php',
       'module/config/routes' => 'config/routes.php',
       'module/config/smileys' => 'config/smileys.php',
       'module/config/user_agents' => 'config/user_agents.php',
       );

   var $errors_files = array(
       'module/errors/error_404' => 'errors/error_404.php',
       'module/errors/error_db' => 'errors/error_db.php',
       'module/errors/error_general' => 'errors/error_general.php',
       'module/errors/error_php' => 'errors/error_php.php',
       );
    
   var $scripts = array(
       'module/scripts/generate' => 'scripts/generate',
       );

    var $module_name = '';
    var $module_path = '';
    
    var $debug = FALSE;
    var $verbose = FALSE;

   // --------------------------------------------------------------------

   function Coolbrew()
   {
      parent::Controller();
      $this->base_path = getcwd();
   }
   
   // --------------------------------------------------------------------

   function index()
   {
      $this->process_arguments();
      
      // create main module folder
      $this->create_folder($this->base_path, $this->module_name);
      
      $this->create_folder($this->module_path, 'config');
      $this->create_files($this->config_files);
      $this->create_folder($this->module_path, 'controllers');
      $this->create_folder($this->module_path, 'db');
//      $this->create_folder($this->module_path, 'db/migrate');
      $this->create_folder($this->module_path, 'docs', FALSE);
      $this->create_folder($this->module_path, 'errors');
      $this->create_files($this->errors_files);
      $this->create_folder($this->module_path, 'helpers');
      $this->create_folder($this->module_path, 'hooks');
//      $this->create_folder($this->module_path, 'language');
      $this->create_folder($this->module_path, 'libraries');
      $this->create_folder($this->module_path, 'models');
      $this->create_folder($this->module_path, 'public');
      $this->create_folder($this->module_path, 'public/css');
      $this->create_folder($this->module_path, 'public/images');
      $this->create_folder($this->module_path, 'public/js');
      $this->create_folder($this->module_path, 'scripts');
      $this->create_files($this->scripts, 0755);
      $this->create_folder($this->module_path, 'views');
      
      echo "\n";
   }
   
   // --------------------------------------------------------------------

   function process_arguments()
   {
      // get the arguments passed to the script
      if ($_SERVER['argc'] < 2)
      {
         $this->display_usage();
      }

      for ($i=1; $i<$_SERVER['argc']; $i++)
      {
         switch ($_SERVER['argv'][$i])
         {
            case '-d':
               $this->debug = TRUE;
               break;
            case '-v':
               $this->verbose = TRUE;
               break;
            default:
               $this->module_name = $_SERVER['argv'][$i];
               break;
         }
      }

      if ($this->module_name == '')
      {
         $this->display_usage();
      }
      
      $this->data['module_name'] = $this->module_name;
      $this->module_path = $this->base_path.'/'.$this->module_name;

      return TRUE;
   }
   
   // --------------------------------------------------------------------

   function create_folder($path, $folder, $add_index = TRUE)
   {
      $this->load->helper(array('file', 'text'));

      if (mkdir($path.'/'.$folder, 0777))
      {
         echo "create  ".$folder."\n";
      }
      else
      {
         echo "unable to create  ".$folder." (".$php_errormsg.")\n";
         exit;
      }
      
      if ($add_index)
      {
         // place the no-access index.html file in the folder
         $content = $this->load->view('module/no-access', NULL, TRUE);
         write_file($path.'/'.$folder.'/index.html', $content);
         chmod($path.'/'.$folder.'/index.html', 0664);
         echo "create  ".$folder."/index.html\n";
      }
   }

   // --------------------------------------------------------------------

   function create_files($file_array, $mode = 0664)
   {
      $this->load->helper(array('file', 'text'));

      foreach ($file_array AS $source => $destination)
      {
         $content = $this->load->view($source, $this->data, TRUE);
         $content = entities_to_ascii($content);
         write_file($this->module_path.'/'.$destination, $content);
         chmod($this->module_path.'/'.$destination, $mode);
         echo "create  ".$destination."\n";
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   function display_usage()
   {
      echo "usage: ../bin/coolbrew [options] module_name\n\n";
      exit;
   }

}
?>