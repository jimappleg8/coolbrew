<?php

class Projects_tags extends Controller {

   function Projects_tags()
   {
      parent::Controller();   
   }
   
   function index()
   {
      $this->load->view('welcome_message');
   }
   
      // --------------------------------------------------------------------

   /**
    * Submit Your Project form
    *
    * @access   public
    * $param    int  The job ID number
    * @return   void
    */
   function submit_project() 
   {
      // (string) mail template 1 (usually internal)
      $mailtpl1 = $this->tag->param(1, 'submit_project_mail');
      
      // (string) mail template 2 (usually to user)
      $mailtpl2 = $this->tag->param(2, 'submit_project_reply');
            
      $form_html = "";
      $display_response = false;

      $this->load->helper(array('form','url'));

      $this->load->library('validation');

      $rules['FName'] = 'trim|required|max_length[25]';
      $rules['LName'] = 'trim|required|max_length[25]';
      $rules['HomePhone'] = 'trim|required';
      $rules['Email'] = 'trim|required|valid_email';
      $rules['Resume'] = 'trim|required';

      $this->validation->set_rules($rules);

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
      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';
      $fields['Subject'] = 'Subject';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $id = $this->_submit_project($mailtpl1, $mailtpl2);
         $display_response = true;
      }
      else
      {
         $this->collector->append_css_file('projects');
         
         $data['empty'] = '';
         
         $form_html = $this->load->view('submit_project_form', $data, TRUE);
      }

      $results[0] = $display_response;
      $results[1] = $form_html;

      return $results;
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from submit resume form;
    *
    */
   function _submit_project($mailtpl1, $mailtpl2)
   {
      $fields = $this->validation->_fields;
      unset($fields['Subject']);
      
      unset($fields['LocationID']);
      unset($fields['CategoryID']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['DateSent'] = date('Y-m-d');
      
      $this->load->database('write');
      
      // get the email address to send the resume
      $sql = 'SELECT ContactEmail, LocationName, Country '.
             'FROM jobs_location '.
             'WHERE ID = '.$this->input->post('LocationID');

      $query = $this->db->query($sql);
      $job = $query->row_array();
            
      $values['LocationName'] = $job['LocationName'];
      
      $sql = 'SELECT CategoryName '.
             'FROM jobs_category '.
             'WHERE ID = '.$this->input->post('CategoryID');

      $query = $this->db->query($sql);
      $cat = $query->row_array();
            
      $values['CategoryName'] = $cat['CategoryName'];

      $this->db->insert('jobs_resume', $values);
      
      $id = $this->db->insert_id();
      
      $values['ID'] = $id;
      $values['ContactEmail'] = $job['ContactEmail'];
      $values['Subject'] = $this->input->post('Subject');

      // add category name to subject if unsolicited resume
      if ($values['Subject'] = '[unsolicited]')
      {
         $values['Subject'] = '[resume] (unsolicited) for '.$cat['CategoryName'];
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
      if ($job['Country'] == 'United States')
      {
         return $id;
      }
      else
      {
         return FALSE;
      }
   }


}
?>