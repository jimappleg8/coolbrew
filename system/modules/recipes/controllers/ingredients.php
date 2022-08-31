<?php

class Ingredients extends Controller {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Ingredients()
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
    * Lists the ingredients for the given recipe
    *
    */
   function index($site_id, $recipe_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Ingredients');
      
      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['ingredients'] = $ingredients;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an ingredient
    *
    */
   function delete($site_id, $ingredient_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Ingredients');
      $this->load->model('Indexes');
      
      $ingredient = $this->Ingredients->get_ingredient_data($ingredient_id);
      $recipe_id = $ingredient['RecipeID'];

      $this->write_db->where('ID', $ingredient_id);
      $this->write_db->delete('rcp_ingredient');
      
      // reorder the sort numbers
      $sql = 'UPDATE `rcp_ingredient` '.
             'SET `Sort` = `Sort` - 1 '.
             'WHERE `Sort` >= '.$ingredient['Sort'].' '.
             'AND RecipeID = '.$recipe_id;
      $query = $this->write_db->query($sql);
      
      // update the index for this recipe
      $this->Indexes->update_search_index($recipe_id);

      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['ingredients'] = $ingredients;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Adds an ingredient
    *
    * Auditing: complete
    */
   function add($site_id, $recipe_id) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Sites');
      $this->load->model('Ingredients');
      $this->load->model('Indexes');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);
      
      $rules['Quantity'] = 'trim';
      $rules['Name'] = 'trim';
      $rules['ProductOne'] = 'trim';
      $rules['ProductTwo'] = 'trim';
      $rules['IsHeading'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Quantity'] = 'Quantity';
      $fields['Name'] = 'Ingredient Name';
      $fields['ProductOne'] = 'Product One';
      $fields['ProductTwo'] = 'Product Two';
      $fields['IsHeading'] = 'Name is a Heading';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_add($site_id, $recipe_id);
      }

      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['ingredients'] = $ingredients;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add ingredient form
    *
    * Auditing: complete
    */
   function _add($site_id, $recipe_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
         
      // determine what the sort number should be
      $sql = 'SELECT MAX(Sort) as SortMax '.
             'FROM rcp_ingredient '.
             'WHERE RecipeID = '.$recipe_id;
      $query = $this->write_db->query($sql);
      $sort = $query->row_array();
      $sort_num = $sort['SortMax'] + 1;

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // the site ID is required for sites using hcgPublic
      $values['RecipeID'] = $recipe_id;
      
      // make sure IsHeading is set either way
      if ( ! isset($values['IsHeading']))
         $values['IsHeading'] = 0;

      // process the form text
      $values['Sort'] = $sort_num;
      
      $this->write_db->insert('rcp_ingredient', $values);

      $this->auditor->audit_insert('rcp_ingredient', '', $values);

      // update the index for this recipe
      $this->Indexes->update_search_index($recipe_id);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an ingredient record
    *
    * Auditing: complete
    */
   function edit($site_id, $ingredient_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
            
      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Ingredients');
      $this->load->model('Products');
      $this->load->model('Indexes');
      $this->load->model('Sites');
      $this->load->library(array('validation', 'auditor'));
      
      $old_values = $this->Ingredients->get_ingredient_data($ingredient_id);
      
      $recipe_id = $old_values['RecipeID'];
      
      $rules['Quantity'] = 'trim';
      $rules['Name'] = 'trim';
      $rules['ProductOneSiteID'] = 'trim';
      $rules['ProductOne'] = 'trim';
      $rules['ProductTwoSiteID'] = 'trim';
      $rules['ProductTwo'] = 'trim';
      $rules['IsHeading'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Quantity'] = 'Quantity';
      $fields['Name'] = 'Ingredient Name';
      $fields['ProductOneSiteID'] = 'Product One Site ID';
      $fields['ProductOne'] = 'Product One ID';
      $fields['ProductTwoSiteID'] = 'Product Two Site ID';
      $fields['ProductTwo'] = 'Product Two ID';
      $fields['IsHeading'] = 'Is the Name a heading';

      $this->validation->set_fields($fields);

      $defaults = $old_values;

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('recipes');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['site_id'] = $site_id;
         $data['ingredient_id'] = $ingredient_id;
         $data['ingredients'] = $this->Ingredients->get_ingredients($recipe_id, $site_id);
         $data['recipes'] = $recipes;
         $data['site_list'] = $this->Sites->get_product_sites_list();
         
         $p1data['cats'] = $this->Products->get_product_category_list($defaults['ProductOneSiteID']);
         $p1data['product_id'] = $defaults['ProductOne'];
         $this->load->vars($p1data);
         $data['product_one'] = $this->load->view('ingredients/ajax_products', NULL, TRUE);

         $p2data['cats'] = $this->Products->get_product_category_list($defaults['ProductTwoSiteID']);
         $p2data['product_id'] = $defaults['ProductTwo'];
         $this->load->vars($p2data);
         $data['product_two'] = $this->load->view('ingredients/ajax_products', NULL, TRUE);

         $this->load->vars($data);
   	
         echo $this->load->view('ingredients/edit', NULL, TRUE);
         exit;
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $ingredient_id, $old_values);
         }
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the edit ingredient form
    *
    * Auditing: complete
    */
   function _edit($site_id, $ingredient_id, $old_values)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($ingredient_id == 0)
      {
         show_error('_edit ingredient requires that an ingredient ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // make sure IsHeading is set either way
      if ( ! isset($values['IsHeading']))
         $values['IsHeading'] = 0;

      $tmp = $this->write_db->where('ID', $ingredient_id);
      $this->write_db->update('rcp_ingredient', $values);

      $this->auditor->audit_update('rcp_ingredient', $tmp->ar_where, $old_values, $values);
      
      $recipe_id = $old_values['RecipeID'];
      
      // if a product was linked to this ingredient, save it for later
      if ((($values['ProductOne'] != $old_values['ProductOne']) || 
          ($values['ProductTwo'] != $old_values['ProductTwo']) ||
          ($values['Name'] != $old_values['Name'])) &&
          !($values['ProductOne'] == 0 && $values['ProductTwo'] == 0))
      {
         $this->Ingredients->insert_ingredient_product($values, $old_values);
      }
      
      // update the index for this recipe
      $this->Indexes->update_search_index($recipe_id);

      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['ingredients'] = $ingredients;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Rearranges ingredient items up and down
    *
    * @return void
    * Auditing: incomplete
    */
   function move($site_id, $ingredient_id, $direction) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Ingredients');
      
      $row = $this->Ingredients->get_ingredient_data($ingredient_id);
      $recipe_id = $row['RecipeID'];
      
      // find out how many entries there are in the database
      $sql = 'SELECT ID FROM rcp_ingredient '.
             'WHERE RecipeID = '.$recipe_id;
      $query = $this->write_db->query($sql);
      $num_ingreds = $query->num_rows();

      if ($direction == "dn" && $row['Sort'] < $num_ingreds)
      {
         $sql = 'UPDATE rcp_ingredient '.
                'SET Sort = '.$row['Sort'].' '.
                'WHERE Sort = '.($row['Sort'] + 1).' '.
                'AND RecipeID = '.$recipe_id;
         $query = $this->write_db->query($sql);

         $sql = 'UPDATE rcp_ingredient '.
                'SET Sort = '.($row['Sort'] + 1).' '.
                'WHERE ID = '.$ingredient_id;
         $query = $this->write_db->query($sql);
      }
      elseif ($direction == "up" && $row['Sort'] > 1)
      {
         $sql = 'UPDATE rcp_ingredient '.
                'SET Sort = '.$row['Sort'].' '.
                'WHERE Sort = '.($row['Sort'] - 1).' '.
                'AND RecipeID = '.$recipe_id;
         $query = $this->write_db->query($sql);

         $sql = 'UPDATE rcp_ingredient '.
                'SET Sort = '.($row['Sort'] - 1).' '.
                'WHERE ID = '.$ingredient_id;
         $query = $this->write_db->query($sql);
      }

      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['ingredients'] = $ingredients;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Processes and parses a text block on ingredients
    *
    * Auditing: complete
    */
   function process($site_id, $recipe_id) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
            
      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Ingredients');
      $this->load->model('Products');
      $this->load->model('Indexes');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['Ingredients'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Ingredients'] = 'An Ingredients List';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() != FALSE)
      {
         $this->_process($site_id, $recipe_id);
      }
      
      $ingredients = $this->Ingredients->get_ingredients($recipe_id, $site_id);
      $recipes['ingredient_exists'] = (count($ingredients) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['ingredients'] = $ingredients;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/list', NULL, TRUE);
      exit;

   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the edit ingredient form
    *
    */
   function _process($site_id, $recipe_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($recipe_id == 0)
      {
         show_error('_process requires that a recipe ID be supplied.');
      }
      
      $list = $this->input->post('Ingredients');
      
      $this->Ingredients->process_ingredient_list($recipe_id, $list);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Builds a select list of products based on given site ID.
    *
    */
   function ajax_products($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Products');
      
      $data['cats'] = $this->Products->get_product_category_list($site_id);
      $data['product_id'] = '';

      $this->load->vars($data);
   	
      echo $this->load->view('ingredients/ajax_products', NULL, TRUE);
      exit;
   }


}
?>
