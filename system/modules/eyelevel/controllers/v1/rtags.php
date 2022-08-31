<?php

class Rtags extends Controller {

   var $brand_name;    // the brand's name based on the site ID
   var $site_base_url; // the brand's URL based on the site ID
   
   var $default_labels = array(
      'Customer'     => 'Your name',
      'Email'        => 'E-mail',
      'Reason'       => 'Reason',
      'ChooseReason' => 'Select...',
      'Message'      => 'Comment or Question',
      'SubmitText'   => 'Send',
   );

   //-------------------------------------------------------------------------
   
   function Rtags()
   {
      parent::Controller();
      $this->load->library('session');
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Creates a contact us form that sends the email to Eye Level. It stores
    *  contacts is the CoolBrew database in case there issues with the emails
    *  not being sent or received.
    *
    * It allows for up to 3 emails to go out:
    *   $mailtpl1 - usually internal (e.g. contact-mail.php)
    *   $mailtpl2 - usually to user  (e.g. contact-reply.php)
    *   $mailtpl2 - usually internal (e.g. contact-safe.php)
    *
    * If no emails are specified in the tag, data is just saved to
    * the database.
    *
    */
   function contact()
   {
      $this->load->library('Rtag');
      $this->load->model('Sites');

      $tpl_path = BASEPATH.'modules/eyelevel/views/rtags/v1/';

      // (string) the site id
      $site_id = $this->rtag->param('site-id');
      
      // (string) mail form template
      $formtpl = $this->rtag->param('form-tpl', 'contact');
      
      // (string) mail template 1 (usually to Eye Level)
      $mailtpl1 = $this->rtag->param('mail-tpl', 'contact-mail.tpl');
      $mailtpl1 = ($mailtpl1 == '') ? 'contact-mail.tpl' : $mailtpl1;
      $mailtpl1 = ($mailtpl1 == 'contact-mail.tpl') ? $tpl_path.$mailtpl1 : $mailtpl1;
      
      // (string) mail template 2 (usually to user)
      $mailtpl2 = $this->rtag->param('reply-tpl', '');
      $mailtpl2 = ($mailtpl2 == 'contact-reply.tpl') ? $tpl_path.$mailtpl2 : $mailtpl2;
            
      // (string) mail template 3 (usually internal, so we can monitor)
      $mailtpl3 = $this->rtag->param('safe-tpl', '');
      $mailtpl3 = ($mailtpl3 == 'contact-safe.tpl') ? $tpl_path.$mailtpl3 : $mailtpl3;
            
      // (string) the file that the form should point to
      $action = $this->rtag->param('action', '#');

      // (string) the file that we should go to to thank the user
      // if no file is specified, then we use the default from the template
      $thankyou = $this->rtag->param('thank-you', '');

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
      $this->site_base_url = $site['Domain'];

      $this->load->helper(array('form','url'));

      $this->load->library('validation');

      $rules['Customer'] = 'trim|required';
      $rules['Email'] = 'trim|required';
      $rules['Reason'] = 'trim|required';
      $rules['Message'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['Customer'] = 'Customer name';
      $fields['Email'] = 'Email';
      $fields['Reason'] = 'Reason';
      $fields['Message'] = 'Message';
      $fields['cntr'] = 'character counter';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $reasons = array(
                   '' => $this->default_labels['ChooseReason'],
                   'Order' => 'Order',
                   'Product' => 'Product Suggestion',
                   'Site' => 'Store Suggestion',
                   'Return' => 'Return or Damaged Product',
                   'Technical Support' => 'Technical Support',
                   'Other' => 'Other',
                 );
      
      if ($this->validation->run() == FALSE)
      {
         $data['siteid'] = SITE_ID;
         $data['hcg_site'] = $this->brand_name;
         $data['form_part'] = 1;
         $data['reasons'] = $reasons;
         $data['action'] = $action;
         $data['labels'] = $this->default_labels;
      }
      else
      {         
         $this->_contact_process($site_id, $mailtpl1, $mailtpl2, $mailtpl3, $action);
         $data['form_part'] = 2;
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
      
      $results[0] = $data['form_part'];
      $results[1] = $html;

      echo $html;
   }
   
   //-------------------------------------------------------------------------  
   
   /**
    * Process the form data from 'contact' form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _contact_process($site_id, $mailtpl1, $mailtpl2, $mailtpl3, $action)
   {
      $this->load->model('Contact');

      $fields = $this->validation->_fields;
      unset($fields['cntr']);

      $values = $this->Contact->insert_contact($fields, $site_id);
      
      $values['brand_name'] = $this->brand_name;
      $values['DateSent'] = date("Y-m-d");
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
/* Location: ./system/modules/eyelevel/controllers/v1/rtags.php */