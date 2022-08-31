<?php

class Faqs extends Controller {

   function Faqs()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'faqs'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of the FAQs by category/product
    *
    */
   function index($site_id, $category_code = 'all')
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

      $this->load->helper('text');
      $this->load->model('Sites');
      $this->load->model('Categories');
      $this->load->model('Items');
      $this->load->model('Shared');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $category_list = $this->Categories->get_category_tree($site_id);

      if ($category_code == 'all')
      {
         $category = array();
         $faq_list = $this->Items->get_faqs_in_site($site_id);
         $nocat_list = $this->Items->get_nocat_faqs_in_site($site_id);
         $product_list = $this->Items->get_product_faqs_in_site($site_id);
         $admin['limited'] = FALSE;
      }
      elseif ($category_code == 'none')
      {
         $category = array();
         $faq_list = array();
         $nocat_list = $this->Items->get_nocat_faqs_in_site($site_id);
         $admin['limited'] = TRUE;
      }
      else
      {
         $category = $this->Categories->get_category_data_by_code($site_id, $category_code);
         $faq_list = $this->Products->get_products_in_category($site_id, $category['ID']);
         $admin['limited'] = TRUE;
      }
      
      // make sure ShortQuestion has a value
      for ($i=0, $total_faqs=count($faq_list); $i<$total_faqs; $i++)
      {
         if (trim($faq_list[$i]['ShortQuestion']) == '')
         {
            $faq_list[$i]['ShortQuestion'] = $faq_list[$i]['Question'];
         }
      }
      for ($i=0, $total_faqs=count($nocat_list); $i<$total_faqs; $i++)
      {
         if (trim($nocat_list[$i]['ShortQuestion']) == '')
         {
            $nocat_list[$i]['ShortQuestion'] = $nocat_list[$i]['Question'];
         }
      }
      for ($i=0, $total_cats=count($product_list); $i<$total_cats; $i++)
      {
         for ($j=0, $total_faqs=count($product_list[$i]['FAQs']); $j<$total_faqs; $j++)
         {
            if (trim($product_list[$i]['FAQs'][$j]['ShortQuestion']) == '')
            {
               $product_list[$i]['FAQs'][$j]['ShortQuestion'] = $product_list[$i]['FAQs'][$j]['Question'];
            }
         }
         for ($j=0, $total_prods=count($product_list[$i]['Products']); $j<$total_prods; $j++)
         {
            for ($k=0, $total_faqs=count($product_list[$i]['Products'][$j]['FAQs']); $k<$total_faqs; $k++)
            {
               if (trim($product_list[$i]['Products'][$j]['FAQs'][$k]['ShortQuestion']) == '')
               {
                  $product_list[$i]['Products'][$j]['FAQs'][$k]['ShortQuestion'] = $product_list[$i]['Products'][$j]['FAQs'][$k]['Question'];
               }
            }
         }
      }


