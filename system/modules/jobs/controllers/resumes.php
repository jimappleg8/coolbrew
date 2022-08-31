<?php

class Resumes extends Controller {

   var $export_fields = array(
      'jr.ID',
      'jr.FName',
      'jr.MName',
      'jr.LName',
      'jr.Address',
      'jr.HomePhone',
      'jr.WorkPhone',
      'jr.Email',
      'jr.Resume',
      'jr.CoverLtr',
      'jr.JobID',
      'jr.LocationName',
      'jr.CategoryName',
      'jr.DateSent',
      'jr.EEOGender',
      'jr.EEOEthnicity',
      'jr.EEORace',
      'jr.EEOMultiPrime',
      'jr.EEOSignature',
      'jr.EEODate',
      'j.JobNum',
      'j.Title', 
      'j.Manager', 
      'j.Summary', 
      'j.Description',
      'j.FilledNotes',
      'jc.CategoryName',
      'jco.CompanyName',
      'jl.LocationName',
      'jl.City AS LocationCity',
      'jl.State AS LocationState',
      'jl.Country AS LocationCountry',
      'jl.ContactEmail AS LocationContactEmail',
   );

   // --------------------------------------------------------------------

   function Resumes()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * Displays an EEO form
    *
    */
   function view_eeo($resume_id) 
   {      
      $this->Jobs_people->check('Jobs');

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

      $rules['EEOGender'] = 'trim';
      $rules['EEOEthnicity'] = 'trim';
      $rules['EEORace'] = 'trim';

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

      $sql = 'SELECT * FROM jobs_resume '.
             'WHERE ID = '.$resume_id;

      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['Name'] = $defaults['FName'].' '.$defaults['MName'].' '.$defaults['LName'];
      
//      echo "<pre>"; print_r($defaults); echo "</pre>";

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs');
         
         $data['resume_id'] = $resume_id;
         
         return $this->load->view('resumes/eeo_view', $data, TRUE);
      }
      else
      {
         redirect('jobs/index');
      }
  }
   
   // --------------------------------------------------------------------

   /**
    * Generates a form that allows users to select a date range for a 
    * CVS listing of job applications.
    *
    */
   function export_applications() 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      set_time_limit(0);
      
      $rules['StartDate'] = 'required';
      $rules['EndDate'] = 'required';

      $this->validation->set_rules($rules);

      $fields['StartDate'] = 'Start Date';
      $fields['EndDate'] = 'End Date';

      $this->validation->set_fields($fields);

      $defaults['StartDate'] = date('Y').'-01-01';
      $defaults['EndDate'] = date('Y-m-d');
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {         
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Applications');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
      
         $this->load->vars($data);
   	
         return $this->load->view('resumes/applications_export', NULL, TRUE);
      }
      else
      {
         echo "Validate is TRUE..."; exit;
         $this->_export_applications();
      }
   }

   // --------------------------------------------------------------------

   /**
    * Generates a CVS listing of job applications
    *
    */
   function _export_applications()
   {
      echo "Entering the processing function..."; exit;
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $this->Jobs_people->check('Jobs');
      
      $this->load->database('write');
      $this->load->dbutil();
      
      $field_list = implode($this->export_fields, ', ');
      
      $sql = 'SELECT '.$field_list.' '.
             'FROM jobs_resume AS jr, jobs AS j, jobs_category AS jc, '.
               'jobs_company AS jco, jobs_location AS jl '.
             'WHERE jr.JobID = j.ID '.
             'AND j.LocationID = jl.ID '.
             'AND j.CategoryID = jc.ID '.
             'AND j.CompanyID = jco.ID '.
             'AND jr.DateSent >= "'.$values['StartDate'].'" '.
             'AND jr.DateSent <= "'.$values['EndDate'].'"';

      echo $sql; exit;
      
      $query = $this->db->query($sql);
      
      echo '<pre>'; print_r($query->result_array()); echo '</pre>'; exit;
      
      $today = date('Ymd');

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=application_list_".$today.".csv");

      echo $this->dbutil->csv_from_result($query);
      exit;
   }

}

?>