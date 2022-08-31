<?php

class Jobs extends Controller {

   function Jobs()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of jobs
    *
    */
   function index($orderby = "JobNum", $direction = "asc")
   {
      $this->Jobs_people->check('Jobs');
      
      $jobs['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');
   
      $this->load->database('write');

      $sql = "SELECT jobs.ID, jobs.Title, jobs.JobNum, jobs.Manager, ".
               "jobs.CreatedDate, jobs.Status, jobs_people.FirstName, ".
               "jobs_people.LastName, jobs_location.LocationName, ". 
               "jobs_category.CategoryName, jobs_company.CompanyName ".
             "FROM jobs, jobs_location, jobs_category, jobs_company, ".
               "jobs_people " . 
             "WHERE jobs.LocationID = jobs_location.ID ".
             "AND jobs.CategoryID = jobs_category.ID ".
             "AND jobs.CompanyID = jobs_company.ID ".
             "AND jobs.CreatedBy = jobs_people.Username ".
             "AND jobs.Status <= 1 ".
             "ORDER BY ".$orderby." ".$direction.', jobs.Title ASC';
             
      $query = $this->db->query($sql);
      $job_list = $query->result_array();

      $jobs['job_exists'] = ($query->num_rows() > 0) ? TRUE : FALSE;
      
      $this->collector->append_css_file('jobs_adm');
      
      $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
      $data['jobs'] = $jobs;
      $data['job_list'] = $job_list;
      $data['orderby'] = $orderby;
      $data['direction'] = ($direction == 'asc') ? 'desc' : 'asc';
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('jobs/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of filled jobs
    *
    */
   function filled()
   {
      $this->Jobs_people->check('Jobs');
      
      $jobs['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');
         
      $this->load->helper('text');
   
      $this->load->database('write');

      $sql = "SELECT * FROM jobs " .
             "WHERE Status = 2";

      $query = $this->db->query($sql);
      $job_list = $query->result_array();

      $num_jobs = count($job_list);

      if ($num_jobs == 0)
      {
         $jobs['job_exists'] = false;
      }
      else
      {
         $jobs['job_exists'] = true;
         
         $sql = "SELECT * FROM jobs_people";

         $query = $this->db->query($sql);
         $peo_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($peo_list); $i++)
         {
            $people_list[$peo_list[$i]['Username']] = $peo_list[$i]['FirstName'].' '.$peo_list[$i]['LastName'];
         }

         $sql = "SELECT * FROM jobs_location";

         $query = $this->db->query($sql);
         $loc_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($loc_list); $i++)
         {
            $location_list[$loc_list[$i]['ID']] = $loc_list[$i]['LocationName'];
         }

         $sql = "SELECT * FROM jobs_category";

         $query = $this->db->query($sql);
         $cat_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cat_list); $i++)
         {
            $category_list[$cat_list[$i]['ID']] = $cat_list[$i]['CategoryName'];
         }

         $sql = "SELECT * FROM jobs_company";

