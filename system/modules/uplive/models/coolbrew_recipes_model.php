<?php

class Coolbrew_recipes_model extends Model {

   var $dev_db;
   var $stage_db;
   var $live_db;
   var $test_db;

   // --------------------------------------------------------------------

   function Coolbrew_recipes_model()
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
    * Moves all recipe data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_all($source, $target, $site_id = 'all', $where = '')
   {
      $msgs = array();
      
      $msgs[] = $this->move_rcp_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_index($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_ingredient($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_nutritional($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_nutritional_calories($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_recipe($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_recipe_category($source, $target, $site_id, $where);
      $msgs[] = $this->move_rcp_recipe_site($source, $target, $site_id, $where);
      
      $msg = implode('<br />', $msgs).'<br />';
      
      return $msg;
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT c.* '.
                         'FROM rcp_category AS c '.
                         'WHERE c.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_index data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_index($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_index';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT i.* '.
                         'FROM rcp_index AS i, rcp_recipe_site AS rs '.
                         'WHERE i.RecipeID = rs.RecipeID '.
                         'AND rs.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_ingredient data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_ingredient($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_ingredient';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT i.* '.
                         'FROM rcp_ingredient AS i, rcp_recipe_site AS rs '.
                         'WHERE i.RecipeID = rs.RecipeID '.
                         'AND rs.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_nutritional data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_nutritional($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_nutritional';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT n.* '.
                         'FROM rcp_nutritional AS n, rcp_recipe_site AS rs '.
                         'WHERE n.RecipeID = rs.RecipeID '.
                         'AND rs.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_nutritional_calories data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_nutritional_calories($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_nutritional_calories';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT nc.* '.
                         'FROM rcp_nutritional_calories AS nc, rcp_recipe_site AS rs '.
                         'WHERE nc.RecipeID = rs.RecipeID '.
                         'AND rs.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_recipe data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_recipe($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_recipe';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT r.* '.
                         'FROM rcp_recipe AS r, rcp_recipe_site AS rs '.
                         'WHERE r.ID = rs.RecipeID '.
                         'AND rs.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_recipe_category data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_recipe_category($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_recipe_category';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT rc.* '.
                         'FROM rcp_recipe_category AS rc, rcp_category AS c '.
                         'WHERE rc.CategoryID = c.ID '.
                         'AND c.SiteID = "'.$site_id.'"';

      return $this->_move_table($settings);
   }

   // --------------------------------------------------------------------

   /**
    * Moves rcp_recipe_site data from one database to another
    *
    * @access   public
    * @param    string     the source (dev, stage or live)
    * @param    string     the destination  (dev, stage or live)
    * @param    string     the site ID
    * @param    string     the where statement if there is one
    * @return   string
    */
   function move_rcp_recipe_site($source, $target, $site_id = 'all', $where = '')
   {
      $settings['table']   = 'rcp_recipe_site';
      $settings['source']  = $source;
      $settings['target']  = $target;
      $settings['src_db']  = $source.'_db';
      $settings['tgt_db']  = $target.'_db';
      $settings['site_id'] = $site_id;
      $settings['where']   = $where;
      $settings['mywhere'] = $this->dbtools->fix_where('SiteID', $site_id, $where);
      
      $settings['sql'] = 'SELECT rs.* '.
                         'FROM rcp_recipe_site AS rs '.
                         'WHERE rs.SiteID = "'.$site_id.'"';

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
      
      if ($site_id != 'all')
      {
         $sql = $settings['sql'];
         if ($where != '')
         {
            $sql .= ' AND '.$where;
         }
         $this->dbtools->db_table_copy_sql($this->$src_db, $this->$tgt_db, $table, $sql);
      }
      elseif ($where != '')
      {
         $sql = 'SELECT * FROM '.$table;
         if ($mywhere != '')
         {
            $sql .= ' WHERE '.$mywhere;
         }
         $this->dbtools->db_table_copy_sql($this->$src_db, $this->$tgt_db, $table, $sql);
      }
      else
      {
         $this->dbtools->db_table_copy_simple($this->$src_db, $this->$tgt_db, $table);
      }

      $msg = 'Successfully updated '.$this->$tgt_db->database.
             ': '.$table.' on '.$target;
      if ($where != '')
      {
         $msg .= ' WHERE '.$where;
      }
      return $msg;
   }

}

/* End of file coolbrew_products_model.php */
/* Location: ./system/modules/uplive/models/coolbrew_products_model.php */