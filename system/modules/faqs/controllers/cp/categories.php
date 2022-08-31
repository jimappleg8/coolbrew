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
   function index($site_id = '')
   {
      $this->administrator->check_login();

      $site_id = 'shared';
      
      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');
      
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
      $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
      $data['submenu'] = get_main_submenu('Categories');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      $data['root_id'] = $root;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/categories/list', NULL, TRUE);
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

      redirect("cp/categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a FAQ category
    *
    */
   function add($site_id, $parent, $sort, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';
      
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
         $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
         $data['submenu'] = get_submenu($site_id, 'Categories');
         $data['site_id'] = $site_id;
         $data['parent'] = $parent;
         $data['sort'] = $sort;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/categories/add', NULL, TRUE);
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
      $this->administrator->check_login();

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

      redirect("cp/categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a category
    *
    */
   function edit($site_id, $category_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';
      
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
         $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
         $data['submenu'] = get_submenu($site_id, 'Categories');
         $data['category_id'] = $category_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['admin'] = $admin;
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/categories/edit', NULL, TRUE);
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
      $this->administrator->check_login();

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

      redirect("cp/categories/index/".$site_id.'/');
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
      $this->administrator->check_login();

      $site_id = 'shared';

      $this->load->model('Categories');

      // detect if the page is just being reloaded
      if ($this_action > $this->session->userdata('last_action'))
      {
         $this->session->set_userdata('last_action', $this_action);
         
         $this->Categories->move_category($cat_id, $direction);
      }

      redirect("cp/categories/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ's category list
    *
    * Auditing: incomplete
    */
   function assign($site_id, $faq_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Categories');
      $this->load->model('Items');
      $this->load->model('Item_category');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);

      $category_list = $this->Categories->get_category_tree($site_id);
      
      $admin['category_exists'] = (count($category_list) == 0) ? FALSE : TRUE;

      foreach ($category_list AS $cat)
      {
         $rules['cat'.$cat['ID']] = 'trim';
         $fields['cat'.$cat['ID']] = 'Category #'.$cat['ID'];
         $defaults['cat'.$cat['ID']] = 0;
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);

      $assigned = $this->Categories->get_all_category_ids($faq_id);
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
         $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
         $data['submenu'] = get_main_submenu('FAQs');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['faq'] = $this->Items->get_faq_record($faq_id);
         $data['admin'] = $admin; // errors and messages
         $data['faq_id'] = $faq_id;
         $data['category_list'] = $category_list;

         $this->load->vars($data);
   	
         return $this->load->view('cp/categories/assign', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_assign($site_id, $faq_id, $assigned);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit pcategory form
    *
    * Auditing: incomplete
    */
   function _assign($site_id, $faq_id, $assigned)
   {
      $this->administrator->check_login();

      if ($faq_id == 0)
      {
         show_error('_assign requires that a faq ID be supplied.');
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
            // it has been unchecked
            $this->Item_category->delete_link($faq_id, 0, $cat_id);
         }
         elseif ( ! $was_assigned && $is_assigned)
         {
            // it has been newly checked
            $this->Item_category->insert_link($faq_id, 0, $cat_id);
         }
      }

      $this->session->set_userdata('faq_message', 'The categories for this FAQ have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/categories/assign/'.$site_id.'/'.$faq_id.'/'. $last_action.'/');
   }

}
?>