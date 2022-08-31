<?php

class Jobs_Tags extends Controller {

   function Jobs_Tags()
   {
      parent::Controller();   
   }
   
   // --------------------------------------------------------------------

   /**
    * Generate list of jobs
    *
    * @access   public
    * @return   void
    */
   function listing($orderby = "CategoryName", $direction = "asc")
   {
      $this->load->database('write');
      
      $sql = "SELECT jobs.ID, jobs.Title, jobs.JobNum, jobs_location.LocationName, jobs_category.CategoryName, jobs_company.CompanyName ".
             "FROM jobs, jobs_location, jobs_category, jobs_company " . 
             "WHERE jobs.LocationID = jobs_location.ID ".
             "AND jobs.CategoryID = jobs_category.ID ".
             "AND jobs.CompanyID = jobs_company.ID ".
             "AND jobs.Status = 1 ".
             "ORDER BY ".$orderby." ".$direction;

      $query = $this->db->query($sql);
      $jobs = $query->result_array();
      
      $num_jobs = $query->num_rows();
            
      $data['jobs'] = $jobs;
      $data['num_jobs'] = $num_jobs;
      $data['orderby'] = $orderby;
      $data['direction'] = $direction;
   	
      return $this->load->view('jobs_list', $data, TRUE);
   
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates job detail page
    *
    * @access   public
    * $param    int  The job ID number to be displayed
    * @return   void
    */
   function detail($job_id) 
   {
      $this->load->helper(array('url', 'typography'));

      $this->load->database('write');
   
      if ( ! preg_match('/^RX/', $job_id))
      {
         $sql = "SELECT jobs.*, jobs_location.LocationName, jobs_category.CategoryName, jobs_company.CompanyName ".
                "FROM jobs, jobs_location, jobs_category, jobs_company " . 
                "WHERE jobs.LocationID = jobs_location.ID ".
                "AND jobs.CategoryID = jobs_category.ID ".
                "AND jobs.CompanyID = jobs_company.ID ".
                "AND jobs.ID = ".$job_id;
      }
      else
      {
         $sql = "SELECT jobs.*, jobs_location.LocationName, jobs_category.CategoryName, jobs_company.CompanyName ".
                "FROM jobs, jobs_location, jobs_category, jobs_company " . 
                "WHERE jobs.LocationID = jobs_location.ID ".
                "AND jobs.CategoryID = jobs_category.ID ".
                "AND jobs.CompanyID = jobs_company.ID ".
                "AND jobs.JobNum = '".$job_id."'";
      }
      
      $query = $this->db->query($sql);
      $jobs = $query->row_array();

      $jobs['Summary'] = auto_typography($jobs['Summary']);
      $jobs['Summary'] = auto_link($jobs['Summary']);
      $jobs['Description'] = auto_typography($jobs['Description']);
      $jobs['Description'] = auto_link($jobs['Description']);

      $data['jobs'] = $jobs;
   	
      return $this->load->view('jobs_detail', $data, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Generates unsolicited jobs form
    *
    * @access   public
    * $param    int  The job ID number to be displayed
    * @return   void
    */
   function unsolicited() 
   {
      $this->load->helper(array('form','url'));
      $this->load->model('Jobs_location');
      $this->load->library('validation');
      
      $rules['LocationID'] = 'required';
      $rules['CategoryID'] = 'required';

      $this->validation->set_rules($rules);

      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
//         echo "<pre>"; print_r($this); echo "</pre>";
//         exit;
         
         $this->load->database('write');
   
         // Locations
         $data['locations'] = $this->Jobs_location->get_distinct_location_list();
      
         // Categories
         $sql = 'SELECT ID, CategoryName '.
                'FROM jobs_category '.
                'WHERE Status = 1 '.
                'ORDER BY CategoryName';

         $query = $this->db->query($sql);
         $categories = $query->result_array();
         
         $data['categories'] = array(''=>'-- Choose a category --');
         for ($i=0; $i<count($categories); $i++)
         {
            $data['categories'][$categories[$i]['ID']] = $categories[$i]['CategoryName'];
         }

         return $this->load->view('unsolicited', $data, TRUE);
      }
      else
      {
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
         header('location:'.base_url().'careers/submit_resume.php/0/'.$values['LocationID'].'/'.$values['CategoryID'].'/');
      }
   }
   
   // --------------------------------------------------------------------

   /**
    * Submit Your Resume form
    *
    * @access   public
    * $param    int  The job ID number
    * @return   void
    */
   function submit_resume($job_id = '', $loc_id = 0, $cat_id = 0) 
   {
      // (string) mail template 1 (usually internal)
      $mailtpl1 = $this->tag->param(1, 'submit_resume_mail');
      
      // (string) mail template 2 (usually to user)
      $mailtpl2 = $this->tag->param(2, 'submit_resume_reply');
            
      $form_html = "";
      $display_response = false;

      $this->load->helper(array('form','url'));
      $this->load->model('Job');
      $this->load->model('Jobs_location');
      $this->load->library('validation');

      $rules['LocationID'] = 'required';
      $rules['CategoryID'] = 'required';
      $rules['FName'] = 'trim|required|max_length[25]';
      $rules['LName'] = 'trim|required|max_length[25]';
      $rules['HomePhone'] = 'trim|required';
      $rules['Email'] = 'trim|required|valid_email';
      $rules['Resume'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';
      $fields['FName'] = 'First Name';
      $fields['MName'] = 'Middle Name or Initial';
      $fields['LName'] = 'Last Name';
      $fields['Address'] = 'Address';
      $fields['HomePhone'] = 'Home Phone';
      $fields['WorkPhone'] = 'Work Phone';
      $fields['Email'] = 'Email';
      $fields['Resume'] = 'Resume';
      $fields['CoverLtr'] = 'Cover Letter';
      $fields['JobID'] = 'Job ID';
      $fields['Subject'] = 'Subject';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $id = $this->_submit_resume($mailtpl1, $mailtpl2);
         $display_response = true;
      }
      else
      {
         $this->collector->append_css_file('jobs');
         
         // check if this form was called with a specific job in mind
         if ($job_id != '0' && $job_id != '')
         {
            $job = $this->Job->get_job_data($job_id);
      
            $data['JobID'] = $job['ID'];
            $data['JobNum'] = $job['JobNum'];
            $data['Title'] = $job['Title'];
            $data['LocationID'] = $job['LocationID'];
            $data['CategoryID'] = $job['CategoryID'];
            $data['Subject'] = '[resume] ('.$job['JobNum'].') '.$job['Title'];
         }
         // if no specific job, then location and category should be specified
         else
         {
//            echo "no id";
            $data['JobID'] = $job_id;
            $data['JobNum'] = 'None';
            $data['Title'] = 'None';
            $data['LocationID'] = $loc_id;
            $data['CategoryID'] = $cat_id;
            $data['Subject'] = '[unsolicited]';
         }

         $defaults['LocationID'] = ($loc_id == 0) ? '': $loc_id;
         $defaults['CategoryID'] = ($cat_id == 0) ? '': $cat_id;

         $this->validation->set_defaults($defaults);

         $data['locations'] = $this->Jobs_location->get_distinct_location_list();
         $data['all_locations'] = $this->Jobs_location->get_location_array();
         $data['categories'] = $this->Job->get_category_list();
         
         $data['job_id'] = $job_id;
         $data['loc_id'] = $loc_id;
         $data['cat_id'] = $cat_id;

         $form_html = $this->load->view('submit_resume_form', $data, TRUE);
      }

      $results[0] = $display_response;
      $results[1] = $form_html;
      if (isset($id))
      {
         $results[2] = $id;
      }

      return $results;
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from submit resume form;
    *
    */
   function _submit_resume($mailtpl1, $mailtpl2)
   {
      $fields = $this->validation->_fields;
      unset($fields['Subject']);
      
      unset($fields['LocationID']);
      unset($fields['CategoryID']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['DateSent'] = date('Y-m-d');
      
      $this->load->database('write');
      
      // get the basics for this LocationID
      $loc = $this->Jobs_location->get_location_data($this->input->post('LocationID'));
      
      $values['LocationName'] = $loc['LocationName'];
      
      $sql = 'SELECT CategoryName '.
             'FROM jobs_category '.
             'WHERE ID = '.$this->input->post('CategoryID');

      $query = $this->db->query($sql);
      $cat = $query->row_array();
            
      $values['CategoryName'] = $cat['CategoryName'];

      $this->db->insert('jobs_resume', $values);
      
      $id = $this->db->insert_id();
      
      $values['ID'] = $id;
      $values['Subject'] = $this->input->post('Subject');

      // add category name to subject if unsolicited resume
      // also, get possible multiple emails for location
      if ($values['Subject'] == '[unsolicited]')
      {
         $values['Subject'] = '[resume] (unsolicited) for '.$cat['CategoryName'];
         $values['ContactEmail'] = $this->Jobs_location->get_emails($loc['LocationName']);
      }
      else
      {
         $values['ContactEmail'] = array($loc['ContactEmail']);
      }
      
      // send e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail)) {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      // send the email internally
      $mail_content = $this->load->view($mailtpl1, $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   
      // send reply to user
      $mail_content2 = $this->load->view($mailtpl2, $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content2)."\n");
      pclose($fd);

      // only return ID if resume is for a US job
      if ($loc['Country'] == 'United States')
      {
         return $id;
      }
      else
      {
         return FALSE;
      }
   }

   // --------------------------------------------------------------------

   /**
    * Send a job to a friend form
    *
    * @access   public
    * $param    int  The job ID number
    * @return   void
    */
   function email_to_a_friend($job_id) 
   {
      // (string) mail template 1, sent to friend
      $mailtpl1 = $this->tag->param(1, 'friend_mail');
      
      $form_html = "";
      $display_response = false;

      $this->load->helper(array('form','url'));

      $this->load->library('validation');

      $rules['FriendEmail'] = 'trim|require|valid_email';
      $rules['YourName'] = 'trim|require';
      $rules['YourEmail'] = 'trim|require|valid_email';

      $this->validation->set_rules($rules);

      $fields['FriendEmail'] = 'Your Friend\'s Email';
      $fields['YourName'] = 'Your Name';
      $fields['YourEmail'] = 'Your Email';
      $fields['Subject'] = 'Subject';
      $fields['Message'] = 'Personal Message';
      $fields['JobID'] = 'Job ID';
      $fields['JobNum'] = 'Job No.';
      $fields['Title'] = 'Title';
      $fields['LocationName'] = 'Location';

      $this->validation->set_fields($fields);
      
      $data['JobID'] = $job_id;

      $this->load->database('write');

      $sql = 'SELECT jobs.JobNum, jobs.Title, jobs_location.LocationName '.
             'FROM jobs, jobs_location '.
             'WHERE jobs.LocationID = jobs_location.ID '.
             'AND jobs.ID = '.$data['JobID'];

      $query = $this->db->query($sql);
      $job = $query->row_array();
      
      $data['JobNum'] = $job['JobNum'];
      $data['Title'] = $job['Title'];
      $data['LocationName'] = $job['LocationName'];
      
      $defaults['Subject'] = $data['Title'];

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_email_to_a_friend($mailtpl1);
         $display_response = true;
      }
      else
      {
         $this->collector->append_css_file('jobs');
         
         $form_html = $this->load->view('friend_form', $data, TRUE);
      }

      $results[0] = $display_response;
      $results[1] = $form_html;

      return $results;
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from email to a friend form;
    * includes: save to db and sending internal and autoreply emails
    *
    */
   function _email_to_a_friend($mailtpl1)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['DateSent'] = date("Y-m-d");
      $values['URL'] = 'http://'. $_SERVER['HTTP_HOST']. '/careers/detail.php/'.$values['JobID'].'/';
      
      $this->load->database('write');

      $sql = 'SELECT JobNum, Title FROM jobs '.
             'WHERE ID = '.$values['JobID'];

      $query = $this->db->query($sql);
      $job = $query->row_array();

      $values['Title'] = $job['Title'];
      $values['JobNum'] = $job['JobNum'];

      // send e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail)) {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      // send the email internally
      $mail_content = $this->load->view($mailtpl1, $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   
   }

   // --------------------------------------------------------------------

   /**
    * Employee Equal Opportunity form
    *
    * @access   public
    * $param    int  The job ID number
    * @return   void
    */
   function submit_eeo($resume_id) 
   {      
      $form_html = "";
      $display_response = false;

      $data['resume_id'] = $resume_id;
      
      $data['multi'] = array(
         '' => '',
         'white' => 'White',
         'black' => 'Black or African American',
         'islander' => 'Native Hawaiian or Other Pacific Islander',
         'asian' => 'Asian',
         'native' => 'American Indian or Alaskan Native',
      );

      $this->load->helper(array('form','url'));

      $this->load->library('validation');

      $rules['EEOGender'] = 'trim|require';
      $rules['EEOEthnicity'] = 'trim|require';
      $rules['EEORace'] = 'trim|require';

      $this->validation->set_rules($rules);

      $fields['EEOGender'] = 'Gender';
      $fields['EEOEthnicity'] = 'Ethnicity';
      $fields['EEORace'] = 'Race';
      $fields['EEOMultiPrime'] = 'Race';
      $fields['Name'] = 'Name';
      $fields['EEOSignature'] = 'Signature';
      $fields['EEODate'] = 'Date';

      $this->validation->set_fields($fields);
      
      $this->load->database('write');

      $sql = 'SELECT FName, MName, LName '.
             'FROM jobs_resume '.
             'WHERE ID = '.$data['resume_id'];

      $query = $this->db->query($sql);
      $resume = $query->row_array();
      
      $defaults['Name'] = $resume['FName'].' '.$resume['MName'].' '.$resume['LName'];
      $defaults['EEOGender'] = 'no_response';
      $defaults['EEOEthnicity'] = 'no_response';
      $defaults['EEORace'] = 'no_response';
      $defaults['EEODate'] = date('d M Y');

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_submit_eeo($resume_id);
         $display_response = true;
      }
      else
      {
         $this->collector->append_css_file('jobs');
         
         $form_html = $this->load->view('affirmative_action_form', $data, TRUE);
      }

      $results[0] = $display_response;
      $results[1] = $form_html;

      return $results;
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from EEO form;
    *
    */
   function _submit_eeo($resume_id)
   {
      $fields = $this->validation->_fields;
      unset($fields['Name']);
      unset($fields['resume_id']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $values['EEODate'] = date('Y-m-d', strtotime($values['EEODate']));

      $this->load->database('write');
      
      $this->db->where('ID', $resume_id);
      $this->db->update('jobs_resume', $values);

   }

}

?>
