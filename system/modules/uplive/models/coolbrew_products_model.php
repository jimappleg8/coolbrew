<?php

class Coolbrew_products_model extends Model {

   var $dev_db;
   var $stage_db;
   var $live_db;

   // --------------------------------------------------------------------

   function Coolbrew_products_model()
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
    * Moves all product data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_all($source, $target, $site_id = 'all', $where = '')
   {
      $msgs = array();
      
      $msgs[] = $this->move_pr_product_site($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_ingredient($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_ingredient_link($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_product($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_product_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_nlea($source, $target, $site_id, $where);
      $msgs[] = $this->move_pr_symbol($source, $target, $site_id, $where);
     
      $msg = implode('<br />', $msgs).'<br />';
      
      return $msg;
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT c.* '.
                         'FROM pr_category AS c '.
                         'WHERE c.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_ingredient data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_ingredient($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_ingredient';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT i.* '.
                         'FROM pr_ingredient AS i '.
                         'WHERE i.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_ingredient_link data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_ingredient_link($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_ingredient_link';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT il.* '.
                         'FROM pr_ingredient_link AS il, pr_ingredient AS i '.
                         'WHERE il.IngredientID = i.ID '.
                         'AND i.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_product data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_product($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_product';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT p.* '.
                         'FROM pr_product AS p, pr_product_site AS ps '.
                         'WHERE p.ProductID = ps.ProductID '.
                         'AND ps.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }
   
   // --------------------------------------------------------------------

   /**
    * Moves pr_product_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_product_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_product_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT pc.* '.
                         'FROM pr_product_category AS pc, pr_category AS c '.
                         'WHERE pc.CategoryID = c.CategoryID '.
                         'AND c.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_product_site data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_product_site($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_product_site';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT ps.* '.
                         'FROM pr_product_site AS ps '.
                         'WHERE ps.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_nlea data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_nlea($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_nlea';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT n.* '.
                         'FROM pr_nlea AS n, pr_product_site AS ps '.
                         'WHERE n.ProductID = ps.ProductID '.
                         'AND ps.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves pr_symbol data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_pr_symbol($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'pr_symbol';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      // This function copies the same data regardless of  
      // whether Site ID is selected or not.
      $settings['sql'] = 'SELECT * FROM pr_symbol';

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