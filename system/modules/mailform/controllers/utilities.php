<?php

class Utilities extends Controller {

   var $testmode = FALSE;

   function Utilities()
   {
      parent::Controller();
   }
   
   // --------------------------------------------------------------------

   /**
    * Resend contact us emails
    *
    * This is needed because of an issue with the rTag that prevented 
    * emails from being sent properly. The data is in the database, 
    * however, so we can regenerate them.
    *
    * The script send out the emails in batches of 100 with a 2 minute 
    * delay between sends. Since there are about 700 emails, there will be
    * a run time of about 15 minutes.
    *
    * I moved the script to the dev server before running it:
    * http://webadmin.hcgweb.net/admin/mailform.php/utilities/send_emails
    *
    */
   function send_emails()
   {
      // This is to prevent any accidental running of the script
//      exit;

      $this->load->database('production');
      $this->load->model('Sites');

      $site_id = 'cs';
      $start_id = 307168;
      $end_id = 309467;
      $form_id = 'CS_contactus';
      // the template can be a remote one in the case of an rTag
      // so use either a full path or a URL to specify.
      $mailtpl = '/var/opt/httpd/system/modules/mailform/views/rtags/v1/contactus-mail-2.tpl';
      $page_url = 'www.celestialseasonings.com/connect-with-us/contact-form';

      $brand_name = $this->Sites->get_brand_name($site_id);

      set_time_limit(0);
      ob_start();

      $sql = 'SELECT * '.
             'FROM wf_contactus '.
             'WHERE id >= '.$start_id.' '.
             'AND id < '.$end_id.' '.
             'AND form_id = "'.$form_id.'"';
      $query = $this->db->query($sql);
      $results = $query->result_array();
      
      $total = 0;
      foreach ($results AS $result)
      {
         $values = array();

         $values['FName'] = $result['fname'];
         $values['LName'] = $result['lname'];
         $values['Email'] = $result['email'];
         $values['Address1'] = $result['address1'];
         $values['Address2'] = $result['address2'];
         $values['City'] = $result['city'];
         $values['State'] = $result['state'];
         $values['Zip'] = $result['zip'];
         $values['Phone'] = $result['phone'];
         $values['Comment'] = $result['comment'];
         $values['Marketing'] = ($result['marketing'] == 0) ? 'NO' : 'YES';
         $values['Release'] = ($result['release'] == 0) ? 'NO' : 'YES';
         $values['brand_name'] = $brand_name;
         $values['DateSent'] = date("D, j M Y H:i:s O", $result['submit_ts']);
         $values['URL'] = $page_url;

         $this->_send_email($mailtpl, $values);

         echo $total.' | '.$values['DateSent'].' | '.$values['Email'].': '.$values['FName'].' '.$values['LName'].'<br/>';

         // ------------------------------------------------------------------
         // flush the output buffering to keep display up-to-date
         // ------------------------------------------------------------------
         while (ob_get_level() > 0)
         {
            ob_end_flush();
         }
         flush();

         $total++;

         if ($total % 100 == 0)
         {
            sleep(120);
            echo '<br /></br />';
         } 
      }
      exit;
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

      if ( ! $this->testmode)
      {
         $fd = popen($sendmail,"w");
         fputs($fd, stripslashes($mail_content)."\n");
         pclose($fd);
      }
      else
      {
         echo '<pre>';
         echo htmlentities($mail_content);
         echo '</pre>';
         echo '<br /></br /><br /></br />';
      }

      return TRUE;
   }

   //-------------------------------------------------------------------------
   
   /**
    * Checks all unread messages in the wf_contactus database table for spam
    * using Akismet.
    *
    * @access   public
    * @returns  null  
    */
   function process_unread_messages()
   {
      $this->load->library('akismet');
      $this->load->library('user_agent');
      
      $params['api_key'] = 'f76bf9349cf0';
      
      $this->Akismet->initialize($params);
      
      $comment = array();
      $comment['blog'] = ''; // required
      $comment['user_ip'] = $this->input->server('REMOTE_ADDR');
      $comment['user_agent'] = $this->agent->agent_string();
      $comment['referrer'] = $this->agent->referrer();
      $comment['permalink'] = '';
      $comment['comment_type'] = '';
      $comment['comment_author'] = '';
      $comment['comment_author_email'] = '';
      $comment['comment_author_url'] = '';
      $comment['comment_content'] = '';
      
      $this->Akismet->submit_spam($comment);
   }
   
}

/* End of file utilities.php */
/* Location: ./system/modules/mailform/controllers/utilities.php */