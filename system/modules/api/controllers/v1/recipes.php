<?php

class Recipes extends Controller {

   var $_headers;
   
   var $post = array();
   
   var $is_error = FALSE;
   var $error_msg = '';

   function Recipes()
   {
      parent::Controller();   
      $this->load->helper(array('url', 'v1/xml', 'v1/json'));
      $this->load->model('v1/Keys');
      $this->load->model('v1/Sites');
   }
   
   // --------------------------------------------------------------------

   /**
    * recipeSelectors
    *
    */
   function recipeSelectors()
   {
      // establish what information was submitted
      $this->_get_value(4, 'api-key');
      $this->_get_value(5, 'format');
      $this->_get_value(6, 'site-id');

      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($this->post['api-key'], $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/recipes/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/recipes/Categories');
      $this->Categories->init_db($level);

      // check if service type is valid
      $valid_service_types = array('xml', 'json');
      if ( ! $this->_valid_service_type($this->post['format'], $valid_service_types))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }
      
      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($this->post['site-id'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $status = $this->Sites->error_msg;
      }

      $data['products'] = $this->Products->get_product_list($this->post['site-id']);
      $data['lists'] = $this->Categories->get_category_lists($this->post['site-id']);

      echo '<pre>'; print_r($data); echo '</pre>';
   }
   
   // --------------------------------------------------------------------

   /**
    * recipeSearch
    *
    */
   function recipeSearch()
   {
      $this->load->helper('v1/sort');
      
      $status = '';
      $search = array();
      $recipes = array();

      // establish what information was submitted
      $this->_get_value(4, 'api-key');
      $this->_get_value(5, 'format');
      $this->_get_value(6, 'site-id');
      $this->_get_value(7, 'query', '');
      $this->_get_value(8, 'product', '');
      $this->_get_value(9, 'category', '');
      $this->_get_value(10, 'limit', 10);
      $this->_get_value(11, 'offset', 0);
     
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($this->post['api-key'], $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/recipes/Categories');
      $this->Categories->init_db($level);
      $this->load->model('v1/recipes/Indexes');
      $this->Indexes->init_db($level);
      $this->load->model('v1/recipes/Ingredients');
      $this->Ingredients->init_db($level);
      $this->load->model('v1/recipes/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/recipes/Recipes');
      $this->Recipes->init_db($level);

      // check if service type is valid
      $valid_service_types = array('xml', 'json');
      if ( ! $this->_valid_service_type($this->post['format'], $valid_service_types))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }
      
      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($this->post['site-id'], $this->is_error)))
      {
         $this->is_error = TRUE;
         $status = $this->Sites->error_msg;
      }

//      echo '<pre>'; print_r($this->post); echo '</pre>'; exit;
      
