<?php

class Companies extends Controller {

   var $aco = array();

   function Companies()
   {
      parent::Controller();
      $this->load->library('session');

      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->load->library('tacl', $options);

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
   function index($site_id) 
   {
      // this should redirect to the people list
      header('Location:'.site_url('sites/people/index/'.$site_id.'/'));
   }

   // --------------------------------------------------------------------

   /**
    * Updates a company entry
    *
    */
   function edit($site_id, $company_id, $this_action) 
   {
      if ( ! $this->administrator->acl_check('adm-company', 'edit'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text', 'date', 'pulldown'));
      $this->load->model('Company');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $company = $this->Company->get_company_data($company_id);
      $site = $this->Sites->get_site_data($site_id);
      
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
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'People &amp; Permissions');
         $data['site_id'] = $site_id;
         $data['company_id'] = $company_id;
         $data['company'] = $company;
         $data['site'] = $site;

         $this->load->vars($data);
   	
         return $this->load->view('sites/companies/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $company_id, $company);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit company form
    *
    */
   function _edit($site_id, $company_id, $old_values)
   {
      if ( ! $this->administrator->acl_check('adm-company', 'edit'))
         redirect('cp/login/sorry');

      if ($company_id == '')
      {
         show_error('_edit_company requires that a company ID be supplied.');
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
      
      redirect('sites/people/index/'.$site_id.'/');
   }
   
}
?>