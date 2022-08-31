<?php

class Rtags extends Controller {

   function Rtags()
   {
      parent::Controller();
      $this->load->helper(array('url', 'form', 'text', 'typography'));
   }

   //-------------------------------------------------------------------------

   /**
    * generates a search form where the user can choose by keywords 
    * or any defined category
    */
   function search()
   {
      $this->load->library('Rtag');
      
      // (string) The site ID
      $site_id = $this->rtag->param('site-id');
      
      // (string) The url for detail pages
      $detail_url = $this->rtag->param('detail-url', '/recipes/detail.php?code={RecipeCode}');

      // (string) The view name in case we want to override the default
      $tpl = $this->rtag->param('tpl', "search");

      // (string) the HTML file for home page
      $home_page = $this->rtag->param('home-page', '');

      // (string) the action URL for the form
      $action = $this->rtag->param('action', '/recipes/search.php');

      // (string) sort product lists by brand?
      $by_brand = $this->rtag->param('by-brand', FALSE);

      // (string) the database server to use to pull the recipe data
      $server_level = $this->rtag->param('server-level', 'live');

      $this->load->model('v1/Indexes');
      $this->Indexes->init_db($server_level);
      $this->load->model('v1/Recipes');
      $this->Recipes->init_db($server_level);
      $this->load->model('v1/Ingredients');
      $this->Ingredients->init_db($server_level);
      $this->load->model('v1/Products');
      $this->Products->init_db($server_level);
      $this->load->model('v1/Categories');
      $this->Categories->init_db($server_level);
      $this->load->library('validation');
      
      // this is temporary to establish the reverse transverse tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);
      
      $list = $this->Categories->get_category_lists($site_id);

      // should have a test to see if one of them has a value
      $rules['Words'] = 'trim';
      $rules['Product'] = 'trim';
      foreach ($list AS $item)
      {
         $rules[$item['Code']] = 'trim';
      }

      $this->validation->set_rules($rules);

      $fields['Words'] = 'Words';
      $fields['Product'] = 'Product';
      foreach ($list AS $item)
      {
         $fields[$item['Code']] = $item['Name'];
      }

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $query_str = '';
      if ($this->validation->run() == TRUE)
      {
         foreach ($fields AS $key => $value)
            $data['search'][$key] = $this->input->post($key);

         $data['recipes'] = $this->_search($site_id, $search_desc);
         $data['search_desc'] = $search_desc;
         
         $this->validation->set_defaults($data['search']);
      }

      $data['home_page'] = ($home_page != '') ? file_get_contents($home_page) : '';
      $data['detail_url'] = $detail_url;
      $data['action'] = $action;
      $data['products'] = $this->Products->get_product_list($site_id, $by_brand);
      $data['lists'] = $this->Categories->get_category_lists($site_id);
      
      return $this->load->view('rtags/v1/'.$tpl, $data, TRUE);

   }

   //-------------------------------------------------------------------------

   /**
    * processes the search form results
    */
   function _search($site_id, &$search_desc)
   {
      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

      $exact = TRUE;
      
      $search_desc = 'All recipes';
      $recipe_list = array();
      $matches = array();
      $match_cnt = 0;
      $no_matches = FALSE;
      $show_all = TRUE;
       
      // see if this has a word search component
      
      if ($search['Words'] != '')
      {
         $show_all = FALSE;
         $search_desc .= ' with "'.$search['Words'].'"';

         $words = array_values($this->Indexes->stem_phrase($search['Words']));
         
         $word_matches = $this->Indexes->get_index_records_by_word($site_id, $words, $exact);

         foreach ($word_matches AS $wm)
         {
            $matches[$match_cnt][] = $wm['RecipeID'];
         }
         if ( ! empty($matches[$match_cnt]))
            $match_cnt++;
         else
            $no_matches = TRUE;
      }
      
      // check to see if there is a products component
      if ($search['Product'] != '')
      {
         $show_all = FALSE;
         $search_desc .= ' that use "'.$this->Products->get_product_name($search['Product']).'"';
         
         $prod_matches = $this->Ingredients->get_recipes_by_product($search['Product']);

         foreach ($prod_matches AS $pm)
         {
            $matches[$match_cnt][] = $pm['RecipeID'];
         }
         if ( ! empty($matches[$match_cnt]))
            $match_cnt++;
         else
            $no_matches = TRUE;
      }
      
      // check to see if there is a category component
      $cats = $this->Categories->get_categories($site_id);
      $cat_count = 0;
      foreach ($cats AS $cat)
      {
         if ($search[$cat['CategoryCode']] != '')
         {
            $show_all = FALSE;
            $cat_count++;
            $search_desc .= ($cat_count > 1) ? ' and' : '';
            $search_desc .= ' in the "'.$this->Categories->get_category_name($search[$cat['CategoryCode']]).'" category';

            $cat_matches = $this->Categories->get_recipes_by_category($search[$cat['CategoryCode']]);
            
            foreach ($cat_matches AS $cm)
            {
               $matches[$match_cnt][] = $cm['RecipeID'];
            }

            if ( ! empty($matches[$match_cnt]))
               $match_cnt++;
            else
               $no_matches = TRUE;
         }
      }
     
//      echo "<pre>"; print_r($matches); echo "</pre>";

      // if nothing was entered, show all rather than none
      if ($show_all == TRUE)
      {
         $word_matches = $this->Recipes->get_all_recipes_in_site($site_id);

         foreach ($word_matches AS $wm)
         {
            $matches[$match_cnt][] = $wm['RecipeID'];
         }      
      }

//      echo "<pre>"; print_r($matches); echo "</pre>";

      if ($exact && $no_matches == FALSE)
      {
         for ($i=0; $i<count($matches); $i++)
         {
            if ($i == 0)
            {
               $recipe_list = $matches[$i];
            }
            else
            {
               $recipe_list = array_intersect($matches[$i], $recipe_list);
            }
         }
      }
      elseif ( ! $exact)
      {
         foreach ($matches AS $match_array)
         {
            $recipe_list = array_merge($match_array, $recipe_list);
         }
      }
      
//      echo "<pre>"; print_r($recipe_list); echo "</pre>";

      // get the recipes themselves
      $recipes = array();
      foreach ($recipe_list AS $key => $item)
      {
         $recipes[] = $this->Recipes->get_recipe_data($site_id, $item);
      }
      
//      echo "<pre>"; print_r($recipes); echo "</pre>";
      
      $search_desc .= '.';

      return $recipes;
   }

