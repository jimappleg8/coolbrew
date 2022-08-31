<?php

class Companies extends Controller {

   var $aco = array();

   function Companies()
   {
      parent::Controller();
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper('url');
      
      include APPPATH().'/config/acl_admin.php';
      $this->aco['adm'] = $acl_admin;

      include APPPATH().'/config/acl_sites.php';
      $this->aco['sites'] = $acl_sites;
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of people
    *
    */
   function index() 
   {
      // this should redirect to the people list
      header('Location:'.site_url('cp/people/index'));
   }

   // --------------------------------------------------------------------
   
   /**
    * Deletes a company
    *
    */
   function delete($company_id, $this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->model('Company');
      
      // first check if there are any people associated with the company
      if ($this->Company->people_assigned_to_company($company_id))
      {
         $this->session->set_userdata('people_message', 'The company "'.$values['CompanyName'].'" could not be deleted because people are still assigned to it. Delete or move the people and try again.');

         redirect("cp/people/index");
      }
      else
      {
         $old_values = $this->Company->get_company_data($company_id);
         
         $this->Company->delete_company($company_id, $old_values);

         $this->session->set_userdata('people_message', 'The company "'.$old_values['CompanyName'].'" has been deleted.');
      
         redirect("cp/people/index");
      }
   }

   // --------------------------------------------------------------------

   /**
    * Adds a company entry
    *
    */
   function add($this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->model('People');
      $this->load->model('Company');
      $this->load->library('validation');
      
      $rules['CompanyName'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['CompanyName'] = 'Company Name';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['tabs'] = $this->administrator->get_main_tabs('All People');
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('cp/companies/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
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
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['CompanyName'] = ascii_to_entities($values['CompanyName']);

      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $id = $this->Company->insert_company($values);

      $this->session->set_userdata('people_message', 'The company "'.$values['CompanyName'].'" has been added.');

      redirect('cp/people/index#client_'.$id);
   }
   
   // --------------------------------------------------------------------

   /**
    * Updates a company entry
    *
    */
   function edit($company_id, $this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text', 'date', 'pulldown'));
      $this->load->model('Company');
      $this->load->library('validation');
      
      $company = $this->Company->get_company_data($company_id);
      
      $rules['CompanyName'] = 'trim|required';
      $rules['Address1'] = 'trim';
      $rules['Address2'] = 'trim';
      $rules['City'] = 'trim';
      $rules['State'] = 'trim';
      $rules['Zip'] = 'trim';
      $rules['Country'] = 'trim';
      $rules['TimeZone'] = 'trim';
      $rules['WebAddress'] = 'trim';
      $rules['OfficePhone'] = 'trim';
      $rules['FaxPhone'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['CompanyName'] = 'First Name';
      $fields['Address1'] = 'Address 1';
      $fields['Address2'] = 'Address 2';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Zip'] = 'Zip';
      $fields['Country'] = 'Country';
      $fields['TimeZone'] = 'TimeZone';
      $fields['WebAddress'] = 'Web Address';
      $fields['OfficePhone'] = 'Office Phone';
      $fields['FaxPhone'] = 'Fax Phone';

      $this->validation->set_fields($fields);

      $defaults = $company;
      $defaults['CompanyName'] = entities_to_ascii($defaults['CompanyName']);
      $defaults['Address1'] = entities_to_ascii($defaults['Address1']);
      $defaults['Address2'] = entities_to_ascii($defaults['Address2']);
      $defaults['City'] = entities_to_ascii($defaults['City']);
      $defaults['WebAddress'] = entities_to_ascii($defaults['WebAddress']);
         
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('All People');
         $data['company_id'] = $company_id;
         $data['company'] = $company;
         $data['people_assigned'] = $this->Company->people_assigned_to_company($company_id);

         $this->load->vars($data);
   	
         return $this->load->view('cp/companies/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($company_id, $company);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit company form
    *
    */
   function _edit($company_id, $old_values)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      if ($company_id == '')
      {
         show_error('companies/_edit requires that a company ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['CompanyName'] = ascii_to_entities($values['CompanyName']);
      $values['Address1'] = ascii_to_entities($values['Address1']);
      $values['Address2'] = ascii_to_entities($values['Address2']);
      $values['City'] = ascii_to_entities($values['City']);
      $values['WebAddress'] = ascii_to_entities($values['WebAddress']);

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Company->update_company($company_id, $values, $old_values);

      $this->session->set_userdata('people_message', 'The company "'.$values['CompanyName'].'" has been updated.');

      redirect('cp/people/index#client_'.$company_id);
   }
   
}
?>