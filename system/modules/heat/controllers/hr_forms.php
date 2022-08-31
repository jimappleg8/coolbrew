<?php

class Hr_forms extends Controller {

   var $heat_db;
   
   function Hr_forms()
   {
      parent::Controller();
      $this->heat_db = $this->load->database('heat', TRUE);
      $this->load->library('session');
   }
   
   function index()
   {
      $this->load->helper('url');
      
      $sql = 'SELECT * '.
             'FROM INFORMATION_SCHEMA.COLUMNS '.
             'WHERE TABLE_TYPE = \'BASE TABLE\'';
      $query = $this->heat_db->query($sql);
      $results = $query->result_array();
      
//      foreach ($results AS $result)
//      {
//         echo $result['TABLE_NAME']."<br />";
//      }
      echo "<pre>"; print_r($results); echo "</pre>";
      
      $username = $this->session->userdata('username');
      if ($username == '')
      {
//         redirect('login/login_user');
         echo "you are not logged in.";
      }
      $this->load->view('forms_list');
   }
   
   //-------------------------------------------------------------------------

   /**
    * New employee form filled out by HR
    *
    */   
   function hr_newemployee_form()
   {            
      $data['display_response'] = false;

      $this->load->helper(array('form','url'));
      
      $this->load->model('Heat');

      $this->load->library('validation');

      $rules['NewEmployee'] = 'trim|required';
      $rules['Department'] = 'trim|required';
      $rules['Title'] = 'trim|required';
      $rules['EmployeeLocation'] = 'trim|required';
      $rules['HRAuthorizedBy'] = 'trim|required';
      $rules['ManagerLoginID'] = 'trim|required';
      $rules['AssistantLoginID'] = 'trim';
      $rules['StartDate'] = 'trim|required';
      $rules['EmployeeStatus'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['NewEmployee'] = 'Employee Name';
      $fields['Department'] = 'Department';
      $fields['Title'] = 'Title';
      $fields['EmployeeLocation'] = 'Employee Location';
      $fields['HRAuthorizedBy'] = 'HR Authorized By';
      $fields['ManagerLoginID'] = 'Manager\'s Login ID';
      $fields['AssistantLoginID'] = 'Assistant\'s Login ID';
      $fields['StartDate'] = 'Start Date';
      $fields['EmployeeStatus'] = 'Employee Status';

      $this->validation->set_fields($fields);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_hr_newemployee_form();
         $data['display_response'] = true;
      }

      $data['facilities'] = $this->Heat->get_facilities_list();
      $data['employee_status'] = $this->Heat->get_employee_status_list();
      $data['authorized_by'] = $this->session->userdata('username');

      // display the form or the thankyou page
      return $this->load->view('webmaster_form', $data, TRUE);

   }

   //-------------------------------------------------------------------------
   
   /**
    * Process the form data from 'hr_newemployee' form;
    *
    */
   function _hr_newemployee_form()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
   
      // send e-mail
      $sendmail = ini_get('sendmail_path');
      if (empty($sendmail)) {
         $sendmail = "/usr/sbin/sendmail -t ";
      }
   
      $mail_content = $this->load->view($mailtpl1, $values, TRUE);
      $fd = popen($sendmail,"w");
      fputs($fd, stripslashes($mail_content)."\n");
      pclose($fd);
   
   }

}
?>