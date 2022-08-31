<?php

class Recipes extends Controller {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Recipes()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'recipes'));
      $this->load->helper(array('url', 'menu'));

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Generates a listing of this site's recipes
    *
    */
   function index($site_id, $category_code = 'all')
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $recipes['error_msg'] = $this->session->userdata('recipe_error');
      if ($this->session->userdata('recipe_error') != '')
         $this->session->set_userdata('recipe_error', '');
         
      $this->load->model('Recipes');
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, 0, 1);

      $site = $this->Sites->get_site_data($site_id);
      
      $category_list = $this->Categories->get_category_tree($site_id);
      $category_lookup = $this->Categories->get_category_lookup($site_id);

      if ($category_code == 'all')
      {
         $category = array();
         $recipe_list = $this->Recipes->get_recipes_in_site($site_id);
         $recipes['limited'] = FALSE;
      }
      elseif ($category_code == 'none')
      {
         $category = array();
         $recipe_list = $this->Recipes->get_nocat_recipes_in_site($site_id);
         $recipes['limited'] = TRUE;
      }
      else
      {
         $category = $this->Categories->get_category_data_by_code($site_id, $category_code);
         $recipe_list = $this->Recipes->get_recipes_in_category($site_id, $category['ID']);
         $recipes['limited'] = TRUE;
      }
      
