<?php

class Shared extends Controller {

   function Shared()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'faqs'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Manages which shared FAQs are used on this site
    *
    * Auditing: incomplete
    */
   function manage($site_id, $this_action = '') 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      // This deals with the case when the method is accessed from the submenu
      // This should always be a fresh page load, however, so it should be safe.
      if ($this_action == '')
      {
         $this_action = $this->session->userdata('last_action') + 1;
      }

      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Items');
      $this->load->model('Answers');
      $this->load->model('Shared');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);

      $shared_list = $this->Shared->get_shared_faqs($site_id);
      
      $admin['shared_exists'] = (count($shared_list) == 0) ? FALSE : TRUE;

      // make sure ShortQuestion has a value
      for ($i=0, $total_faqs = count($shared_list); $i<$total_faqs; $i++)
      {
         if (trim($shared_list[$i]['ShortQuestion']) == '')
         {
            $shared_list[$i]['ShortQuestion'] = $shared_list[$i]['Question'];
         }
      }

      foreach ($shared_list AS $shared)
      {
         $rules['faq-'.$shared['ID']] = 'trim';
         $fields['faq-'.$shared['ID']] = 'FAQ #'.$shared['ID'];
         $defaults['faq-'.$shared['ID']] = ($shared['Assigned'] == TRUE) ? 1 : 0;
         foreach ($shared['Answers'] AS $answer)
         {
            $rules['answer-'.$shared['ID'].'-'.$answer['ID']] = 'trim';
            $fields['answer-'.$shared['ID'].'-'.$answer['ID']] = 'Answer #'.$shared['ID'].'-'.$answer['ID'];
            $defaults['answer-'.$shared['ID'].'-'.$answer['ID']] = ($answer['Assigned'] == TRUE) ? 1 : 0;
         }
      }

      $this->validation->set_rules($rules);
      $this->validation->set_fields($fields);
      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('faqs');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
         $data['submenu'] = get_submenu($site_id, 'Manage Shared FAQs');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['admin'] = $admin; // errors and messages
         $data['shared_list'] = $shared_list;

         $this->load->vars($data);
   	
         return $this->load->view('sites/shared/manage', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_manage($site_id, $shared_list);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the manage shared FAQs form
    *
    * Auditing: incomplete
    */
   function _manage($site_id, $shared_list)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      foreach ($shared_list AS $shared)
      {
         $was_assigned = ($shared['Assigned'] == 1) ? TRUE : FALSE;
         $fieldname = 'faq-'.$shared['ID'];
         $is_assigned = ($values[$fieldname] == 1) ? TRUE : FALSE;
         
         if ($was_assigned && ! $is_assigned)
         {
            // The FAQ has been unchecked. In this case it doesn't matter what 
            // answers may be checked, if they were checked before, they are all 
            // automatically unchecked.
            $answer_id = '';
            foreach ($shared['Answers'] AS $answer)
            {
               $a_was_assigned = ($answer['Assigned'] == 1) ? TRUE : FALSE;
               
               if ($a_was_assigned == TRUE)
               {
                  $this->Sites->delete_link($site_id, $shared['ID'], $answer['ID']);
               }
            }
         }
         elseif ( ! $was_assigned && $is_assigned)
         {
            // The FAQ has been newly checked. In this case, we can assume that no
            // answers were previously checked, so we just look for answers that are
            // checked now.
            $answer_selected = FALSE;
            foreach ($shared['Answers'] AS $answer)
            {
               $answername = 'answer-'.$shared['ID'].'-'.$answer['ID'];
               $a_is_assigned = ($values[$answername] == 1) ? TRUE : FALSE;
               
               if ($a_is_assigned == TRUE)
               {
                  $this->Sites->insert_link($site_id, $shared['ID'], $answer['ID']);
                  $answer_selected = TRUE;
               }
            }

            if ($answer_selected == FALSE)
            {
               // no answer was selected, so default to the first answer
               $this->Sites->insert_link($site_id, $shared['ID'], $shared['Answers'][0]['ID']);

               $this->session->set_userdata('faq_error', 'No answer was checked for the FAQ begining "'.character_limiter($shared['ShortQuestion'], 50).'" so the first answer was chosen automatically.');
            }
         }
         elseif ($was_assigned && $is_assigned)
         {
            // The FAQ was check before and is still checked. In this case, we need to
            // check all the answers and but see if any were unchecked or was added.
            $answer_selected = FALSE;
            foreach ($shared['Answers'] AS $answer)
            {
               $a_was_assigned = ($answer['Assigned'] == 1) ? TRUE : FALSE;
               $answername = 'answer-'.$shared['ID'].'-'.$answer['ID'];
               $a_is_assigned = ($values[$answername] == 1) ? TRUE : FALSE;

               if ($a_was_assigned && ! $a_is_assigned)
               {
                  // it has been unchecked
                  $this->Sites->delete_link($site_id, $shared['ID'], $answer['ID']);
               }
               elseif ( ! $a_was_assigned && $a_is_assigned)
               {
                  // it has been newly checked
                  $this->Sites->insert_link($site_id, $shared['ID'], $answer['ID']);
                  $answer_selected = TRUE;
               }
               elseif ($a_was_assigned && $a_is_assigned)
               {
                  // it is still checked
                  $answer_selected = TRUE;
               }
            }

            if ($answer_selected == FALSE)
            {
               
               // no answer was selected, so default to the first answer
               $this->Sites->insert_link($site_id, $shared['ID'], $shared['Answers'][0]['ID']);
               
               $this->session->set_userdata('faq_error', 'No answer was checked for the FAQ begining "'.character_limiter($shared['ShortQuestion'], 50).'" so the first answer was chosen automatically.');
            }
         }
         else
         {
            // The FAQ was not checked and is still not checked. In this case we 
            // ignore all answers. Even if the user checks one, it will not be 
            // added because an FAQ must be selected first.
         }
      }

      $this->session->set_userdata('faq_message', 'The shared FAQs for this site have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('sites/shared/manage/'.$site_id.'/'.$last_action);
   }

}
?>