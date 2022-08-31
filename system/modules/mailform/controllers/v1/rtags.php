<?php

class Rtags extends Controller {

   var $brand_name;    // the brand's name based on the site ID
   var $site_base_url; // the brand's URL based on the site ID
   
   var $default_labels = array(
      'FName'     => 'First Name',
      'LName'     => 'Last Name',
      'Address1'  => 'Address Line 1',
      'Address2'  => 'Address Line 2',
      'City'      => 'City',
      'State'     => 'State',
      'ChooseState' => '-- Please select a state --',
      'Country'   => 'Country',
      'ChooseCountry' => '-- Please select a country --',
      'Zip'       => 'Zip/Postal Code',
      'Phone'     => 'Daytime Phone',
      'Email'     => 'Email',
      'Email2'    => 'Confirm your Email',
      'Comment'   => 'Message',
      'Marketing' => '##default##',
      'Release'   => 'From time to time, we select consumer comments to post on our web site. Please check this box if you would like your comments to be considered.',
      'SubmitText' => 'Send message',
   );

   //-------------------------------------------------------------------------
   
   function Rtags()
   {
      parent::Controller();
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

      // (string) the file that the form should point to
      $action = $this->rtag->param('action', 'contact-us');

      // (string) mail form template
      $formtpl = $this->rtag->param('form-tpl', 'contact-us');
      
      // (string) serialized array of form labels
      $labels = $this->rtag->param('labels', '');
      
      // process the labels
      if ($labels != '')
      {
         $labels_array = unserialize($labels);
         $this->default_labels = array_merge($this->default_labels, $labels_array);
      }
      
      // Token used to protect against Cross-Site Request Forgery (CSRF) attacks
      $form_token = random_string('unique');
      $this->session->set_userdata('form_token', $form_token);

      $this->load->library('validation');

      $rules['FName'] = 'trim';
      $rules['LName'] = 'trim';
      $rules['Email'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['FName'] = 'First Name';
      $fields['LName'] = 'Last Name';
      $fields['Email'] = 'Email';
      $fields['form_token'] = 'CSRF Token';

      $this->validation->set_fields($fields);
      
      $defaults['form_token'] = $form_token;
      $defaults['form_part'] = 1;
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      $data['siteid'] = SITE_ID;
      $data['hcg_site'] = $this->brand_name;
      $data['form_part'] = 1;
      $data['action'] = $action;
      $data['labels'] = $this->default_labels;

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

      $tpl_path = BASEPATH.'modules/mailform/views/rtags/v1/';

      // (string) mail template 1 (usually internal)
      $mailtpl1 = $this->rtag->param('mail-tpl', 'contactus-mail.tpl');
      $mailtpl1 = ($mailtpl1 == '') ? 'contactus-mail.tpl' : $mailtpl1;
      $mailtpl1 = ($mailtpl1 == 'contactus-mail.tpl') ? $tpl_path.$mailtpl1 : $mailtpl1;
      
      // (string) mail template 2 (usually to user)
      $mailtpl2 = $this->rtag->param('reply-tpl', 'contactus-reply.tpl');
      $mailtpl2 = ($mailtpl2 == '') ? 'contactus-reply.tpl' : $mailtpl2;
      $mailtpl2 = ($mailtpl2 == 'contactus-reply.tpl') ? $tpl_path.$mailtpl2 : $mailtpl2;
            
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
      $privacy = $this->rtag->param('privacy', '/privacy.php');

      // (string) display the release checkbox
      $release = $this->rtag->param('release', TRUE);
      
      // (string) serialized array of form labels
      $labels = $this->rtag->param('labels', '');
      
      // process the labels
      if ($labels != '')
      {
         $labels_array = unserialize($labels);
         $this->default_labels = array_merge($this->default_labels, $labels_array);
      }
      
      $this->brand_name = $this->Sites->get_brand_name($site_id);
      $site = $this->Sites->get_site_data($site_id);
//      echo '<pre>'; print_r($site); echo '</pre>'; 
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

      $rules['FName'] = 'trim|required|max_length[25]';
      $rules['LName'] = 'trim|required|max_length[25]';
      $rules['Address1'] = 'trim|required';
      $rules['City'] = 'trim|required';
      $rules['State'] = 'trim|required';
      $rules['Country'] = 'trim';
      $rules['Zip'] = 'trim|required';
      $rules['Email'] = 'trim|required|valid_email|matches[Email2]';
      $rules['Email2'] = 'trim|required';
      $rules['ProductUPC'] = 'trim';
      $rules['BestByDateLotCode'] = 'trim';
      $rules['Comment'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['FName'] = 'First Name';
      $fields['LName'] = 'Last Name';
      $fields['Address1'] = 'Address Line 1';
      $fields['Address2'] = 'Address Line 2';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Country'] = 'Country';
      $fields['Zip'] = 'Zip/Postal Code';
      $fields['Phone'] = 'Daytime Phone';
      $fields['Email'] = 'Email';
      $fields['Email2'] = 'Email Confirmation';
      $fields['ProductUPC'] = 'Product Name or UPC';
      $fields['BestByDateLotCode'] = 'Best By Date or Lot Code';
      $fields['Comment'] = 'Message';
      $fields['Marketing'] = 'Marketing';
      $fields['Release'] = 'Release';
      $fields['form_token'] = 'CSRF Token';

      $this->validation->set_fields($fields);
      
      $defaults['Marketing'] = 'YES';
      $defaults['Release'] = 'YES';
      $defaults['form_token'] = $form_token;
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      // don't run validation tests the first time we enter
      $validation_passed = FALSE;
      if ($this->input->post('form_part') != 1)
      {
         $validation_passed = $this->validation->run();
      }

      if ($validation_passed == FALSE)
      {
         $data['siteid'] = SITE_ID;
         $data['hcg_site'] = $this->brand_name;
         $data['form_part'] = 2;
         $data['states'] = get_state_list();
         $data['states'][''] = $this->default_labels['ChooseState'];
         $data['countries'] = get_country_list();
         $data['countries'][''] = $this->default_labels['ChooseCountry'];
         $data['action'] = $action;
         $data['privacy'] = $privacy;
         $data['marketing'] = $marketing;
         $data['release'] = $release;
         $data['labels'] = $this->default_labels;
      }
      else
      {         
         $this->_contact_us_process($site_id, $mailtpl1, $mailtpl2, $mailtpl3, $action);
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

      $html = $this->load->view('rtags/v1/'.$formtpl, $data, TRUE);
      
//      $results[0] = $data['form_part'];
//      $results[1] = $html;

      echo $html;
   }
   
   //-------------------------------------------------------------------------  
   
   /**
    * Process the form data from 'contact' form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _contact_us_process($site_id, $mailtpl1, $mailtpl2, $mailtpl3, $action)
   {
      $fields = $this->validation->_fields;
      unset($fields['Email2']);
      unset($fields['form_token']);  

      $values = $this->Contactus->save_submision($fields, $site_id);
      
      $values['brand_name'] = $this->brand_name;
      $values['brand_name_encoded'] = mb_encode_mimeheader($this->brand_name, 'UTF-8', 'Q');
      $values['DateSent'] = date("Y-m-d");
      $values['site_url'] = 'http://'.$this->site_base_url;
      $values['URL'] = $this->site_base_url.$action;
   
      // send e-mail(s)
      $this->_send_email($mailtpl1, $values);  
      $this->_send_email($mailtpl2, $values);
      $this->_send_email($mailtpl3, $values);

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

   
}

/* End of file rtags.php */
/* Location: ./system/modules/mailform/controllers/v1/rtags.php */