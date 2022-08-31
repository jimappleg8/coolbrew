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
      $this->load->helper(array('url', 'text'));
   }

   // --------------------------------------------------------------------

   /**
    * Generates a listing of vendors associated with this site
    *
    */
   function index($site_id)
   {
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');
   
      $this->load->model('Sites');
      $this->load->model('Site_vendors');
      
      $site = $this->Sites->get_site_data($site_id);

      $site_vendors = $this->Site_vendors->get_site_vendors($site_id);
      
      $admin['vendor_exists'] = (count($site_vendors) == 0) ? FALSE : TRUE;
      
      $data['site_vendors'] = $site_vendors;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Vendors');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;

      $this->load->vars($data);
   	
      return $this->load->view('sites/vendors/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Displays info about a vendor on a single page
    *
    */
   function view($site_id, $site_vendor_id)
   {
      $this->load->model('Sites');
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      $this->load->model('Site_vendors');
      
      $site = $this->Sites->get_site_data($site_id);
      $site_vendor = $this->Site_vendors->get_site_vendor_data($site_vendor_id);
      $vendor = $this->Vendors->get_vendor_data($site_vendor['VendorID']);
      $current_clients = $this->Site_vendors->get_current_vendor_sites($site_vendor['VendorID'], $site_id);
      $former_clients = $this->Site_vendors->get_former_vendor_sites($site_vendor['VendorID'], $site_id);
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Vendors');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['vendor_id'] = $site_vendor['VendorID'];
      $data['site_vendor_id'] = $site_vendor_id;
      $data['vendor'] = $vendor;
      $data['site_vendor'] = $site_vendor;
      $data['current_clients'] = $current_clients;
      $data['former_clients'] = $former_clients;

      $this->load->vars($data);
   	
      return $this->load->view('sites/vendors/view', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes the specified vendor
    *
    */
   function delete($site_id, $vendor_id) 
   {
      $this->administrator->check_login();

      $this->load->model('Site_vendors');

      $this->Site_vendors->delete_site_vendor($vendor_id);

      $this->session->set_userdata('site_message', 'The site vendor record has been deleted.');

      redirect('sites/vendors/index/'.$site_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Adds a vendor to the specified site
    *
    */
   function add($site_id, $this_action) 
   {
      $this->load->helper(array('ckeditor', 'text'));
      
      $this->load->library('validation');
      
      $this->load->model('Sites');
      $this->load->model('Site_vendors');
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $rules['ServiceID'] = 'trim|callback_one_required[NewServiceName]';
      $rules['NewServiceName'] = 'trim';
      $rules['ServiceDesc'] = 'trim';
      $rules['URL'] = 'trim';
      $rules['VendorID'] = 'trim|callback_one_required[VendorName]';
      $rules['VendorName'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ServiceID'] = 'Service ID';
      $fields['NewServiceName'] = 'New Service Name';
      $fields['ServiceDesc'] = 'Service Description';
      $fields['URL'] = 'URL';
      $fields['VendorID'] = 'Vendor';
      $fields['VendorName'] = 'Vendor Name';
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
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['vendors'] = $this->Vendors->get_vendors_list();
         $data['services'] = $this->Vendor_services->get_services_list();
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Vendors');

         $this->load->vars($data);
         return $this->load->view('sites/vendors/add', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add site_vendor form
    *
    */
   function _add($site_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      if (isset($values['NewServiceName']) && $values['NewServiceName'] != "")
      {
         // create a new service record
         $service['Name'] = $values['NewServiceName'];

         $values['ServiceID'] = $this->Vendor_services->insert_service($service);
         
         // And rebuild the tree so it is up-to-date
         $this->Vendor_services->rebuild_tree(1, 1);
      }
      unset($values['NewServiceName']);

      if (isset($values['VendorName']) && $values['VendorName'] != "")
      {
         // create a new vendor record
         $vendor['VendorName'] = $values['VendorName'];
         $vendor['CreatedDate'] = date('Y-m-d H:i:s');
         $vendor['CreatedBy'] = $this->session->userdata('username');

         $values['VendorID'] = $this->Vendors->insert_vendor($vendor);
      }
      unset($values['VendorName']);
      
      $values['SiteID'] = $site_id;
      $values['ServiceDesc'] = ascii_to_entities($values['ServiceDesc']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $site_vendor_id = $this->Site_vendors->insert_site_vendor($values);

      redirect('sites/vendors/view/'.$site_id.'/'.$site_vendor_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Edits a vendor for the specified site
    *
    */
   function edit($site_id, $vendor_id, $this_action) 
   {
      $this->load->helper(array('ckeditor', 'text'));
      
      $this->load->library('validation');
      
      $this->load->model('Sites');
      $this->load->model('Site_vendors');
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['ServiceID'] = 'trim|callback_one_required[NewServiceName]';
      $rules['NewServiceName'] = 'trim';
      $rules['ServiceDesc'] = 'trim';
      $rules['URL'] = 'trim';
      $rules['VendorID'] = 'trim|callback_one_required[VendorName]';
      $rules['VendorName'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ServiceID'] = 'Service ID';
      $fields['NewServiceName'] = 'New Service Name';
      $fields['ServiceDesc'] = 'Service Description';
      $fields['URL'] = 'URL';
      $fields['VendorID'] = 'Vendor';
      $fields['VendorName'] = 'Vendor Name';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);
      
      $defaults = $this->Site_vendors->get_site_vendor_data($vendor_id);
      $defaults['ServiceDesc'] = entities_to_ascii($defaults['ServiceDesc']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['statuses'] = array(
               'current' => 'Current vendor',
               'former' => 'Former vendor',
            );
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['site_id'] = $site_id;
         $data['vendor_id'] = $vendor_id;
         $data['site'] = $site;
         $data['vendors'] = $this->Vendors->get_vendors_list();
         $data['services'] = $this->Vendor_services->get_services_list();
         $data['vendor'] = $this->Vendors->get_vendor_data($defaults['VendorID']);
         $data['site_vendor'] = $this->Site_vendors->get_site_vendor_data($vendor_id);
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Vendors');

         $this->load->vars($data);
         return $this->load->view('sites/vendors/edit', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $vendor_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit_site_vendor form
    *
    */
   function _edit($site_id, $vendor_id)
   {
      if ($vendor_id == 0)
      {
         show_error('_edit_site_domain requires that a vendor ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      if (isset($values['NewServiceName']) && $values['NewServiceName'] != "")
      {
         // create a new service record
         $service['Name'] = $values['ServiceName'];
         $service['CreatedDate'] = date('Y-m-d H:i:s');
         $service['CreatedBy'] = $this->session->userdata('username');

         $values['ServiceID'] = $this->Vendor_service->insert_service($service);
      }
      unset($values['NewServiceName']);

      if (isset($values['VendorName']) && $values['VendorName'] != "")
      {
         // create a new vendor record
         $vendor['VendorName'] = $values['VendorName'];
         $vendor['CreatedDate'] = date('Y-m-d H:i:s');
         $vendor['CreatedBy'] = $this->session->userdata('username');

         $values['VendorID'] = $this->Vendors->insert_vendor($vendor);
      }
      unset($values['VendorName']);

      $values['SiteID'] = $site_id;
      $values['ServiceDesc'] = ascii_to_entities($values['ServiceDesc']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Site_vendors->update_site_vendor($vendor_id, $values);

      redirect('sites/vendors/view/'.$site_id.'/'.$vendor_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Validation callback checking if one or the other of the fields
    *  has content.
    */
   function one_required($str, $field)
   {
      if ($str == '' && $_POST[$field] == '')
      {
         $this->validation->set_message('one_required', 'Either the %s field or the '.$field.' field needs to be filled in.');
         return FALSE;
      }
      return TRUE;
   }


}
?>