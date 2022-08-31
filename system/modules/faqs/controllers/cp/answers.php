<?php

class Answers extends Controller {

   function Answers()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'faqs'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Deletes an Answer
    *
    */
   function delete($site_id, $answer_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

      $this->load->helper('text');
      $this->load->model('Answers');
      
      if ($this->Answers->answer_is_being_used($answer_id))
      {
         $this->session->set_userdata('faq_error', 'This answer is being used in a website and cannot be deleted. Please choose another answer for those sites and try again.');
      }
      else
      {
         // get the current record so we can display a status message
         $answer = $this->Answers->get_answer_data($answer_id);
         
         $faq_id = $this->Answers->get_answer_faq_id($answer_id);
      
         // delete the FAQ record and associated records
         $this->Answers->delete_answer($answer_id);
      
         if (strlen($answer['Answer']) > 50)
         {
            $this->session->set_userdata('faq_message', 'The answer beginning "'.character_limiter($answer['Answer'], 50).'"... has been deleted.');
         }
         else
         {
            $this->session->set_userdata('faq_message', 'The answer  "'.$answer['Answer'].'" has been deleted.');
         }
      }
      
      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/faqs/edit/shared/'.$faq_id.'/'.$last_action);
   }

   // --------------------------------------------------------------------

   /**
    * Adds an answer
    *
    */
   function add($site_id, $faq_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

      $this->load->helper(array('form', 'text'));
      $this->load->model('Answers');
      $this->load->model('Indexes');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['NewAnswer'] = 'trim|required';
      $rules['NewNote'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['NewAnswer'] = 'Answer';
      $fields['NewNote'] = 'Note';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $last_action = $this->session->userdata('last_action') + 1;
         redirect('cp/faqs/edit/shared/'.$faq_id.'/'.$last_action);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id, $faq_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add FAQ form
    *
    */
   function _add($site_id, $faq_id)
   {
      $this->administrator->check_login();

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $values['Answer'] = $values['NewAnswer'];
      $values['Note'] = $values['NewNote'];
      unset($values['NewAnswer']);
      unset($values['NewNote']);
      
      $values['SiteID'] = $site_id;
      $values['FaqID'] = $faq_id;
      $values['Answer'] = ascii_to_entities($values['Answer']);
      $values['Note'] = ascii_to_entities($values['Note']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $answer_id = $this->Answers->insert_answer($values);
      
      // update the index for this faq
      $this->Indexes->update_search_index($faq_id);

      $this->session->set_userdata('faq_message', 'The new answer has been added.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/faqs/edit/shared/'.$faq_id.'/'.$last_action);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ
    *
    */
   function edit($site_id, $answer_id, $this_action) 
   {
      $this->administrator->check_login();

      $site_id = 'shared';

      $this->load->helper(array('form', 'text'));
      $this->load->model('Answers');
      $this->load->model('Indexes');
      $this->load->library('validation');

      $answer = $this->Answers->get_answer_data($answer_id);

      $rules['Answer'.$answer_id] = 'trim|required';
      $rules['Note'.$answer_id] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Answer'.$answer_id] = 'Answer';
      $fields['Note'.$answer_id] = 'Note';

      $this->validation->set_fields($fields);

      $defaults['Answer'.$answer_id] = $answer['Answer'];
      $defaults['Note'.$answer_id] = $answer['Note'];
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $last_action = $this->session->userdata('last_action') + 1;
         redirect('cp/faqs/edit/shared/'.$faq_id.'/'.$last_action);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $answer_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates an FAQ record
    *
    */
   function _edit($site_id, $answer_id)
   {
      $this->administrator->check_login();

      if ($answer_id == 0)
      {
         show_error('_edit_item requires that an faq ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $values['Answer'] = $values['Answer'.$answer_id];
      $values['Note'] = $values['Note'.$answer_id];
      unset($values['Answer'.$answer_id]);
      unset($values['Note'.$answer_id]);
      
      $values['Answer'] = ascii_to_entities($values['Answer']);
      $values['Note'] = ascii_to_entities($values['Note']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Answers->update_answer($answer_id, $values);
      
      // update the index for this faq
      $faq_id = $this->Answers->get_answer_faq_id($answer_id);
      $this->Indexes->update_search_index($faq_id);
      
      if (strlen($values['Answer']) > 50)
      {
         $this->session->set_userdata('faq_message', strip_tags('The answer beginning "'.character_limiter($values['Answer'], 50).'"... has been updated.'));
      }
      else
      {
         $this->session->set_userdata('faq_message', strip_tags('The answer  "'.$values['Answer'].'" has been updated.'));
      }

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/faqs/edit/shared/'.$faq_id.'/'.$last_action);
   }


}
?>