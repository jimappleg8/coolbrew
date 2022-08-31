<?php

/*

Here is a template for the settings array:

$settings = array(
   'mail-tpl'    => '',
   'reply-tpl'   => '',
   'safe-tpl'    => '',
   'action'      => '',
   'left-column' => '120',
   'redirect'    => 'consumer-info.php',
   'thank-you'   => '',
   'form-tpl'    => 'contact_us',
   'site-id'     => 'cs',
   'marketing'   => TRUE,
   'privacy'     => '/privacy.php',
   'release'     => TRUE,
   'labels'      => array(
      'FName'         => 'First Name',
      'LName'         => 'Last Name',
      'Address1'      => 'Address Line 1',
      'Address2'      => 'Address Line 2',
      'City'          => 'City',
      'State'         => 'State',
      'ChooseState'   => '-- Please select a state --',
      'Country'       => 'Country',
      'ChooseCountry' => '-- Please select a country --',
      'Zip'           => 'Zip/Postal Code',
      'Phone'         => 'Daytime Phone',
      'Email'         => 'Email',
      'Email2'        => 'Confirm your Email',
      'ProductUPC'    => 'Product UPC',
      'ProductUPCDesc' => 'Enter the 10 digits beneath the bar code.',
      'BestByDateLotCode' => 'Best by Date and Lot Code',
      'BestByDateLotCodeDesc' => 'Enter the use-by or best-by date along with any other codes printed or stamped on the package.  If you are unable to locate any please just put "none."',
      'Comment'       => 'Message',
      'Marketing'     => '##default##',
      'Release'       => 'From time to time, we select consumer comments to post on our web site. Please check this box if you would like your comments to be considered.',
      'SubmitText'    => 'Send message',
   ),
);

*/

class Mailform_Tags extends Controller {

   var $site_id;       // the site ID
   var $brand_name;    // the brand's name based on the site ID
   var $site_base_url; // the brand's URL based on the site ID

   //-------------------------------------------------------------------------

