<?php

class Companies extends Controller {

   function Companies()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of job companies
    *
    */
   function index() 
   {
      $this->Jobs_people->check('Companies');
      
      $company['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');

      $this->load->database('write');

      $sql = "SELECT * FROM jobs_company " .
             "WHERE Status <= 1 ".
             "ORDER BY CompanyName";
      
      $query = $this->db->query($sql);
      $company_list = $query->result_array();

      $num_biz = count($company_list);
      $company['company_exists'] = ($num_biz == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('jobs_adm');

      $data['tabs'] = $this->Jobs_people->get_tabs('Companies');
      $data['company'] = $company;
      $data['company_list'] = $company_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('companies/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Deletes a company
    *
    */
   function delete($cmpny_id) 
   {
      $this->Jobs_people->check('Companies');
      
      $this->load->helper('url');

      $this->load->database('write');
      
      $sql = "SELECT * FROM jobs ".
             "WHERE CompanyID = ".$cmpny_id." ".
             "AND Status <= 1";

      $query = $this->db->query($sql);
      $probs = $query->result_array();
   
      if (count($probs) == 0)
      {
         $sql = "UPDATE jobs_company " . 
                "SET Status = 2 " .
                "WHERE ID = ".$cmpny_id;

         $this->db->query($sql);
      }
      else
      {
         $sql = "SELECT CompanyName FROM jobs_company " .
                "WHERE ID = ".$cmpny_id;
      
         $query = $this->db->query($sql);
         $row = $query->row_array();
   
         $this->session->set_userdata('jobs_error', "You cannot delete the company \"".$row['CompanyName']."\" because there are active jobs that use it.<br>Please return to the Jobs tab and change those jobs to another company.");
      }
      redirect("companies/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a company entry
    *
    */
   function add($this_action) 
   {
      $this->Jobs_people->check('Companies');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['CompanyName'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['CompanyName'] = 'Company Name';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Companies');
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('companies/add', NULL, TRUE);
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
    * Processes the add company form
    *
    */
   function _add()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      if ($values['Status'] == '')
         $values['Status'] = 1;
      
      // process the form text (convert special characters and the like)
      $values['CompanyName'] = ascii_to_entities($values['CompanyName']);
      
      $values['SiteID'] = SITE_ID;
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->insert('jobs_company', $values);

      redirect("companies/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a company entry
    *
    */
   function edit($cmpny_id, $this_action) 
   {
      $this->Jobs_people->check('Companies');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['CompanyName'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['CompanyName'] = 'Company Name';

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs_company '.
             'WHERE ID = '.$cmpny_id.' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();

      $defaults['CompanyName'] = entities_to_ascii($defaults['CompanyName']);

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Companies');
         $data['cmpny_id'] = $cmpny_id;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('companies/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($cmpny_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit company form
    *
    */
   function _edit($cmpny_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['CompanyName'] = ascii_to_entities($values['CompanyName']);
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->where('ID', $cmpny_id);
      $this->db->update('jobs_company', $values);

      redirect("companies/index");
   }


}

?>