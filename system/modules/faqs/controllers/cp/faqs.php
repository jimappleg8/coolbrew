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
    * Generates a listing of the FAQs by category
    *
    */
   function index($site_id = '', $category_code = 'all')
   {
      $this->administrator->check_login();
      
      $site_id = 'shared';
      
      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper('text');
      $this->load->model('Sites');
      $this->load->model('Categories');
      $this->load->model('Items');
      $this->load->model('Shared');
      $this->load->model('Answers');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $category_list = $this->Categories->get_category_tree($site_id);

      if ($category_code == 'all')
      {
         $category = array();
         $faq_list = $this->Shared->get_faqs_in_site($site_id);
         $nocat_list = $this->Shared->get_nocat_faqs_in_site($site_id);
         $admin['limited'] = FALSE;
      }
      elseif ($category_code == 'none')
      {
         $category = array();
         $faq_list = array();
         $nocat_list = $this->Shared->get_nocat_faqs_in_site($site_id);
         $admin['limited'] = TRUE;
      }
      else
      {
//         $category = $this->Categories->get_category_data_by_code($site_id, $category_code);
//         $product_list = $this->Products->get_products_in_category($site_id, $category['ID']);
//         $products['limited'] = TRUE;
      }
      
      $answers = $this->Answers->get_answers_in_site($site_id);
      
      // make sure ShortQuestion has a value
      for ($i=0, $total_faqs = count($faq_list); $i<$total_faqs; $i++)
      {
         if (trim($faq_list[$i]['ShortQuestion']) == '')
         {
            $faq_list[$i]['ShortQuestion'] = $faq_list[$i]['Question'];
         }
      }
      for ($i=0, $total_faqs = count($nocat_list); $i<$total_faqs; $i++)
      {
         if (trim($nocat_list[$i]['ShortQuestion']) == '')
         {
            $nocat_list[$i]['ShortQuestion'] = $nocat_list[$i]['Question'];
         }
      }

      $admin['faq_exists'] = (count($faq_list) == 0 && count($nocat_list) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('faqs');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
      $data['submenu'] = get_main_submenu('FAQs');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['category_code'] = $category_code;
      $data['category'] = $category;
      $data['admin'] = $admin;
      $data['category_list'] = $category_list;
      $data['faq_list'] = $faq_list;
      $data['nocat_list'] = $nocat_list;
      $data['answers'] = $answers;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/faqs/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an FAQ
    *
    */
   function delete($site_id, $faq_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

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
      
      redirect("cp/faqs/index/".$faq['SiteID']);
   }

   // --------------------------------------------------------------------

   /**
    * Adds an FAQ
    *
    */
   function add($site_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

      $this->load->helper(array('fckeditor', 'text'));
      $this->load->model('Sites');
      $this->load->model('Items');
      $this->load->model('Indexes');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['Question'] = 'trim|required';
      $rules['Note'] = 'trim';
      $rules['Answer'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['Question'] = 'Question';
      $fields['Note'] = 'Answer Note';
      $fields['Answer'] = 'Answer';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('faqs');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
         $data['submenu'] = get_main_submenu('FAQs');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/faqs/add', NULL, TRUE);
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
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;
      $values['Question'] = ascii_to_entities($values['Question']);
      $values['Answer'] = ascii_to_entities($values['Answer']);
      $values['Note'] = ascii_to_entities($values['Note']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $faq_id = $this->Items->insert_faq($values);
      
      // update the index for this faq
      $this->Indexes->update_search_index($faq_id);

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/faqs/edit/'.$site_id.'/'.$faq_id.'/'.$last_action);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ
    *
    */
   function edit($site_id, $faq_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('fckeditor', 'text'));
      $this->load->model('Sites');
      $this->load->model('Indexes');
      $this->load->model('Items');
      $this->load->model('Shared');
      $this->load->model('Answers');
      $this->load->library('validation');

      $site = $this->Sites->get_site_data($site_id);
      $faq = $this->Items->get_faq_record($faq_id);
      $answers = $this->Answers->get_answers($faq_id);

      $rules['ShortQuestion'] = 'trim';
      $rules['Question'] = 'trim|required';
      $rules['NewAnswer'] = 'trim';
      $rules['NewNote'] = 'trim';
      $rules['FlagAsNew'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['Sort'] = 'trim';
      $rules['Keywords'] = 'trim';
      foreach($answers AS $answer)
      {
         $rules['Answer'.$answer['ID']] = 'trim';
         $rules['Note'.$answer['ID']] = 'trim';
      }

      $this->validation->set_rules($rules);

      $fields['ShortQuestion'] = 'Short Question';
      $fields['Question'] = 'Question';
      $fields['NewAnswer'] = 'Answer';
      $fields['NewNote'] = 'Note';
      $fields['FlagAsNew'] = 'Flag as New';
      $fields['Status'] = 'Status';
      $fields['Sort'] = 'Sort';
      $fields['Keywords'] = 'Keywords';
      foreach($answers AS $answer)
      {
         $fields['Answer'.$answer['ID']] = 'Answer '.$answer['ID'];
         $fields['Note'.$answer['ID']] = 'Note '.$answer['ID'];
      }
      
      $this->validation->set_fields($fields);

      $defaults = $faq;
      $defaults['ShortQuestion'] = entities_to_ascii($defaults['ShortQuestion']);
      $defaults['Question'] = entities_to_ascii($defaults['Question']);
      foreach($answers AS $answer)
      {
         $defaults['Answer'.$answer['ID']] = entities_to_ascii($answer['Answer']);
         $defaults['Note'.$answer['ID']] = entities_to_ascii($answer['Note']);
      }
      
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
         $data['tabs'] = $this->administrator->get_main_tabs('Shared FAQs');
         $data['submenu'] = get_main_submenu('FAQs');
         $data['admin'] = $admin;
         $data['faq_id'] = $faq_id;
         $data['faq'] = $faq;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['answers'] = $answers;
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/faqs/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $faq_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ record
    *
    */
   function _edit($site_id, $faq_id)
   {
      $this->administrator->check_login();

      if ($faq_id == 0)
      {
         show_error('_edit_item requires that an faq ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $faq['ShortQuestion'] = ascii_to_entities($values['ShortQuestion']);
      $faq['Question'] = ascii_to_entities($values['Question']);
      $faq['FlagAsNew'] = $values['FlagAsNew'];
      $faq['Status'] = $values['Status'];
      $faq['Sort'] = $values['Sort'];
      $faq['Keywords'] = $values['Keywords'];
      $faq['RevisedDate'] = date('Y-m-d H:i:s');
      $faq['RevisedBy'] = $this->session->userdata('username');

      $this->Shared->update_faq($faq_id, $faq);
      
      // update the index for this faq
      $this->Indexes->update_search_index($faq_id);
      
      $this->session->set_userdata('faq_message', 'This FAQ has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/faqs/edit/shared/'.$faq_id.'/'.$last_action);
   }


}
?>