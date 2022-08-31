<?php

class Tell extends Controller {

   var $tell_name;
   var $language;
   var $site_id;
   
   var $base_url;
   var $base_uri;

   var $tell_data = array();
   var $data = array();
   

   function Tell()
   {
      parent::Controller();   
      $this->load->library('session');
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
      $this->tell_name = $this->tag->param(1);

      // (string) The language code for the contest
      $this->language = $this->tag->param(2, 'en_US');

      // (string) The site ID
      $this->site_id = $this->tag->param(3, SITE_ID);
      
      // Set the base URL
      $this->base_url = $this->config->site_url().'/'.$this->uri->segment(1).$this->uri->slash_segment(2, 'both');
      $this->base_uri = $this->uri->segment(1).$this->uri->slash_segment(2, 'leading');

      $this->load->model('Tell');

      $this->tell_data = $this->Tell->get_tell_by_name($this->site_id, $this->tell_name, $this->language);
      
      $this->data['base_url'] = $this->base_url;
      $this->data['base_uri'] = $this->base_uri;
      
      $this->data['error_message'] = '';
      
      $this->data['num_friends'] = $this->tell_data['NumFriendFields'];
      $this->data['offer_sender_copy'] = $this->tell_data['SendSenderCopy'];
      
      $this->data['privacy_policy'] = $this->tell_data['PrivacyPolicy'];

      $this->data['meta_title'] = $this->tell_data['MetaTitle'];
      $this->data['meta_description'] = $this->tell_data['MetaDescription'];
      $this->data['meta_keywords'] = $this->tell_data['MetaKeywords'];
      $this->data['meta_abstract'] = $this->tell_data['MetaAbstract'];
      $this->data['meta_robots'] = $this->tell_data['MetaRobots'];
      
      $this->collector->prepend_css_file('plugs-tags');
      $this->data['css'] = $this->collector->wrap_css();

      $this->data['privacy_link'] = site_url('tell/privacy');
      
      if ($this->session->userdata('SenderFirstName') != ''
          || $this->session->userdata('SenderLastName') != ''
          || $this->session->userdata('SenderEmail') != '')
      {
         $this->data['landing_link'] = site_url('tell/index'.
         '/'.urlencode($this->session->userdata('SenderFirstName')).
         '/'.urlencode($this->session->userdata('SenderLastName')).
         '/'.urlencode($this->session->userdata('SenderEmail')));
      }
      else
      {
         $this->data['landing_link'] = site_url('tell/index');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Displays the Tell a Friend form
    *
    */
   function index($fname = '', $lname = '', $email = '')
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->library('parser');

      $rules['SenderFirstName'] = 'trim|required';
      $rules['SenderLastName'] = 'trim|required';
      $rules['SenderEmail'] = 'trim|required|valid_email';
      $rules['URL'] = 'trim';
      $rules['Message'] = 'trim';
      for ($i=1; $i<=$this->data['num_friends']; $i++)
      {
         $rules['Friend'.$i.'Email'] = 'trim|callback_check_friend['.$i.']';
         $rules['Friend'.$i.'FirstName'] = 'trim';
         $rules['Friend'.$i.'LastName'] = 'trim';
      }
      if ($this->data['offer_sender_copy'] == 1)
      {
         $rules['SenderCopy'] = 'trim';
      }
      $this->validation->set_rules($rules);

      $fields['SenderFirstName'] = 'Sender\'s First Name';
      $fields['SenderLastName'] = 'Sender\'s Last Name';
      $fields['SenderEmail'] = 'Sender\'s Email';
      $fields['URL'] = 'URL';
      $fields['Message'] = 'Message to friends';
      for ($i=1; $i<=$this->data['num_friends']; $i++)
      {
         $fields['Friend'.$i.'Email'] = 'Friend '.$i.'\'s First Name';
         $fields['Friend'.$i.'FirstName'] = 'Friend '.$i.'\'s First Name';
         $fields['Friend'.$i.'LastName'] = 'Friend '.$i.'\'s First Name';
      }
      if ($this->data['offer_sender_copy'] == 1)
      {
         $fields['SenderCopy'] = 'trim';
      }
      $this->validation->set_fields($fields);

      $defaults['SenderFirstName'] = $this->security->xss_clean($fname);
      $defaults['SenderLastName'] = $this->security->xss_clean($lname);
      $defaults['SenderEmail'] = $this->security->xss_clean($email);
      $defaults['URL'] = $this->tell_data['URL'];
      
      $this->validation->set_defaults($defaults);
      
      // set the session variables so we can access the default info
      $this->session->set_userdata('SenderFirstName', $defaults['SenderFirstName']);
      $this->session->set_userdata('SenderLastName', $defaults['SenderLastName']);
      $this->session->set_userdata('SenderEmail', $defaults['SenderEmail']);

      $this->validation->set_error_delimiters('<span style="color:red;">', '</span>');

      if ($this->validation->run() == FALSE)
      {
         // first we need to get the generated form
         $this->data['action'] = 'tell/index';

         $this->load->vars($this->data);
         $this->data['tell_a_friend_form'] = $this->load->view('tell_form', NULL, TRUE);

         // parse entry form template in database
         $this->data['page_content'] = $this->parser->parse($this->tell_data['FormTemplate'], $this->data, TRUE, TRUE);

         // parse wrapper template in database
         return $this->parser->parse($this->tell_data['WrapperTemplate'], $this->data, TRUE, TRUE);
      }
      else
      {
         return $this->_index();
      }      
   }

   // --------------------------------------------------------------------

   /**
    * Processes the tell a friend form
    *
    */
   function _index()
   {
      $this->load->model('Sender');
      $this->load->model('Friend');
      
      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // set up e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail))
      {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
      $this->data['sender_first_name'] = $values['SenderFirstName'];
      $this->data['sender_last_name'] = $values['SenderLastName'];
      $this->data['sender_email'] = $values['SenderEmail'];
      $this->data['message'] = $values['Message'];
      $this->data['url'] = $values['URL'];
      
      // first, save the sender record
      $sender['TellID'] = $this->tell_data['ID'];
      $sender['FirstName'] = $values['SenderFirstName'];
      $sender['LastName'] = $values['SenderLastName'];
      $sender['Email'] = $values['SenderEmail'];
      $sender['URL'] = $values['URL'];
      $sender['Message'] = $values['Message'];
      $sender['DateSent'] = date('Y-m-d H:i:s');
      
      $sender_id = $this->Sender->insert_sender($sender);
      
      // and send the sender an email if requested
      if (isset($values['SenderCopy']))
      {
         if ($values['SenderCopy'] == 1)
         {
            $this->data['friend_first_name'] = $sender['FirstName'];
            $this->data['friend_last_name'] = $sender['LastName'];
            $this->data['friend_email'] = $sender['Email'];
            $this->data['copy_of'] = 'Copy of ';
//            $this->data['copy_statement']

            $mail_content = $this->parser->parse($this->tell_data['EmailTemplate'], $this->data, TRUE, TRUE);
            $fd = popen($sendmail,"w");
            fputs($fd, stripslashes($mail_content)."\n");
            pclose($fd);         
         }
      }
      
      // now insert each friend record
      for ($i=1; $i<=$this->data['num_friends']; $i++)
      {
         $friend['SenderID'] = $sender_id;
         $friend['FirstName'] = $values['Friend'.$i.'FirstName'];
         $friend['LastName'] = $values['Friend'.$i.'LastName'];
         $friend['Email'] = $values['Friend'.$i.'Email'];

         if ($friend['Email'] != '')
         {
            $friend_id = $this->Friend->insert_friend($friend);
            
            $this->data['friend_first_name'] = $friend['FirstName'];
            $this->data['friend_last_name'] = $friend['LastName'];
            $this->data['friend_email'] = $friend['Email'];
            $this->data['copy_of'] = '';

            $mail_content = $this->parser->parse($this->tell_data['EmailTemplate'], $this->data, TRUE, TRUE);
            $fd = popen($sendmail,"w");
            fputs($fd, stripslashes($mail_content)."\n");
            pclose($fd);         
         }
      }
      
      // parse the entry wrapper template in database
      $this->data['page_content'] = $this->parser->parse($this->tell_data['ResultsTemplate'], $this->data, TRUE, TRUE);
      
      // parse wrapper template in database
      return $this->parser->parse($this->tell_data['WrapperTemplate'], $this->data, TRUE, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Displays the Privacy Policy page
    *
    */
   function privacy()
   {
      $this->load->library('parser');
      
      // parse rules page template in database
      $this->data['page_content'] = $this->parser->parse($this->tell_data['PrivacyPolicy'], $this->data, TRUE, TRUE);

      // parse wrapper template in database
      return $this->parser->parse($this->tell_data['WrapperTemplate'], $this->data, TRUE, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * check to see if all or none of the friend data is entered
    *
    */
   function check_friend($str, $param)
   {
      $friend['Email'] = $_POST['Friend'.$param.'Email'];
      $friend['FirstName'] = $_POST['Friend'.$param.'FirstName'];
      $friend['LastName'] = $_POST['Friend'.$param.'LastName'];

      $error_messages = array();
      $errors = FALSE;
         
      if ($friend['FirstName'] == '' && $friend['LastName'] == '' && $friend['Email'] == '')
      {
         if ($param != '1')
         {
            // all fields are blank, so no foul
            return TRUE;
         }
         else
         {
            // they must enter info for at least one friend
            $this->validation->set_message('check_friend', 'You must enter info for at least one friend.');
            return FALSE;
         }
      }
      
      if ($friend['Email'] == '')
      {
         $error_messages[] = "Email";
         $errors = TRUE;
      }
      else
      {
         if ( ! $this->validation->valid_email($friend['Email']))
         {
            $this->validation->set_message('check_friend', 'The Email field must contain a valid email address.');
            return FALSE;
         }
      }
      
      if ($friend['FirstName'] == '')
      {
         $error_messages[] = "First Name";
         $errors = TRUE;
      }

      if ($friend['LastName'] == '')
      {
         $error_messages[] = "Last Name";
         $errors = TRUE;
      }

      if ($errors == TRUE)
      {
         if (count($error_messages) == 1)
         {
            $error_str = $error_messages[0].' field is';
         }
         elseif (count($error_messages) == 2)
         {
            $error_str = $error_messages[0].' and '.$error_messages[1].' fields are';
         }
         else
         {
            $error_str = $error_messages[0].', '.$error_messages[1].', and '.$error_messages[2].' fields are';
         }
         $this->validation->set_message('check_friend', 'Friend info is incomplete: the '.$error_str.' required.');
         return FALSE;
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