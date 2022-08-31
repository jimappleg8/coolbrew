<?php

class Dashboards extends Controller {

   var $aco = array();

   function Dashboards()
   {
      parent::Controller();
      $this->load->library('session');
      
      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->load->library('tacl', $options);

      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper('url');
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates the site-level dashboard
    *
    */
   function index($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');
      $this->load->model('Links');

      $site = $this->Sites->get_site_data($site_id);
      
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Dashboard');
      $data['site_domains'] = $this->site_domains($site_id);
      $data['quick_links'] = $this->quick_links($site_id);
      $data['brand_sites'] = $this->brand_sites($site_id);
      $data['about_this_site'] = $this->about_this_site($site_id);
      $data['thumbnail_image'] = $this->thumbnail_image($site_id);
      $data['repository_url'] = $this->repository_url($site_id);
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates the Quick Links widget
    *
    */
   function quick_links($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');
      $this->load->model('Links');

      $site = $this->Sites->get_site_data($site_id);
      
      $data['quick_links'] = $this->Links->get_quick_links($site_id);
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/widget-quick-links', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates the Brand Sites widget
    *
    */
   function brand_sites($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');

      $site = $this->Sites->get_site_data($site_id);
      
      $data['brand_sites'] = $this->Sites->get_sites_in_same_brand($site_id);
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/widget-brand-sites', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates the Site Domains widget
    *
    */
   function site_domains($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');

      $site = $this->Sites->get_site_data($site_id);
      
      $data['domains'] = $this->Sites->get_domains_by_primary($site_id);
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/widget-site-domains', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates the About This Site widget
    *
    */
   function about_this_site($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');
      $this->load->model('Links');

      $site = $this->Sites->get_site_data($site_id);
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/widget-about-this-site', NULL, TRUE);

   }

   // --------------------------------------------------------------------
   
   /**
    * Generates the Repository URL widget
    *
    */
   function repository_url($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');

      $site = $this->Sites->get_site_data($site_id);
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['site'] = $site;
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/widget-repo-url', NULL, TRUE);

   }

   // --------------------------------------------------------------------
   
   /**
    * Generates the Thumbnail Image widget
    *
    */
   function thumbnail_image($site_id)
   {
      $admin['group'] = $this->session->userdata('group');

      $this->load->model('Sites');
      require_once BASEPATH.'modules/admin/libraries/AppSTW.php';

      $site = $this->Sites->get_site_data($site_id);
      
      // get the thumbnail from either the cache or the service
      $data['url'] = 'http://www.'.$site['Domain'];
      $data['src'] = AppSTW::getLargeThumbnail($data['url'], true, true);
      if ($data['src'] == '')
      {
         $data['src'] = '/aa/thumbnails/being-generated.jpg';
      }
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('sites/dashboards/widget-thumbnail', NULL, TRUE);

   }


}
?>