//      echo "<pre>"; print_r($recipe_list); echo "</pre>";

      $recipes['recipe_exists'] = (count($recipe_list) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('recipes');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Recipes');
      $data['submenu'] = get_submenu($site_id, 'Recipes');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['recipes'] = $recipes;
      $data['recipe_list'] = $recipe_list;
      $data['category'] = $category;
      $data['category_list'] = $category_list;
      $data['category_lookup'] = $category_lookup;
      
      $this->load->vars($data);
   	
      return $this->load->view('recipes/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a recipe
    *
    * Auditing: complete
    */
   function delete($site_id, $recipe_id)
   {
      $this->load->model('Categories');
      $this->load->model('Indexes');
      $this->load->model('Ingredients');
      $this->load->model('Recipes');
      $this->load->model('Recipe_sites');
      $this->load->library('auditor');

      // delete the index for this recipe
      $idxs = $this->Indexes->get_index_records($recipe_id);
      foreach ($idxs AS $idx)
      {
         $tmp = $this->write_db->where($idx);
         $this->write_db->delete('rcp_index');
         $this->auditor->audit_delete('rcp_index', $tmp->ar_where, $idx);
      }
      
      // delete related ingredients
      $ingreds = $this->Ingredients->get_ingredient_records($recipe_id);
      foreach ($ingreds AS $ingred)
      {
         $tmp = $this->write_db->where($ingred);
         $this->write_db->delete('rcp_ingredient');
         $this->auditor->audit_delete('rcp_ingredient', $tmp->ar_where, $ingred);
      }
      
      // delete the recipe from the categories
      $cats = $this->Categories->get_all_category_ids($recipe_id);
      foreach ($cats AS $cat)
      {
         $values['RecipeID'] = $recipe_id;
         $values['CategoryID'] = $cat['CategoryID'];
         $tmp = $this->write_db->where($values);
         $this->write_db->delete('rcp_recipe_category');
         $this->auditor->audit_delete('rcp_recipe_category', $tmp->ar_where, $values);
      }
      
      // delete all site links for this recipe
      $this->Recipe_sites->delete_all_recipe_sites($recipe_id);

      // delete nutrition facts (when implemented)
      
      // delete the recipe itself
      $values = $this->Recipes->get_recipe_record($recipe_id);
      $tmp = $this->write_db->where('ID', $recipe_id);
      $this->write_db->delete('rcp_recipe');
      $this->auditor->audit_delete('rcp_recipe', $tmp->ar_where, $values);
      
      $this->session->set_userdata('recipe_message', $values['Title'].' has been deleted.');

      redirect('recipes/index/'.$site_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Adds a recipe
    *
    * Auditing: complete
    */
   function add($site_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Sites');
      $this->load->model('Ingredients');
      $this->load->model('Recipes');
      $this->load->model('Recipe_sites');
      $this->load->model('Indexes');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);
      
      $rules['Title'] = 'trim|callback_one_required[XMLText]';
      $rules['XMLText'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Title'] = 'Recipe Title';
      $fields['XMLText'] = 'XML File of a Recipe';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('recipes');
      
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Recipes');
         $data['submenu'] = get_submenu($site_id, 'Recipes');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['recipes'] = $recipes; // errors and messages
      
         $this->load->vars($data);
   	
         return $this->load->view('recipes/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            if ($this->input->post('XMLText') != '')
            {
               $this->_add_xml($site_id);
            }
            else
            {
               $this->_add($site_id);
            }
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Validation callback checking if one or the other of the fields
    *  has content.
    */
   function one_required($str, $field)
   {
      if ($str == '' && $_POST[$field] == '')
      {
         $this->validation->set_message('one_required', 'Either the %s field or the '.$field.' field needs to be filled in.');
         return FALSE;
      }
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add recipe form
    *
    * Auditing: complete
    */
   function _add($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $fields = $this->validation->_fields;
      unset($fields['XMLText']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['RecipeCode'] = url_title($values['Title']);
      $values['Language'] = 'en_US';
      $values['Status'] = 'active';
      $values['Featured'] = 0;
      
      $recipe_id = $this->Recipes->insert_recipe($values);
      
      // add this site to the rcp_recipe_sites table
      $this->Recipe_sites->insert_recipe_site($recipe_id, $site_id);

      // update the index for this recipe
      $this->Indexes->update_search_index($recipe_id);

      $last_action = $this->session->userdata('last_action') + 1;

      redirect("recipes/edit/".$site_id.'/'.$recipe_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add recipe form when XML is submitted
    */
   function _add_xml($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      // get XML string
      $data = $this->input->post('XMLText');
      $myXML = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
      
      $fields = array('Title', 'PrepTime', 'CookTime', 'Yield', 'Description', 
                      'Citation', 'Ingredients', 'Directions', 'Keywords');
      
      $values = get_object_vars($myXML);
      foreach ($fields AS $key)
      {
         if (is_object($values[$key]))
         {
            unset($values[$key]);
         }
      }

      // process the directions so they format OK in wysiwyg editor
      $pattern = '/(.*)/m';
      $replacement = '<div>$1</div>';
      $values['Directions'] = preg_replace($pattern, $replacement, $values['Directions']);
      $values['Directions'] = str_replace('<div></div>', '', $values['Directions']);

//      echo '<pre>'; print_r($values); echo '</pre>'; exit;
      
      // pull the ingredient list out for later processing
      $list = $values['Ingredients'];
      unset($values['Ingredients']);
      
      $values['RecipeCode'] = url_title($values['Title']);
      $values['Language'] = 'en_US';
      $values['Status'] = 'active';
      $values['Featured'] = 0;

      $recipe_id = $this->Recipes->insert_recipe($values);
      
      // process the ingredient list
      $this->Ingredients->process_ingredient_list($recipe_id, $list);
      
      // add this site to the rcp_recipe_sites table
      $this->Recipe_sites->insert_recipe_site($recipe_id, $site_id);

      // update the index for this recipe
      $this->Indexes->update_search_index($recipe_id);

      $last_action = $this->session->userdata('last_action') + 1;

      redirect("recipes/edit/".$site_id.'/'.$recipe_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Updates a recipe
    *
    * Auditing: complete
    */
   function edit($site_id, $recipe_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
            
      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $this->load->helper(array('ckeditor', 'fckeditor', 'text'));
      $this->load->model('Recipes');
      $this->load->model('Sites');
      $this->load->model('Recipe_sites');
      $this->load->model('Indexes');
      $this->load->model('Ingredients');
      $this->load->model('Products');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);
      
      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;
      
      $old_values = $this->Recipes->get_recipe_record($recipe_id);
      
      $rules['Title'] = 'trim';
      $rules['RecipeCode'] = 'trim';
      $rules['Description'] = 'trim';
      $rules['Directions'] = 'trim';
      $rules['Keywords'] = 'trim';
      $rules['Citation'] = 'trim';
      $rules['Yield'] = 'trim';
      $rules['PrepTime'] = 'trim';
      $rules['CookTime'] = 'trim';
      $rules['Language'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['Featured'] = 'trim';
      $rules['FlagAsNew'] = 'trim';

      $rules['Quantity'] = 'trim';
      $rules['Name'] = 'trim';
      $rules['ProductOne'] = 'trim';
      $rules['ProductTwo'] = 'trim';
      $rules['IsHeading'] = 'trim';
      $rules['Ingredients'] = 'trim';

      $rules['Sites'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Title'] = 'Title';
      $fields['RecipeCode'] = 'Recipe Code';
      $fields['Description'] = 'Description';
      $fields['Directions'] = 'Directions';
      $fields['Keywords'] = 'Keywords';
      $fields['Citation'] = 'Citation';
      $fields['Yield'] = 'Yield';
      $fields['PrepTime'] = 'Prep Time';
      $fields['CookTime'] = 'Cook Time';
      $fields['Language'] = 'Language';
      $fields['Status'] = 'Status';
      $fields['Featured'] = 'Featured';
      $fields['FlagAsNew'] = 'Featured';

      $fields['Quantity'] = 'Quantity';
      $fields['Name'] = 'Ingredient Name';
      $fields['ProductOne'] = 'Product One';
      $fields['ProductTwo'] = 'Product Two';
      $fields['IsHeading'] = 'Product Two';
      $fields['Ingredients'] = 'Ingredient List';

      $fields['Sites'] = 'Sites List';

      $this->validation->set_fields($fields);

      $defaults = $old_values;

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('recipes');

         // get data for the various pulldown lists
         $data['statuses'] = array('active' => 'active', 
                                   'pending' => 'pending', 
                                   'inactive' => 'inactive');
         $data['languages'] = array('en_US' => 'en_US', 
                                    'en_CA' => 'en_CA', 
                                    'fr_CA' => 'fr_CA');
         $data['cats'] = $this->Products->get_product_category_list($site_id);

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Recipes');
         $data['submenu'] = get_submenu($site_id, 'Recipes');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['recipe'] = $old_values;
         $data['recipes'] = $recipes; // errors and messages
         $data['recipe_id'] = $recipe_id;
         
         // get the results of the ingredients template
         $mydata['last_action'] = $this->session->userdata('last_action') + 1;
         $mydata['ingredients'] = $ingredients;
         $mydata['recipes'] = $recipes;
         $mydata['site_id'] = $site_id;
         $this->load->vars($mydata);
         $data['ingredients'] =  $this->load->view('ingredients/list', NULL, TRUE);
         
         // get the results of the sites template
         $mydata = array();
         $mydata['last_action'] = $this->session->userdata('last_action') + 1;
         $mydata['sites'] = $this->Recipe_sites->get_sites($recipe_id);
         $recipes['site_exists'] = (count($mydata['sites']) > 0) ? TRUE : FALSE;
         $mydata['recipes'] = $recipes;
         $mydata['site_id'] = $site_id;
         $mydata['recipe_id'] = $recipe_id;
         $this->load->vars($mydata);
         $data['sites'] =  $this->load->view('sites/list', NULL, TRUE);

         // adjust the sites list
         $data['site_list'] = $this->Sites->get_sites_list();
         $data['site_list'][''] = '-- Please choose a site --';
         foreach ($mydata['sites'] AS $mysite)
         {
            unset($data['site_list'][$mysite['SiteID']]);
         }

         $this->load->vars($data);
   	
         return $this->load->view('recipes/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
//         if ($this_action > $this->session->userdata('last_action'))
//         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $recipe_id, $old_values);
//         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the edit product form
    *
    * Auditing: complete
    */
   function _edit($site_id, $recipe_id, $old_values)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($recipe_id == 0)
      {
         show_error('_edit recipe requires that a recipe ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      unset($fields['Quantity']);
      unset($fields['Name']);
      unset($fields['ProductOne']);
      unset($fields['ProductTwo']);
      unset($fields['IsHeading']);
      unset($fields['Ingredients']);
      unset($fields['Sites']);

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $this->Recipes->update_recipe($recipe_id, $values, $old_values);
      
      // update the index for this recipe
      $this->Indexes->update_search_index($recipe_id);

      $this->session->set_userdata('recipe_message', $values['Title'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('recipes/edit/'.$site_id.'/'.$recipe_id.'/'.$last_action.'/');
   }
   
}
?>
