<?php

class Links extends Controller {

   function Links()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'seo'));
      $this->load->helper(array('url', 'menu'));
   }

   // -----------------------------------------------------------------------

   function index($site_id)
   {
      $this->load->model('Sites');
      $this->load->model('Indexes');
      
      $site = $this->Sites->get_site_data($site_id);

      $this->collector->append_css_file('admin');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'SEO');
      $data['submenu'] = get_submenu($site_id, 'Link Tools');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['index_exists'] = $this->Indexes->index_exists($site_id);

      return $this->load->view('links/index', $data, TRUE);
   }
   
   // -----------------------------------------------------------------------

   function index_site($site_id, $this_action)
   {
      $this->load->helper(array('form', 'text'));
      $this->load->model('Sites');
      $this->load->model('Indexes');
      $this->load->library('Site_index');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);
      
//      echo "<pre>"; print_r($site); echo "</pre>";
      
      $rules['RootURL'] = 'trim|required';
      $rules['MaxPages'] = 'trim';
      $rules['Extensions'] = 'trim';
      $rules['WwwTreatment'] = 'trim';
      $rules['IndexTreatment'] = 'trim';
      $rules['IndexAppend'] = 'trim';
      $rules['QueryExcludes'] = 'trim';
      $rules['ExternalTitles'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['RootURL'] = 'URL';
      $fields['MaxPages'] = 'Max Pages';
      $fields['Extensions'] = 'Extensions';
      $fields['WwwTreatment'] = 'WWW Treatment';
      $fields['IndexTreatment'] = 'Index';
      $fields['IndexAppend'] = 'Index Append';
      $fields['QueryExcludes'] = 'Ses';
      $fields['ExternalTitles'] = 'Get External Titles';

      $this->validation->set_fields($fields);

      if ($this->Indexes->index_exists($site_id) == TRUE)
      {
         $defaults = $this->Indexes->get_index_config($site_id);
      }
      else
      {
         $defaults['RootURL'] = $this->site_index->url_normalize($site['Protocol'].$site['Domain'], 'http', 'append');
         $defaults['MaxPages'] = '0';
         $defaults['Extensions'] = 'asp aspx cfm cgi htm html php pl';
         $defaults['WwwTreatment'] = 'default';
         $defaults['IndexTreatment'] = 'default';
         $defaults['IndexAppend'] = 'index.';
         $defaults['QueryExcludes'] = 'id sid PHPSESSID';
         $defaults['ExternalTitles'] = 0;
      }
   
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'SEO');
         $data['submenu'] = get_submenu($site_id, 'Link Tools');
         $data['site_id'] = $site_id;
         $data['site'] = $site;

         return $this->load->view('links/index-site', $data, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->_index_site($site_id);
         }
      }
   }
   
   // -----------------------------------------------------------------------

   /**
    * Processes the index_site form
    */
   function _index_site($site_id)
   {
      $this->load->library('Site_index');
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

      $search['WwwTreatment'] = ($search['WwwTreatment'] == 'default') ? FALSE : $search['WwwTreatment'];

      $search['ExternalTitles'] = (isset($search['ExternalTitles'])) ? $search['ExternalTitles'] : 0;
      
      $search['SiteID'] = $site_id;

      $this->site_index->index($search);

      redirect("links/show_index/".$site_id.'/');
   }

   // -----------------------------------------------------------------------

   /**
    * Display the site's latest index
    */
   function show_index($site_id)
   {
      $this->load->model('Sites');
      $this->load->model('Indexes');
      
      $site = $this->Sites->get_site_data($site_id);

      $this->collector->append_css_file('admin');

      $data = $this->Indexes->get_site_index($site_id);

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'SEO');
      $data['submenu'] = get_submenu($site_id, 'Link Tools');
      $data['site_id'] = $site_id;
      $data['site'] = $site;

      return $this->load->view('links/index-site-results', $data, TRUE);
   }

   // -----------------------------------------------------------------------

   /**
    * Display the details about a link
    */
   function detail($link_id)
   {
      $this->load->model('Sites');
      $this->load->model('Indexes');
      $this->load->model('Referrers');
      
      $data['link'] = $this->Indexes->get_all_link_data($link_id);
      
      $site_id = $data['link']['SiteID'];

      $data['site'] = $this->Sites->get_site_data($site_id);
      
      $data['links'] = $this->Referrers->get_page_links($link_id);
      $data['referrers'] = $this->Referrers->get_page_referrers($link_id);

      $this->collector->append_css_file('admin');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'SEO');
      $data['submenu'] = get_submenu($site_id, 'Link Tools');
      $data['site_id'] = $site_id;

      return $this->load->view('links/detail', $data, TRUE);
   }

   // -----------------------------------------------------------------------

   /**
    * Generates a CVS listing of index
    *
    */
   function export($site_id)
   {
      $this->load->database('read');
      $this->load->dbutil();
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $sql = 'SELECT * FROM seo_index '.
             'WHERE SiteID = "'.$site_id.'"';
      $queryA = $this->db->query($sql);
      $results = $queryA->row_array();

      $sql = 'SELECT Location, Type, URL, Title, MetaDescription, '.
               'MetaKeywords, MetaAbstract, MetaRobots, Text '.
             'FROM seo_index_link '.
             'WHERE IndexID = '.$results['ID'].' '.
             'ORDER BY Location DESC, Type DESC, URL ASC';
      $query = $this->db->query($sql);

      $datetime = strtotime($results['IndexedDate']);
      $today = date('Y-m-d_H-i-s', $datetime);

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$site_id."_index_".$today.".csv");

      echo $this->dbutil->csv_from_result($query);
      exit;
   }


}
?>