   //-------------------------------------------------------------------------

   /**
    * displays a recipe detail page
    */
   function detail()
   {
      $this->load->library('Rtag');

      // (string) The site ID
      $site_id = $this->rtag->param('site-id');
      
      $recipe_code = $this->rtag->param('recipe-code');
      
      // (string) The view name in case we want to override the default
      $tpl = $this->rtag->param('tpl', 'detail');
      
      // (string) the action URL for the form
      $action = $this->rtag->param('action', '/recipes/search.php');

      // (string) sort product lists by brand?
      $by_brand = $this->rtag->param('by-brand', FALSE);

      // (string) the database server to use to pull the recipe data
      $server_level = $this->rtag->param('server-level', 'live');

      $this->load->model('v1/Recipes');
      $this->Recipes->init_db($server_level);
      $this->load->model('v1/Products');
      $this->Products->init_db($server_level);
      $this->load->model('v1/Categories');
      $this->Categories->init_db($server_level);
      $this->load->library('validation');
      
      // this is temporary to establish the reverse transverse tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);
      
      $list = $this->Categories->get_category_lists($site_id);

      // should have a test to see if one of them has a value
      $rules['Words'] = 'trim';
      $rules['Product'] = 'trim';
      foreach ($list AS $item)
      {
         $rules[$item['Code']] = 'trim';
      }

      $this->validation->set_rules($rules);

      $fields['Words'] = 'Words';
      $fields['Product'] = 'Product';
      foreach ($list AS $item)
      {
         $fields[$item['Code']] = $item['Name'];
      }

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $data['products'] = $this->Products->get_product_list($site_id, $by_brand);
      $data['lists'] = $this->Categories->get_category_lists($site_id);
           
      $recipe_id = $this->Recipes->get_recipe_id_by_code($recipe_code, $site_id);
      
      $data['recipe'] = $this->Recipes->get_recipe_data($site_id, $recipe_id);
      $data['recipe']['AssignedCategories'] = $this->Categories->get_all_category_data($recipe_id);
      
      $data['action'] = $action;

      echo $this->load->view('rtags/v1/'.$tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * Displays a list of recipes by category
    */
   function categoryList()
   {
      $this->load->library('Rtag');

      // (string) The site ID
      $site_id = $this->rtag->param('site-id', SITE_ID);
      
      // (string) The category code
      $category_code = $this->rtag->param('category-code');

      // (string) The url for detail pages
      $detail_url = $this->rtag->param('detail-url', '/recipes/detail.php?code={RecipeCode}');

      // (string) The view name in case we want to override the default
      $tpl = $this->rtag->param('tpl', "category_list");

      // (string) the database server to use to pull the recipe data
      $server_level = $this->rtag->param('server-level', 'live');

      $this->load->model('v1/Recipes');
      $this->Recipes->init_db($server_level);
      $this->load->model('v1/Products');
      $this->Products->init_db($server_level);
      $this->load->model('v1/Categories');
      $this->Categories->init_db($server_level);
      $this->load->library('validation');
            
      $data['category'] = $this->Categories->get_category_data_by_code($site_id, $category_code);
      $category_id = $data['category']['ID'];
      
      $recipe_array = $this->Recipes->get_recipes_in_category($site_id, $category_id, FALSE);
      
      foreach ($recipe_array AS $recipe)
      {
         $data['recipes'][] = $this->Recipes->get_recipe_data($site_id, $recipe['ID']);
      }
      
      $data['detail_url'] = $detail_url;
      $data['category_code'] = $category_code;
      
      echo $this->load->view('rtags/v1/'.$tpl, $data, TRUE);
   }

}

/* End of file rtags.php */
/* Location: ./system/modules/recipes/controllers/v1/rtags.php */