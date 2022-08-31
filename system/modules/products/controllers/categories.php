<?php

class Categories extends Controller {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables

   function Categories()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'products'));
      $this->load->helper(array('url', 'menu'));

      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('read', TRUE);
      $this->hcg_db = $this->load->database('hcg_read', TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of this site's product categories
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $products['error_msg'] = $this->session->userdata('products_error');
      if ($this->session->userdata('products_error') != '')
         $this->session->set_userdata('products_error', '');
         
      $this->load->model('Sites');
      $this->load->model('Categories');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      $site = $this->Sites->get_site_data($site_id);

      $category_list = $this->Categories->get_category_tree($site_id);

      $products['category_exists'] = (count($category_list) < 2) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('products');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
      $data['submenu'] = get_submenu($site_id, 'Categories');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['products'] = $products;
      $data['root_id'] = $root;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('categories/list', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes a product category
    *
    * Auditing: incomplete
    */
   function delete($site_id, $cat_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Product_categories');
      
      // delete all references to this category in pr_products_category
      $sql = 'DELETE FROM pr_product_category '. 
             'WHERE CategoryID = '.$cat_id;
      $this->cb_db->query($sql);
      $this->hcg_db->query($sql);
      
      $category = $this->Categories->get_category_data($cat_id, $site_id);

      // delete the category record itself
      $sql = 'DELETE FROM pr_category '. 
             'WHERE CategoryID = '.$cat_id;
      $this->cb_db->query($sql);
      $this->hcg_db->query($sql);
      
      // get a list of this category's children
      $sql = 'SELECT CategoryID FROM pr_category '.
             'WHERE CategoryParentID = '.$category['CategoryID'].' '.
             'ORDER BY CategoryOrder';
      $query = $this->cb_db->query($sql);
      $children = $query->result_array();
      
      // get a list of categories whose CategoryOrder will need to be adjusted
      $sql = 'SELECT CategoryID, CategoryOrder FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryOrder > '.$category['CategoryOrder'].' '.
             'AND CategoryParentID = '.$category['CategoryParentID'];
      $query = $this->cb_db->query($sql);
      $belows = $query->result_array();
      
      // change the parent IDs of children to this category's parent ID
      for ($i=0; $i<count($children); $i++)
      {
         $values['CategoryParentID'] = $category['CategoryParentID'];
         $values['CategoryOrder'] = $category['CategoryOrder'] + $i;
         $this->cb_db->where('CategoryID', $children[$i]['CategoryID']);
         $this->cb_db->update('pr_category', $values);
         $this->hcg_db->where('CategoryID', $children[$i]['CategoryID']);
         $this->hcg_db->update('pr_category', $values);
      }
      
      $offset = count($children) - 1;
      foreach ($belows AS $below)
      {
         $values['CategoryOrder'] = $below['CategoryOrder'] + $offset;
         $this->cb_db->where('CategoryID', $below['CategoryID']);
         $this->cb_db->update('pr_category', $values);
         $this->hcg_db->where('CategoryID', $below['CategoryID']);
         $this->hcg_db->update('pr_category', $values);
      }

      redirect('categories/index/'.$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a product category
    *
    * Auditing: incomplete
    */
   function add($site_id, $parent, $sort, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('fckeditor', 'text'));
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);

      // generate the list of products without categories
      $nocat_list = $this->Products->get_nocat_products_in_site($site_id);
      foreach ($nocat_list AS $prod)
      {
         $rules['prod'.$prod['ProductID']] = 'trim';
         $fields['prod'.$prod['ProductID']] = 'Product #'.$prod['ProductID'];
         $defaults['prod'.$prod['ProductID']] = 0;
      }

      $rules['CategoryCode'] = 'trim|required';
      $rules['CategoryName'] = 'trim|required';
      $rules['CategoryDescription'] = 'trim';
      $rules['CategoryText'] = 'trim';
      $rules['CategoryType'] = 'trim';
      $rules['CategoryOrder'] = 'trim';
      $rules['SESFilename'] = 'trim';
      $rules['Language'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaMisc'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['CategoryCode'] = 'Category Code';
      $fields['CategoryName'] = 'Category Name';
      $fields['CategoryDescription'] = 'Category Description';
      $fields['CategoryText'] = 'Category Text';
      $fields['CategoryType'] = 'Category Type';
      $fields['CategoryOrder'] = 'Category Order';
      $fields['SESFilename'] = 'SES Filename';
      $fields['Language'] = 'Language';
      $fields['Status'] = 'Status';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaMisc'] = 'Meta Misc';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';

      $this->validation->set_fields($fields);

      $defaults['CategoryType'] = 'attribute';
      $defaults['CategoryOrder'] = $sort;
      $defaults['Status'] = 'active';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');
         
         $data['statuses'] = array('active' => 'active',
                                   'discontinued' => 'discontinued');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
         $data['submenu'] = get_submenu($site_id, 'Categories');
         $data['site_id'] = $site_id;
         $data['parent'] = $parent;
         $data['sort'] = $sort;
         $data['site'] = $site;
         $data['nocat_list'] = $nocat_list;
      
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
      $sql = 'SELECT CategoryID, CategoryOrder FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryParentID = '.$parent.' '.
             'AND CategoryOrder >= '.$sort;
      $query = $this->cb_db->query($sql);
      $sort_list = $query->result_array();
      
      if ($query->num_rows() > 0)
      {
         foreach($sort_list AS $item)
         {
            $item['CategoryOrder'] = $item['CategoryOrder'] + 1;
            $this->cb_db->where('CategoryID', $item['CategoryID']);
            $this->cb_db->update('pr_category', $item);
            $this->hcg_db->where('CategoryID', $item['CategoryID']);
            $this->hcg_db->update('pr_category', $item);
         }
      }

      // Now, insert the record
      $fields = $this->validation->_fields;
      
      $prods = array();
      foreach ($fields AS $key => $value)
      {
         // pull out the products that were selected
         if (substr($key, 0, 4) == 'prod')
         {
            $prods[$key] = $this->input->post($key);
         }
         else
         {
            $values[$key] = $this->input->post($key);
         }
      }
      
      $values['SiteID'] = $site_id;      
      $values['CategoryParentID'] = $parent;
      $values['CategoryCode'] = ascii_to_entities($values['CategoryCode']);
      $values['CategoryName'] = ascii_to_entities($values['CategoryName']);
      $values['CategoryDescription'] = ascii_to_entities($values['CategoryDescription']);
      $values['CategoryText'] = ascii_to_entities($values['CategoryText']);
      $values['SESFilename'] = ascii_to_entities($values['SESFilename']);
      $values['MetaTitle'] = ascii_to_entities($values['MetaTitle']);
      $values['MetaMisc'] = ascii_to_entities($values['MetaMisc']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);

//      $values['CreatedDate'] = date('Y-m-d H:i:s');
//      $values['CreatedBy'] = $this->session->userdata('username');

      $this->cb_db->insert('pr_category', $values);
      $this->hcg_db->insert('pr_category', $values);
      
      $category_id = $this->cb_db->insert_id();
      
      $this->auditor->audit_insert('pr_category', '', $values);

      // And rebuild the tree so it is up-to-date
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      // now, assign selected products to this category
      foreach ($prods AS $key => $value)
      {
         $prod_id = substr($key, 4);

         if ($this->input->post($key) == 1)
         {
            // it has been newly checked, insert into pr_product_category
            $this_prod['CategoryID'] = $category_id;
            $this_prod['ProductID'] = $prod_id;
            $this->cb_db->insert('pr_product_category', $this_prod);
            $this->hcg_db->insert('pr_product_category', $this_prod);
         }
      }
      redirect("categories/index/".$site_id.'/');
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
      
      $this->load->helper(array('fckeditor', 'text'));
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->library('validation');

      $site = $this->Sites->get_site_data($site_id);
      $old_values = $this->Categories->get_category_data($cat_id, $site_id);
      
      // generate the list of products without categories
      $nocat_list = $this->Products->get_nocat_products_in_site($site_id);
      foreach ($nocat_list AS $prod)
      {
         $rules['prod'.$prod['ProductID']] = 'trim';
         $fields['prod'.$prod['ProductID']] = 'Product #'.$prod['ProductID'];
         $defaults['prod'.$prod['ProductID']] = 0;
      }

      $rules['CategoryCode'] = 'trim|required';
      $rules['CategoryName'] = 'trim|required';
      $rules['CategoryDescription'] = 'trim';
      $rules['CategoryText'] = 'trim';
      $rules['CategoryType'] = 'trim';
      $rules['CategoryOrder'] = 'trim';
      $rules['CategoryParentID'] = 'trim';
      $rules['SESFilename'] = 'trim';
      $rules['Language'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaMisc'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['CategoryCode'] = 'Category Code';
      $fields['CategoryName'] = 'Category Name';
      $fields['CategoryDescription'] = 'Category Description';
      $fields['CategoryText'] = 'Category Text';
      $fields['CategoryType'] = 'Category Type';
      $fields['CategoryOrder'] = 'Category Order';
      $fields['CategoryParentID'] = 'Category Parent ID';
      $fields['SESFilename'] = 'SES Filename';
      $fields['Language'] = 'Language';
      $fields['Status'] = 'Status';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaMisc'] = 'Meta Misc';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      $defaults['CategoryCode'] = entities_to_ascii($defaults['CategoryCode']);
      $defaults['CategoryName'] = entities_to_ascii($defaults['CategoryName']);
      $defaults['CategoryDescription'] = entities_to_ascii($defaults['CategoryDescription']);
      $defaults['CategoryText'] = entities_to_ascii($defaults['CategoryText']);
      $defaults['SESFilename'] = entities_to_ascii($defaults['SESFilename']);
      $defaults['MetaTitle'] = entities_to_ascii($defaults['MetaTitle']);
      $defaults['MetaMisc'] = entities_to_ascii($defaults['MetaMisc']);
      $defaults['MetaDescription'] = entities_to_ascii($defaults['MetaDescription']);
      $defaults['MetaKeywords'] = entities_to_ascii($defaults['MetaKeywords']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');

         $data['statuses'] = array('active' => 'active',
                                   'discontinued' => 'discontinued');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
         $data['submenu'] = get_submenu($site_id, 'Categories');
         $data['cat_id'] = $cat_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['parents'] = $this->Categories->get_parent_list($site_id, $cat_id);
         $data['nocat_list'] = $nocat_list;
      
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
      
      $prods = array();
      foreach ($fields AS $key => $value)
      {
         // pull out the products that were selected
         if (substr($key, 0, 4) == 'prod')
         {
            $prods[$key] = $this->input->post($key);
         }
         else
         {
            $values[$key] = $this->input->post($key);
         }
      }

      $values['CategoryCode'] = ascii_to_entities($values['CategoryCode']);
      $values['CategoryName'] = ascii_to_entities($values['CategoryName']);
      $values['CategoryDescription'] = ascii_to_entities($values['CategoryDescription']);
      $values['CategoryText'] = ascii_to_entities($values['CategoryText']);
      $values['SESFilename'] = ascii_to_entities($values['SESFilename']);
      $values['MetaTitle'] = ascii_to_entities($values['MetaTitle']);
      $values['MetaMisc'] = ascii_to_entities($values['MetaMisc']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);

//      $values['RevisedDate'] = date('Y-m-d H:i:s');
//      $values['RevisedBy'] = $this->session->userdata('username');

      // find out if the parent ID changed
      $category = $this->Categories->get_category_data($cat_id, $site_id);
      if ($category['CategoryParentID'] != $values['CategoryParentID'])
      {
         // if so, then get a count of the children of the new parent 
         // add one to get the new CategoryOrder for this category
         $sql = 'SELECT CategoryID FROM pr_category '.
                'WHERE CategoryParentID = '.$values['CategoryParentID'];
         $query = $this->cb_db->query($sql);
         $children = $query->result_array();
         $values['CategoryOrder'] = count($children) + 1;
      
         // get a list of all categories below this one in its old parent
         $sql = 'SELECT CategoryID, CategoryOrder FROM pr_category '.
                'WHERE SiteID = "'.$site_id.'" '.
                'AND CategoryOrder > '.$category['CategoryOrder'].' '.
                'AND CategoryParentID = '.$category['CategoryParentID'];
         $query = $this->cb_db->query($sql);
         $belows = $query->result_array();

         // adjust the CategoryOrder (-1) for each of those categories
         foreach ($belows AS $below)
         {
            $item['CategoryOrder'] = $below['CategoryOrder'] - 1;
            $this->cb_db->where('CategoryID', $below['CategoryID']);
            $this->cb_db->update('pr_category', $item);
            $this->hcg_db->where('CategoryID', $below['CategoryID']);
            $this->hcg_db->update('pr_category', $item);
         }
      }

      // update the edited category
      $this->Categories->update_category($cat_id, $values, $old_values);
      
      // Since we can change the CategoryOrder, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      // now, assign selected products to this category
      foreach ($prods AS $key => $value)
      {
         $prod_id = substr($key, 4);

         if ($this->input->post($key) == 1)
         {
            // it has been newly checked, insert into pr_product_category
            $this_prod['CategoryID'] = $cat_id;
            $this_prod['ProductID'] = $prod_id;
            $this->cb_db->insert('pr_product_category', $this_prod);
            $this->hcg_db->insert('pr_product_category', $this_prod);
         }
      }

      redirect("categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a product's category list
    *
    * Auditing: incomplete
    */
   function assign($site_id, $product_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      
      $products['error_msg'] = $this->session->userdata('products_error');
      if ($this->session->userdata('products_error') != '')
         $this->session->set_userdata('products_error', '');

      $products['message'] = $this->session->userdata('product_message');
      if ($this->session->userdata('product_message') != '')
         $this->session->set_userdata('product_message', '');

      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $category_list = $this->Categories->get_category_tree($site_id);
      
      $products['category_exists'] = (count($category_list) == 0) ? FALSE : TRUE;

      $this->load->library('validation');
      
      foreach ($category_list AS $cat)
      {
         $rules['cat'.$cat['CategoryID']] = 'trim';
         $fields['cat'.$cat['CategoryID']] = 'Category #'.$cat['CategoryID'];
         $defaults['cat'.$cat['CategoryID']] = 0;
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);

      $assigned = $this->Categories->get_all_category_ids($product_id, $site_id);
      foreach ($assigned AS $cat)
      {
         $defaults['cat'.$cat['CategoryID']] = 1;
      }

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
         $data['submenu'] = get_submenu($site_id, 'Products');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['product'] = $this->Products->get_product_data($product_id, $site_id);
         $data['products'] = $products; // errors and messages
         $data['product_id'] = $product_id;
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
            $this->_assign($site_id, $product_id, $assigned);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit pcategory form
    *
    * Auditing: incomplete
    */
   function _assign($site_id, $product_id, $assigned)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($product_id == 0)
      {
         show_error('_edit_pcategory requires that a product ID be supplied.');
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
            $this_cat['ProductID'] = $product_id;
            $this->cb_db->where($this_cat);
            $this->cb_db->delete('pr_product_category');
            $this->hcg_db->where($this_cat);
            $this->hcg_db->delete('pr_product_category');
         }
         elseif ( ! $was_assigned && $is_assigned)
         {
            // it has been newly checked, insert into pr_product_category
            $this_cat['CategoryID'] = $cat_id;
            $this_cat['ProductID'] = $product_id;
            $this->cb_db->insert('pr_product_category', $this_cat);
            $this->hcg_db->insert('pr_product_category', $this_cat);
         }
      }

      $product = $this->Products->get_product_data($product_id, $site_id);

      $this->session->set_userdata('product_message', 'The categories for '.$product['ProductName'].' have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;

      redirect('categories/assign/'.$site_id.'/'.$product_id.'/'. $last_action.'/');
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

         $sql = 'SELECT CategoryParentID, CategoryOrder, SiteID '.
                'FROM pr_category '.
                'WHERE CategoryID = '.$cat_id;
   
         $query = $this->cb_db->query($sql);
         $row = $query->row_array();
         
         // determine how many children are on this level
         $sql = 'SELECT CategoryID '.
                'FROM pr_category '.
                'WHERE CategoryParentID = '.$row['CategoryParentID'].' '.
                'AND SiteID = \''.$row['SiteID'].'\'';
   
         $query = $this->cb_db->query($sql);
         $parent = $query->result_array();
         $children = $query->num_rows();
      
         if ($direction == "dn" && $row['CategoryOrder'] < $children)
         {
            $sql = 'UPDATE pr_category '.
                   'SET CategoryOrder = '.$row['CategoryOrder'].' '.
                   'WHERE CategoryOrder = '.($row['CategoryOrder'] + 1).' '.
                   'AND CategoryParentID = '.$row['CategoryParentID'].' '.
                   'AND SiteID = \''.$row['SiteID'].'\'';
            $this->cb_db->query($sql);
            $this->hcg_db->query($sql);

            $sql = 'UPDATE pr_category '.
                   'SET CategoryOrder = '.($row['CategoryOrder'] + 1).' '.
                   'WHERE CategoryID = '.$cat_id;
            $this->cb_db->query($sql);
            $this->hcg_db->query($sql);
         }
         elseif ($direction == "up" && $row['CategoryOrder'] > 1)
         {
            $sql = 'UPDATE pr_category '.
                   'SET CategoryOrder = '.$row['CategoryOrder'].' '.
                   'WHERE CategoryOrder = '.($row['CategoryOrder'] - 1).' '.
                   'AND CategoryParentID = '.$row['CategoryParentID'].' '.
                   'AND SiteID = \''.$row['SiteID'].'\'';
            $this->cb_db->query($sql);
            $this->hcg_db->query($sql);

            $sql = 'UPDATE pr_category '.
                   'SET CategoryOrder = '.($row['CategoryOrder'] - 1).' '.
                   'WHERE CategoryID = '.$cat_id;
            $this->cb_db->query($sql);
            $this->hcg_db->query($sql);
         }

      // And rebuild the tree so it is up-to-date
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);
      }
      redirect("categories/index/".$site_id.'/');
   }

   
}
?>
