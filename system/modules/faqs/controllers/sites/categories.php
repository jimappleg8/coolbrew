<?php

class Categories extends Controller {

   function Categories()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'faqs'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of all FAQ categories for this site
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');
      
      // clear the search session variable
      $this->session->set_userdata('faq_query', '');

      $this->load->model('Sites');
      $this->load->model('Categories');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      $site = $this->Sites->get_site_data($site_id);
      
      $category_list = $this->Categories->get_category_tree($site_id);

      $admin['faq_exists'] = (count($category_list) <= 1) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('faqs');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
      $data['submenu'] = get_submenu($site_id, 'Categories');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      $data['root_id'] = $root;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/categories/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an FAQ Category
    *
    */
   function delete($category_id, $this_action) 
   {
      $this->load->model('Categories');
      
      // get the site ID
      $faq_category = $this->Categories->get_faq_category_data($category_id);
      $site_id = $faq_category['SiteID'];
      
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      // delete the actual FAQ category record
      $this->Categories->delete_faq_category($category_id);
         
      $this->session->set_userdata('faq_message', 'The FAQ category "'.$faq_category['Name'].'" has been deleted.');

      redirect("sites/categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a FAQ category
    *
    */
   function add($site_id, $parent, $sort, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('Sites');
      $this->load->model('Categories');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['FaqCode'] = 'trim|required';
      $rules['Name'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['Sort'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['FaqCode'] = 'FAQ Code';
      $fields['Name'] = 'Name';
      $fields['Description'] = 'Category Description';
      $fields['Sort'] = 'Category Order';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('faqs');

         $data['statuses'] = array('active' => 'active', 
                                   'inactive' => 'inactive',
                                   'pending' => 'pending');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
         $data['submenu'] = get_submenu($site_id, 'FAQs');
         $data['site_id'] = $site_id;
         $data['parent'] = $parent;
         $data['sort'] = $sort;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('sites/categories/add', NULL, TRUE);
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
    */
   function _add($site_id, $parent, $sort)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      // now insert the record
      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['SiteID'] = $site_id;      
      $values['ParentID'] = $parent;
      $values['Sort'] = $sort;
      $values['FaqCode'] = ascii_to_entities($values['FaqCode']);
      $values['Name'] = ascii_to_entities($values['Name']);
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $this->Categories->insert_category($values);

//      $this->auditor->audit_insert('faqs_category', '', $values);

      $this->session->set_userdata('faq_message', 'The FAQ category "'.$values['Name'].'" has been added.');

      redirect("sites/categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a category
    *
    */
   function edit($site_id, $category_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Categories');

      $site = $this->Sites->get_site_data($site_id);
      $category = $this->Categories->get_faq_category_data($category_id);

      $rules['FaqCode'] = 'trim|required';
      $rules['Name'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['FaqCode'] = 'FAQ Code';
      $fields['Name'] = 'Name';
      $fields['Description'] = 'Category Description';
      $fields['ParentID'] = 'Parent ID';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults = $category;
      $defaults['FaqCode'] = entities_to_ascii($defaults['FaqCode']);
      $defaults['Name'] = entities_to_ascii($defaults['Name']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('faqs');

         $data['statuses'] = array('active' => 'active', 
                                   'inactive' => 'inactive',
                                   'pending' => 'pending');
         $data['parents'] = $this->Categories->get_parent_list($site_id, $category_id);
         
         $data['last_action'] = $this->session->userdata('last_action') + 1;      
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
         $data['submenu'] = get_submenu($site_id, 'FAQs');
         $data['category_id'] = $category_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['admin'] = $admin;
      
         $this->load->vars($data);
   	
         return $this->load->view('sites/categories/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $category_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a category record
    *
    */
   function _edit($site_id, $category_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      if ($category_id == 0)
      {
         show_error('_edit_category requires that a category ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['FaqCode'] = ascii_to_entities($values['FaqCode']);
      $values['Name'] = ascii_to_entities($values['Name']);
      $values['Description'] = ascii_to_entities($values['Description']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Categories->update_category($category_id, $values);
      
      $this->session->set_userdata('faq_message', 'The FAQ category "'.$values['Name'].'" has been updated.');

      redirect("sites/categories/index/".$site_id.'/');
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
         
         $this->Categories->move_category($cat_id, $direction);
      }

      redirect("sites/categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ's category list
    *
    * Auditing: incomplete
    */
   function assign($site_id, $faq_id, $answer_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Items');
      $this->load->model('Categories');
      $this->load->model('Item_category');
      $this->load->model('Products');
      $this->load->model('Item_product');
      $this->load->model('Item_product_category');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);

      // PART 1: FAQ Categories
      
      $category_list = $this->Categories->get_category_tree($site_id);
      
      $admin['category_exists'] = (count($category_list) == 0) ? FALSE : TRUE;

      $rules = array();
      $fields = array();
      $defaults = array();

      foreach ($category_list AS $cat)
      {
         $rules['cat'.$cat['ID']] = 'trim';
         $fields['cat'.$cat['ID']] = 'Category #'.$cat['ID'];
         $defaults['cat'.$cat['ID']] = 0;
      }

      $assigned = $this->Categories->get_all_category_ids($faq_id, $answer_id);
      foreach ($assigned AS $cat)
      {
         $defaults['cat'.$cat['CategoryID']] = 1;
      }
      
      // PART 2: Product Categories and Products
      
      $product_list = $this->Products->get_products_tree($site_id, $faq_id, $answer_id);

      $admin['product_exists'] = (count($product_list) == 0) ? FALSE : TRUE;

      foreach ($product_list AS $cat)
      {
         $rules['prodcat'.$cat['CategoryID']] = 'trim';
         $fields['prodcat'.$cat['CategoryID']] = 'Product Category #'.$cat['CategoryID'];
         $defaults['prodcat'.$cat['CategoryID']] = $cat['Assigned'];
         foreach ($cat['Products'] AS $prod)
         {
            $rules['prod'.$prod['ProductID']] = 'trim';
            $fields['prod'.$prod['ProductID']] = 'Product #'.$prod['ProductID'];
            $defaults['prod'.$prod['ProductID']] = $prod['Assigned'];
         }
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);
      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
         $data['submenu'] = get_submenu($site_id, 'FAQs');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['faq'] = $this->Items->get_faq_data($faq_id, $answer_id);
         $data['admin'] = $admin; // errors and messages
         $data['faq_id'] = $faq_id;
         $data['answer_id'] = $answer_id;
         $data['category_list'] = $category_list;
         $data['product_list'] = $product_list;

         $this->load->vars($data);
   	
         return $this->load->view('sites/categories/assign', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_assign($site_id, $faq_id, $answer_id, $assigned, $product_list);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit pcategory form
    *
    * Auditing: incomplete
    */
   function _assign($site_id, $faq_id, $answer_id, $assigned, $product_list)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($faq_id == 0)
      {
         show_error('_assign requires that a faq ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
      {
         if (substr($key, 0, 3) == 'cat')
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
               // it has been unchecked
               $this->Item_category->delete_link($faq_id, $answer_id, $cat_id);
            }
            elseif ( ! $was_assigned && $is_assigned)
            {
               // it has been newly checked
               $this->Item_category->insert_link($faq_id, $answer_id, $cat_id);
            }
         }
         elseif (substr($key, 0, 7) == 'prodcat')
         {
            $cat_id = substr($key, 7);

            $was_assigned = FALSE;
            foreach ($product_list AS $cat)
            {
               if ($cat['CategoryID'] == $cat_id)
                  $was_assigned = ($cat['Assigned'] == 1) ? TRUE :  FALSE;
            }

            $is_assigned = ($this->input->post($key) == 1) ? TRUE : FALSE;

            if ($was_assigned && ! $is_assigned)
            {
               // it has been unchecked
               $this->Item_product_category->delete_link($faq_id, $answer_id, $cat_id);
            }
            elseif ( ! $was_assigned && $is_assigned)
            {
               // it has been newly checked
               $this->Item_product_category->insert_link($faq_id, $answer_id, $cat_id);
            }
         }
         elseif (substr($key, 0, 4) == 'prod')
         {
            $prod_id = substr($key, 4);

            $was_assigned = FALSE;
            foreach ($product_list AS $cat)
            {
               foreach ($cat['Products'] AS $prod)
               {
                  if ($prod['ProductID'] == $prod_id)
                  $was_assigned = ($prod['Assigned'] == 1) ? TRUE :  FALSE;
               }
            }

            $is_assigned = ($this->input->post($key) == 1) ? TRUE : FALSE;

            if ($was_assigned && ! $is_assigned)
            {
               // it has been unchecked
               $this->Item_product->delete_link($faq_id, $answer_id, $prod_id);
            }
            elseif ( ! $was_assigned && $is_assigned)
            {
               // it has been newly checked
               $this->Item_product->insert_link($faq_id, $answer_id, $prod_id);
            }
         }
      }

      $this->session->set_userdata('faq_message', 'The categories for this FAQ have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('sites/categories/assign/'.$site_id.'/'.$faq_id.'/'.$answer_id.'/'.$last_action.'/');
   }

}
?>