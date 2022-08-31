<?php

class Vendors extends Controller {

   var $aco = array();

   function Vendors()
   {
      parent::Controller();
      $this->load->library('session');
      
      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->load->library('tacl', $options);

      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper(array('url', 'menu', 'text'));
   }

   // --------------------------------------------------------------------

   /**
    * Generates a listing of all vendors
    *
    */
   function index()
   {
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');
   
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      $this->load->model('Site_domains');
      
      $service_list = $this->Vendor_services->get_service_tree();
      $vendor_list = $this->Vendors->get_vendors_in_service();
      $registrar_list = $this->Site_domains->get_registrar_vendors();
      $dns_list = $this->Site_domains->get_dns_vendors();
      
      $admin['vendor_exists'] = (count($vendor_list) == 0 && count($nocats_list) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
      $data['submenu'] = get_vendors_submenu('Vendors');
      $data['service_list'] = $service_list;
      $data['vendor_list'] = $vendor_list;
      $data['registrar_list'] = $registrar_list;
      $data['dns_list'] = $dns_list;
      $data['admin'] = $admin;

      $this->load->vars($data);
   	
      return $this->load->view('cp/vendors/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Displays info about a vendor on a single page
    *
    */
   function view($vendor_id)
   {
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      $this->load->model('Site_domains');
      $this->load->model('Site_vendors');
      
      $vendor = $this->Vendors->get_vendor_data($vendor_id);
      $current_clients = $this->Site_vendors->get_current_vendor_sites($vendor_id);
      $former_clients = $this->Site_vendors->get_former_vendor_sites($vendor_id);
      $domains = $this->Site_domains->get_vendor_domains($vendor_id);
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
      $data['submenu'] = get_vendors_submenu('Vendors');
      $data['vendor_id'] = $vendor_id;
      $data['vendor'] = $vendor;
      $data['current_clients'] = $current_clients;
      $data['former_clients'] = $former_clients;
      $data['domains'] = $domains;

      $this->load->vars($data);
   	
      return $this->load->view('cp/vendors/view', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes the specified vendor
    *
    */
   function delete($vendor_id) 
   {
      $this->administrator->check_login();

      $this->load->model('Vendors');
      
      $vendor = $this->Vendors->get_vendor_data($vendor_id);

      $this->Vendors->delete_vendor($vendor_id);

      $this->session->set_userdata('site_message', 'The vendor record for "'.$vendor['VendorName'].'" has been deleted.');

      redirect('cp/vendors/index/');
   }

   // --------------------------------------------------------------------

   /**
    * Adds a vendor
    *
    */
   function add($this_action) 
   {
      $this->load->helper(array('ckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Site_vendors');
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      
      $rules['VendorName'] = 'trim|required';
      $rules['SiteID'] = 'trim|required';
      $rules['ServiceID'] = 'trim|callback_one_required[NewServiceName]';
      $rules['NewServiceName'] = 'trim';
      $rules['ServiceDesc'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['VendorName'] = 'Vendor Name';
      $fields['SiteID'] = 'Site ID';
      $fields['ServiceID'] = 'Service ID';
      $fields['NewServiceName'] = 'New Service Name';
      $fields['ServiceDesc'] = 'Service Description';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['statuses'] = array(
               'current' => 'Current vendor',
               'former' => 'Former vendor',
            );
         $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
         $data['submenu'] = get_vendors_submenu('Vendors');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['vendors'] = $this->Vendors->get_vendors_list();
         $data['sites'] = $this->Sites->get_sites_list();
         $data['services'] = $this->Vendor_services->get_services_list();

         $this->load->vars($data);
         return $this->load->view('cp/vendors/add', NULL, TRUE);

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
    * Processes the add vendor form
    *
    */
   function _add()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $client[$key] = $this->input->post($key);

      // first save the new service if there is one
      if (isset($values['NewServiceName']) && $values['NewServiceName'] != "")
      {
         // create a new service record
         $service['Name'] = $values['NewServiceName'];

         $client['ServiceID'] = $this->Vendor_services->insert_service($service);
         
         // And rebuild the tree so it is up-to-date
         $this->Vendor_services->rebuild_tree(1, 1);
      }
      unset($client['NewServiceName']);
      
      // save the new vendor
      $vendor['VendorName'] = $client['VendorName'];
      unset($client['VendorName']);
      $vendor['CreatedDate'] = date('Y-m-d H:i:s');
      $vendor['CreatedBy'] = $this->session->userdata('username');
      $client['VendorID'] = $this->Vendors->insert_vendor($vendor);

      // now save the Site Vendor record
      $client['ServiceDesc'] = ascii_to_entities($client['ServiceDesc']);
      $client['CreatedDate'] = date('Y-m-d H:i:s');
      $client['CreatedBy'] = $this->session->userdata('username');
      $this->Site_vendors->insert_site_vendor($client);
      
      $this->session->set_userdata('site_message', 'The vendor '.$vendor['VendorName'].' has been added.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/vendors/edit/'.$client['VendorID'].'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Edits a vendor
    *
    */
   function edit($vendor_id, $this_action) 
   {
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');
   
      $this->load->helper(array('ckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Vendors');
      
      $rules['VendorName'] = 'trim|required';
      $rules['Address'] = 'trim';
      $rules['VendorURL'] = 'trim';
      $rules['AboutThisVendor'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['VendorName'] = 'Vendor Name';
      $fields['Address'] = 'Address';
      $fields['VendorURL'] = 'Vendor URL';
      $fields['AboutThisVendor'] = 'About This Vendor';

      $this->validation->set_fields($fields);
      
      $defaults = $this->Vendors->get_vendor_data($vendor_id);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
         $data['submenu'] = get_vendors_submenu('Vendors');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['vendor_id'] = $vendor_id;
         $data['vendor'] = $this->Vendors->get_vendor_data($vendor_id);
         $data['admin'] = $admin;

         $this->load->vars($data);
         return $this->load->view('cp/vendors/edit', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($vendor_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit_vendor form
    *
    */
   function _edit($vendor_id)
   {
      if ($vendor_id == 0)
      {
         show_error('_edit_vendor requires that a vendor ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Vendors->update_vendor($vendor_id, $values);
      
      $vendor = $this->Vendors->get_vendor_data($vendor_id);
      
      $this->session->set_userdata('site_message', 'The vendor '.$vendor['VendorName'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('cp/vendors/edit/'.$vendor_id.'/'.$last_action.'/');
   }


}
?>