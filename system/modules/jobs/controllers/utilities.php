<?php

class Utilities extends Controller {

   function Utilities()
   {
      parent::Controller();
   }
   
   // --------------------------------------------------------------------

   /**
    * Resend resume emails
    *
    * This is needed because of a bug I introduced to the resume processing
    * script that prevented the emails from being sent. The data is in the 
    * database, however, so we can regenerate them.
    *
    * The script send out the emails in batches of 100 with a 2 minute 
    * delay between sends. Since there are about 700 emails, there will be
    * a run time of about 15 minutes.
    *
    * I moved the script to the live server before running it:
    * http://www.hain-celestial.com/careers/admin.php/utilities/send_emails
    *
    */
   function send_emails()
   {
      // This is to prevent any accidental running of the script
      exit;
      
      $testmode = TRUE;
      
      $startDate = ($testmode) ? '2011-04-29' : '2011-04-29';
      $endDate = ($testmode) ? '2011-04-19' : '2011-04-19';
      $job_id = 321;
      
      set_time_limit(0);

      $this->load->model('Jobs_location');
      $this->load->database('production');

      $sql = 'SELECT jr.ID, jr.JobID, jr.FName, jr.MName, jr.LName, jr.Address, '.
               'jr.HomePhone, jr.WorkPhone, jr.Email, jr.Resume, jr.CoverLtr, '.
               'jr.DateSent, jr.LocationName, jl.ContactEmail, j.JobNum, j.Title, '.
               'jc.CategoryName '.
             'FROM jobs_resume AS jr, jobs AS j, jobs_category AS jc, jobs_location AS jl '.
             'WHERE jr.JobID = j.ID '.
             'AND j.LocationID = jl.ID '.
             'AND j.CategoryID = jc.ID '.
             'AND jr.JobID = '.$job_id.' '.
             'AND jr.DateSent >= "'.$startDate.'"';
//             'AND jr.DateSent <= "'.$endDate.'"';
      $query = $this->db->query($sql);
      $results = $query->result_array();

      // e-mail settings
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail)) {
         $sendmail = "/usr/sbin/sendmail -t ";
      }

      $total = 0;
      foreach ($results AS $values)
      {
         // check if this resume was submitted with a specific job in mind
         if ($values['JobID'] != '0' && $values['JobID'] != '')
         {
            $values['Subject'] = '[resume] ('.$values['JobNum'].') '.$values['Title'];
         }
         // if no specific job, then location and category should be specified
         else
         {
            $values['Subject'] = '[resume] (unsolicited) for '.$values['CategoryName'];
            // This is set up to allow for multiple emails.
            $values['ContactEmail'] = $this->Jobs_location->get_emails($values['LocationName']);
         }

         $mail_content = $this->load->view('submit_resume_mail', $values, TRUE);

         if ( ! $testmode)
         {
            // send the email internally
            $fd = popen($sendmail,"w");
            fputs($fd, stripslashes($mail_content)."\n");
            pclose($fd);
         }
         else
         {
//            echo '<pre>';
//            echo htmlentities($mail_content);
//            echo '</pre>';
//            echo '<br /></br /><br /></br />';
         }
         echo $total.' | '.date('Y-m-d h:i:s').' | '.$values['Email'].': '.$values['Subject'].'<br/>';
         $total++;
         if ($total % 100 == 0)
         {
            sleep(120);
            echo '<br /></br />';
         } 
      }
      exit;
   }

}

?>