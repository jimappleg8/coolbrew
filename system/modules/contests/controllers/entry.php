<?php

class Entry extends Controller {

   var $contest_name;
   var $language;
   var $site_id;
   
   var $base_url;
   var $base_uri;

   var $data = array();
   

   function Entry()
   {
      parent::Controller();   
      $this->load->helper('url');
      $this->_initialize();
   }

   // --------------------------------------------------------------------

   /**
    * Initialize the template variables
    *
    */
   function _initialize()
   {
      // (string) The contest name
      $this->contest_name = $this->tag->param(1);

      // (string) The language code for the contest
      $this->language = $this->tag->param(2, 'en_US');

      // (string) The site ID
      $this->site_id = $this->tag->param(3, SITE_ID);
      
      // Set the base URL
      $this->base_url = $this->config->site_url().'/'.$this->uri->segment(1).$this->uri->slash_segment(2, 'both');
      $this->base_uri = $this->uri->segment(1).$this->uri->slash_segment(2, 'leading');

      $this->load->model('Contests');

      $this->contest = $this->Contests->get_contest_by_name($this->site_id, $this->contest_name, $this->language);
      
      $this->data['base_url'] = $this->base_url;
      $this->data['base_uri'] = $this->base_uri;
      
      $this->data['error_message'] = '';
      
      $this->data['short_rules'] = $this->contest['ShortRules'];

      $this->data['meta_title'] = $this->contest['MetaTitle'];
      $this->data['meta_description'] = $this->contest['MetaDescription'];
      $this->data['meta_keywords'] = $this->contest['MetaKeywords'];
      $this->data['meta_abstract'] = $this->contest['MetaAbstract'];
      $this->data['meta_robots'] = $this->contest['MetaRobots'];

      $this->data['landing_link'] = site_url('entry/index');
      $this->data['entry_link'] = site_url('entry/enter');
      $this->data['rules_link'] = site_url('entry/rules');
      
      $this->_get_list_items();
      
   }

   // --------------------------------------------------------------------

