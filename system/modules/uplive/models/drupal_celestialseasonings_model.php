<?php

/**
 * Model for the drupal_celestialseasonings databases
 *
 * This model encodes what we know about how the various tables in this
 * database need to be uploaded, including what tables should be excluded
 * and which ones need special treatment to ensure data integrity.
 *
 */

class Drupal_celestialseasonings_model extends Model {

   var $dev_db;
   var $stage_db;
   var $live_db;

   /**
    * Tables that should not be copied at all
    */
   var $excluded = array(
      'cache',
      'cache_block',
      'cache_bootstrap',
      'cache_field',
      'cache_filter',
      'cache_form',
      'cache_image',
      'cache_menu',
      'cache_metatag',
      'cache_page',
      'cache_path',
      'cache_token',
      'cache_update',
      'cache_views',
      'cache_views_data',
      'hcgnewsletter_signup',
      'hcgnewsletter_coupon',
      'watchdog',
   );
   /**
    * Tables that are managed by move_nodes()
    */
   var $managed = array(
   );      
      
   // --------------------------------------------------------------------

   function Drupal_imagine_model()
   {
      parent::Model();
      $this->load->library('Dbtools');

      $config['hostname'] = "bolwebdev1:3306";
      $config['username'] = "csDrupaler";
      $config['password'] = "br3wT3a";
      $config['database'] = "drupal_celestialseasonings_dev";
      $config['dbdriver'] = "mysql";
      $config['dbprefix'] = "";
      $config['active_r'] = TRUE;
      $config['pconnect'] = TRUE;
      $config['db_debug'] = TRUE;
      $config['cache_on'] = FALSE;
      $config['cachedir'] = ""; 
      $config['char_set'] = "utf8";
      $config['dbcollat'] = "utf8_general_ci";
      $this->dev_db = $this->load->database($config, TRUE);

      $config['hostname'] = "mysql-app-master:3306";
      $config['username'] = "csDrupaler";
      $config['password'] = "Sl33pyB34r";
      $config['database'] = "drupal_celestialseasonings_stage";
      $this->stage_db = $this->load->database($config, TRUE);
      
      $config['hostname'] = "bolwebdb1:3306";
      $config['username'] = "csDrupaler";
      $config['password'] = "Sl33pyB34r";
      $config['database'] = "drupal_celestialseasonings_live";
      $this->live_db = $this->load->database($config, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Moves Imagine tables from one database to another
    *
    * @access   public
    * @param    string      the source (dev, stage or live)
    * @param    string      the destination (dev, stage or live)
    * @param    string      whether to move the excluded tables
    * @return   string
    */
   function move_tables($source, $target, $move_all = FALSE)
   {
      set_time_limit(0);

      $src_db = $source.'_db';
      $tgt_db = $target.'_db';

      // get a list of all tables in the database
      $src_tables = $this->dbtools->list_tables($this->$src_db);
      $tgt_tables = $this->dbtools->list_tables($this->$tgt_db);
      
      $skip_me = array_merge($this->excluded, $this->managed);
     
      // move over the basic tables
      foreach ($src_tables AS $table)
      {
         ob_start();

         $key = array_search($table, $tgt_tables);
         if ($key !== FALSE)
         {
            unset($tgt_tables[$key]);
         }

         if (in_array($table, $skip_me) && $move_all == FALSE)
         {
            echo 'Skipping: '.$table.'<br />';
            continue;
         }

         echo 'Moving: '.$table.' ('.$source.'->'.$target.')<br />';
         $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, $table);

         ob_end_flush();
         flush();
      }
      

      // drop any tables that are at the target but are not at the source
      foreach ($tgt_tables AS $table)
      {
         ob_start();

         echo 'Dropping: '.$table.' (on '.$target.')<br />';
         $this->dbtools->drop_table($this->$tgt_db, $table);

         ob_end_flush();
         flush();
      }
      
      $this->move_nodes($source, $target, $move_all);
   }
      
   // --------------------------------------------------------------------
   
   /**
    * Deals with any special nodes that need to be moved
    *
    */
   function move_nodes($source, $target, $move_all)
   {

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Sets a value in the Drupal "variable" table
    *
    * This method required clearing the cache tables which may not 
    * always be ideal, but I don't think it's a big deal in our case.
    *
    * Examples:
    *
    * take the site offline:
    *   set_drupal_variable('site_offline', 's:1:"1";');
    *
    * bring the site back online
    *   set_drupal_variable('site_offline', 's:1:"0";');
    *
    * set the error reporting to "write errors to the log"
    *   set_drupal_variable('error_level', 's:1:"0";');
    *
    */
   function set_drupal_variable($name, $value)
   {
      $query_array = array();
      
      $query_array[] = "UPDATE variable SET value='".$value."' ".
                       "WHERE name = '".$name."';";
      $query_array[] = 'TRUNCATE TABLE cache;';
      $query_array[] = 'TRUNCATE TABLE cache_content;';
      $query_array[] = 'TRUNCATE TABLE cache_page;';
      
      foreach ($query_array as $sql)
      {
         $tgt_db->query($sql);
      }
      return TRUE;
   }


}

/* End of file drupal_imagine_model.php */
/* Location: ./system/modules/uplive/models/drupal_imagine_model.php */