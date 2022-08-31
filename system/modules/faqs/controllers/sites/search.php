<?php

class Search extends Controller {

   function Search()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'faqs'));
      $this->load->helper(array('url', 'menu'));
   }
   
   //-------------------------------------------------------------------------

   /**
    * generates an FAQs search form
    */
   function index($site_id, $post = 'form')
   {
      $admin['message'] = $this->session->userdata('faq_message');
      if ($this->session->userdata('faq_message') != '')
         $this->session->set_userdata('faq_message', '');

      $admin['error_msg'] = $this->session->userdata('faq_error');
      if ($this->session->userdata('faq_error') != '')
         $this->session->set_userdata('faq_error', '');

      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Shared');
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['Words'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Words'] = 'Words';

      $this->validation->set_fields($fields);

      if ($post != 'form')
      {
         $query = unserialize($this->session->userdata('faq_query'));
         $defaults = $query;
         $this->validation->set_defaults($defaults);
      }

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('faqs');
      
      $faq_list = array();
      
      if ($this->validation->run() == TRUE || $post != 'form')
      {
         $data['search'] = TRUE;
         $faq_list = $this->_search($site_id, $post);
      }

      $admin['faq_exists'] = (count($faq_list) == 0) ? FALSE : TRUE;

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'FAQs');
      $data['submenu'] = get_submenu($site_id, 'Search FAQs');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      $data['faq_list'] = $faq_list;
      $data['shared'] = $this->Shared->get_shared_faqs_lookup($site_id);

      return $this->load->view('sites/search/search', $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * processes the search form results
    */
   function _search($site_id, $post)
   {
      $this->load->model('Items');
      $this->load->model('Keywords');
      
      if ($post == 'form')
      {
         $fields = $this->validation->_fields;
      
         foreach ($fields AS $key => $value)
            $search[$key] = $this->input->post($key);
      }
      else
      {
         $query = unserialize($this->session->userdata('faq_query'));

         foreach ($query AS $key => $value)
            $search[$key] = $value;
      }

//      $exact = (isset($search['Exact'])) ? TRUE : FALSE;
      $exact = TRUE;

      // store the values in the session for use later
      $query = serialize($search);
      $this->session->set_userdata('faq_query', $query);

      $faqs = $this->Items->search_faqs($search, $site_id, $exact);

      // make sure each ShortQuestion has a value
      for ($i=0, $total_faqs = count($faqs); $i<$total_faqs; $i++)
      {
         if (trim($faqs[$i]['ShortQuestion']) == '')
         {
            $faqs[$i]['ShortQuestion'] = $faqs[$i]['Question'];
         }
      }
      
      // add search request to keywords database
      $this->Keywords->insert_keywords($site_id, $search['Words'], $faqs);

      return $faqs;
   }

}
?>