      $admin['faq_exists'] = (count($faq_list) == 0 && count($nocat_list) == 0) ? FALSE : TRUE;
      $admin['product_exists'] = (count($product_list) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('faqs');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
      $data['submenu'] = get_submenu($site_id, 'FAQs');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['category_code'] = $category_code;
      $data['category'] = $category;
      $data['admin'] = $admin;
      $data['category_list'] = $category_list;
      $data['faq_list'] = $faq_list;
      $data['nocat_list'] = $nocat_list;
      $data['product_list'] = $product_list;
      $data['shared'] = $this->Shared->get_shared_faqs_lookup($site_id);
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/faqs/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an FAQ
    *
    */
   function delete($site_id, $faq_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->helper('text');
      $this->load->model('Indexes');
      $this->load->model('Items');
      
      // get the current record so we can display a status message
      $faq = $this->Items->get_faq_record($faq_id);
      
      // delete the FAQ record and associated records
      $this->Items->delete_faq($faq_id);
      
      if (strlen($faq['Question']) > 50)
      {
         $this->session->set_userdata('faq_message', 'The FAQ beginning "'.character_limiter($faq['Question'], 50).'"... has been deleted.');
      }
      else
      {
         $this->session->set_userdata('faq_message', 'The FAQ  "'.$faq['Question'].'" has been deleted.');
      }
      
      redirect("sites/faqs/index/".$site_id);
   }

   // --------------------------------------------------------------------

   /**
    * Adds an FAQ
    *
    */
   function add($site_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->helper(array('fckeditor', 'text'));
      $this->load->model('Sites');
      $this->load->model('Items');
      $this->load->model('Indexes');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['ShortQuestion'] = 'trim';
      $rules['Question'] = 'trim|required';
      $rules['Answer'] = 'trim|required';
      $rules['FlagAsNew'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['Sort'] = 'trim';
      $rules['Keywords'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ShortQuestion'] = 'Short Question';
      $fields['Question'] = 'Question';
      $fields['Answer'] = 'Answer';
      $fields['FlagAsNew'] = 'Flag as New';
      $fields['Status'] = 'Status';
      $fields['Sort'] = 'Sort';
      $fields['Keywords'] = 'Keywords';

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
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('sites/faqs/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add FAQ form
    *
    */
   function _add($site_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;
      $values['ShortQuestion'] = ascii_to_entities($values['ShortQuestion']);
      $values['Question'] = ascii_to_entities($values['Question']);
      $values['Answer'] = ascii_to_entities($values['Answer']);
      $values['Note'] = '';
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $faq_id = $this->Items->insert_faq($values);
      
      // update the index for this recipe
      $this->Indexes->update_search_index($faq_id);

      if (strlen($faq['Question']) > 50)
      {
         $this->session->set_userdata('faq_message', 'The FAQ beginning "'.character_limiter($faq['Question'], 50).'"... has been added.');
      }
      else
      {
         $this->session->set_userdata('faq_message', 'The FAQ  "'.$faq['Question'].'" has been added.');
      }

      redirect("sites/faqs/index/".$site_id);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ
    *
    */
   function edit($site_id, $faq_id, $answer_id, $this_action) 
   {
      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('ckeditor', 'text'));
      $this->load->model('Sites');
      $this->load->model('Indexes');
      $this->load->model('Items');
      $this->load->model('Shared');
      $this->load->library('validation');

      $site = $this->Sites->get_site_data($site_id);
      $faq = $this->Items->get_faq_data($faq_id, $answer_id);

      $rules['ShortQuestion'] = 'trim';
      $rules['Question'] = 'trim|required';
      $rules['Answer'] = 'trim|required';
      $rules['FlagAsNew'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['Sort'] = 'trim';
      $rules['Keywords'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ShortQuestion'] = 'Short Question';
      $fields['Question'] = 'Question';
      $fields['Answer'] = 'Answer';
      $fields['FlagAsNew'] = 'Flag as New';
      $fields['Status'] = 'Status';
      $fields['Sort'] = 'Sort';
      $fields['Keywords'] = 'Keywords';

      $this->validation->set_fields($fields);

      $defaults = $faq;
      $defaults['ShortQuestion'] = entities_to_ascii($defaults['ShortQuestion']);
      $defaults['Question'] = entities_to_ascii($defaults['Question']);
      $defaults['Answer'] = entities_to_ascii($defaults['Answer']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('categories');

         $data['statuses'] = array('active' => 'active', 
                                   'inactive' => 'inactive',
                                   'pending' => 'pending');
         
         $data['last_action'] = $this->session->userdata('last_action') + 1;      
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
         $data['submenu'] = get_submenu($site_id, 'FAQs');
         $data['admin'] = $admin;
         $data['faq_id'] = $faq_id;
         $data['answer_id'] = $answer_id;
         $data['faq'] = $faq;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['is_shared'] = $this->Shared->faq_is_shared($faq_id);
      
         $this->load->vars($data);
   	
         return $this->load->view('sites/faqs/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $faq_id, $answer_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ record
    *
    */
   function _edit($site_id, $faq_id, $answer_id)
   {
      if ($faq_id == 0)
      {
         show_error('_edit_item requires that an faq ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;
      $values['ShortQuestion'] = ascii_to_entities($values['ShortQuestion']);
      $values['Question'] = ascii_to_entities($values['Question']);
      $values['Answer'] = ascii_to_entities($values['Answer']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Items->update_faq($faq_id, $values);
      
      // update the index for this recipe
      $this->Indexes->update_search_index($faq_id);
      
      $this->session->set_userdata('faq_message', 'This FAQ has been updated.');

      // I feel like I should stay on the edit page
      $last_action = $this->session->userdata('last_action') + 1;
      redirect('sites/faqs/edit/'.$site_id.'/'.$faq_id.'/'.$answer_id.'/'. $last_action);
   }


}
?>