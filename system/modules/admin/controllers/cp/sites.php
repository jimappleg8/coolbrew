<?php

class Sites extends Controller {

   var $aco = array();

   function Sites()
   {
      parent::Controller();
      $this->load->library('session');

      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->load->library('tacl', $options);

      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper(array('url', 'text'));
      
      include APPPATH().'/config/acl_admin.php';
      $this->aco['adm'] = $acl_admin;

      include APPPATH().'/config/acl_sites.php';
      $this->aco['sites'] = $acl_sites;
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of sites
    *
    */
   function index()
   {
      $this->administrator->check_login();
      
      $admin['error_msg'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');
      
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $admin['group'] = $this->session->userdata('group');
      
      $this->load->model('Sites');
   
      $site_list = $this->Sites->get_sites();
      $inactive_list = $this->Sites->get_inactive_sites();
      
      $admin['site_exists'] = (count($site_list) == 0) ? FALSE : TRUE;
      
      $brands = $this->Sites->get_sites_by_brand();

      $data['brands'] = $brands;
      
//      echo "<pre>"; print_r($brands); echo "</pre>";
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');
      
      $data['tabs'] = $this->administrator->get_main_tabs('Sites');
      $data['admin'] = $admin;
      $data['site_list'] = $site_list;
      $data['inactive_list'] = $inactive_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/sites/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Generates report about all sites
    *
    */
   function report() 
   {
      $this->administrator->check_login();
      
      $this->load->database('write');
      
      $sql = 'SELECT sd.SiteID, b.Name AS BrandName, sd.Domain, '.
               'v.VendorName, sv.Service '.
             'FROM adm_site AS s, adm_brand AS b, adm_site_domain AS sd, '.
               'adm_site_brand AS sb, adm_vendor AS v, adm_site_vendor AS sv '.
             'WHERE s.ID = sb.SiteID '.
             'AND s.ID = sd.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1 '.
             'AND sv.SiteID = s.ID '.
             'AND v.ID = sv.VendorID '.
             'ORDER BY b.Name, sd.Domain, sv.Service';
      $query = $this->db->query($sql);
      $sites = $query->result_array();
      
      $data['sites'] = $sites;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/sites/report', NULL, TRUE);

   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a site listing.
    *
    */
   function add($this_action) 
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      
      $this->load->library('validation');
      
      $this->load->model('Sites');
      
      $rules['ID'] = 'trim|required|callback_check_site_id';
      $rules['Description'] = 'trim';
      $rules['OldBrandID'] = 'trim';
      $rules['NewBrandName'] = 'trim';
      $rules['OldDomainID'] = 'trim';
      $rules['NewDomain'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ID'] = 'Site ID';
      $fields['Description'] = 'Description';
      $fields['OldBrandID'] = 'Existing Brand ID';
      $fields['NewBrandName'] = 'New Brand Name';
      $fields['OldDomainID'] = 'Existing Domain ID';
      $fields['NewDomain'] = 'New Domain';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['tabs'] = $this->administrator->get_main_tabs('Sites');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         
         $data['brands'] = $this->Sites->get_brands_list();
         $data['domains'] = $this->Sites->get_domains_list();
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/sites/add', NULL, TRUE);
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
    * Make sure the selected site is not already being used.
    *
    */
   function check_site_id($str)
   {
      $this->load->model('Sites');
      
      $result = $this->Sites->get_site_data($str);
      
      if (count($result) > 0)
      {
         $this->validation->set_message('check_site_id', 'The Site ID you selected is already being used.');
         return FALSE;
      }
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add site form
    *
    */
   function _add()
   {
      if ( ! $this->administrator->check_group('admin'))
         redirect('cp/login/sorry');

      $this->load->database('read');
      $this->load->model('Vendors');
      $this->load->model('Site_vendors');
      $this->load->model('Vendor_services');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      if ($values['OldBrandID'] == '')
      {
         // create a new brand record
         $brand['ID'] = url_title($values['NewBrandName']);
         $brand['Name'] = $values['NewBrandName'];
         $brand['CreatedDate'] = date('Y-m-d H:i:s');
         $brand['CreatedBy'] = $this->session->userdata('username');
         $this->db->insert('adm_brand', $brand);
         
         $site_brand['BrandID'] = $brand['ID'];
      }
      else
      {
         $site_brand['BrandID'] = $values['OldBrandID'];
      }

      // create a new site_brand record
      $site_brand['SiteID'] = $values['ID'];
      $this->db->insert('adm_site_brand', $site_brand);
      
      if ($values['OldDomainID'] == '')
      {
         // create new domain record
         $site_domain['SiteID'] = $values['ID'];
         $site_domain['Domain'] = $values['NewDomain'];
         $site_domain['PrimaryDomain'] = 1;
         $site_domain['RegistrarVendor'] = $this->Vendors->get_unknown_vendor_id();
         $site_domain['DNSVendor'] = $this->Vendors->get_unknown_vendor_id();
         $this->db->insert('adm_site_domain', $site_domain);
      }
      else
      {
         // update existing domain record with new site ID
         $site_domain['SiteID'] = $values['ID'];
         $site_domain['PrimaryDomain'] = 1;
         $this->db->where('ID', $values['OldDomainID']);
         $this->db->update('adm_site_domain', $site_domain);
      }
      
      // create an unknown hosting vendor record
      $vendor['SiteID'] = $values['ID'];
      $vendor['VendorID'] = $this->Vendors->get_unknown_vendor_id();
      $vendor['ServiceID'] = $this->Vendor_services->get_hosting_id();
      $vendor['CreatedDate'] = date('Y-m-d H:i:s');
      $vendor['CreatedBy'] = $this->session->userdata('username');
      $this->Site_vendors->insert_site_vendor($vendor);

      // create the new site record

      $site['ID'] = $values['ID'];
      $site['Description'] = ascii_to_entities($values['Description']);
      $site['CreatedDate'] = date('Y-m-d H:i:s');
      $site['CreatedBy'] = $this->session->userdata('username');
      $this->db->insert('adm_site', $site);
      
      // Add the site resources and actions to the access control list
      // No permissions are set up at this time.
      $resource = '';
      foreach ($this->aco['sites'] AS $aco)
      {
         if ($aco[0] != $resource)
         {
            $resource = $aco[0];
            $this->tacl->create_resource($site['ID'].'-'.$aco[0]);
         }
         $this->tacl->create_action($site['ID'].'-'.$aco[0], $aco[1]);
      }
      
      $new_site = $this->Sites->get_site_data($site['ID']);
      $this->session->set_userdata('admin_message', 'The site "'.$new_site['Domain'].'" has been added.');

      redirect("cp/sites/index");
   }
   
}
?>