      if ( ! $this->is_error)
      {
         $exact = TRUE;
      
         $recipe_list = array();
         $matches = array();
         $match_cnt = 0;
         $no_matches = FALSE;
         $show_all = TRUE;
       
         // see if this has a word search component
      
         if ($this->post['query'] != '')
         {
            $show_all = FALSE;

            $words = array_values($this->Indexes->stem_phrase($this->post['query']));

            $word_matches = $this->Indexes->get_index_records_by_word($this->post['site-id'], $words, $exact);

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
         if ($this->post['product'] != '')
         {
            $show_all = FALSE;
         
            $prod_matches = $this->Ingredients->get_recipes_by_product($this->post['product']);
         
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
         $cats = $this->Categories->get_categories($this->post['site-id']);
         foreach ($cats AS $cat)
         {
            if (isset($this->post[$cat['CategoryCode']]) && $this->post[$cat['CategoryCode']] != '')
            {
               $show_all = FALSE;

               $cat_matches = $this->Categories->get_recipes_by_category($this->post[$cat['CategoryCode']]);
            
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
     
//         echo "<pre>"; print_r($matches); echo "</pre>";

         // if nothing was entered, show all rather than none
         if ($show_all == TRUE)
         {
            $word_matches = $this->Recipes->get_all_recipes_in_site($this->post['site-id']);
         
            foreach ($word_matches AS $wm)
            {
               $matches[$match_cnt][] = $wm['RecipeID'];
            }      
         }

//         echo "<pre>"; print_r($matches); echo "</pre>";

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
      
//         echo "<pre>"; print_r($recipe_list); echo "</pre>";

         // get the recipes themselves
         $recipes = array();
         foreach ($recipe_list AS $key => $item)
         {
            $recipes[] = $this->Recipes->get_recipe_data($this->post['site-id'], $item);
         }
      
      }
      
         echo "<pre>"; print_r($recipes); echo "</pre>"; exit;

      // capture error message is there is one
      if ($this->is_error)
      {
         $status = $this->error_msg;
      }
      else
      {
         $status = 'success';
      }
      
      // assemble the search data
      $search['SiteID'] = $this->post['site-id'];
      $search['BrandName'] = $this->Sites->get_brand_name($this->post['site-id']);
      $search['ProductNum'] = $this->post['product-num'];
      $search['ProductName'] = $this->Products->get_product_name_by_upc($this->post['product-num']);
      $search['Zip'] = $this->post['zip'];
      $search['Radius'] = $this->post['radius'];
      $search['Count'] = $this->post['count'];
      $search['Sort'] = $this->post['sort'];
      $search['GoogleMapKey'] = $this->Api->get_map_key($this->post['site-id'], 'live');
      
      switch ($this->post['format'])
      {
         case 'json':
            $data['source'] = $level;
            $data['search'] = process_json_array($search);
            $data['stores'] = array();
            foreach ($stores AS $store)
            {
               $data['stores'][] = process_json_array($store);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/stores/locator-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['search'] = process_xml_array($search);
            $data['stores'] = array();
            foreach ($stores AS $store)
            {
               $data['stores'][] = process_xml_array($store);
            }
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/stores/locator-xml', $data, TRUE);
            break;
      }
      
      if ( ! headers_sent() && ! empty($this->_headers))
      {
         foreach ($this->_headers as $header)
         {
            header($header);
         }
      }
      echo $response;
      return;
   }

   // --------------------------------------------------------------------

   /**
    * Recipe Category Detail
    *
    */
   function categoryDetail()
   {

   }
   
   // -----------------------------------------------------------------
   
   /**
    * Recipe Detail
    *
    */
   function recipeDetail()
   {
      // establish what information was submitted
      $this->_get_value(4, 'api-key');
      $this->_get_value(5, 'format');
      $this->_get_value(6, 'site-id');
      $this->_get_value(7, 'id-type');
      $this->_get_value(8, 'recipe-id');

      $this->is_error = FALSE;
      $recipe = array();
      $categories = array();
      
      // check if key is valid
      if ( ! $level = $this->Keys->valid_key($key, $this->is_error))
      {
         $level = 'live';
         $this->is_error = TRUE;
         $status = $this->Keys->error_msg;
      }

      $this->load->model('v1/products/Products');
      $this->Products->init_db($level);
      $this->load->model('v1/products/Categories');
      $this->Categories->init_db($level);

      // check if service type is valid
      $valid_service_types = array('xml', 'json');
      if ( ! $this->_valid_service_type($service_type, $valid_service_types))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }
      
      // check if the Site ID is valid
      if ( ! ($this->Sites->valid_site_id($site_id, $this->is_error)))
      {
         $this->is_error = TRUE;
         $status = $this->Sites->error_msg;
      }
      
      // Check if type and identifier are valid
      $valid_types = array('id' => 'integer', 'code' => 'string', 'upc' => 'integer');
      if ( ! $this->_valid_type_id($type, $identifier, $valid_types, TRUE))
      {
         $this->is_error = TRUE;
         $status = $this->error_msg;
      }      
      
      if ( ! $this->is_error)
      {
         // convert the product code to an ID number
         if ($code != '')
         {
            $id = $this->Products->get_product_id_by_code($code, $site_id);
         }
   
         if ($upc != '')
         {
            $id = $this->Products->get_product_id_by_upc($upc, $site_id);
         }
   
         if ($id != '')
         {
            $product = $this->Products->get_product_data($id, $site_id);
            
            if ( ! empty($product))
            {
               $product['SiteID'] = $site_id;
               $product['NutritionFacts'] = $this->Products->nutrition_facts($id, $site_id);
   
               $categories = $this->Categories->get_all_category_ids($id);

               $status = 'success';
            }
            else
            {
               $this->is_error = TRUE;
               $status = 'error: the product was not found.';
            }
         }
         else
         {
            $this->is_error = TRUE;
            $status = 'error: the product was not found.';
         }
      }
      
      switch ($service_type)
      {
         case 'json':
            $data['source'] = $level;
            $data['product'] = process_json_array($product);
            $data['categories'] = $categories;
            $data['status'] = $status;
            $this->_headers = array('Content-Type: application/json');
            $response = $this->load->view('v1/products/product-detail-json', $data, TRUE);
            break;
         default:  // xml
            $data['source'] = $level;
            $data['product'] = process_xml_array($product);
            $data['categories'] = $categories;
            $data['status'] = $status;
            $this->_headers = array('Content-Type: text/xml');
            $response = $this->load->view('v1/products/product-detail-xml', $data, TRUE);
            break;
      }
      
      if ( ! headers_sent() && ! empty($this->_headers))
      {
         foreach ($this->_headers as $header)
         {
            header($header);
         }
      }
      echo $response;
      return;
   }
   
   // --------------------------------------------------------------------

   /**
    * Gets the correct parameter value from either URL segment or POST
    *
    * @access   public
    * @return   array
    */
   function _get_value($segment, $key, $default = NULL)
   {
      // if both URl and Post are used, POST takes precedence
      if ($this->input->post($key))
      {
         $this->post[$key] = $this->input->post($key, TRUE);
      }
      elseif ($this->uri->segment($segment))
      {
         $this->post[$key] = $this->uri->segment($segment);
      }
      elseif ($default !== NULL)
      {
         $this->post[$key] = $default;
      }
      else
      {
         $this->post[$key] = '';
         // see if an error has already been thrown
         if ($this->is_error == TRUE)
         {
            return TRUE;
         }
         else
         {
            $this->is_error = TRUE;
            $this->error_msg = 'The required field "'.$key.'" is missing.';
         }
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Checks the validity of the Service Type variable
    *
    * @access   public
    * @return   array
    */
   function _valid_service_type($type, $valid_types)
   {
      // see if an error has already been thrown
      if ($this->is_error == TRUE)
      {
         return TRUE;
      }
      
      $type = strtolower($type);

      if ($type == '')
      {
         $this->error_msg = 'error: the service type is missing.';
         return FALSE;
      }
      
      if ( ! in_array($type, $valid_types))
      {
         $this->error_msg = 'error: the service type is invalid.';
         return FALSE;
      }
      return TRUE;
   }
   

}  // END of Recipes Class

/* End of file recipes.php */
/* Location: ./system/modules/api/controllers/v1/recipes.php */