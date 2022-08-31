<?php

/**
 * Model for the drupal_imagine databases
 *
 * This model encodes what we know about how the various tables in this
 * database need to be uploaded, including what tables should be excluded
 * and which ones need special treatment to ensure data integrity.
 *
 */

class Drupal_imagine_model extends Model {

   var $dev_db;
   var $stage_db;
   var $live_db;

   /**
    * Tables that should not be copied at all
    */
   var $excluded = array(
      'cache',
      'cache_block',
      'cache_content',
      'cache_filter',
      'cache_form',
      'cache_menu',
      'cache_page',
      'cache_rules',
      'cache_update',
      'cache_views',
      'cache_views_data',
      'comments',
      'flag_content',
      'flag_counts',
      'flood',
      'history',
      'print_mail_page_counter',
      'print_page_counter',
      'profile_values',   // not being used yet
      'sessions',
      'temporary_imagine_node',
      'users',
      'votingapi_cache',  // used by fivestar module
      'votingapi_vote',   // used by fivestar module
      'watchdog',
   );
   /**
    * Tables that are managed by move_nodes()
    */
   var $managed = array(
      'content_type_recipe_lab_submission', 
      'node',
      'node_revisions',
   );      
      
   // --------------------------------------------------------------------

   function Drupal_imagine_model()
   {
      parent::Model();
      $this->load->library('Dbtools');

      $config['hostname'] = "bolwebdev1:3306";
      $config['username'] = "ifDrupaler";
      $config['password'] = "tybBiftIr";
      $config['database'] = "drupal_imagine";
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
      $config['username'] = "ifDrupaler";
      $config['password'] = "tybBiftIr";
      $config['database'] = "drupal_imagine_stage";
      $this->stage_db = $this->load->database($config, TRUE);
      
      $config['hostname'] = "bolwebdb1:3306";
      $config['username'] = "ifDrupaler";
      $config['password'] = "tybBiftIr";
      $config['database'] = "drupal_imagine_live";
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
    * Deals with the recipe lab submissions
    *
    * This function works by pulling the user-generated nodes out of the
    * live database and into a temporary table. They are then re-inserted
    * after the managed configuration and content is moved live.
    *
    */
   function move_nodes($source, $target, $move_all)
   {
      // check if the tables have already been moved
      if ($move_all == TRUE)
      {
         return;
      }
      
      set_time_limit(0);

      $src_db = $source.'_db';
      $tgt_db = $target.'_db';

      // from dev to staging, we can just copy the tables directly.
      if ($source == 'dev' && $target == 'stage')
      {
         foreach ($this->managed AS $table)
         {
            ob_start();

            echo 'Moving: '.$table.' ('.$source.'->'.$target.')<br />';
            $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, $table);

            ob_end_flush();
            flush();
         }
         return;
      }
      
      // clear the recipe_lab_submissions nodes on the staging site

      echo 'Deleting recipe_lab_submission nodes on source server.<br />';

      $sql = 'SELECT nid FROM node WHERE type = "recipe_lab_submission"';
      $query = $this->$src_db->query($sql);
      $nodes = $query->result_array();
      
      $query_array = array();
      foreach ($nodes AS $node)
      {
         $query_array[] = 'DELETE FROM node WHERE nid = '.$node['nid'].';';
         $query_array[] = 'DELETE FROM node_revisions WHERE nid = '.$node['nid'].';';
      }
      $query_array[] = 'TRUNCATE TABLE content_type_recipe_lab_submission;';
      $query_array[] = $this->create_temporary_imagine_node();
      $query_array[] = 'TRUNCATE TABLE temporary_imagine_node;';
      foreach ($query_array as $sql)
      {
         $result = $this->$src_db->query($sql);
      }
      
      // TODO: I may want to reset the counter on nodes and node_revisions at 
      // this point, but I don't know that it is particularly important.

      // Get a list of all nodes of type recipe_lab_submission from the live database
      // and save them to a temporary table that has all the info that is needed to 
      // recreate them in the drupal database structure.
      //
      // The temporary table is not really used, but I am creating it as a kind of
      // backup if something goes wrong. Also, if the number of submitted recipes 
      // gets very large, we may need to use the table instead of counting on the
      // data being in memory.
      //
      // The node_access table can be ignored because it is not used in this case.
      // The node_type table can also be ignored because it contains config info.
      
      echo 'Populating temporary table from the target server.<br />';

      $sql = 'SELECT n.*, nr.body, nr.teaser, nr.log, nr.timestamp, '.
               'nr.format, rl.field_labrecipe_ingredients_value, '.
               'rl.field_labrecipe_ingredients_format, rl.field_recipe_submitter_uid, '.
               'rl.field_remove_edit_value, rl.field_share_value '.
             'FROM node AS n '.
             'LEFT JOIN node_revisions AS nr '.
               'ON n.vid = nr.vid '.
             'LEFT JOIN content_type_recipe_lab_submission AS rl '.
               'ON n.vid = rl.vid '.
             'WHERE n.type = "recipe_lab_submission"';
      $query = $this->$tgt_db->query($sql);
      $temps = $query->result_array();
      
      foreach ($temps AS $temp)
      {
         $this->$src_db->insert('temporary_imagine_node', $temp);
      }
      
      // re-insert the recipe_lab_submission nodes to the staging tables
      
      echo 'Re-inserting recipe lab submissions to the source database.<br />';
      foreach ($temps AS $temp)
      {
         // re-insert the node record
         $node = array();
         $node['vid'] = 0;
         $node['type'] = $temp['type'];
         $node['language'] = $temp['language'];
         $node['title'] = $temp['title'];
         $node['uid'] = $temp['uid'];
         $node['status'] = $temp['status'];
         $node['created'] = $temp['created'];
         $node['changed'] = $temp['changed'];
         $node['comment'] = $temp['comment'];
         $node['promote'] = $temp['promote'];
         $node['moderate'] = $temp['moderate'];
         $node['sticky'] = $temp['sticky'];
         $node['tnid'] = $temp['tnid'];
         $node['translate'] = $temp['translate'];

         $this->$src_db->insert('node', $node);
         $nid = $this->$src_db->insert_id();

         // re-insert the node_revisions record
         $revision = array();
         $revision['nid'] = $nid;
         $revision['uid'] = $temp['uid'];
         $revision['title'] = $temp['title'];
         $revision['body'] = $temp['body'];
         $revision['teaser'] = $temp['teaser'];
         $revision['log'] = $temp['log'];
         $revision['timestamp'] = $temp['timestamp'];
         $revision['format'] = $temp['format'];
         
         $this->$src_db->insert('node_revisions', $revision);
         $vid = $this->$src_db->insert_id();
         
         // update the node record with the new vid
         $this->$src_db->where('nid', $nid);
         $update = array();
         $update['vid'] = $vid;
         $this->$src_db->update('node', $update);
         
         // re-insert the content_type_recipe_lab_submission record
         $recipe = array();
         $recipe['vid'] = $vid;
         $recipe['nid'] = $nid;
         $recipe['field_labrecipe_ingredients_value'] = $temp['field_labrecipe_ingredients_value'];
         $recipe['field_labrecipe_ingredients_format'] = $temp['field_labrecipe_ingredients_format'];
         $recipe['field_recipe_submitter_uid'] = $temp['field_recipe_submitter_uid'];
         $recipe['field_remove_edit_value'] = $temp['field_remove_edit_value'];
         $recipe['field_share_value'] = $temp['field_share_value'];
         
         $this->$src_db->insert('content_type_recipe_lab_submission', $recipe); 
      }
      
      // upload the modified database tables to the live server.
      echo 'Moving: node ('.$source.'->'.$target.')<br />';
      $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, 'node');
      echo 'Moving: node_revisions ('.$source.'->'.$target.')<br />';
      $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, 'node_revisions');
      echo 'Moving: content_type_recipe_lab_submission ('.$source.'->'.$target.')<br />';
      $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, 'content_type_recipe_lab_submission');

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Returns the SQL to build the temporary_imagine_node table if it
    *  is needed.
    *
    */
   function create_temporary_imagine_node()
   {
      $create = 'CREATE TABLE IF NOT EXISTS `temporary_imagine_node` ( '.
                  '`nid` int(10) unsigned NOT NULL auto_increment, '.
                  '`vid` int(10) unsigned NOT NULL default \'0\', '.
                  '`type` varchar(32) NOT NULL default \'\', '.
                  '`language` varchar(12) NOT NULL default \'\', '.
                  '`title` varchar(255) NOT NULL default \'\', '.
                  '`uid` int(11) NOT NULL default \'0\', '.
                  '`status` int(11) NOT NULL default \'1\', '.
                  '`created` int(11) NOT NULL default \'0\', '.
                  '`changed` int(11) NOT NULL default \'0\', '.
                  '`comment` int(11) NOT NULL default \'0\', '.
                  '`promote` int(11) NOT NULL default \'0\', '.
                  '`moderate` int(11) NOT NULL default \'0\', '.
                  '`sticky` int(11) NOT NULL default \'0\', '.
                  '`tnid` int(10) unsigned NOT NULL default \'0\', '.
                  '`translate` int(11) NOT NULL default \'0\', '.
                  '`body` longtext NOT NULL, '.
                  '`teaser` longtext NOT NULL, '.
                  '`log` longtext NOT NULL, '.
                  '`timestamp` int(11) NOT NULL default \'0\', '.
                  '`format` int(11) NOT NULL default \'0\', '.
                  '`field_labrecipe_ingredients_value` longtext, '.
                  '`field_labrecipe_ingredients_format` int(10) unsigned default NULL, '.
                  '`field_recipe_submitter_uid` int(10) unsigned default NULL, '.
                  '`field_remove_edit_value` longtext, '.
                  '`field_share_value` longtext, '.
                  'PRIMARY KEY  (`nid`) '.
                ') ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ';
      return $create;
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

   // --------------------------------------------------------------------
   
   /**
    * Script written to clear out a ton of spam that was submitted through
    *  the recipe submission system. The spam was discovered in March 2013
    *  but was being created before then.
    *
    */
   function remove_recipe_spam()
   {
      $last_valid_id = '390';
      $clean_db = 'live_db';
      $ip_file = '/Users/japplega/Desktop/spam-ips-'.$clean_db.'.txt';
      $stat_file = '/Users/japplega/Desktop/spam-stats-'.$clean_db.'.txt';
      $max_records = 1000;
      
      set_time_limit(0);
      ob_start();

      $handle = fopen($ip_file, "a");
      $shandle = fopen($stat_file, "a");
      
      // create an unending loop that we will exit using break
      for ($i=0; $i<$max_records; $i++)
      {
         // Get a record from content_type_recipe
         $sql = 'SELECT vid, nid '.
                'FROM content_type_recipe '.
                'WHERE nid > '.$last_valid_id.' '.
                'LIMIT 1';
         $query = $this->$clean_db->query($sql);
         $node = $query->row_array();
         
         if (empty($node))
         {
            break;
         }

         // get the user ID for this node
         $sql = 'SELECT type, title, uid, created '.
                'FROM node '.
                'WHERE nid = '.$node['nid'] .' '.
                'AND vid = '.$node['vid'];
         $query = $this->$clean_db->query($sql);
         $user = $query->row_array();
         
         $write_me = $node['nid'].'|'.$user['type'].'|'.$user['created'].'|'.$user['title']."\n";
         echo $write_me.'<br />';
         fwrite($shandle, $write_me);
         
         // delete the records related to this node and node_revision
         $where = array('nid' => $node['nid'], 'vid' => $node['vid']);
         $this->$clean_db->delete('node', $where);
         $this->$clean_db->delete('node_revisions', $where);
         $this->$clean_db->delete('content_field_glutein_free', $where);
         $this->$clean_db->delete('content_field_recipe_category', $where);
         $this->$clean_db->delete('content_field_recipe_image', $where);
         $this->$clean_db->delete('content_field_recipe_ingredient', $where);
         $this->$clean_db->delete('content_field_recipe_serves', $where);
         $this->$clean_db->delete('content_field_recipe_source', $where);
         $this->$clean_db->delete('content_type_recipe', $where);
         $this->$clean_db->delete('content_field_recipe_featured', $where);
         $this->$clean_db->delete('content_field_recipe_product', $where);

         // delete the comment statistics
         $where = array('nid' => $node['nid']);
         $this->$clean_db->delete('node_comment_statistics', $where);

         // delete the user that created this record
         if ($user['uid'] != 0)
         {
            $where = array('uid' => $user['uid']);
            $this->$clean_db->delete('users', $where);
         }

         // delete any URL Aliases
         if ($user['uid'] != 0)
         {
            $where = array('src' => 'user/'.$user['uid']);
            $this->$clean_db->delete('url_alias', $where);
         }
         $where = array('src' => 'node/'.$node['nid']);
         $this->$clean_db->delete('url_alias', $where);

         // delete flag content for this deleted node
         $where = array('content_type' => 'node', 'content_id' => $node['nid']);
         $this->$clean_db->delete('flag_content', $where);
         $this->$clean_db->delete('flag_counts', $where);

         // delete the history entry for this node
         if ($user['uid'] != 0)
         {
            $where = array('uid' => $user['uid'], 'nid' => $node['nid']);
            $this->$clean_db->delete('history', $where);
         }

         // look for comments attached to this node
         $sql = 'SELECT *'.
                'FROM comments '.
                'WHERE nid = '.$node['nid'];
         $query = $this->$clean_db->query($sql);
         $comments = $query->result_array();

         foreach ($comments AS $comment)
         {
            if ($comment['uid'] != 0)
            {
               $where = array('uid' => $comment['uid']);
               $this->$clean_db->delete('users', $where);
            }
            $where = array('cid' => $comment['cid']);
            $this->$clean_db->delete('comments', $where);
            
            $write_me = $comment['cid'].'|comment|'.$comment['timestamp'].'|'.$comment['subject']."\n";
            echo $write_me.'<br />';
            fwrite($shandle, $write_me);

            $write_me = $comment['hostname']."\n";
            fwrite($handle, $write_me);
         }
         
         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

      }
      fclose($handle);
      fclose($shandle);
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Removes the records created with the recipe_lab_submission_shared
    *  content type. This was one of the content types that was left
    *  open to submissions by authenticated users.
    *
    */
   function remove_recipe_lab_spam()
   {
      // Get a record from node
      $clean_db = 'stage_db';
      $ip_file = '/Users/japplega/Desktop/spam-ips-'.$clean_db.'.txt';
      $stat_file = '/Users/japplega/Desktop/spam-stats-'.$clean_db.'.txt';
      $max_records = 1000;
      
      set_time_limit(0);
      ob_start();

      $handle = fopen($ip_file, "a");
      $shandle = fopen($stat_file, "a");
      
      // create an unending loop that we will exit using break
      for ($i=0; $i<$max_records; $i++)
      {
         // Get a record from node table
         $sql = 'SELECT vid, nid, type, title, uid, created '.
                'FROM node '.
                'WHERE type = "recipe_lab_submission_shared" '.
                'LIMIT 1';
         $query = $this->$clean_db->query($sql);
         $node = $query->row_array();
         
         if (empty($node))
         {
            break;
         }

         $write_me = $node['nid'].'|'.$node['type'].'|'.$node['created'].'|'.$node['title']."\n";
         echo $write_me.'<br />';
         fwrite($shandle, $write_me);
         
         // delete the records related to this node and node_revision
         $where = array('nid' => $node['nid'], 'vid' => $node['vid']);
         $this->$clean_db->delete('node', $where);
         $this->$clean_db->delete('node_revisions', $where);

         // delete the comment statistics
         $where = array('nid' => $node['nid']);
         $this->$clean_db->delete('node_comment_statistics', $where);

         // delete the user that created this record
         if ($node['uid'] != 0)
         {
            $where = array('uid' => $node['uid']);
            $this->$clean_db->delete('users', $where);
         }

         // delete any URL Aliases
         if ($node['uid'] != 0)
         {
            $where = array('src' => 'user/'.$node['uid']);
            $this->$clean_db->delete('url_alias', $where);
         }
         $where = array('src' => 'node/'.$node['nid']);
         $this->$clean_db->delete('url_alias', $where);

         // delete flag content for this deleted node
         $where = array('content_type' => 'node', 'content_id' => $node['nid']);
         $this->$clean_db->delete('flag_content', $where);
         $this->$clean_db->delete('flag_counts', $where);

         // delete the history entry for this node
         if ($node['uid'] != 0)
         {
            $where = array('uid' => $node['uid'], 'nid' => $node['nid']);
            $this->$clean_db->delete('history', $where);
         }

         // look for comments attached to this node
         $sql = 'SELECT *'.
                'FROM comments '.
                'WHERE nid = '.$node['nid'];
         $query = $this->$clean_db->query($sql);
         $comments = $query->result_array();

         foreach ($comments AS $comment)
         {
            if ($comment['uid'] != 0)
            {
               $where = array('uid' => $comment['uid']);
               $this->$clean_db->delete('users', $where);
            }
            $where = array('cid' => $comment['cid']);
            $this->$clean_db->delete('comments', $where);
            
            $write_me = $comment['cid'].'|comment|'.$comment['timestamp'].'|'.$comment['subject']."\n";
            echo $write_me.'<br />';
            fwrite($shandle, $write_me);

            $write_me = $comment['hostname']."\n";
            fwrite($handle, $write_me);
         }
         
         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

      }
      fclose($handle);
      fclose($shandle);
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   function process_ips()
   {
      // Get a record from content_type_recipe
      $clean_db = 'live_db';
      $ip_file = '/Users/japplega/Desktop/spam-ips-'.$clean_db.'.txt';
      
      set_time_limit(0);
      ob_start();
      
      setlocale(LC_ALL, 'en_US.UTF-8');

      $handle = fopen($ip_file, "r");
      
      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         $ip = trim($data[0]);
         

         $sql = 'SELECT vid, nid '.
                'FROM content_type_recipe '.
                'WHERE nid > '.$last_valid_id.' '.
                'LIMIT 1';
         $query = $this->$clean_db->query($sql);
         $node = $query->row_array();
         
         if (empty($node))
         {
            break;
         }

         // get the user ID for this node
         $sql = 'SELECT type, title, uid, created '.
                'FROM node '.
                'WHERE nid = '.$node['nid'] .' '.
                'AND vid = '.$node['vid'];
         $query = $this->$clean_db->query($sql);
         $user = $query->row_array();
         
         $write_me = $node['nid'].'|'.$user['type'].'|'.$user['created'].'|'.$user['title']."\n";
         echo $write_me.'<br />';
         fwrite($shandle, $write_me);
         
         // delete the records related to this node and node_revision
         $where = array('nid' => $node['nid'], 'vid' => $node['vid']);
         $this->$clean_db->delete('node', $where);
         $this->$clean_db->delete('node_revisions', $where);
         $this->$clean_db->delete('content_field_glutein_free', $where);
         $this->$clean_db->delete('content_field_recipe_category', $where);
         $this->$clean_db->delete('content_field_recipe_image', $where);
         $this->$clean_db->delete('content_field_recipe_ingredient', $where);
         $this->$clean_db->delete('content_field_recipe_serves', $where);
         $this->$clean_db->delete('content_field_recipe_source', $where);
         $this->$clean_db->delete('content_type_recipe', $where);
         $this->$clean_db->delete('content_field_recipe_featured', $where);
         $this->$clean_db->delete('content_field_recipe_product', $where);

         // delete the comment statistics
         $where = array('nid' => $node['nid']);
         $this->$clean_db->delete('node_comment_statistics', $where);

         // delete the user that created this record
         if ($user['uid'] != 0)
         {
            $where = array('uid' => $user['uid']);
            $this->$clean_db->delete('users', $where);
         }

         // delete any URL Aliases
         if ($user['uid'] != 0)
         {
            $where = array('src' => 'user/'.$user['uid']);
            $this->$clean_db->delete('url_alias', $where);
         }
         $where = array('src' => 'node/'.$node['nid']);
         $this->$clean_db->delete('url_alias', $where);

         // delete flag content for this deleted node
         $where = array('content_type' => 'node', 'content_id' => $node['nid']);
         $this->$clean_db->delete('flag_content', $where);
         $this->$clean_db->delete('flag_counts', $where);

         // delete the history entry for this node
         if ($user['uid'] != 0)
         {
            $where = array('uid' => $user['uid'], 'nid' => $node['nid']);
            $this->$clean_db->delete('history', $where);
         }

         // look for comments attached to this node
         $sql = 'SELECT *'.
                'FROM comments '.
                'WHERE nid = '.$node['nid'];
         $query = $this->$clean_db->query($sql);
         $comments = $query->result_array();

         foreach ($comments AS $comment)
         {
            if ($comment['uid'] != 0)
            {
               $where = array('uid' => $comment['uid']);
               $this->$clean_db->delete('users', $where);
            }
            $where = array('cid' => $comment['cid']);
            $this->$clean_db->delete('comments', $where);
            
            $write_me = $comment['cid'].'|comment|'.$comment['timestamp'].'|'.$comment['subject']."\n";
            echo $write_me.'<br />';
            fwrite($shandle, $write_me);

            $write_me = $comment['hostname']."\n";
            fwrite($handle, $write_me);
         }
         
         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

      }
      fclose($handle);
      fclose($shandle);
      return TRUE;
   }

}

/* End of file drupal_imagine_model.php */
/* Location: ./system/modules/uplive/models/drupal_imagine_model.php */