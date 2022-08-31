<?php

class Site_vendors extends Controller {

   var $aco = array();

   function Site_vendors()
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
    * Adds a vendor client
    *
    */
   function add($vendor_id, $this_action) 
   {
      $this->load->helper(array('ckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Site_vendors');
      $this->load->model('Vendors');
      $this->load->model('Vendor_services');
      
      $rules['SiteID'] = 'trim|required';
      $rules['ServiceID'] = 'trim|callback_one_required[NewServiceName]';
      $rules['NewServiceName'] = 'trim';
      $rules['ServiceDesc'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

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
         $data['vendor_id'] = $vendor_id;
         $data['vendor'] = $this->Vendors->get_vendor_data($vendor_id);
         $data['sites'] = $this->Sites->get_sites_list();
         $data['services'] = $this->Vendor_services->get_services_list();

         $this->load->vars($data);
         return $this->load->view('cp/site_vendors/add', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($vendor_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add vendor form
    *
    */
   function _add($vendor_id)
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
      
      $client['VendorID'] = $vendor_id;
      $client['ServiceDesc'] = ascii_to_entities($client['ServiceDesc']);
      $client['CreatedDate'] = date('Y-m-d H:i:s');
      $client['CreatedBy'] = $this->session->userdata('username');

      // now save the Site Vendor record
      $this->Site_vendors->insert_site_vendor($client);
      
      $this->session->set_userdata('site_message', 'The client record has been added.');

      redirect('cp/vendors/view/'.$vendor_id.'/');
   }

}
?>