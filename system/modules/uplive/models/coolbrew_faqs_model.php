<?php

class Coolbrew_faqs_model extends Model {

   var $dev_db;
   var $stage_db;
   var $live_db;

   // --------------------------------------------------------------------

   function Coolbrew_faqs_model()
   {
      parent::Model();
      $this->load->library('Dbtools');

      $this->dev_db = $this->load->database('dev-write', TRUE);
      $this->stage_db = $this->load->database('stage-write', TRUE);
      $this->live_db = $this->load->database('live-write', TRUE);

      $config['hostname'] = "bolwebdev1:3306";
      $config['username'] = "brewuser_test";
      $config['password'] = "fr33tyr8";
      $config['database'] = "coolbrew_test";
      $config['dbdriver'] = "mysql";
      $config['dbprefix'] = "";
      $config['active_r'] = TRUE;
      $config['pconnect'] = TRUE;
      $config['db_debug'] = TRUE;
      $config['cache_on'] = FALSE;
      $config['cachedir'] = ""; 
      $config['char_set'] = "utf8";
      $config['dbcollat'] = "utf8_general_ci";
      $this->test_db = $this->load->database($config, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Moves all faq data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faq_all($source, $target, $site_id = 'all', $where = '')
   {
      $msgs = array();
      
      $msgs[] = $this->move_faqs_answer($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_index($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_item($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_item_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_item_product($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_item_product_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_keyword($source, $target, $site_id, $where);
      $msgs[] = $this->move_faqs_site($source, $target, $site_id, $where);
     
      $msg = implode('<br />', $msgs).'<br />';
      
      return $msg;
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_answer data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_answer($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_answer';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT a.* '.
                         'FROM faqs_site as s, faqs_answer AS a '.
                         'WHERE a.ID = s.AnswerID '.
                         'AND s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT c.* '.
                         'FROM faqs_category AS c '.
                         'WHERE c.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_index data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_index($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_index';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT i.* '.
                         'FROM faqs_site as s, faqs_item AS f, faqs_index AS i '.
                         'WHERE s.FaqID = f.ID '.
                         'AND f.ID = i.FaqID '.
                         'AND s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_item data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_item($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_item';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT f.* '.
                         'FROM faqs_site as s, faqs_item AS f '.
                         'WHERE f.ID = s.FaqID '.
                         'AND s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_item_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_item_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_item_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT c.* '.
                         'FROM faqs_site as s, faqs_item_category AS c '.
                         'WHERE c.FaqID = s.FaqID '.
                         'AND s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_item_product data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_item_product($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_item_product';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT p.* '.
                         'FROM faqs_site as s, faqs_item_product AS p '.
                         'WHERE p.FaqID = s.FaqID '.
                         'AND s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_item_product_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_item_product_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_item_product_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT c.* '.
                         'FROM faqs_site as s, faqs_item_product_category AS c '.
                         'WHERE c.FaqID = s.FaqID '.
                         'AND s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_keyword data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_keyword($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_keyword';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT k.* '.
                         'FROM faqs_keyword AS k '.
                         'WHERE k.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves faqs_site data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_faqs_site($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'faqs_site';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT s.* '.
                         'FROM faqs_site AS s '.
                         'WHERE s.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves table's data from one database to another
    *
    * @access   public
    * @param    array      all the info needed to make the move
    * @return   string     the confirmation message
    */
   function _move_table($settings)
   {
      $table   = $settings['table'];
      $source  = $settings['source'];
      $target  = $settings['target'];
      $src_db  = $settings['src_db'];
      $tgt_db  = $settings['tgt_db'];
      $site_id = $settings['site_id'];
      $where   = $settings['where'];
      $mywhere = $settings['mywhere'];
      
      $where_msg = '';
      
      if ($site_id != 'all')
      {
         $sql = $settings['sql'];
         $where_msg .= 'WHERE SiteID = "'.$site_id.'"';
         if ($where != '')
         {
            $sql .= ' AND '.$where;
            $where_msg .= ' AND '.$where;
         }
         $this->dbtools->db_table_copy_sql($this->$src_db, $this->$tgt_db, $table, $sql);
      }
      elseif ($where != '')
      {
         $sql = 'SELECT * FROM '.$table;
         if ($mywhere != '')
         {
            $sql .= ' WHERE '.$mywhere;
            $where_msg .= ' WHERE '.$mywhere;
         }
         $this->dbtools->db_table_copy_sql($this->$src_db, $this->$tgt_db, $table, $sql);
      }
      else
      {
         $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, $table);
      }

      $msg = 'Successfully updated '.$this->$tgt_db->database.
             ': '.$table.' on '.$target;
      if ($where_msg != '')
      {
         $msg .= ' '.$where_msg;
      }
      return $msg;
   }

}

/* End of file coolbrew_products_model.php */
/* Location: ./system/modules/uplive/models/coolbrew_products_model.php */