         $query = $this->db->query($sql);
         $cmpny_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cmpny_list); $i++)
         {
            $company_list[$cmpny_list[$i]['ID']] = $cmpny_list[$i]['CompanyName'];
         }

         // assign names to the $jobs data
         for ($i=0; $i<$num_jobs; $i++)
         {
            $job_list[$i]['FilledByName'] = $people_list[($job_list[$i]['FilledBy'])];
            $job_list[$i]['Location'] = $location_list[($job_list[$i]['LocationID'])];
            $job_list[$i]['Category'] = $category_list[($job_list[$i]['CategoryID'])];
            $job_list[$i]['Company'] = $company_list[($job_list[$i]['CompanyID'])];
         }
      }
      
      $this->collector->append_css_file('jobs_adm');
      
      $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
      $data['jobs'] = $jobs;
      $data['job_list'] = $job_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('jobs/filled_list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of on-hold jobs
    *
    */
   function onhold()
   {
      $this->Jobs_people->check('Jobs');
      
      $jobs['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');
         
      $this->load->helper('text');
   
      $this->load->database('write');

      $sql = "SELECT * FROM jobs " .
             "WHERE Status = 3";

      $query = $this->db->query($sql);
      $job_list = $query->result_array();

      $num_jobs = count($job_list);

      if ($num_jobs == 0)
      {
         $jobs['job_exists'] = false;
      }
      else
      {
         $jobs['job_exists'] = true;

         $sql = "SELECT * FROM jobs_people";

         $query = $this->db->query($sql);
         $peo_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($peo_list); $i++)
         {
            $people_list[$peo_list[$i]['Username']] = $peo_list[$i]['FirstName'].' '.$peo_list[$i]['LastName'];
         }

         $sql = "SELECT * FROM jobs_location";

         $query = $this->db->query($sql);
         $loc_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($loc_list); $i++)
         {
            $location_list[$loc_list[$i]['ID']] = $loc_list[$i]['LocationName'];
         }

         $sql = "SELECT * FROM jobs_category";

         $query = $this->db->query($sql);
         $cat_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cat_list); $i++)
         {
            $category_list[$cat_list[$i]['ID']] = $cat_list[$i]['CategoryName'];
         }

         $sql = "SELECT * FROM jobs_company";

         $query = $this->db->query($sql);
         $cmpny_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cmpny_list); $i++)
         {
            $company_list[$cmpny_list[$i]['ID']] = $cmpny_list[$i]['CompanyName'];
         }

         // assign names to the $jobs data
         for ($i=0; $i<$num_jobs; $i++)
         {
            $job_list[$i]['OnHoldByName'] = $people_list[($job_list[$i]['OnHoldBy'])];
            $job_list[$i]['Location'] = $location_list[($job_list[$i]['LocationID'])];
            $job_list[$i]['Category'] = $category_list[($job_list[$i]['CategoryID'])];
            $job_list[$i]['Company'] = $company_list[($job_list[$i]['CompanyID'])];
         }
      }
      
      $this->collector->append_css_file('jobs_adm');
      
      $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
      $data['jobs'] = $jobs;
      $data['job_list'] = $job_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('jobs/onhold_list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a CVS listing of jobs
    *
    */
   function export($orderby = "LocationName", $direction = "asc")
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->database('write');
      $this->load->dbutil();
      
      $sql = "SELECT jobs.JobNum, jobs.Title, jobs.Manager, ".
               "jobs.CreatedDate, jobs_people.FirstName, ".
               "jobs_people.LastName, jobs_location.LocationName, ". 
               "jobs_category.CategoryName, jobs_company.CompanyName ".
             "FROM jobs, jobs_location, jobs_category, jobs_company, ".
               "jobs_people " . 
             "WHERE jobs.LocationID = jobs_location.ID ".
             "AND jobs.CategoryID = jobs_category.ID ".
             "AND jobs.CompanyID = jobs_company.ID ".
             "AND jobs.CreatedBy = jobs_people.Username ".
             "AND jobs.Status <= 1 ".
             "ORDER BY ".$orderby." ".$direction;

      $query = $this->db->query($sql);
      
      $today = date('Ymd');

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=joblist_".$today.".csv");

      echo $this->dbutil->csv_from_result($query);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Generates a printable listing of jobs
    *
    */
   function print_jobs($orderby = "LocationName", $direction = "asc")
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->database('write');
      
      $sql = "SELECT jobs.ID, jobs.Title, jobs.JobNum, jobs.Manager, ".
               "jobs.CreatedDate, jobs.Status, jobs_people.FirstName, ".
               "jobs_people.LastName, jobs_location.LocationName, ". 
               "jobs_category.CategoryName, jobs_company.CompanyName ".
             "FROM jobs, jobs_location, jobs_category, jobs_company, ".
               "jobs_people " . 
             "WHERE jobs.LocationID = jobs_location.ID ".
             "AND jobs.CategoryID = jobs_category.ID ".
             "AND jobs.CompanyID = jobs_company.ID ".
             "AND jobs.CreatedBy = jobs_people.Username ".
             "AND jobs.Status <= 1 ".
             "ORDER BY ".$orderby." ".$direction;

      $query = $this->db->query($sql);
      $job_list = $query->result_array();

      $jobs['job_exists'] = ($query->num_rows() > 0) ? TRUE : FALSE;

      $this->collector->append_css_file('jobs_adm');
      
      $data['jobs'] = $jobs;
      $data['job_list'] = $job_list;
      $data['orderby'] = $orderby;
      $data['direction'] = ($direction == 'asc') ? 'desc' : 'asc';
      
      $this->load->vars($data);
   	
      return $this->load->view('jobs/print', NULL, TRUE);

   }

   // --------------------------------------------------------------------

   /**
    * Generates a printable listing of filled jobs
    *
    */
   function print_filled()
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper('text');
   
      $this->load->database('write');

      $sql = "SELECT * FROM jobs " .
             "WHERE Status = 2";

      $query = $this->db->query($sql);
      $job_list = $query->result_array();

      $num_jobs = count($job_list);

      if ($num_jobs == 0)
      {
         $jobs['job_exists'] = false;
      }
      else
      {
         $jobs['job_exists'] = true;

         $sql = "SELECT * FROM jobs_people";

         $query = $this->db->query($sql);
         $peo_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($peo_list); $i++)
         {
            $people_list[$peo_list[$i]['Username']] = $peo_list[$i]['FirstName'].' '.$peo_list[$i]['LastName'];
         }

         $sql = "SELECT * FROM jobs_location";

         $query = $this->db->query($sql);
         $loc_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($loc_list); $i++)
         {
            $location_list[$loc_list[$i]['ID']] = $loc_list[$i]['LocationName'];
         }

         $sql = "SELECT * FROM jobs_category";

         $query = $this->db->query($sql);
         $cat_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cat_list); $i++)
         {
            $category_list[$cat_list[$i]['ID']] = $cat_list[$i]['CategoryName'];
         }

         $sql = "SELECT * FROM jobs_company";

         $query = $this->db->query($sql);
         $cmpny_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cmpny_list); $i++)
         {
            $company_list[$cmpny_list[$i]['ID']] = $cmpny_list[$i]['CompanyName'];
         }

         // assign names to the $jobs data
         for ($i=0; $i<$num_jobs; $i++)
         {
            $job_list[$i]['FilledByName'] = $people_list[($job_list[$i]['FilledBy'])];
            $job_list[$i]['Location'] = $location_list[($job_list[$i]['LocationID'])];
            $job_list[$i]['Category'] = $category_list[($job_list[$i]['CategoryID'])];
            $job_list[$i]['Company'] = $company_list[($job_list[$i]['CompanyID'])];
         }
      }
      
      $this->collector->append_css_file('jobs_adm');
      
      $data['jobs'] = $jobs;
      $data['job_list'] = $job_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('jobs/filled_print', NULL, TRUE);

   }

   // --------------------------------------------------------------------

   /**
    * Generates a printable listing of on-hold jobs
    *
    */
   function print_onhold()
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper('text');
   
      $this->load->database('write');

      $sql = "SELECT * FROM jobs " .
             "WHERE Status = 3";

      $query = $this->db->query($sql);
      $job_list = $query->result_array();

      $num_jobs = count($job_list);

      if ($num_jobs == 0)
      {
         $jobs['job_exists'] = false;
      }
      else
      {
         $jobs['job_exists'] = true;

         $sql = "SELECT * FROM jobs_people";

         $query = $this->db->query($sql);
         $peo_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($peo_list); $i++)
         {
            $people_list[$peo_list[$i]['Username']] = $peo_list[$i]['FirstName'].' '.$peo_list[$i]['LastName'];
         }

         $sql = "SELECT * FROM jobs_location";

         $query = $this->db->query($sql);
         $loc_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($loc_list); $i++)
         {
            $location_list[$loc_list[$i]['ID']] = $loc_list[$i]['LocationName'];
         }

         $sql = "SELECT * FROM jobs_category";

         $query = $this->db->query($sql);
         $cat_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cat_list); $i++)
         {
            $category_list[$cat_list[$i]['ID']] = $cat_list[$i]['CategoryName'];
         }

         $sql = "SELECT * FROM jobs_company";

         $query = $this->db->query($sql);
         $cmpny_list = $query->result_array();

         // restructure the data for easier reference
         for ($i=0; $i<count($cmpny_list); $i++)
         {
            $company_list[$cmpny_list[$i]['ID']] = $cmpny_list[$i]['CompanyName'];
         }

         // assign names to the $jobs data
         for ($i=0; $i<$num_jobs; $i++)
         {
            $job_list[$i]['OnHoldByName'] = $people_list[($job_list[$i]['OnHoldBy'])];
            $job_list[$i]['Location'] = $location_list[($job_list[$i]['LocationID'])];
            $job_list[$i]['Category'] = $category_list[($job_list[$i]['CategoryID'])];
            $job_list[$i]['Company'] = $company_list[($job_list[$i]['CompanyID'])];
         }
      }
      
      $this->collector->append_css_file('jobs_adm');
      
      $data['jobs'] = $jobs;
      $data['job_list'] = $job_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('jobs/onhold_print', NULL, TRUE);

   }

   // --------------------------------------------------------------------

   /**
    * Toggles the job status between published and unpublished (1 and 0)
    *
    */
   function toggle($job_id, $this_action)
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper('url');
      
      $last_action = $this->session->userdata('last_action');

      // detect if page is being refreshed
      if ($this_action > $last_action)
      {
         $this->session->set_userdata('last_action', $this_action + 1);
   
         $this->load->database('write');

         $sql = "SELECT Status FROM jobs ".
                "WHERE ID = ".$job_id;
      
         $query = $this->db->query($sql);
         $row = $query->row_array();

         $new_status = ($row['Status'] == 1) ? 0 : 1;
         
         $sql = "UPDATE jobs ". 
                "SET Status = ".$new_status." ".
                "WHERE ID = ".$job_id;
   
         $this->db->query($sql);
      }

      redirect("jobs/index");
   }
      
   // --------------------------------------------------------------------

   /**
    * Copy existing job to new record
    *
    */
   function copy($job_id) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper('url');

      $this->load->database('write');

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $job = $query->row_array();
      
      unset($job['ID']);
      unset($job['FilledNotes']);
      unset($job['FilledDate']);
      unset($job['FilledBy']);
      unset($job['OnHoldNotes']);
      unset($job['OnHoldDate']);
      unset($job['OnHoldBy']);
      unset($job['RevisedDate']);
      unset($job['RevisedBy']);

      $job['Status'] = 0;

      // generate job number
      $prefix = 'RX'.date('y');
      $sql = 'SELECT JobNum FROM jobs '.
             'WHERE JobNum LIKE "'.$prefix.'%" '.
             'ORDER BY JobNum DESC';
      $query = $this->db->query($sql);
      $row = $query->row_array();
      $nextnum = (int) substr($row['JobNum'], 4) + 1;
      $job['JobNum'] = $prefix.str_pad($nextnum, 4, '0', STR_PAD_LEFT);
      
      $job['SiteID'] = SITE_ID;
      $job['CreatedDate'] = date('Y-m-d H:i:s');
      $job['CreatedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->insert('jobs', $job);
      
      // check if the location is active
      $sql = 'SELECT Status, LocationName FROM jobs_location '.
             'WHERE ID = '.$job['LocationID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_location " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['LocationID'];
         $this->db->query($sql);
         $errors[] = 'Location: '.$row['LocationName'];
      }

      // check if the category is active
      $sql = 'SELECT Status, CategoryName FROM jobs_category '.
             'WHERE ID = '.$job['CategoryID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_category " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['CategoryID'];
         $this->db->query($sql);
         $errors[] = 'Category: '.$row['CategoryName'];
      }

      // check if the company is active
      $sql = 'SELECT Status, CompanyName FROM jobs_company '.
             'WHERE ID = '.$job['CompanyID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_company " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['CompanyID'];
         $this->db->query($sql);
         $errors[] = 'Company: '.$row['CompanyName'];
      }

      if ( ! empty($errors))
      {
         // display an error message if needed
         $jobs_error = "To copy the requested job record, a previously deleted location, category and/or company had to be re-activated. You may want to update the job and re-delete these: ";
         $jobs_error .= "<ul>";
         foreach ($errors AS $error)
         {
            $jobs_error .= "<li>".$error."</li>";
         }
         $jobs_error .= "</ul>";
         $this->session->set_userdata('jobs_error', $jobs_error);
      }
      
      redirect("jobs/index");
   }

   // --------------------------------------------------------------------

   /**
    * Fills job listing
    *
    */
   function fill($job_id, $this_action) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['FilledNotes'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['FilledNotes'] = 'Notes';

      $this->validation->set_fields($fields);

      $this->load->database('write');

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $data['job'] = $query->row_array();
      
      $defaults['FilledNotes'] = entities_to_ascii($data['job']['FilledNotes']);

      $this->validation->set_defaults($defaults);

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');

         $data['job_id'] = $job_id;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('jobs/filled_add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_fill($job_id);
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Fills a job by setting it's status to 2
    *
    */
   function _fill($job_id) 
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['Status'] = 2;
      
      // process the form text (convert special characters and the like)
      $values['FilledNotes'] = ascii_to_entities($values['FilledNotes']);
      
      $values['FilledDate'] = date('Y-m-d H:i:s');
      $values['FilledBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->where('ID', $job_id);
      $this->db->update('jobs', $values);

      redirect("jobs/index");
   }

   // --------------------------------------------------------------------

   /**
    * Puts job listing on hold
    *
    */
   function hold($job_id, $this_action) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['OnHoldNotes'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['OnHoldNotes'] = 'Notes';

      $this->validation->set_fields($fields);

      $this->load->database('write');

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $data['job'] = $query->row_array();
      
      $defaults['OnHoldNotes'] = entities_to_ascii($data['job']['OnHoldNotes']);

      $this->validation->set_defaults($defaults);

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');

         $data['job_id'] = $job_id;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('jobs/onhold_add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_hold($job_id);
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Puts a job on hold by setting it's status to 3
    *
    */
   function _hold($job_id) 
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['Status'] = 3;
      
      // process the form text (convert special characters and the like)
      $values['OnHoldNotes'] = ascii_to_entities($values['OnHoldNotes']);
      
      $values['OnHoldDate'] = date('Y-m-d H:i:s');
      $values['OnHoldBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->where('ID', $job_id);
      $this->db->update('jobs', $values);

      redirect("jobs/index");
   }

   // --------------------------------------------------------------------

   /**
    * Reopens a filled job by setting it's status to 1
    *
    */
   function open($job_id) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper('url');

      $this->load->database('write');

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $job = $query->row_array();

      // update job record
      if ($job['FilledNotes'] != '')
      {
         if (strpos($job['FilledNotes'], "Previous Notes:"))
         {
            $values['FilledNotes'] = str_replace($job['FilledNotes']. "Previous Notes:\n\n", "");
         }
         $values['FilledNotes'] = "\n\nPrevious Notes:\n\n".
            'Filled '.$job['FilledDate'].' by '.
            $job['FilledBy']."\n\n".
            $job['FilledNotes'];
      }
      $values['Status'] = 1;
      $values['FilledDate'] = NULL;
      $values['FilledBy'] = '';
   
      $this->db->where('ID', $job_id);
      $this->db->update('jobs', $values);

      // check if the location is active
      $sql = 'SELECT Status, LocationName FROM jobs_location '.
             'WHERE ID = '.$job['LocationID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_location " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['LocationID'];
         $this->db->query($sql);
         $errors[] = 'Location: '.$row['LocationName'];
      }

      // check if the category is active
      $sql = 'SELECT Status, CategoryName FROM jobs_category '.
             'WHERE ID = '.$job['CategoryID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_category " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['CategoryID'];
         $this->db->query($sql);
         $errors[] = 'Category: '.$row['CategoryName'];
      }

      // check if the company is active
      $sql = 'SELECT Status, CompanyName FROM jobs_company '.
             'WHERE ID = '.$job['CompanyID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_company " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['CompanyID'];
         $this->db->query($sql);
         $errors[] = 'Company: '.$row['CompanyName'];
      }

      if ( ! empty($errors))
      {
         // display an error message if needed
         $jobs_error = "To re-open the requested job record, a previously deleted location, category and/or company had to be re-activated. You may want to update the job and re-delete these: ";
         $jobs_error .= "<ul>";
         foreach ($errors AS $error)
         {
            $jobs_error .= "<li>".$error."</li>";
         }
         $jobs_error .= "</ul>";
         $this->session->set_userdata('jobs_error', $jobs_error);
      }

      redirect("jobs/index");
   }

   // --------------------------------------------------------------------

   /**
    * Reactivates an on-hold job by setting it's status to 1
    *
    */
   function reactivate($job_id) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper('url');

      $this->load->database('write');

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $job = $query->row_array();

      // update job record
      if ($job['OnHoldNotes'] != '')
      {
         if (strpos($job['OnHoldNotes'], "Previous Notes:"))
         {
            $values['OnHoldNotes'] = str_replace($job['OnHoldNotes']. "Previous Notes:\n\n", "");
         }
         $values['OnHoldNotes'] = "\n\nPrevious Notes:\n\n".
            'Filled '.$job['OnHoldDate'].' by '.
            $job['OnHoldBy']."\n\n".
            $job['OnHoldNotes'];
      }
      $values['Status'] = 1;
      $values['OnHoldDate'] = NULL;
      $values['OnHoldBy'] = '';
   
      $this->db->where('ID', $job_id);
      $this->db->update('jobs', $values);

      // check if the location is active
      $sql = 'SELECT Status, LocationName FROM jobs_location '.
             'WHERE ID = '.$job['LocationID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_location " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['LocationID'];
         $this->db->query($sql);
         $errors[] = 'Location: '.$row['LocationName'];
      }

      // check if the category is active
      $sql = 'SELECT Status, CategoryName FROM jobs_category '.
             'WHERE ID = '.$job['CategoryID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_category " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['CategoryID'];
         $this->db->query($sql);
         $errors[] = 'Category: '.$row['CategoryName'];
      }

      // check if the company is active
      $sql = 'SELECT Status, CompanyName FROM jobs_company '.
             'WHERE ID = '.$job['CompanyID'];
      $query = $this->db->query($sql);
      $row = $query->row_array();
      if ($row['Status'] == 2)
      {
         // re-open this location
         $sql = "UPDATE jobs_company " . 
                "SET Status = 1 " .
                "WHERE ID = ".$job['CompanyID'];
         $this->db->query($sql);
         $errors[] = 'Company: '.$row['CompanyName'];
      }

      if ( ! empty($errors))
      {
         // display an error message if needed
         $jobs_error = "To reactivate the requested job record, a previously deleted location, category and/or company had to be re-activated. You may want to update the job and re-delete these: ";
         $jobs_error .= "<ul>";
         foreach ($errors AS $error)
         {
            $jobs_error .= "<li>".$error."</li>";
         }
         $jobs_error .= "</ul>";
         $this->session->set_userdata('jobs_error', $jobs_error);
      }

      redirect("jobs/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Displays a filled job listing
    *
    */
   function view_filled($job_id, $this_action) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['LocationID'] = 'trim';
      $rules['CategoryID'] = 'trim';
      $rules['CompanyID'] = 'trim';
      $rules['Title'] = 'trim';
      $rules['Summary'] = 'trim';
      $rules['Description'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';
      $fields['CompanyID'] = 'Company';
      $fields['JobNum'] = 'Job No.';
      $fields['Title'] = 'Job Title';
      $fields['FilledDate'] = 'Filled Date';
      $fields['FilledByName'] = 'Filled By';
      $fields['FilledNotes'] = 'Filled Notes';
      $fields['Summary'] = 'Summary';
      $fields['Description'] = 'Description';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['Summary'] = entities_to_ascii($defaults['Summary']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      $defaults['FilledNotes'] = entities_to_ascii($defaults['FilledNotes']);
      $defaults['FilledDate'] = date('d M Y', strtotime($defaults['FilledDate']));

      // look up filler's name
      $sql = 'SELECT FirstName, LastName FROM jobs_people '.
             'WHERE Username = \''.$defaults['FilledBy'].'\' ';
      $query = $this->db->query($sql);
      $filler = $query->row_array();
      
      $defaults['FilledByName'] = $filler['FirstName'].' '.$filler['LastName'];

      $this->validation->set_defaults($defaults);
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
      
         $this->load->database('write');

         // Locations
         $sql = 'SELECT ID, LocationName '.
                'FROM jobs_location '.
                'WHERE Country = \'United States\' '.
                'ORDER BY LocationName';

         $query = $this->db->query($sql);
         $locations = $query->result_array();
         
         $data['locations'] = array(''=>'');
         for ($i=0; $i<count($locations); $i++)
         {
            $data['locations'][$locations[$i]['ID']] = $locations[$i]['LocationName'];
         }
      
         // Categories
         $sql = 'SELECT ID, CategoryName '.
                'FROM jobs_category '.
                'ORDER BY CategoryName';

         $query = $this->db->query($sql);
         $categories = $query->result_array();
         
         $data['categories'] = array(''=>'');
         for ($i=0; $i<count($categories); $i++)
         {
            $data['categories'][$categories[$i]['ID']] = $categories[$i]['CategoryName'];
         }

         // Companies
         $sql = 'SELECT ID, CompanyName '.
                'FROM jobs_company '.
                'ORDER BY CompanyName';

         $query = $this->db->query($sql);
         $companies = $query->result_array();
         
         $data['companies'] = array(''=>'');
         for ($i=0; $i<count($companies); $i++)
         {
            $data['companies'][$companies[$i]['ID']] = $companies[$i]['CompanyName'];
         }
         
         $data['job_id'] = $job_id;
         $data['job_num'] = $defaults['JobNum'];
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('jobs/filled_view', NULL, TRUE);
      }
      else
      {
         redirect('jobs/filled');
      }
   }

   // --------------------------------------------------------------------
   
   /**
    * Displays an on-hold job listing
    *
    */
   function view_onhold($job_id, $this_action) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['LocationID'] = 'trim';
      $rules['CategoryID'] = 'trim';
      $rules['CompanyID'] = 'trim';
      $rules['Title'] = 'trim';
      $rules['Summary'] = 'trim';
      $rules['Description'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';
      $fields['CompanyID'] = 'Company';
      $fields['JobNum'] = 'Job No.';
      $fields['Title'] = 'Job Title';
      $fields['OnHoldDate'] = 'On-Hold Date';
      $fields['OnHoldByName'] = 'On-Hold By';
      $fields['OnHoldNotes'] = 'On-Hold Notes';
      $fields['Summary'] = 'Summary';
      $fields['Description'] = 'Description';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs '.
             'WHERE ID = '.$job_id.' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['Summary'] = entities_to_ascii($defaults['Summary']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      $defaults['OnHoldNotes'] = entities_to_ascii($defaults['OnHoldNotes']);
      $defaults['OnHoldDate'] = date('d M Y', strtotime($defaults['OnHoldDate']));

      // look up on-holder's name
      $sql = 'SELECT FirstName, LastName FROM jobs_people '.
             'WHERE Username = \''.$defaults['OnHoldBy'].'\' ';
      $query = $this->db->query($sql);
      $onholder = $query->row_array();
      
      $defaults['OnHoldByName'] = $onholder['FirstName'].' '.$onholder['LastName'];

      $this->validation->set_defaults($defaults);
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
      
         $this->load->database('write');

         // Locations
         $sql = 'SELECT ID, LocationName '.
                'FROM jobs_location '.
                'WHERE Country = \'United States\' '.
                'ORDER BY LocationName';

         $query = $this->db->query($sql);
         $locations = $query->result_array();
         
         $data['locations'] = array(''=>'');
         for ($i=0; $i<count($locations); $i++)
         {
            $data['locations'][$locations[$i]['ID']] = $locations[$i]['LocationName'];
         }
      
         // Categories
         $sql = 'SELECT ID, CategoryName '.
                'FROM jobs_category '.
                'ORDER BY CategoryName';

         $query = $this->db->query($sql);
         $categories = $query->result_array();
         
         $data['categories'] = array(''=>'');
         for ($i=0; $i<count($categories); $i++)
         {
            $data['categories'][$categories[$i]['ID']] = $categories[$i]['CategoryName'];
         }

         // Companies
         $sql = 'SELECT ID, CompanyName '.
                'FROM jobs_company '.
                'ORDER BY CompanyName';

         $query = $this->db->query($sql);
         $companies = $query->result_array();
         
         $data['companies'] = array(''=>'');
         for ($i=0; $i<count($companies); $i++)
         {
            $data['companies'][$companies[$i]['ID']] = $companies[$i]['CompanyName'];
         }
         
         $data['job_id'] = $job_id;
         $data['job_num'] = $defaults['JobNum'];
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('jobs/onhold_view', NULL, TRUE);
      }
      else
      {
         redirect('jobs/onhold');
      }
   }

   // --------------------------------------------------------------------

   /**
    * Adds a job listing
    *
    */
   function add($this_action) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      $this->load->model('Jobs_location');
      $this->load->model('Jobs_category');
      $this->load->model('Jobs_company');
      $this->load->library('validation');
      
      $rules['LocationID'] = 'required';
      $rules['CategoryID'] = 'trim|required';
      $rules['CompanyID'] = 'trim|required';
      $rules['Title'] = 'trim|required';
      $rules['Manager'] = 'trim|required';
      $rules['Summary'] = 'trim|required';
      $rules['Description'] = 'trim|required';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';
      $fields['CompanyID'] = 'Company';
      $fields['JobNum'] = 'Job No.';
      $fields['Title'] = 'Job Title';
      $fields['Manager'] = 'Manager';
      $fields['Summary'] = 'Summary';
      $fields['Description'] = 'Description';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults['Status'] = '1';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['locations'] = $this->Jobs_location->get_location_list();
         $data['categories'] = $this->Jobs_category->get_category_list();
         $data['companies'] = $this->Jobs_company->get_company_list();         
         $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
      
         $this->load->vars($data);
   	
         return $this->load->view('jobs/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_add();
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add job form
    *
    */
   function _add()
   {
      $this->load->model('Jobs');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      if ($values['Status'] == '')
         $values['Status'] = 0;

      $values['JobNum'] = $this->Jobs->generate_job_number();
      
      // process the form text (convert special characters and the like)
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['Summary'] = ascii_to_entities($values['Summary']);
      $values['Description'] = ascii_to_entities($values['Description']);
      
      $values['SiteID'] = SITE_ID;
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->Jobs->insert_job($values);

      redirect("jobs/index");
   }

   // --------------------------------------------------------------------

   /**
    * Updates a job listing
    *
    */
   function edit($job_id, $this_action) 
   {
      $this->Jobs_people->check('Jobs');
      
      $this->load->helper(array('form', 'url', 'text'));
      $this->load->model('Jobs');
      $this->load->model('Jobs_location');
      $this->load->model('Jobs_category');
      $this->load->model('Jobs_company');
      $this->load->library('validation');
      
      $rules['LocationID'] = 'trim|required';
      $rules['CategoryID'] = 'trim|required';
      $rules['CompanyID'] = 'trim|required';
      $rules['Title'] = 'trim|required';
      $rules['Manager'] = 'trim|required';
      $rules['Summary'] = 'trim|required';
      $rules['Description'] = 'trim|required';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['LocationID'] = 'Location';
      $fields['CategoryID'] = 'Category';
      $fields['CompanyID'] = 'Company';
      $fields['JobNum'] = 'Job No.';
      $fields['Title'] = 'Job Title';
      $fields['Manager'] = 'Manager';
      $fields['Summary'] = 'Summary';
      $fields['Description'] = 'Description';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults = $this->Jobs->get_job_data($job_id);
      
      $defaults['Title'] = entities_to_ascii($defaults['Title']);
      $defaults['Summary'] = entities_to_ascii($defaults['Summary']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Jobs');
      
         $this->load->database('write');

         $data['locations'] = $this->Jobs_location->get_location_list();
         $data['categories'] = $this->Jobs_category->get_category_list();
         $data['companies'] = $this->Jobs_company->get_company_list();         
         $data['job_id'] = $job_id;
         $data['job_num'] = $defaults['JobNum'];
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('jobs/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($job_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit job form
    *
    */
   function _edit($job_id)
   {
      $fields = $this->validation->_fields;
      unset($fields['JobNum']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      if ($values['Status'] == '')
         $values['Status'] = 0;
      
      // process the form text (convert special characters and the like)
      $values['Title'] = ascii_to_entities($values['Title']);
      $values['Summary'] = ascii_to_entities($values['Summary']);
      $values['Description'] = ascii_to_entities($values['Description']);
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->Jobs->update_job($job_id, $values);

      redirect("jobs/index");
   }

}

?>