<?php

class Categories extends Controller {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Categories()
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
    * Generates a listing of this site's recipe categories
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $recipes['error_msg'] = $this->session->userdata('recipe_error');
      if ($this->session->userdata('recipe_error') != '')
         $this->session->set_userdata('recipe_error', '');
         
      $this->load->model('Categories');
      $this->load->model('Recipes');
      $this->load->model('Sites');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      $site = $this->Sites->get_site_data($site_id);

      $category_list = $this->Categories->get_category_tree($site_id);

      $recipes['category_exists'] = (count($category_list) < 2) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('recipes');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Recipes');
      $data['submenu'] = get_submenu($site_id, 'Categories');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['recipes'] = $recipes;
      $data['root_id'] = $root;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('categories/list', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes a recipe category
    *
    * Auditing: incomplete
    */
   function delete($site_id, $cat_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Categories');
      $this->load->model('Recipes');
      
      // delete all references to this category in rcp_recipe_category
      $sql = 'DELETE FROM rcp_recipe_category '. 
             'WHERE CategoryID = '.$cat_id;
      $this->write_db->query($sql);
      
      $category = $this->Categories->get_category_data($cat_id);

      // delete the category record itself
      $sql = 'DELETE FROM rcp_category '. 
             'WHERE ID = '.$cat_id;
      $this->write_db->query($sql);
      
      // get a list of this category's children
      $sql = 'SELECT ID FROM rcp_category '.
             'WHERE ParentID = '.$category['ID'].' '.
             'ORDER BY Sort';
      $query = $this->write_db->query($sql);
      $children = $query->result_array();
      
      // get a list of categories whose CategoryOrder will need to be adjusted
      $sql = 'SELECT ID, Sort FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND Sort > '.$category['Sort'].' '.
             'AND ParentID = '.$category['ParentID'];
      $query = $this->write_db->query($sql);
      $belows = $query->result_array();
      
      // change the parent IDs of children to this category's parent ID
      for ($i=0; $i<count($children); $i++)
      {
         $values['ParentID'] = $category['ParentID'];
         $values['Sort'] = $category['Sort'] + $i;
         $this->write_db->where('ID', $children[$i]['ID']);
         $this->write_db->update('rcp_category', $values);
      }
      
      $offset = count($children) - 1;
      foreach ($belows AS $below)
      {
         $values['Sort'] = $below['Sort'] + $offset;
         $this->write_db->where('ID', $below['ID']);
         $this->write_db->update('rcp_category', $values);
      }

      redirect('categories/index/'.$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a recipe category
    *
    * Auditing: incomplete
    */
   function add($site_id, $parent, $sort, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('Categories');
      $this->load->model('Recipes');
      $this->load->model('Sites');
      $this->load->library(array('validation'));
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['CategoryName'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['CategoryName'] = 'Category Name';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('recipes');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Recipes');
         $data['submenu'] = get_submenu($site_id, 'Categories');
         $data['site_id'] = $site_id;
         $data['parent'] = $parent;
         $data['sort'] = $sort;
         $data['site'] = $site;
         $data['parents'] = $this->Categories->get_parent_list($site_id);
      
         $this->load->vars($data);
   	
         return $this->load->view('categories/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id, $parent, $sort);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add category form
    *
    * Auditing: incomplete
    */
   function _add($site_id, $parent, $sort)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      // update needed sort fields to make room for insert
      $sql = 'SELECT ID, Sort FROM rcp_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$parent.' '.
             'AND Sort >= '.$sort;
      $query = $this->write_db->query($sql);
      $sort_list = $query->result_array();
      
      if ($query->num_rows() > 0)
      {
         foreach($sort_list AS $item)
         {
            $item['Sort'] = $item['Sort'] + 1;
            $this->write_db->where('ID', $item['ID']);
            $this->write_db->update('rcp_category', $item);
         }
      }

      // Now, insert the record
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;      
      $values['ParentID'] = $parent;
      $values['Sort'] = $sort;
      $values['Status'] = 'active';
      $values['CategoryCode'] = url_title($values['CategoryName']);
      $values['Language'] = 'en_US';
            
      $category_id = $this->Categories->insert_recipe_category($site_id, $values);

      $last_action = $this->session->userdata('last_action') + 1;

      redirect("categories/edit/".$site_id.'/'.$category_id.'/'.$last_action);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a product category
    *
    * Auditing: incomplete
    */
   function edit($site_id, $cat_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->library('validation');

      $site = $this->Sites->get_site_data($site_id);
      $old_values = $this->Categories->get_category_data($cat_id);

      $rules['CategoryCode'] = 'trim|required';
      $rules['CategoryName'] = 'trim|required';
      $rules['CategoryText'] = 'trim';
      $rules['ParentID'] = 'trim|required';
      $rules['Sort'] = 'trim';
      $rules['Language'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaAbstract'] = 'trim';
      $rules['MetaRobots'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['CategoryCode'] = 'Category Code';
      $fields['CategoryName'] = 'Category Name';
      $fields['CategoryText'] = 'Category Text';
      $fields['ParentID'] = 'Parent ID';
      $fields['Sort'] = 'Category Order';
      $fields['Language'] = 'Language';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaAbstract'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Abstract';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('recipes');

         $data['statuses'] = array('active' => 'active', 
                                   'pending' => 'pending', 
                                   'inactive' => 'inactive');
         $data['languages'] = array('en_US' => 'en_US', 
                                    'en_CA' => 'en_CA', 
                                    'fr_CA' => 'fr_CA');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Recipes');
         $data['submenu'] = get_submenu($site_id, 'Categories');
         $data['cat_id'] = $cat_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['parents'] = $this->Categories->get_parent_list($site_id, $cat_id);
      
         $this->load->vars($data);
   	
         return $this->load->view('categories/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $cat_id, $old_values);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a category record
    *
    * Auditing: incomplete
    */
   function _edit($site_id, $cat_id, $old_values)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($cat_id == 0)
      {
         show_error('_edit_category requires that a category ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // find out if the parent ID changed
      $category = $this->Categories->get_category_data($cat_id);
      if ($category['ParentID'] != $values['ParentID'])
      {
         // if so, then get a count of the children of the new parent 
         // add one to get the new Sort for this category
         $sql = 'SELECT ID FROM rcp_category '.
                'WHERE ParentID = '.$values['ParentID'];
         $query = $this->write_db->query($sql);
         $children = $query->result_array();
         $values['Sort'] = count($children) + 1;
      
         // get a list of all categories below this one in its old parent
         $sql = 'SELECT ID, Sort FROM rcp_category '.
                'WHERE SiteID = "'.$site_id.'" '.
                'AND Sort > '.$category['Sort'].' '.
                'AND ParentID = '.$category['ParentID'];
         $query = $this->write_db->query($sql);
         $belows = $query->result_array();

         // adjust the CategoryOrder (-1) for each of those categories
         foreach ($belows AS $below)
         {
            $item['Sort'] = $below['Sort'] - 1;
            $this->Categories->update_recipe_category($site_id, $below['ID'], $item, $below);
         }
      }

      // finally, update the edited category
      $this->Categories->update_recipe_category($site_id, $cat_id, $values, $old_values);
      
      redirect("categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a recipes's category list
    *
    * Auditing: incomplete
    */
   function assign($site_id, $recipe_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      
      $recipes['error_msg'] = $this->session->userdata('recipe_error');
      if ($this->session->userdata('recipe_error') != '')
         $this->session->set_userdata('recipe_error', '');

      $recipes['message'] = $this->session->userdata('recipe_message');
      if ($this->session->userdata('recipe_message') != '')
         $this->session->set_userdata('recipe_message', '');

      $this->load->model('Categories');
      $this->load->model('Recipes');
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $category_list = $this->Categories->get_category_tree($site_id);
      
      $recipes['category_exists'] = (count($category_list) == 0) ? FALSE : TRUE;

      $this->load->library('validation');
      
      foreach ($category_list AS $cat)
      {
         $rules['cat'.$cat['ID']] = 'trim';
         $fields['cat'.$cat['ID']] = 'Category #'.$cat['ID'];
         $defaults['cat'.$cat['ID']] = 0;
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);

      $assigned = $this->Categories->get_all_category_ids($recipe_id);
      foreach ($assigned AS $cat)
      {
         $defaults['cat'.$cat['CategoryID']] = 1;
      }

      $this->validation->set_defaults($defaults);
      
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
         $data['recipe'] = $this->Recipes->get_recipe_record($recipe_id, $site_id);
         $data['recipes'] = $recipes; // errors and messages
         $data['recipe_id'] = $recipe_id;
         $data['category_list'] = $category_list;

         $this->load->vars($data);
   	
         return $this->load->view('categories/assign', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_assign($site_id, $recipe_id, $assigned);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the assign form
    *
    * Auditing: incomplete
    */
   function _assign($site_id, $recipe_id, $assigned)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($recipe_id == 0)
      {
         show_error('_assign requires that a recipe ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
      {
         $cat_id = substr($key, 3);

         $was_assigned = FALSE;
         foreach ($assigned AS $cat)
         {
            if ($cat['CategoryID'] == $cat_id)
               $was_assigned = TRUE;
         }

         $is_assigned = ($this->input->post($key) == 1) ? TRUE : FALSE;

         if ($was_assigned && ! $is_assigned)
         {
            // it has been unchecked, delete from pr_product_category
            $this_cat['CategoryID'] = $cat_id;
            $this_cat['RecipeID'] = $recipe_id;
            $this->write_db->where($this_cat);
            $this->write_db->delete('rcp_recipe_category');
         }
         elseif ( ! $was_assigned && $is_assigned)
         {
            // it has been newly checked, insert into pr_product_category
            $this_cat['CategoryID'] = $cat_id;
            $this_cat['RecipeID'] = $recipe_id;
            $this->write_db->insert('rcp_recipe_category', $this_cat);
         }
      }

      $recipe = $this->Recipes->get_recipe_record($recipe_id, $site_id);

      $this->session->set_userdata('recipe_message', 'The categories for '.$recipe['Title'].' have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;

      redirect('categories/assign/'.$site_id.'/'.$recipe_id.'/'. $last_action.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Rearranges category items up and down
    *
    * @return void
    * Auditing: incomplete
    */
   function move($site_id, $cat_id, $direction, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->model('Categories');

      // detect if the page is just being reloaded
      if ($this_action > $this->session->userdata('last_action'))
      {
         $this->session->set_userdata('last_action', $this_action);

         $sql = 'SELECT ParentID, Sort, SiteID '.
                'FROM rcp_category '.
                'WHERE ID = '.$cat_id;
   
         $query = $this->write_db->query($sql);
         $row = $query->row_array();
         
         // determine how many children are on this level
         $sql = 'SELECT ID '.
                'FROM rcp_category '.
                'WHERE ParentID = '.$row['ParentID'].' '.
                'AND SiteID = \''.$row['SiteID'].'\'';
   
         $query = $this->write_db->query($sql);
         $parent = $query->result_array();
         $children = $query->num_rows();
      
         if ($direction == "dn" && $row['Sort'] < $children)
         {
            $sql = 'UPDATE rcp_category '.
                   'SET Sort = '.$row['Sort'].' '.
                   'WHERE Sort = '.($row['Sort'] + 1).' '.
                   'AND ParentID = '.$row['ParentID'].' '.
                   'AND SiteID = \''.$row['SiteID'].'\'';
            $query = $this->write_db->query($sql);

            $sql = 'UPDATE rcp_category '.
                   'SET Sort = '.($row['Sort'] + 1).' '.
                   'WHERE ID = '.$cat_id;
            $query = $this->write_db->query($sql);
         }
         elseif ($direction == "up" && $row['Sort'] > 1)
         {
            $sql = 'UPDATE rcp_category '.
                   'SET Sort = '.$row['Sort'].' '.
                   'WHERE Sort = '.($row['Sort'] - 1).' '.
                   'AND ParentID = '.$row['ParentID'].' '.
                   'AND SiteID = \''.$row['SiteID'].'\'';
            $query = $this->write_db->query($sql);

            $sql = 'UPDATE rcp_category '.
                   'SET Sort = '.($row['Sort'] - 1).' '.
                   'WHERE ID = '.$cat_id;
            $query = $this->write_db->query($sql);
         }

      // And rebuild the tree so it is up-to-date
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);
      }
      redirect("categories/index/".$site_id.'/');
   }

   
}
?>
