<?php

class Settings extends Controller {

   var $aco = array();

   function Settings()
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
    * Edits the settings for the specified site
    *
    */
   function index($site_id, $this_action) 
   {
      if ( ! $this->administrator->acl_check($site_id.'-settings', 'edit'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Site_domains');
      
      $site = $this->Sites->get_site_data($site_id);
      $domain = $this->Site_domains->get_primary_domain($site_id);
      
      $rules['Description'] = 'trim';
      $rules['Status'] = 'trim|required';
      $rules['RedirectSiteID'] = 'trim';
      $rules['Region'] = 'trim';
      $rules['Type'] = 'trim';
      $rules['LaunchDate'] = 'trim';
      $rules['DiscontinuedDate'] = 'trim';
      $rules['DomainID'] = 'trim';
      $rules['DevVendorURL'] = 'trim';
      $rules['DevVendorName'] = 'trim';
      $rules['DevURL'] = 'trim';
      $rules['StageURL'] = 'trim';
      $rules['LiveURL'] = 'trim';
      $rules['ProductLink'] = 'trim';
      $rules['RecipeLink'] = 'trim';
      $rules['RepositoryURL'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Description'] = 'Description';
      $fields['Status'] = 'Status';
      $fields['RedirectSiteID'] = 'Redirect Site ID';
      $fields['Region'] = 'Region';
      $fields['Type'] = 'Site Type';
      $fields['LaunchDate'] = 'Launch Date';
      $fields['DiscontinuedDate'] = 'Discontinued';
      $fields['DomainID'] = 'Existing Domain ID';
      $fields['DevVendorURL'] = 'Vendor\'s Development URL';
      $fields['DevVendorName'] = 'Vendor\'s Development Name';
      $fields['DevURL'] = 'Development URL';
      $fields['StageURL'] = 'Staging URL';
      $fields['LiveURL'] = 'Live URL';
      $fields['ProductLink'] = 'Link pattern for Products';
      $fields['RecipeLink'] = 'Link pattern for Recipes';
      $fields['RepositoryURL'] = 'URL for the SVN Repository or other';

      $this->validation->set_fields($fields);
      
      $defaults = $site;

      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      $defaults['DomainID'] = $domain['ID'];
            
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         // get data for the various pulldown lists
         $data['regions']  = array('' => '--choose a region--',
                                   'United States' => 'United States', 
                                   'Canada' => 'Canada',
                                   'Europe' => 'Europe',
                                   'Other' => 'Other',
                                   );
         $data['types']  = array('' => '--choose a site type--',
                                   'Branded' => 'Branded (B2C)', 
                                   'B2B' => 'Business to Business (B2B)',
                                   'Intranet' => 'Intranet',
                                   'Landing Page' => 'Landing Page',
                                   'Promotion' => 'Promotion',
                                   'Other' => 'Other',
                                   );
         $data['statuses'] = array('' => '--choose a status--',
                                   'active' => 'active', 
                                   'inactive' => 'inactive');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['admin'] = $admin;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Settings');
         $data['domains'] = $this->Site_domains->get_domains_list($site_id);
         $data['sites'] = $this->Sites->get_sites_list();

         $this->load->vars($data);
         return $this->load->view('sites/settings/edit', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $domain);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit_site_settings form
    *
    */
   function _edit($site_id, $domain)
   {
      if ( ! $this->administrator->acl_check($site_id.'-settings', 'edit'))
         redirect('cp/login/sorry');

      if ($site_id == '')
      {
         show_error('settings/_edit requires that a site ID be supplied.');
      }

      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $new_domain_id = $values['DomainID'];
      unset($values['DomainID']);
         
      $values['Description'] = ascii_to_entities($values['Description']);
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->db->where('ID', $site_id);
      $this->db->update('adm_site', $values);
      
      // change primary domain if needed
      if ($new_domain_id != $domain['ID'])
      {
         $values = array();
         $old_values = $domain;
         $values['PrimaryDomain'] = 0;
         $this->Site_domains->update_site_domain($domain['ID'], $values, $old_values);

         $values = array();
         $old_values = $this->Site_domains->get_domain_data($new_domain_id);
         $values['PrimaryDomain'] = 1;
         $values['SiteID'] = $site_id;
         $this->Site_domains->update_site_domain($new_domain_id, $values, $old_values);          
      }

      $this->session->set_userdata('site_message', 'The site data has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('sites/settings/index/'.$site_id.'/'.$last_action);
   }

   // --------------------------------------------------------------------
   
   /**
    * Edits the About This Site column for the specified site
    *
    */
   function about($site_id, $this_action) 
   {
      if ( ! $this->administrator->acl_check($site_id.'-settings', 'edit'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');

      $this->load->helper(array('ckeditor', 'form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $rules['AboutThisSite'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['AboutThisSite'] = 'About This Site';

      $this->validation->set_fields($fields);
      
      $defaults['AboutThisSite'] = $site['AboutThisSite'];
            
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['admin'] = $admin;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Dashboard');

         $this->load->vars($data);
         return $this->load->view('sites/settings/about', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_about($site_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit_about_this_site form
    *
    */
   function _about($site_id)
   {
      if ( ! $this->administrator->acl_check($site_id.'-settings', 'edit'))
         redirect('cp/login/sorry');

      if ($site_id == '')
      {
         show_error('settings/_about requires that a site ID be supplied.');
      }

      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['AboutThisSite'] = $values['AboutThisSite'];
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->db->where('ID', $site_id);
      $this->db->update('adm_site', $values);
      
      $this->session->set_userdata('site_message', 'The About This Site information has been updated.');

      redirect('sites/dashboards/index/'.$site_id);
   }


}
?>