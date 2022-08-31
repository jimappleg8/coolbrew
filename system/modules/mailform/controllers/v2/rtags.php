<?php

class Rtags extends Controller {

   var $site_id;       // the site ID
   var $brand_name;    // the brand's name based on the site ID
   var $site_base_url; // the brand's URL based on the site ID
   var $user_ip;
   var $user_agent;
   var $referrer;
      
   //-------------------------------------------------------------------------
   
   function Rtags()
   {
      parent::Controller();
      $this->load->library('contact_form');
      $this->load->library('session');
   }
   
    //-------------------------------------------------------------------------
   
   /**
    * Creates a standard Contact Us form. This is the next generation after
    * the contactus_form tag. This form is set up to work in two parts and has
    * security features to prevent Cross-Site Request Forgery (CSRF) attacks.
    *
    */
   function contactUsStart()
   {
      $this->load->helper(array('form','url','string'));
      $this->load->library('Rtag');
      $this->load->library('validation');

      // (string) the file that the form should point to
      $action = $this->rtag->param('action', 'contact-us');

      // (string) mail form template
      $formtpl = $this->rtag->param('form-tpl', 'contact-us');
      
      // (string) serialized array of form labels
      $labels = $this->rtag->param('labels', '');
      
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

      $data['siteid'] = SITE_ID;
      $data['hcg_site'] = $this->brand_name;
      $data['form_part'] = 1;
      $data['action'] = $action;
      $data['labels'] = $this->contact_form->set_labels($labels);

      return $this->load->view('rtags/v1/'.$formtpl, $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Creates the full Contact Us form. This form cannot be accessed directly
    * and needs to receive data from the contact_us_start form. If it is not
    * accessed correctly, it will redirect to the file specified in the 
    * redirect tag parameter.
    *
    * It allows for up to 3 emails to go out:
    *   $mailtpl1 - usually internal (e.g. contactus-mail.php)
    *   $mailtpl2 - usually to user  (e.g. contactus-reply.php)
    *   $mailtpl2 - usually internal (e.g. contactus-safe.php)
    *
    * If no emails are specified in the tag, data is just saved to
    * the database.
    *
    */
   function contactUs()
   {
      $this->load->helper(array('form','url','pulldown'));
      $this->load->model('Contactus');
      $this->load->model('Sites');
      $this->load->library('validation');
      $this->load->library('Rtag');

      $tpl_path = BASEPATH.'modules/mailform/views/rtags/v2/';

      // (string) mail template 1 (usually internal)
      $mailtpl1 = $this->rtag->param('mail-tpl', 'contactus-mail.tpl');
      $mailtpl1 = ($mailtpl1 == '') ? 'contactus-mail.tpl' : $mailtpl1;
      $mailtpl1 = ($mailtpl1 == 'contactus-mail.tpl') ? $tpl_path.$mailtpl1 : $mailtpl1;
      $mailtpl1 = ($mailtpl1 == 'contactus-mail-premier.tpl') ? $tpl_path.$mailtpl1 : $mailtpl1;
      
      // (string) mail template 2 (usually to user)
      $mailtpl2 = $this->rtag->param('reply-tpl', 'contactus-reply.tpl');
      $mailtpl2 = ($mailtpl2 == '') ? 'contactus-reply.tpl' : $mailtpl2;
      $mailtpl2 = ($mailtpl2 == 'contactus-reply.tpl') ? $tpl_path.$mailtpl2 : $mailtpl2;
      $mailtpl2 = ($mailtpl2 == 'contactus-reply-premier.tpl') ? $tpl_path.$mailtpl2 : $mailtpl2;
            
      // (string) mail template 3 (usually internal)
      $mailtpl3 = $this->rtag->param('safe-tpl', '');
      $mailtpl3 = ($mailtpl3 == 'contactus-safe.tpl') ? $tpl_path.$mailtpl3 : $mailtpl3;
            
      // (string) the file that the form should point to
      $action = $this->rtag->param('action', '#');

      // (string) the file that we should go to if there is an error
      $redirect = $this->rtag->param('redirect', 'consumer-info.php');
      
      // (string) the file that we should go to to thank the user
      // if no file is specified, then we use the default from the template
      $thankyou = $this->rtag->param('thank-you', '');

      // (string) mail form template
      $formtpl = $this->rtag->param('form-tpl', 'contact-us');
      
      // (string) the site id
      $site_id = $this->rtag->param('site-id');
      
      // (string) display the marketing checkbox
      $marketing = $this->rtag->param('marketing', TRUE);

      // (string) the url for the privacy policy
      $privacy = $this->rtag->param('privacy', '');

      // (string) display the release checkbox
      $release = $this->rtag->param('release', TRUE);
      
      // (string) serialized array of form labels
      $labels = $this->rtag->param('labels', '');
      
      // (string) override for the Consumer Relations phone number
      $support_phone = $this->rtag->param('support-phone', '');      

      // (string) override for the Consumer Relations email address
      $support_email = $this->rtag->param('support-email', '');

      // (string) link to Store Locator on website
      $locator_link = $this->rtag->param('locator-link', '');

      // (string) link to Home Page on website
      $home_link = $this->rtag->param('home-link', '/');

      // (string) the IP address of the form submitter
      $this->user_ip = $this->rtag->param('user-ip', '');

      // (string) the agent of the form submitter
      $this->user_agent = $this->rtag->param('user-agent', '');

      // (string) the referrer of the form submitter
      $this->referrer = $this->rtag->param('referrer', '');

      // update the contact_form object with site-supplied information:
      $this->contact_form->set_support_phone($support_phone);
      $this->contact_form->set_support_email($support_email);
      $this->contact_form->set_locator_link($locator_link);
      
      $this->site_id = $site_id;
      $this->brand_name = $this->Sites->get_brand_name($site_id);
      $site = $this->Sites->get_site_data($site_id);
//      echo '<pre>'.$site_id.'<br />'.$this->brand_name.'<br />'; print_r($site); echo '</pre>';
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
      
      // don't run validation tests the first time we enter
      $validation_passed = FALSE;
//      echo '<pre>validation_passed: '.$validation_passed; print_r($_POST); echo '</pre>'; exit;
      if (isset($_POST['form_part']) && ($this->input->post('form_part') != 1))
      {
         $validation_passed = $this->validation->run();
      }
      
      $data['home_link'] = $home_link;

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
         $data['privacy'] = $privacy;
         $data['marketing'] = $marketing;
         $data['release'] = $release;
      }
      else
      {         
         $this->_contact_us_process($mailtpl1, $mailtpl2, $mailtpl3, $action);
         $data['form_part'] = 3;
//         exit;
         if ($thankyou != '')
         {
            $html = "<script type=\"text/javascript\">\n".
                    "//<![CDATA[\n".
                    "window.location=\"".$thankyou."\";\n".
                    "//]]>\n".
                    "</script>";
            echo $html;
            exit;
         }
      }

      $html = $this->load->view('rtags/v2/'.$formtpl, $data, TRUE);
      
      echo $html;
   }
   
   //-------------------------------------------------------------------------  
   
   /**
    * Process the form data from 'contact' form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _contact_us_process($mailtpl1, $mailtpl2, $mailtpl3, $action)
   {
      $this->load->library('user_agent');

      $fields = $this->validation->_fields;
      unset($fields['Email2']);
      unset($fields['form_token']);
      
      $subject = $this->input->post('Subject');
      unset($fields['Subject']);  // this should only be temporary!

      $_POST['user_ip'] = $this->user_ip;
      $_POST['user_agent'] = $this->user_agent;
      $_POST['referrer'] = $this->referrer;

      $protocol = ( ! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
      
      // now we check for spam
      $params['api_key'] = 'f76bf9349cf0';
      $params['blog_url'] = $protocol.$this->site_base_url;
      $this->load->library('akismet', $params);
      
      $comment = array();
      $comment['blog'] = $protocol.$this->site_base_url; // required
      $comment['user_ip'] = $this->input->post('user_ip');
      $comment['user_agent'] = $this->input->post('user_agent');
      $comment['referrer'] = $this->input->post('referrer');
      $comment['permalink'] = $protocol.$this->site_base_url.$action;
      $comment['comment_type'] = 'contact us';
      $comment['comment_author'] = $this->input->post('FName').' '.$this->input->post('LName');
      $comment['comment_author_email'] = $this->input->post('Email');
      $comment['comment_author_url'] = '';
      $comment['comment_content'] = $this->input->post('Comment');
      
      $_POST['spam'] = ($comment['user_agent'] != '') ? $this->akismet->is_spam($comment) : 0;
      
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
         $values['site_url'] = 'http://'.$this->site_base_url;
         $values['URL'] = $this->site_base_url.'/'.$action;
         $values['support_email'] = $this->contact_form->get_support_email();
         $values['support_phone'] = $this->contact_form->get_support_phone();
         $values['support_hours'] = $this->contact_form->get_support_hours();
         $values['locator_link'] = $this->contact_form->get_locator_link();
         
//         echo '<pre>'; print_r($values); echo '</pre>'; exit;
   
         // send e-mail(s)
         $this->_send_email($mailtpl1, $values);  
         $this->_send_email($mailtpl2, $values);
         $this->_send_email($mailtpl3, $values);
//      }

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

      $this->load->library('parser');

      // make sure sendmail is available
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail))
      {
         $sendmail = "/usr/sbin/sendmail -t ";
      }

      $mail_content = file_get_contents($mailtpl);
      $mail_content = $this->parser->parse($mail_content, $values, TRUE, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);

      return TRUE;
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
      $data = array();
      $data['address'] = $this->contact_form->get_support_address();
      $data['phone'] = $this->contact_form->get_support_phone();
      $data['hours'] = $this->contact_form->get_support_hours();
      
      $json = $this->load->view('rtags/v2/support_info', $data, TRUE);
      
      echo $json;

   }


}

/* End of file rtags.php */
/* Location: ./system/modules/mailform/controllers/v2/rtags.php */