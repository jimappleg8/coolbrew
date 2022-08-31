<?php

class Recipes_Tags extends Controller {

   function Recipes_Tags()
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
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "search");

      $this->load->model('Products');
      $this->load->model('Categories');
      $this->load->library('validation');
      
      // check for search params passed via the URI
      $params = $this->uri->uri_to_assoc();
      if ( ! empty($params))
      {
         // this overrides any form that might be submitted
         $_POST = $params;
      }
      
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
      
      $this->collector->prepend_css_file('recipes-tags');
      
      $query_str = '';
      if ($this->validation->run() == TRUE)
      {
         foreach ($fields AS $key => $value)
            $data['search'][$key] = $this->input->post($key);

         $data['recipes'] = $this->_search($site_id);
         
         $query_str = '';
         foreach ($data['search'] AS $key => $value)
         {
            $query_str = ($query_str == '') ? '' : $query_str.'&';
            $query_str .= $key.'='.urlencode($value);
         }
      }

      $data['products'] = $this->Products->get_product_list($site_id);
      $data['lists'] = $this->Categories->get_category_lists($site_id);
      
      $result[0] = $this->load->view($tpl, $data, TRUE);
      $result[1] = $query_str;

      return $result;

   }

   //-------------------------------------------------------------------------

   /**
    * processes the search form results
    */
   function _search($site_id)
   {
      $this->load->model('Indexes');
      $this->load->model('Recipes');
      $this->load->model('Ingredients');
      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

//      $exact = (isset($search['Exact'])) ? TRUE : FALSE;
      $exact = TRUE;
      
      $recipe_list = array();
      $matches = array();
      $match_cnt = 0;
      $no_matches = FALSE;
      $show_all = TRUE;
       
      // see if this has a word search component
      
      if ($search['Words'] != '')
      {
         $show_all = FALSE;
         
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
      foreach ($cats AS $cat)
      {
         if ($search[$cat['CategoryCode']] != '')
         {
            $show_all = FALSE;

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
      
      return $recipes;
   }

   //-------------------------------------------------------------------------

   /**
    * displays a recipe detail page
    */
   function detail($recipe_code)
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "detail");

      $this->load->model('Recipes');
      $this->load->model('Products');
      $this->load->model('Categories');
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
      
      $this->collector->prepend_css_file('recipes-tags');
      
      $data['products'] = $this->Products->get_product_list($site_id);
      $data['lists'] = $this->Categories->get_category_lists($site_id);

      $recipe_id = $this->Recipes->get_recipe_id_by_code($recipe_code);
      $data['recipe'] = $this->Recipes->get_recipe_data($site_id, $recipe_id);

      return $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * Displays a list of recipes by category
    */
   function category_list($category_code)
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "category_list");

      $this->load->model('Recipes');
      $this->load->model('Products');
      $this->load->model('Categories');
      $this->load->library('validation');
            
      $this->collector->prepend_css_file('recipes-tags');
      
      $data['category'] = $this->Categories->get_category_data_by_code($site_id, $category_code);
      $category_id = $data['category']['ID'];

      $recipe_array = $this->Recipes->get_recipes_in_category($site_id, $category_id, FALSE);
      
      foreach ($recipe_array AS $recipe)
      {
         $data['recipes'][] = $this->Recipes->get_recipe_data($site_id, $recipe['ID']);
      }
      
//      echo '<pre>'; print_r($data); echo '</pre>'; exit;
      
      return $this->load->view($tpl, $data, TRUE);
   }

}
?>
