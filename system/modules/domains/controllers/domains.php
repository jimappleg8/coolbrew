<?php

class Domains extends Controller {

//   var $aco = array();

   function Domains()
   {
      parent::Controller();
      $this->load->library('session');

//      $options = array('db' => 'read', 'prefix' => 'adm');
//      $this->load->library('tacl', $options);

      $this->load->library('administrator', array('module_id' => 'domains'));
      $this->load->helper('url');
      
//      include APPPATH().'/config/acl_admin.php';
//      $this->aco['adm'] = $acl_admin;

//      include APPPATH().'/config/acl_sites.php';
//      $this->aco['sites'] = $acl_sites;
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of domains
    *
    * TODO: add site ID to session so we don't lose it on edit
    */
   function index($site_id = '')
   {
      $this->administrator->check_login();
      
      $admin['message'] = $this->session->userdata('domain_message');
      if ($this->session->userdata('domain_message') != '')
         $this->session->set_userdata('domain_message', '');

      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Adm_site_domains');
      $this->load->model('Settings');
      
      $domains = $this->Adm_site_domains->get_domains_by_primary($site_id);
      
      $data['total_domains'] = $this->Adm_site_domains->get_domain_count($site_id);
      
      $admin['site_exists'] = (count($domains) == 0) ? FALSE : TRUE;
      
      $data['domains'] = $domains;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('domains');
      
      $data['site_id'] = $site_id;
      $data['tabs'] = $this->administrator->get_main_tabs('Domains');
      $data['admin'] = $admin;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['primary_registrar'] = $this->Settings->get_primary_registrar();
      $data['primary_dns_vendor'] = $this->Settings->get_primary_dns_vendor();
      
      $this->load->vars($data);
   	
      return $this->load->view('domains/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Generates report about all domains
    *
    */
   function report() 
   {
      $this->administrator->check_login();
      
      $this->load->model('Adm_site_domains');
      $this->load->model('Settings');
      
      $domains = $this->Adm_site_domains->get_domains_by_brand();
      
      $data['total_domains'] = $this->Adm_site_domains->get_domain_count();
      $data['domains'] = $domains;
      $data['primary_registrar'] = $this->Settings->get_primary_registrar();
      $data['primary_dns_vendor'] = $this->Settings->get_primary_dns_vendor();

      $this->load->vars($data);
   	
      return $this->load->view('domains/report', NULL, TRUE);

   }

   // --------------------------------------------------------------------

   /**
    * Deletes a domain
    *
    */
   function delete($domain_id)
   {
      $this->load->model('Adm_site_domains');
      
      $old_values = $this->Adm_site_domains->get_domain_data($domain_id);
      
      // delete the main domain record
      $this->Adm_site_domains->delete_site_domain($domain_id, $old_values);

      $this->session->set_userdata('domain_message', $old_values['Domain'].' has been deleted.');

      redirect("domains/index");
   }

   // --------------------------------------------------------------------

   /**
    * Adds a domain listing.
    *
    */
   function add($this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      
      $this->load->library('validation');
      
      $this->load->model('Sites');
      $this->load->model('Vendors');
      $this->load->model('Adm_site_domains');
      $this->load->model('Settings');
      
      $rules['Domain'] = 'trim|required';
      $rules['SiteID'] = 'trim|required';
      $rules['NotRegistered'] = 'trim';
      $rules['RegistrarVendor'] = 'trim';
      $rules['RegistrarName'] = 'trim';
      $rules['DNSVendor'] = 'trim';
      $rules['PrimaryDNSIsSetUp'] = 'trim';
      $rules['RegistrarShouldBePrimary'] = 'trim';
      $rules['DNSShouldBePrimary'] = 'trim';
      $rules['Notes'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Domain'] = 'Domain Name';
      $fields['SiteID'] = 'Site ID';
      $fields['NotRegistered'] = 'Not Registered';
      $fields['RegistrarVendor'] = 'Registrar Vendor';
      $fields['RegistrarName'] = 'Registrar Name';
      $fields['DNSVendor'] = 'DNS Vendor';
      $fields['DNSName'] = 'DNS Name';
      $fields['PrimaryDNSIsSetUp'] = 'Primary DNS is Set Up';
      $fields['RegistrarShouldBePrimary'] = 'Registrar Should Be Primary';
      $fields['DNSShouldBePrimary'] = 'DNS Should Be Primary';
      $fields['Notes'] = 'Notes';

      $this->validation->set_fields($fields);

      $defaults['NotRegistered'] = 0;
      $defaults['RegistrarShouldBePrimary'] = 1;
      $defaults['DNSShouldBePrimary'] = 1;
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('domains');

         $data['tabs'] = $this->administrator->get_main_tabs('Domains');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         
         $data['sites'] = $this->Sites->get_sites_list();
         $data['vendors'] = $this->Vendors->get_vendors_list();
         $data['primary_registrar'] = $this->Settings->get_primary_registrar();
         $data['primary_dns_vendor'] = $this->Settings->get_primary_dns_vendor();
      
         $this->load->vars($data);
   	
         return $this->load->view('domains/add', NULL, TRUE);
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
    * Processes the add domain form
    *
    */
   function _add()
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      if (isset($values['DNSName']) && $values['DNSName'] != "")
      {
         // create a new vendor record
         $vendor['VendorName'] = $values['DNSName'];
         $vendor['CreatedDate'] = date('Y-m-d H:i:s');
         $vendor['CreatedBy'] = $this->session->userdata('username');
         $this->db->insert('adm_vendor', $vendor);
         
         $values['DNSVendor'] = $this->db->insert_id();
      }
      unset($values['DNSName']);
      
      if (isset($values['RegistrarName']) && $values['RegistrarName'] != "")
      {
         // create a new vendor record
         $vendor['VendorName'] = $values['RegistrarName'];
         $vendor['CreatedDate'] = date('Y-m-d H:i:s');
         $vendor['CreatedBy'] = $this->session->userdata('username');
         $this->db->insert('adm_vendor', $vendor);
         
         $values['RegistrarVendor'] = $this->db->insert_id();
      }
      unset($values['RegistrarName']);
      
      $values['PrimaryDomain'] = 0;
      $values['Notes'] = ascii_to_entities($values['Notes']);;
      
      $this->Adm_site_domains->insert_site_domain($values);

      $this->session->set_userdata('domain_message', $values['Domain'].' has been added.');

      redirect("domains/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a domain listing.
    *
    */
   function edit($domain_id, $this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('domain_message');
      if ($this->session->userdata('domain_message') != '')
         $this->session->set_userdata('domain_message', '');

      $admin['group'] = $this->session->userdata('group');

      $this->load->helper(array('form', 'text'));
      
      $this->load->library('validation');
      
      $this->load->model('Sites');
      $this->load->model('Adm_site_domains');
      $this->load->model('Vendors');
      $this->load->model('Settings');
      $domain = $this->Adm_site_domains->get_domain_data($domain_id);
      
      $rules['Domain'] = 'trim|required';
      $rules['SiteID'] = 'trim|required';
      $rules['NotRegistered'] = 'trim';
      $rules['RegistrarVendor'] = 'trim';
      $rules['RegistrarName'] = 'trim';
      $rules['DNSVendor'] = 'trim';
      $rules['DNSName'] = 'trim';
      $rules['PrimaryDNSIsSetUp'] = 'trim';
      $rules['RegistrarShouldBePrimary'] = 'trim';
      $rules['DNSShouldBePrimary'] = 'trim';
      $rules['Notes'] = 'trim';
      $rules['Brand'] = 'trim';
      $rules['Extension'] = 'trim';
      $rules['Country'] = 'trim';
      $rules['RegistrationDate'] = 'trim';
      $rules['RegistryExpiryDate'] = 'trim';
      $rules['PaidUntilDate'] = 'trim';
      $rules['BusinessUnit'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['DNSType'] = 'trim';
      $rules['TransferLock'] = 'trim';
      $rules['RegProfileName'] = 'trim';
      $rules['RegFirstName'] = 'trim';
      $rules['RegLastName'] = 'trim';
      $rules['RegOrganization'] = 'trim';
      $rules['RegAddress'] = 'trim';
      $rules['RegAddress2'] = 'trim';
      $rules['RegCity'] = 'trim';
      $rules['RegStateProvince'] = 'trim';
      $rules['RegPostalCode'] = 'trim';
      $rules['RegCountry'] = 'trim';
      $rules['RegEmail'] = 'trim';
      $rules['RegPhone'] = 'trim';
      $rules['RegFax'] = 'trim';
      $rules['AdminProfileName'] = 'trim';
      $rules['AdminFirstName'] = 'trim';
      $rules['AdminLastName'] = 'trim';
      $rules['AdminOrganization'] = 'trim';
      $rules['AdminAddress'] = 'trim';
      $rules['AdminAddress2'] = 'trim';
      $rules['AdminCity'] = 'trim';
      $rules['AdminStateProvince'] = 'trim';
      $rules['AdminPostalCode'] = 'trim';
      $rules['AdminCountry'] = 'trim';
      $rules['AdminEmail'] = 'trim';
      $rules['AdminPhone'] = 'trim';
      $rules['AdminFax'] = 'trim';
      $rules['TechProfileName'] = 'trim';
      $rules['TechFirstName'] = 'trim';
      $rules['TechLastName'] = 'trim';
      $rules['TechOrganization'] = 'trim';
      $rules['TechAddress'] = 'trim';
      $rules['TechAddress2'] = 'trim';
      $rules['TechCity'] = 'trim';
      $rules['TechStateProvince'] = 'trim';
      $rules['TechPostalCode'] = 'trim';
      $rules['TechCountry'] = 'trim';
      $rules['TechEmail'] = 'trim';
      $rules['TechPhone'] = 'trim';
      $rules['TechFax'] = 'trim';
      $rules['IDNTranslation'] = 'trim';
      $rules['LocalLanguage'] = 'trim';
      $rules['DNS1'] = 'trim';
      $rules['DNS2'] = 'trim';
      $rules['DNS3'] = 'trim';
      $rules['DNS4'] = 'trim';
      $rules['Field1'] = 'trim';
      $rules['Field2'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Domain'] = 'Domain Name';
      $fields['SiteID'] = 'Site ID';
      $fields['NotRegistered'] = 'Not Registered';
      $fields['RegistrarVendor'] = 'Registrar Vendor';
      $fields['RegistrarName'] = 'Registrar Name';
      $fields['DNSVendor'] = 'DNS Vendor';
      $fields['DNSName'] = 'DNS Name';
      $fields['PrimaryDNSIsSetUp'] = 'Primary DNS is Set Up';
      $fields['RegistrarShouldBePrimary'] = 'Registrar Should Be Primary';
      $fields['DNSShouldBePrimary'] = 'DNS Should Be Primary';
      $fields['Notes'] = 'Notes';
      $fields['Brand'] = 'Brand';
      $fields['Extension'] = 'Extension';
      $fields['Country'] = 'Country';
      $fields['RegistrationDate'] = 'Registration Date';
      $fields['RegistryExpiryDate'] = 'Registry Expiry Date';
      $fields['PaidUntilDate'] = 'Paid Until Date';
      $fields['BusinessUnit'] = 'Business Unit';
      $fields['Status'] = 'Status';
      $fields['DNSType'] = 'DNS Type';
      $fields['TransferLock'] = 'Transfer Lock';
      $fields['RegProfileName'] = 'Reg Profile Name';
      $fields['RegFirstName'] = 'Reg First Name';
      $fields['RegLastName'] = 'Reg Last Name';
      $fields['RegOrganization'] = 'Reg Organization';
      $fields['RegAddress'] = 'Reg Address';
      $fields['RegAddress2'] = 'Reg Address 2';
      $fields['RegCity'] = 'Reg City';
      $fields['RegStateProvince'] = 'Reg State Province';
      $fields['RegPostalCode'] = 'Reg Postal Code';
      $fields['RegCountry'] = 'Reg Country';
      $fields['RegEmail'] = 'Reg Email';
      $fields['RegPhone'] = 'Reg Phone';
      $fields['RegFax'] = 'Reg Fax';
      $fields['AdminProfileName'] = 'Admin Profile Name';
      $fields['AdminFirstName'] = 'Admin First Name';
      $fields['AdminLastName'] = 'Admin Last Name';
      $fields['AdminOrganization'] = 'Admin Organization';
      $fields['AdminAddress'] = 'Admin Address';
      $fields['AdminAddress2'] = 'Admin Address 2';
      $fields['AdminCity'] = 'Admin City';
      $fields['AdminStateProvince'] = 'Admin State Province';
      $fields['AdminPostalCode'] = 'Admin Postal Code';
      $fields['AdminCountry'] = 'Admin Country';
      $fields['AdminEmail'] = 'Admin Email';
      $fields['AdminPhone'] = 'Admin Phone';
      $fields['AdminFax'] = 'Admin Fax';
      $fields['TechProfileName'] = 'Tech Profile Name';
      $fields['TechFirstName'] = 'Tech First Name';
      $fields['TechLastName'] = 'Tech Last Name';
      $fields['TechOrganization'] = 'Tech Organization';
      $fields['TechAddress'] = 'Tech Address';
      $fields['TechAddress2'] = 'Tech Address 2';
      $fields['TechCity'] = 'Tech City';
      $fields['TechStateProvince'] = 'Tech State Province';
      $fields['TechPostalCode'] = 'Tech Postal Code';
      $fields['TechCountry'] = 'Tech Country';
      $fields['TechEmail'] = 'Tech Email';
      $fields['TechPhone'] = 'Tech Phone';
      $fields['TechFax'] = 'Tech Fax';
      $fields['IDNTranslation'] = 'IDN Translation';
      $fields['LocalLanguage'] = 'Local Language';
      $fields['DNS1'] = 'DNS 1';
      $fields['DNS2'] = 'DNS 2';
      $fields['DNS3'] = 'DNS 3';
      $fields['DNS4'] = 'DNS 4';
      $fields['Field1'] = 'Field 1';
      $fields['Field2'] = 'Field 2';

      $this->validation->set_fields($fields);

      $defaults = $domain;
      $defaults['Notes'] = entities_to_ascii($defaults['Notes']);;

      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('domains');

         $data['admin'] = $admin;
         $data['domain'] = $domain;
         $data['tabs'] = $this->administrator->get_main_tabs('Domains');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         
         $data['sites'] = $this->Sites->get_sites_list();
         $data['vendors'] = $this->Vendors->get_vendors_list();
         $data['primary_registrar'] = $this->Settings->get_primary_registrar();
         $data['primary_dns_vendor'] = $this->Settings->get_primary_dns_vendor();
      
         $this->load->vars($data);
   	
         return $this->load->view('domains/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($domain_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a domain record
    *
    */
   function _edit($domain_id)
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      if ($domain_id == 0)
      {
         show_error('_edit_domain requires that a domain ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      if (isset($values['DNSName']) && $values['DNSName'] != "")
      {
         // create a new vendor record
         $vendor['VendorName'] = $values['DNSName'];
         $vendor['CreatedDate'] = date('Y-m-d H:i:s');
         $vendor['CreatedBy'] = $this->session->userdata('username');
         $this->db->insert('adm_vendor', $vendor);
         
         $values['DNSVendor'] = $this->db->insert_id();
      }
      unset($values['DNSName']);
      
      if (isset($values['RegistrarName']) && $values['RegistrarName'] != "")
      {
         // create a new vendor record
         $vendor['VendorName'] = $values['RegistrarName'];
         $vendor['CreatedDate'] = date('Y-m-d H:i:s');
         $vendor['CreatedBy'] = $this->session->userdata('username');
         $this->db->insert('adm_vendor', $vendor);
         
         $values['RegistrarVendor'] = $this->db->insert_id();
      }
      unset($values['RegistrarName']);

      $values['Notes'] = ascii_to_entities($values['Notes']);;

//      echo "<pre>"; print_r($values); echo "</pre>";
      
      $this->load->database('read');
      
      $this->db->where('ID', $domain_id);
      $this->db->update('adm_site_domain', $values);

      $this->session->set_userdata('domain_message', $values['Domain'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('domains/edit/'.$domain_id.'/'.$last_action.'/');
   }
   
}
?>