   /**
    * Displays the landing page
    *
    */
   function index()
   {
      $this->load->library('parser');
      
      // check if contest is still active or if we are viewing on anything 
      // but live site
      if ((time() < strtotime($this->contest['StartDate']) || time() > strtotime($this->contest['EndDate'])) && SERVER_LEVEL == 'live')
      {
         // parse entry closed template in database
         $this->data['entry_content'] = $this->parser->parse($this->contest['EntryClosedTemplate'], $this->data, TRUE, TRUE);

         // parse entry wrapper template in database
         $this->data['page_content'] = $this->parser->parse($this->contest['EntryWrapperTemplate'], $this->data, TRUE, TRUE);

         // parse wrapper template in database
         return $this->parser->parse($this->contest['WrapperTemplate'], $this->data, TRUE, TRUE);
      }
      
      if ($this->contest['EntryIsLandingPage'] == 1)
         redirect('entry/enter');

      // parse landing page template in database
      $this->data['page_content'] = $this->parser->parse($this->contest['LandingPageTemplate'], $this->data, TRUE, TRUE);

      // parse wrapper template in database
      return $this->parser->parse($this->contest['WrapperTemplate'], $this->data, TRUE, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Displays the entry page
    *
    */
   function enter()
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->library('parser');
      $this->load->model('Entries');
      
      // check if contest is still active
      if ((time() < strtotime($this->contest['StartDate']) || time() > strtotime($this->contest['EndDate'])) && SERVER_LEVEL == 'live')
      {
         // parse entry closed template in database
         $this->data['entry_content'] = $this->parser->parse($this->contest['EntryClosedTemplate'], $this->data, TRUE, TRUE);

         // parse entry wrapper template in database
         $this->data['page_content'] = $this->parser->parse($this->contest['EntryWrapperTemplate'], $this->data, TRUE, TRUE);

         // parse wrapper template in database
         return $this->parser->parse($this->contest['WrapperTemplate'], $this->data, TRUE, TRUE);
      }
      
      // get the table data
      if (file_exists(DOCPATH.'/config/contests.php'))
      {
         include DOCPATH.'/config/contests.php';
      }
      if (isset($contests[$this->contest_name][$this->language]))
      {
         $field_spec = $contests[$this->contest_name][$this->language]['fields'];
         $this->table_name = $contests[$this->contest_name][$this->language]['table_name'];
      }
      else
      {
         include APPPATH().'/config/contests.php';
         $field_spec = $contests['default'][$this->language]['fields'];
         $this->table_name = $contests['default'][$this->language]['table_name'];
      }
      
      $quiz_spec = $this->_get_quiz_spec();
      if ($quiz_spec != FALSE)
      {
         $field_spec = array_merge($field_spec, $quiz_spec);
      }

      // clean up array to create unspecified fields
      for ($i=0; $i<count($field_spec); $i++)
      {
         $field_spec[$i]['primary_key'] = $this->ifsetor($field_spec[$i]['primary_key'], 0);
         $field_spec[$i]['default'] = $this->ifsetor($field_spec[$i]['default'], '');
         $field_spec[$i]['size'] = $this->ifsetor($field_spec[$i]['size'], 10);
         $field_spec[$i]['limit'] = $this->ifsetor($field_spec[$i]['limit'], 10);
         $field_spec[$i]['type'] = $this->ifsetor($field_spec[$i]['type'], 'string');
         $field_spec[$i]['question_type'] = $this->ifsetor($field_spec[$i]['question_type'], 'none');
      }
      
      foreach ($field_spec AS $spec)
      {
         if ($spec['primary_key'] == 1) continue;
         if ($spec['type'] == 'submit') continue;
         $rules[$spec['name']] = $spec['rules'];
      }
      $this->validation->set_rules($rules);

      foreach ($field_spec AS $spec)
      {
         if ($spec['primary_key'] == 1) continue;
         if ($spec['type'] == 'submit') continue;
         $fields[$spec['name']] = $spec['label'];
      }
      $this->validation->set_fields($fields);

      foreach ($field_spec AS $spec)
      {
         if ($spec['primary_key'] == 1) continue;
         if ($spec['type'] == 'submit') continue;
         $defaults[$spec['name']] = $spec['default'];
      }
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<br /><span style="color:red;">', '</span>');
      
      $this->field_spec = $field_spec;
      $this->quiz_spec = $quiz_spec;
            
      if ($this->validation->run() == FALSE)
      {         
         // first we need to get the generated form
         $this->data['fields'] = $field_spec;
         $this->data['action'] = 'entry/enter';

         $this->load->vars($this->data);
         $this->data['entry_form'] = $this->load->view('entry_form', NULL, TRUE);

         // parse entry form template in database
         $this->data['entry_content'] = $this->parser->parse($this->contest['EntryFormTemplate'], $this->data, TRUE, TRUE);

         // parse entry wrapper template in database
         $this->data['page_content'] = $this->parser->parse($this->contest['EntryWrapperTemplate'], $this->data, TRUE, TRUE);

         // parse wrapper template in database
         return $this->parser->parse($this->contest['WrapperTemplate'], $this->data, TRUE, TRUE);
      }
      else
      {
         return $this->_enter();
      }      
   }

   // --------------------------------------------------------------------

   /**
    * Processes the entry page
    *
    */
   function _enter()
   {
      if ($this->quiz_spec != FALSE)
      {
         $this->_process_quiz();
      }
      
      $db_fields = $this->db->list_fields($this->table_name);

      foreach ($this->field_spec AS $spec)
      {
         if ($spec['primary_key'] == 1) continue;
         if ($spec['type'] == 'submit') continue;
         // unless a field is in the database, don't include it.
         if (in_array($spec['name'], $db_fields))
         {
            if ($spec['input_type'] == 'date')
            {
               $_date = $this->input->post($spec['name']);
               $values[$spec['name']] = $_date['Year'] . '-' . $_date['Month'] . '-' . $_date['Day'];
            }
            else
            {
               $values[$spec['name']] = $this->input->post($spec['name']);
            }
         }
      }
      
      $values['Lang'] = $this->language;
      $values['Submitted'] = date('Y-m-d H:i:s');
      
      // insert hook here for customizing the db and email content
      if (file_exists(DOCPATH.'hooks/contests_db_email.php'))
      {
         require_once DOCPATH.'hooks/contests_db_email.php';
      }
      
      if ($this->_is_entry_allowed($values))
      {
         $entry_id = $this->Entries->insert_entry($this->table_name, $values);

         if ($entry_id === FALSE)
         {
            $this->data['entry_content'] = '';
            $this->data['error_message'] = '<h2>There was an error.</h2><p>Your entry was not saved. The database may be unavailable at this time. Please try again later.</p>';
            
            // NOTE: send an email to the webmaster?
         }
         else
         {
            // insert hook here for customizing the email content only
            if (file_exists(DOCPATH.'hooks/contests_email.php'))
            {
               require_once DOCPATH.'hooks/contests_email.php';
            }
      
            $this->data['first_name'] = $values['FirstName'];
            $this->data['last_name'] = $values['LastName'];
            $this->data['email'] = $values['Email'];
            
            // send e-mail
            $sendmail = ini_get('sendmail_path');
            if (empty($sendmail))
            {
               $sendmail = "/usr/sbin/sendmail -t ";
            }
   
            $mail_content = $this->parser->parse($this->contest['EntryEmailTemplate'], $this->data, TRUE, TRUE);
            $fd = popen($sendmail,"w");
            fputs($fd, stripslashes($mail_content)."\n");
            pclose($fd);
      
            // parse entry success template in database
            $this->data['entry_content'] = $this->parser->parse($this->contest['EntrySuccessTemplate'], $this->data, TRUE, TRUE);            
         }
      }
      else
      {
         // parse entry rejected template in database
         $this->data['entry_content'] = $this->parser->parse($this->contest['EntryRejectedTemplate'], $this->data, TRUE, TRUE);
      }
      
      // parse the entry wrapper template in database
      $this->data['page_content'] = $this->parser->parse($this->contest['EntryWrapperTemplate'], $this->data, TRUE, TRUE);
      
      // parse wrapper template in database
      return $this->parser->parse($this->contest['WrapperTemplate'], $this->data, TRUE, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Displays the rules page
    *
    */
   function rules()
   {
      $this->load->library('parser');
      
      // parse rules page template in database
      $this->data['page_content'] = $this->parser->parse($this->contest['OfficialRules'], $this->data, TRUE, TRUE);

      // parse wrapper template in database
      return $this->parser->parse($this->contest['WrapperTemplate'], $this->data, TRUE, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * check to see if entry is allowed
    *
    * Not all options are implemented yet...
    *
    */
   function _is_entry_allowed($values)
   {
      if (SERVER_LEVEL != 'live')
      {
         return TRUE;
      }
      
      $this->load->model('Entries');
      
      switch ($this->contest['EntryFrequency'])
      {
         case 'unlimited':
            return TRUE;
            break;
         case 'once':
            // Assumes that the check is on the Email field.
            // We may want to make this definable within the config file
            return ($this->Entries->check_entry_string($this->table_name, 'Email', $values['Email']) == FALSE) ? TRUE : FALSE;
            break;
         default:
            return TRUE;
      }
   }

   // --------------------------------------------------------------------

   /**
    * check to see if the date has been filled in
    *
    */
   function check_date($str)
   {
      $errors = FALSE;
      $error_messages = array();
      
      if ($str['Month'] == '')
      {
         $error_messages[] = "Month";
         $errors = TRUE;
      }

      if ($str['Day'] == '')
      {
         $error_messages[] = "Day";
         $errors = TRUE;
      }

      if ($str['Year'] == '')
      {
         $error_messages[] = "Year";
         $errors = TRUE;
      }
      
      if ($errors == TRUE)
      {
         if (count($error_messages) == 1)
         {
            $error_str = $error_messages[0].' field';
         }
         elseif (count($error_messages) == 2)
         {
            $error_str = $error_messages[0].' and '.$error_messages[1].' fields';
         }
         else
         {
            $error_str = $error_messages[0].', '.$error_messages[1].', and '.$error_messages[2].' fields';
         }
         $this->validation->set_message('check_date', 'The '.$error_str.' are required.');
         return FALSE;
      }
      
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Returns a quiz spec array to be added to the entry form. If the 
    * source data contains multiple groups of questions, it chooses one
    * randomly.
    * 
    */
   function _get_quiz_spec()
   {
      if ($this->contest['QuizIsEnabled'] == 0)
         return FALSE;
      
      // get the quiz_spec if it is needed.
      if (file_exists(DOCPATH.'/config/quiz.php'))
      {
         include DOCPATH.'/config/quiz.php';
      }

      if (isset($quizes[$this->contest_name][$this->language]))
      {
         $quiz_data = $quizes[$this->contest_name][$this->language];
      }
      else
      {
         return FALSE;
      }
      
      // check if quiz_spec has been submitted by a form
      if ($this->input->post('quiz_group') !== FALSE)
      {
         $quiz_group = $this->input->post('quiz_group');
      }
      else
      {
         $max = count($quiz_data['groups']) - 1;
      
         if ($max > 0)
         {
            $quiz_group = mt_rand(0, $max);
         }
         else
         {
            $quiz_group = 0;
         }
      }
      
      $quiz_spec = $quiz_data['groups'][$quiz_group];

      $this->quiz_correct_text = $quiz_data['correct_text'];
      $this->quiz_incorrect_text = $quiz_data['incorrect_text'];
      $this->quiz_incorrect_phrase = $quiz_data['incorrect_phrase'];

      $this->data['quiz_head'] = $quiz_data['quiz_head'];
      $this->data['quiz_group'] = $quiz_group;

      return $quiz_spec;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the quiz results and places the results into the template
    * data for display.
    * 
    */
   function _process_quiz()
   {
      if ($this->contest['QuizIsEnabled'] == 0)
         return FALSE;
      
      $quiz_spec = $this->quiz_spec;
      
      for ($i=0; $i<count($quiz_spec); $i++)
      {
         $their_answer = $this->input->post($quiz_spec[$i]['name']);
         
         $result[$i]['number'] = $i + 1;
         $result[$i]['question'] = $quiz_spec[$i]['question'];
         
         if ($quiz_spec[$i]['question_type'] == 'multiple')
         {
            $result[$i]['their_answer_letter'] = $their_answer.'.';
            $result[$i]['their_answer_text'] = $quiz_spec[$i]['data'][$their_answer];

            if ($quiz_spec[$i]['answer'] == $their_answer)
            {
               $result[$i]['correct_incorrect'] = $this->quiz_correct_text;
               $result[$i]['incorrect_phrase'] = '';
               $result[$i]['correct_answer_letter'] = '';
               $result[$i]['correct_answer_text'] = '';
            }
            else
            {
               $result[$i]['correct_incorrect'] = $this->quiz_incorrect_text;
               $result[$i]['incorrect_phrase'] = $this->quiz_incorrect_phrase;
               $result[$i]['correct_answer_letter'] = $quiz_spec[$i]['answer'].'.';
               $result[$i]['correct_answer_text'] = $quiz_spec[$i]['data'][$quiz_spec[$i]['answer']];
            }
         }
         else  // true-false is the only other option right now
         {
            $result[$i]['their_answer_letter'] = '';
            $result[$i]['their_answer_text'] = $quiz_spec[$i]['data'][$their_answer];

            if ($quiz_spec[$i]['answer'] == $their_answer)
            {
               $result[$i]['correct_incorrect'] = $this->quiz_correct_text;
               $result[$i]['incorrect_phrase'] = '';
               $result[$i]['correct_answer_letter'] = '';
               $result[$i]['correct_answer_text'] = '';
            }
            else
            {
               $result[$i]['correct_incorrect'] = $this->quiz_incorrect_text;
               $result[$i]['incorrect_phrase'] = $this->quiz_incorrect_phrase;
               $result[$i]['correct_answer_letter'] = '';
               $result[$i]['correct_answer_text'] = $quiz_spec[$i]['data'][$quiz_spec[$i]['answer']];
            }
         }
      }

      $this->data['quiz_result_data'] = $result;
      
      // parse the entry wrapper template in database
      $this->data['quiz_results'] = $this->parser->parse($this->contest['QuizResultsTemplate'], $this->data, TRUE, TRUE);

      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Returns a random selection from the lists for this site
    * 
    */
   function _get_list_items()
   {
      $sql = 'SELECT * FROM lists ' .
             'WHERE SiteID = \''.SITE_ID.'\'';
      
      $query = $this->db->query($sql);
      $lists = $query->result_array();

      foreach ($lists AS $list)
      {
         $sql = 'SELECT * FROM lists_item ' .
                'WHERE ListID = '.$list['ID'].' '.
                'ORDER BY SortKey';
      
         $query = $this->db->query($sql);
         $list_items = $query->result_array();

         $max = count($list_items) - 1;
         $listing = $list_items[mt_rand(0, $max)];

         $this->data[$list['ListCode']] = $listing['Content'];
      }
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * simple way to set variables - may want to move this to a helper
    *
    */
   function ifsetor(&$var, $default)
   {
      return isset($var) ? $var : $default;
   }
}
?>