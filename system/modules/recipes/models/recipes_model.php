<?php

class Recipes_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Recipes_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of recipe info from the recipe table only
    *
    * @access   public
    * @return   array
    */
   function get_recipe_record($recipe_id)
   {
      $sql = 'SELECT * '.
             'FROM rcp_recipe '.
             'WHERE ID = '.$recipe_id.' ';

      $query = $this->read_db->query($sql);
      $recipe = $query->row_array();

      return $recipe;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of recipe info including ingredients, etc.
    *
    * @access   public
    * @return   array
    */
   function get_recipe_data($site_id, $recipe_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Products');
      $this->CI->load->model('Ingredients');

      $sql = 'SELECT * '.
             'FROM rcp_recipe '.
             'WHERE ID = '.$recipe_id.' ';
      $query = $this->read_db->query($sql);
      $recipe = $query->row_array();
      
      $recipe = $this->get_category_image($site_id, $recipe);
      
      $recipe['SiteID'] = $site_id;

      // look for embedded recipe link references in directions
      if (preg_match_all('/\[~(.*?)~\]/', $recipe['Directions'], $matches, PREG_PATTERN_ORDER))
      {
         for ($j=0; $j<count($matches[1]); $j++)
         {
            $my_recipe_id = $matches[1][$j];
            $recipe_data = $this->get_recipe_record($my_recipe_id);
            $recipe['RecipeLinks'][$my_recipe_id] = $recipe_data;
         }
      }

      // ------ Ingredient Data ------
      
      $recipe['Ingredients'] = $this->CI->Ingredients->get_ingredients($recipe_id, $site_id);

      // ------ Nutritional Data ------
      
      $sql = 'SELECT Name, Value '.
             'FROM rcp_nutritional '.
             'WHERE RecipeID = '.$recipe_id.' '.
             'ORDER BY ID asc';
      $query = $this->read_db->query($sql);
      $recipe['Nutrition'] = $query->result_array();

      // ------ Calorie Data ------
      
      $sql = 'SELECT Name, Value '.
             'FROM rcp_nutritional_calories '.
             'WHERE RecipeID = '.$recipe_id.' '.
             'ORDER BY ID asc';
      $query = $this->read_db->query($sql);
      $recipe['Calories'] = $query->result_array();

//      echo "<pre>"; print_r($recipe); echo "</pre>";

      return $recipe;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the recipe data with corrected ImageFile info.
    *
    * @access   public
    * @param    array   The recipe array
    * @return   array
    */
   function get_category_image($site_id, $recipe)
   {
      if ($recipe['ImageFile'] != '')
      {
         return $recipe;
      }
      
      if (file_exists(SERVERPATH.'/hcgwebdocs/resources/'. $site_id.'/recipes/beauty/default.jpg'))
      {
         $recipe['ImageFile'] = $site_id. '/recipes/beauty/default.jpg';
         return $recipe;
      }

      return $recipe;
   }

   // --------------------------------------------------------------------

   /**
    * Returns recipe ID for a given recipe given it's code
    *
    * @access   public
    * @param    string    The recipe code
    * @param    string    The site ID
    * @return   array
    */
   function get_recipe_id_by_code($recipe_code, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;

      $sql = 'SELECT r.ID '.
             'FROM rcp_recipe AS r, rcp_recipe_site AS rs '.
             'WHERE r.ID = rs.RecipeID '.
             'AND r.RecipeCode = "'.$recipe_code.'" '.
             'AND rs.SiteID = "'.$site_id.'"';
      $query = $this->read_db->query($sql);
      $result = $query->row_array();

      if (isset($result['ID']))
      {
         return $result['ID'];
      }
      else
      {
         return FALSE;
      }
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of recipe records for all recipes in $site_id that
    *   are assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_recipes_in_site($site_id, $include_pending = TRUE)
   {
      $sql = 'SELECT r.ID, r.Title, r.Status, r.FlagAsNew, '.
                'rc.ID AS CategoryID '.
             'FROM rcp_recipe AS r, rcp_category AS rc, '.
                'rcp_recipe_category AS rrc '.
             'WHERE rc.SiteID = "'.$site_id.'" '.
             'AND rrc.CategoryID = rc.ID '.
             'AND rrc.RecipeID = r.ID '.
             'AND rc.Status = "active" '.
             'AND (r.Status = "active" '.
             'OR r.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR r.Status = "pending")': ')';
      $sql .= "ORDER BY r.Title ASC";
      $query = $this->read_db->query($sql);
      $recipe_list = $query->result_array();
      
      // add information about what categories this recipes is assigned to
      $new_recipe_list = array();
      foreach ($recipe_list AS $recipe)
      {
         if ( ! isset($new_recipe_list[$recipe['ID']]))
         {
            $new_recipe_list[$recipe['ID']] = $recipe;
            unset($new_recipe_list[$recipe['ID']]['CategoryID']);
            $new_recipe_list[$recipe['ID']]['Categories'] = array();
         }
         array_push ($new_recipe_list[$recipe['ID']]['Categories'], $recipe['CategoryID']);
      }
   
      $recipe_list = array_values($new_recipe_list);

      $no_cats = $this->get_nocat_recipes_in_site($site_id);
      $recipe_list = array_merge($no_cats, $recipe_list);
      
      return $recipe_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of all recipes in $site_id
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_all_recipes_in_site($site_id)
   {
      $sql = 'SELECT DISTINCT i.RecipeID, COUNT(*) AS nb, '.
               'SUM(Weight) AS total_weight '.
             'FROM rcp_index AS i, rcp_recipe AS r, rcp_recipe_site AS rs '.
             'WHERE i.RecipeID = r.ID '.
             'AND rs.RecipeID = r.ID '.
             'AND rs.SiteID = "'.$site_id.'" '.
             'GROUP BY i.RecipeID '.
             'ORDER BY nb DESC, total_weight DESC';

      $query = $this->read_db->query($sql);
      $word_matches = $query->result_array();
      
      return $word_matches;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of recipe records for all recipes in $site_id that
    *   are assigned to the specified category.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_recipes_in_category($site_id, $category_id, $include_pending = TRUE)
   {
      $sql = 'SELECT r.ID, r.Title, r.Status, r.FlagAsNew, '.
                'rc.ID AS CategoryID '.
             'FROM rcp_recipe AS r, rcp_category AS rc, '.
                'rcp_recipe_category AS rrc '.
             'WHERE rc.SiteID = "'.$site_id.'" '.
             'AND rrc.CategoryID = rc.ID '.
             'AND rrc.RecipeID = r.ID '.
             'AND rc.Status = "active" '.
             'AND (r.Status = "active" '.
             'OR r.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR r.Status = "pending")': ')';
      $sql .= "ORDER BY r.Featured DESC, r.Title ASC";
      $query = $this->read_db->query($sql);
      $recipe_list = $query->result_array();
      
      // add information about what categories this recipes is assigned to
      $new_recipe_list = array();
      foreach ($recipe_list AS $recipe)
      {
         if ( ! isset($new_recipe_list[$recipe['ID']]))
         {
            $new_recipe_list[$recipe['ID']] = $recipe;
            unset($new_recipe_list[$recipe['ID']]['CategoryID']);
            $new_recipe_list[$recipe['ID']]['Categories'] = array();
         }
         array_push ($new_recipe_list[$recipe['ID']]['Categories'], $recipe['CategoryID']);
      }

      $recipe_list = array();
      foreach ($new_recipe_list AS $recipe)
      {
         if (in_array($category_id, $recipe['Categories']))
         {
            $recipe_list[] = $recipe;
         }
      }
      
      return $recipe_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of recipe records for all recipes in $site_id that
    *   are NOT assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_nocat_recipes_in_site($site_id)
   {
      // first, get a list of all recipes for the site
      $sql = 'SELECT r.ID, r.Title, r.Status, r.FlagAsNew '.
             'FROM rcp_recipe AS r, rcp_recipe_site AS rs '.
             'WHERE rs.RecipeID = r.ID '.
             'AND rs.SiteID = "'.$site_id.'" '.
             'AND ( '.
               'r.Status = "active" '.
               'OR r.Status = "partial" '.
               'OR r.Status = "pending" '.
             ') '.
             'ORDER BY r.Title ASC';

      $query = $this->read_db->query($sql);
      $nocat_recipes = $query->result_array();
      
      // now, get a list of all categories for these products
      $sql = 'SELECT rrc.RecipeID '.
             'FROM rcp_recipe_category AS rrc '.
               'JOIN rcp_category AS rc ON rrc.CategoryID = rc.ID '.
             'WHERE rc.SiteID = "'.$site_id.'"';
   
      $query = $this->read_db->query($sql);
      $nocat_cats = $query->result_array();
      
      // create a lookup array of products without categories
      foreach ($nocat_cats AS $cat)
      {
         $cat_lookup[$cat['RecipeID']] = $cat['RecipeID'];
      }
      
      // now, create the final list by removing products that have a category
      $nocat_list = array();
      foreach ($nocat_recipes AS $recipe)
      {
         if ( ! isset($cat_lookup[$recipe['ID']]))
         {
            $recipe['Categories'] = array();
            $nocat_list[] = $recipe;
         }
      }

      return $nocat_list;
   }

   // --------------------------------------------------------------------
   
   /**
    * Inserts a recipe record
    *
    * @access   public
    * @return   null
    */
   function insert_recipe($values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $this->write_db->insert('rcp_recipe', $values);
      $recipe_id = $this->write_db->insert_id();
      
      $this->CI->auditor->audit_insert('rcp_recipe', '', $values);

      return $recipe_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a recipe record
    *
    * @access   public
    * @return   null
    */
   function update_recipe($recipe_id, $values, $old_values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $tmp = $this->write_db->where('ID', $recipe_id);
      $this->write_db->update('rcp_recipe', $values);

      $this->CI->auditor->audit_update('rcp_recipe', $tmp->ar_where, $old_values, $values);

      return TRUE;
   }

}

?>