   function Mailform_Tags()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('contact_form');
      $this->load->model('Sites');
      $this->brand_name = $this->Sites->get_brand_name(SITE_ID);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Creates a short Contact Us form that is intended to initiate the filling
    * out of the larger contact_us form. This and the related 'contact_us' tag
    * are intended to eventually replace the older 'contactus_form' tag.
    * 
    * This form is intended to prevent Cross-Site Request Forgery (CSRF) 
    * attacks, but the technique used was not working. It needs to be addressed
    * again at a later time.
    *
    */
   function contact_us_start()
   {
      $this->load->helper(array('form','url','string'));
      $this->load->library('validation');
      
      // The first parameter should be the settings array
      $settings = $this->tag->param(1, '');

      if (is_array($settings))
      {
         // (string) the file that the form should point to
         $action = (isset($settings['action'])) ? $settings['action'] : $_SERVER['PHP_SELF'];
         
         // (string) mail form template
         $formtpl = (isset($settings['form-tpl'])) ? $settings['form-tpl'] : 'contact_us';
         
         // (string) array of form labels
         $labels = (isset($settings['labels'])) ? $settings['labels'] : '';      
      }
      elseif ($settings != '')
      {
         // throw an error
         // Use of tag parameters has been deprecated.
      }

      // Token used to protect against Cross-Site Request Forgery (CSRF) attacks
      $form_token = random_string('unique');
      $this->session->set_userdata('form_token', $form_token);

      $rules = $this->contact_form->get_start_rules();
      $this->validation->set_rules($rules);

      $fields = $this->contact_form->get_start_fields();
      $this->validation->set_fields($fields);
      
      $defaults = $this->contact_form->get_start_defaults();
      $defaults['form_token'] = $form_token;
      $defaults['form_part'] = 1;
      $this->validation->set_defaults($defaults);

      $this->collector->append_css_file('mailform_tags');

      // This form is never validated in this method/tag. Instead, the contact_us
      // method validates just the "start" fields and displays any issues on the
      // larger form.

      $data['siteid'] = SITE_ID;
      $data['hcg_site'] = $this->brand_name;
      $data['form_part'] = 1;
      $data['action'] = $action;
      $data['labels'] = $this->contact_form->set_labels($labels);

      return $this->load->view($formtpl, $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Creates the full Contact Us form. This form can either be accessed
    * directly or receive data from the contact_us_start form.
    *
    * It allows for up to 3 emails to go out:
    *   $mailtpl1 - usually internal (e.g. contact_us_mail.php)
    *   $mailtpl2 - usually to user  (e.g. contact_us_reply.php)
    *   $mailtpl2 - usually internal (e.g. contact_us_safe.php)
    *
    * If no emails are specified in the tag, data is just saved to
    * the database.
    *
    */
   function contact_us($form_tpl = '')
   {
      $this->load->helper(array('form','url','pulldown'));
      $this->load->model('Contactus');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      // The first parameter should be the settings array
      $settings = $this->tag->param(1, '');

      if (is_array($settings))
      {
         // (string) mail template 1 (usually internal)
         $mailtpl1 = (isset($settings['mail-tpl'])) ? $settings['mail-tpl'] : '';
         
         // (string) mail template 2 (usually to user)
         $mailtpl2 = (isset($settings['reply-tpl'])) ? $settings['reply-tpl'] : '';
         
         // (string) mail template 3 (usually internal)
         $mailtpl3 = (isset($settings['safe-tpl'])) ? $settings['safe-tpl'] : '';
         
         // (string) the file that the form should point to
         $action = (isset($settings['action'])) ? $settings['action'] : $_SERVER['PHP_SELF'];
         
         // (number) Backward compatibility: the left column width
         $left_column = (isset($settings['left-column'])) ? $settings['left-column'] : '120';
         
         // (string) the file that we should go to if there is an error
         // This is currently not used...
         $redirect = (isset($settings['redirect'])) ? $settings['redirect'] : 'consumer-info.php';
         
         // (string) the file that we should go to to thank the user
         // if no file is specified, then we use the default from the template
         $thankyou = (isset($settings['thank-you'])) ? $settings['thank-you'] : '';
         
         // (string) the template that should be used for the form
         // To use the older template, specify 'contactus_form'
         $formtpl = (isset($settings['form-tpl'])) ? $settings['form-tpl'] : 'contact_us';
         // if they use the old tag, the formtpl will be overridden
         $formtpl = ($form_tpl != '') ? $form_tpl : $formtpl;
         
         // (string) the site id
         $site_id = (isset($settings['site-id'])) ? $settings['site-id'] : SITE_ID;
         
         // (string) display the marketing checkbox
         $marketing = (isset($settings['marketing'])) ? $settings['marketing'] : TRUE;
         
         // (string) the url for the privacy policy
         $privacy = (isset($settings['privacy'])) ? $settings['privacy'] : '/privacy.php';
         
         // (string) display the release checkbox
         $release = (isset($settings['release'])) ? $settings['release'] : TRUE;
         
         // (string) array of form labels
         $labels = (isset($settings['labels'])) ? $settings['labels'] : '';      

         // (string) override for the Consumer Relations phone number
         $support_phone = (isset($settings['support-phone'])) ? $settings['support-phone'] : '';      

         // (string) override for the Consumer Relations email address
         $support_email = (isset($settings['support-email'])) ? $settings['support-email'] : '';

         // (string) link to Store Locator on website
         $locator_link = (isset($settings['locator-link'])) ? $settings['locator-link'] : '';

         // (string) link to Home Page on website
         $home_link = (isset($settings['home-link'])) ? $settings['home-link'] : '/';
      }
      elseif ($settings != '')
      {
         // throw an error
         // Use of tag parameters has been deprecated.
      }
      
      // update the contact_form object with site-supplied information:
      $this->contact_form->set_support_phone($support_phone);
      $this->contact_form->set_support_email($support_email);
      $this->contact_form->set_locator_link($locator_link);
      
      $this->site_id = $site_id;
      $this->brand_name = $this->Sites->get_brand_name($site_id);
      $site = $this->Sites->get_site_data($site_id);
      $this->site_base_url = $site['Domain'];

      // test if form was submitted from the contact_us_start form
      $form_token = $this->session->userdata('form_token');
      if (($form_token == '') ||
          ($form_token != $this->input->post('form_token')))
      {
         // deactivating the redirect until I can figure out why 
         // it is being inconsistant. May replace it with a different idea.
//         header('Location:'.$redirect);
      }

      // if this is coming from the start form, use the start rules
      if ($this->input->post('form_part') == 1)
      {
         $rules = $this->contact_form->get_start_rules();
      }
      else
      {
         $rules = $this->contact_form->get_rules();
      }
      if (isset($_POST['Subject']) && $this->input->post('Subject') == 'Product Complaint')
      {
         $rules['ProductUPC'] = 'trim|required';
         $rules['BestByDateLotCode'] = 'trim|required';
      }
      $this->validation->set_rules($rules);

      $fields = $this->contact_form->get_fields();
      $this->validation->set_fields($fields);
      
      $defaults = $this->contact_form->get_defaults();
      $defaults['form_token'] = $form_token;
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error clearfix"><div class="error-icon"></div><p>', '</p></div>');
      
      $this->collector->append_css_file('mailform_tags');
      
      $validation_passed = $this->validation->run();
      if ($this->input->post('form_part') == 1)
      {
         // display the larger form regardless of results
         $validation_passed = FALSE;
      }

      if ($validation_passed == FALSE)
      {
         $data['siteid'] = $this->site_id;
         $data['hcg_site'] = $this->brand_name;
         $data['form_part'] = 2;
         $data['labels'] = $this->contact_form->set_labels($labels);
         $data['subjects'] = get_subject_list();
         $data['subjects'][''] = $data['labels']['ChooseSubject'];
         $data['states'] = get_state_list();
         $data['states'][''] = $data['labels']['ChooseState'];
         $data['countries'] = get_country_list();
         $data['countries'][''] = $data['labels']['ChooseCountry'];
         $data['action'] = $action;
         $data['left_column'] = $left_column;
         $data['privacy'] = $privacy;
         $data['marketing'] = $marketing;
         $data['release'] = $release;
      }
      else
      {         
         $this->_contact_us_process($mailtpl1, $mailtpl2, $mailtpl3);
         $data['form_part'] = 3;
         $data['home_link'] = $home_link;
         if ($thankyou != '')
         {
            header('Location:'.$thankyou);
         }
      }

      $html = $this->load->view($formtpl, $data, TRUE);
      
      $results[0] = $data['form_part'];
      $results[1] = $html;

      return $results;
   }
   
   //-------------------------------------------------------------------------  
   
   /**
    * Process the form data from 'contact' form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _contact_us_process($mailtpl1, $mailtpl2, $mailtpl3)
   {
      $this->load->library('user_agent');

      $fields = $this->validation->_fields;
      unset($fields['Email2']);
      unset($fields['form_token']);

      $subject = $this->input->post('Subject');
      unset($fields['Subject']);  // this should only be temporary!

      $_POST['user_ip'] = $this->input->server('REMOTE_ADDR');
      $_POST['user_agent'] = $this->agent->agent_string();
      $_POST['referrer'] = $this->agent->referrer();

      $protocol = ( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
      
      // now we check for spam
      $params['api_key'] = 'f76bf9349cf0';
      $params['blog_url'] = $protocol.$_SERVER['HTTP_HOST'];
      $this->load->library('akismet', $params);
      
      $comment = array();
      $comment['blog'] = $protocol.$_SERVER['HTTP_HOST']; // required
      $comment['user_ip'] = $this->input->post('user_ip');
      $comment['user_agent'] = $this->input->post('user_agent');
      $comment['referrer'] = $this->input->post('referrer');
      $comment['permalink'] = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
      $comment['comment_type'] = 'contact us';
      $comment['comment_author'] = $this->input->post('FName').' '.$this->input->post('LName');
      $comment['comment_author_email'] = $this->input->post('Email');
      $comment['comment_author_url'] = '';
      $comment['comment_content'] = $this->input->post('Comment');
      
      $_POST['spam'] = $this->akismet->is_spam($comment);
      
      // we save the form regardless of whether it is spam or not
      $values = $this->Contactus->save_submision($fields, $this->site_id);
      
      // but we only send emails if it is not spam
//      if ( ! $values['spam'])
//      {
         // This is to handle occasional special characters in brand names
         mb_internal_encoding('UTF-8');
      
         $values['Subject'] = $subject;
         $values['brand_name'] = $this->brand_name;
         $values['brand_name_encoded'] = mb_encode_mimeheader($this->brand_name, 'UTF-8', 'Q');
         $values['DateSent'] = date("Y-m-d");
         $values['site_url'] = 'http://'.$_SERVER['HTTP_HOST'];
         $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
         $values['support_email'] = $this->contact_form->get_support_email();
         $values['support_phone'] = $this->contact_form->get_support_phone();
         $values['support_hours'] = $this->contact_form->get_support_hours();
         $values['locator_link'] = $this->contact_form->get_locator_link();
   
         // send e-mail(s)
         $this->_send_email($mailtpl1, $values);  
         $this->_send_email($mailtpl2, $values);
         $this->_send_email($mailtpl3, $values);
//      }

   }
   
  //-------------------------------------------------------------------------
   
   /**
    * DEPRECATED - please use the contact_us tag above for new installations.
    *
    * This tag is now an alias for the contact_us tag.
    *
    */
   function contactus_form()
   {
      $result = $this->contact_us('contactus_form');
      // the older tag expects slightly different results
      $result[0] = ($result[0] == 3) ? TRUE : FALSE;
      return $result;
   }

  //-------------------------------------------------------------------------
   
   /**
    * Returns an array with support information:
    *  - support address
    *  - support phone number
    *  - support hours
    *
    */
   function support_info()
   {
      $result = array();
      $result['address'] = $this->contact_form->get_support_address();
      $result['phone'] = $this->contact_form->get_support_phone();
      $result['hours'] = $this->contact_form->get_support_hours();
      
      return $result;
   }

   //-------------------------------------------------------------------------
   
   //-------------------------------------------------------------------------
   // vMaliciousAttack
   //   This is to test for a specific attack we've been receiving, but it
   //   be expanded to check for other attacks as needed.
   //
   //-------------------------------------------------------------------------
   function vMaliciousAttack($data)
   {
      // 9/2005 - someone keeps sending lots of emails with fake email
      // addresses entered in all fields
      if ($data['FName'] == $data['Email']) {
         return array('FName' => "This field content is not permitted.");
      }
   
      // 9/2005 - someone also seems to be trying to send a multi-part message
      // as part of the comments.
      if (strpos("x".$data['Comment'], "Content-Type: multipart/mixed;")) {
         return array('Comment' => "This field content is not permitted.");
      }
      return true;
   }
   
   //-------------------------------------------------------------------------

   /**
    * Creates a standard Webmaster form
    *
    * Allow for up to 2 emails to go out:
    *   $mailtpl1 - usually 1 internal (contactus_mail.php)
    *   $mailtpl2 - usually 1 to user (contactus_reply.php)
    *
    * If no emails are specified in the tag, data is just saved to
    * the database.
    *
    */   
   function webmaster_form()
   {
      // (string) mail template 1 (usually internal)
      $mailtpl1 = $this->tag->param(1, '');
      
      // (string) mail template 2 (usually to user)
      $mailtpl2 = $this->tag->param(2, '');
            
      $form_html = "";
      $display_response = false;

      $this->load->helper(array('form','url'));

      $this->load->library('validation');

      $rules['FName'] = 'trim|required|max_length[25]|alpha_dash';
      $rules['LName'] = 'trim|required|max_length[25]|alpha_dash';
      $rules['Email'] = 'trim|required|valid_email|matches[Email2]';
      $rules['Email2'] = 'trim|required';
      $rules['Comment'] = 'trim|required';

      // NOTE: vMaliciousAttack should be added when I can determine how 
      // to do it. I'd like to see if there's a way to test multiple fields.

      $this->validation->set_rules($rules);

      $fields['FName'] = 'First Name';
      $fields['LName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Email2'] = 'Email Confirmation';
      $fields['Comment'] = 'Message';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_webmaster_form($mailtpl1, $mailtpl2);
         $display_response = true;
      }
      else
      {
         $data['siteid'] = SITE_ID;
         $data['hcg_site'] = $this->brand_name;

         $form_html = $this->load->view('webmaster_form', $data, TRUE);
      }

      $results[0] = $display_response;
      $results[1] = $form_html;

      return $results;
   }

   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from 'webmaster' form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _webmaster_form($mailtpl1, $mailtpl2)
   {
      $fields = $this->validation->_fields;
      unset($fields['Email2']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['fullname'] = $values['FName']." ".$values['LName'];
      unset($values['FName']);
      unset($values['LName']);
      $values['form_id'] = SITE_ID.'_webmaster';
      $values['submit_ts'] = time();
      
      $this->load->database('write');
      
      $this->db->insert('wf_webmaster', $values);
      
      $values['FName'] = $this->input->post('FName');
      $values['LName'] = $this->input->post('LName');
      $values['brand_name'] = $this->brand_name;
      $values['DateSent'] = date("Y-m-d");
      $values['URL'] = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
   
      // send e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail)) {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      // send the email internally
      if ($mailtpl1 != "") {
         $mail_content = $this->load->view($mailtpl1, $values, TRUE);
         $fd = popen($sendmail,"w");
         fputs($fd, stripslashes($mail_content)."\n");
         pclose($fd);
      }
   
      // send reply to user
      if ($mailtpl2 != "") {
         $mail_content2 = $this->load->view($mailtpl2, $values, TRUE);
         $fd = popen($sendmail,"w");
         fputs($fd, stripslashes($mail_content2)."\n");
         pclose($fd);
      }
   }
   

   //-------------------------------------------------------------------------

   /**
    * Creates a standard Notify Me form
    */   
   function notify_me_form()
   {
      $form_html = "";
      $display_response = false;

      $this->load->helper(array('form','url'));

      $this->load->library('validation');

      $rules['FName'] = 'trim|required';
      $rules['LName'] = 'trim|required';
      $rules['Email'] = 'trim|required|valid_email|matches[Email2]';
      $rules['Email2'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['FName'] = 'First Name';
      $fields['LName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['Email2'] = 'Email Confirmation';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_notify_me_form();
         $display_response = true;
      }
      else
      {
         $data['site_id'] = SITE_ID;
         $data['brand_name'] = $this->brand_name;

         $form_html = $this->load->view('notify_me_form', $data, TRUE);
      }

      $results[0] = $display_response;
      $results[1] = $form_html;

      return $results;
   }

   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from 'notify_me' form;
    * includes: save to db
    *
    */
   function _notify_me_form()
   {
      $fields = $this->validation->_fields;
      unset($fields['Email2']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['SiteID'] = SITE_ID;
      $values['SubmitDate'] = date('Y-m-d H:i:s');
      
      $this->load->database('write');
      
      $this->db->insert('wf_notify', $values);
      
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Send an email using a template
    *
    * @access   public
    * @params   string   the template file
    * @params   array   the values to sent to the template
    * @returns  boolean  
    */
   function _send_email($mailtpl, $values = array())
   {
      if ($mailtpl == '')
      {
         return FALSE;
      }

      // make sure sendmail is available
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail))
      {
         $sendmail = "/usr/sbin/sendmail -t ";
      }

      $mail_content = $this->load->view($mailtpl, $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);

      return TRUE;
   }

   
}

/* End of file tags.php */
/* Location: ./system/modules/mailform/controllers/tags.php */