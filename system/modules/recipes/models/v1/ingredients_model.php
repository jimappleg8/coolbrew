<?php

class Ingredients_model extends Model {

   var $CI;        // CoolBrew object
   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Ingredients_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      $this->read_db = $this->load->database($level.'-read', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of data for this ingredient ID
    *
    * @access   public
    * @return   array
    */
   function get_ingredient_data($ingredient_id)
   {
      $sql = 'SELECT * '.
             'FROM rcp_ingredient '.
             'WHERE ID = '.$ingredient_id;
      $query = $this->read_db->query($sql);
      $ingredient = $query->row_array();
      
      return $ingredient;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of ingredient records for this recipe
    * It does not include the associated product data.
    *
    * @access   public
    * @return   array
    */
   function get_ingredient_records($recipe_id)
   {
      $sql = 'SELECT * '.
             'FROM rcp_ingredient '.
             'WHERE RecipeID = '.$recipe_id;
      $query = $this->read_db->query($sql);
      $ingredients = $query->result_array();
      
      return $ingredients;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of ingredients for this recipe
    *
    * @access   public
    * @return   array
    */
   function get_ingredients($recipe_id, $site_id)
   {
      $this->CI =& get_instance();
      $this->CI->load->model('Products');
      
      $sql = 'SELECT * '.
             'FROM rcp_ingredient '.
             'WHERE RecipeID = '.$recipe_id.' '.
             'ORDER BY Sort asc';
      $query = $this->read_db->query($sql);
      $ingredients = $query->result_array();
            
      for ($i=0; $i<count($ingredients); $i++)
      {
         $ingredients[$i]['ProductOneArray'] = array();
         $ingredients[$i]['ProductTwoArray'] = array();

         if ($ingredients[$i]['ProductOne'] != 0)
         {
            // find out if this product has a local instance
            $ingredients[$i]['ProductOneSiteID'] = $this->CI->Products->get_site_id($site_id, $ingredients[$i]['ProductOneSiteID'], $ingredients[$i]['ProductOne']);
            $sql = 'SELECT p.ProductID, p.ProductName, p.SESFilename AS ProductCode '.
                   'FROM pr_product AS p '.
                   'WHERE p.ProductID = '.$ingredients[$i]['ProductOne'];
            $query = $this->read_db->query($sql);
            $ingredients[$i]['ProductOneArray'] = $query->row_array();
            $ingredients[$i]['ProductOneArray']['ProductCategory'] = $this->CI->Products->get_category_path($ingredients[$i]['ProductOneSiteID'], $ingredients[$i]['ProductOne']);
            $ingredients[$i]['ProductOneArray']['SiteID'] = $ingredients[$i]['ProductOneSiteID'];

            // look for opening and closing {prod1} tags
            preg_match_all('/\{prod1\}(.*)\{\/prod1\}/', $ingredients[$i]['Name'], $matches1);           
            for ($j=0, $cnt=count($matches1[0]); $j<$cnt; $j++)
            {
               $link = $this->_get_product_link($ingredients[$i]['ProductOneArray'], $matches1[1][$j]);
               $ingredients[$i]['Name'] = str_replace($matches1[0][$j], $link, $ingredients[$i]['Name']);
            }

            // look for individual {prod1} tags
            preg_match_all('/\{prod1\}/', $ingredients[$i]['Name'], $matches2);           
            for ($j=0, $cnt=count($matches2[0]); $j<$cnt; $j++)
            {
               $link = $this->_get_product_link($ingredients[$i]['ProductOneArray'], '');
               $ingredients[$i]['Name'] = str_replace($matches2[0][$j], $link, $ingredients[$i]['Name']);
            }
         }

         if ($ingredients[$i]['ProductTwo'] != 0)
         {
            $sql = 'SELECT p.ProductID, p.ProductName, p.SESFilename AS ProductCode '.
                   'FROM pr_product AS p '.
                   'WHERE p.ProductID = '.$ingredients[$i]['ProductTwo'];
            $query = $this->read_db->query($sql);
            $ingredients[$i]['ProductTwoArray'] = $query->row_array();
            $ingredients[$i]['ProductTwoArray']['ProductCategory'] = $this->CI->Products->get_category_path($ingredients[$i]['ProductTwoSiteID'], $ingredients[$i]['ProductTwo']);
            $ingredients[$i]['ProductTwoArray']['SiteID'] = $ingredients[$i]['ProductTwoSiteID'];

            // look for opening and closing {prod2} tags
            preg_match_all('/\{prod2\}(.*)\{\/prod2\}/', $ingredients[$i]['Name'], $matches3);           
            for ($j=0, $cnt=count($matches3[0]); $j<$cnt; $j++)
            {
               $link = $this->_get_product_link($ingredients[$i]['ProductTwoArray'], $matches3[1][$j]);
               $ingredients[$i]['Name'] = str_replace($matches3[0][$j], $link, $ingredients[$i]['Name']);
            }

            // look for individual {prod2} tags
            preg_match_all('/\{prod2\}/', $ingredients[$i]['Name'], $matches4);           
            for ($j=0, $cnt=count($matches4[0]); $j<$cnt; $j++)
            {
               $link = $this->_get_product_link($ingredients[$i]['ProductTwoArray'], '');
               $ingredients[$i]['Name'] = str_replace($matches4[0][$j], $link, $ingredients[$i]['Name']);
            }
         }

         // check case where the Ingredient Name is left blank
         if ($ingredients[$i]['ProductOne'] != 0 || $ingredients[$i]['ProductTwo'] != 0)
         {
            if ($ingredients[$i]['Name'] == '')
            {
               $link1 = $this->_get_product_link($ingredients[$i]['ProductOneArray'], '');
               $link2 = $this->_get_product_link($ingredients[$i]['ProductTwoArray'], '');
               $ingredients[$i]['Name'] = trim($link1.' '.$link2);
            }
         }
      
         // look for embedded recipe link references
         if (preg_match_all('/\[~(.*?)~\]/', $ingredients[$i]['Name'], $matches, PREG_PATTERN_ORDER))
         {
            for ($j=0; $j<count($matches[1]); $j++)
            {
               $my_recipe_id = $matches[1][$j];
               $recipe_data = $this->get_recipe_record($my_recipe_id);
               
               $link = $this->_get_recipe_link($recipe_data);
               $ingredients[$i]['Name'] = str_replace('[~'.$my_recipe_id.'~]', $link, $ingredients[$i]['Name']);
            }
         }
      }
      
      return $ingredients;
   }

   // --------------------------------------------------------------------

   /**
    * Returns product link
    *
    * @access   private
    * @return   array
    */
   function _get_product_link($product, $text = '')
   {
      $this->load->helper('text');
      
      if (empty($product) || ! isset($product['ProductName']))
      {
         return '';
      }

      $sql = 'SELECT s.* '.
             'FROM adm_site AS s '.
             'WHERE s.ID = "'.$product['SiteID'].'"';
      $query = $this->read_db->query($sql);
      $site = $query->row_array();
      
      // flatten out the data structure
      $product['ProductCategoryID'] = $product['ProductCategory'][0]['ProductCategoryID'];
      $product['ProductCategoryCode'] = $product['ProductCategory'][0]['ProductCategoryCode'];
      
      $product['ProductCategoryIDPath'] = '';
      $product['ProductCategoryCodePath'] = '';
      $cnt = count($product['ProductCategory']) - 1;
      for ($i=$cnt; $i>=0; $i--)
      {
         $product['ProductCategoryIDPath'] .= $product['ProductCategory'][$i]['ProductCategoryID'].'/';
         $product['ProductCategoryCodePath'] .= $product['ProductCategory'][$i]['ProductCategoryCode'].'/';
      }
      $product['ProductCategoryIDPath'] = trim($product['ProductCategoryIDPath'], '/');
      $product['ProductCategoryCodePath'] = trim($product['ProductCategoryCodePath'], '/');
      unset($product['ProductCategory']);
      
      // process the link pattern
      $href = $site['ProductLink'];
      foreach ($product AS $key => $value)
      {
         $href = str_replace('{'.$key.'}', $value, $href);
      }
      
      // get the base URL
      $server_level = (SERVER_LEVEL == 'local') ? 'Dev' : ucfirst(SERVER_LEVEL);
      $base_url = $site[$server_level.'URL'];
      
      if ($text == '')
      {
         $text = trim($product['ProductName']);
      }
      else
      {
         $text = trim($text);
      }
      
      $link = '';
      $link .= '<a href="'.$base_url.$href.'" target="_blank">';
      $link .= $text;
      $link .= '</a>';
      
      return $link;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns recipe link
    *
    * @access   private
    * @return   array
    */
   function _get_recipe_link($recipe)
   {
      // get site info for all sites associated with this recipe
      $sql = 'SELECT s.* '.
             'FROM adm_site AS s, rcp_recipe AS r, rcp_recipe_site AS rs '.
             'WHERE r.ID = rs.RecipeID '.
             'AND s.ID = rs.SiteID '.
             'AND r.ID = '.$recipe['ID'];
      $query = $this->read_db->query($sql);
      $sites = $query->result_array();

      $link = '';
      $link .= '<a href="';
      $link .= '/recipes/detail.php/';  // get recipe link pattern for the site
      $link .= $recipe['RecipeCode'].'">';
      $link .= $recipe['Title'];
      $link .= '</a>';
      
      return $link;
   }

   // --------------------------------------------------------------------

   /**
    * Returns recipe title for given recipe ID
    *
    * @access   public
    * @return   array
    */
   function get_recipe_record($recipe_id)
   {
      $sql = 'SELECT rcp_recipe.* '.
             'FROM rcp_recipe '.
             'WHERE ID = '.$recipe_id;

      $query = $this->read_db->query($sql);
      $recipe = $query->row_array();

      return $recipe;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns list of recipe IDs containing the specified product
    *
    * @access   public
    * @param    int       the product ID to look for
    * @return   array
    */
   function get_recipes_by_product($product_id)
   {
      $sql = 'SELECT RecipeID '.
             'FROM rcp_ingredient '.
             'WHERE (ProductOne = '.$product_id.' '.
             'OR ProductTwo = '.$product_id.')';
      $query = $this->read_db->query($sql);
      $prod_matches = $query->result_array();

      return $prod_matches;
   }

}

?>
