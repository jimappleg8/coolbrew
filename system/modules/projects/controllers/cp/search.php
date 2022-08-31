<?php

class Search extends Controller {

   function Search()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   //-------------------------------------------------------------------------

   /**
    * generates a Projects search form
    */
   function index()
   {
      $this->administrator->check_login();

      $admin['message'] = $this->session->userdata('project_message');
      if ($this->session->userdata('project_message') != '')
         $this->session->set_userdata('project_message', '');

      $admin['error_msg'] = $this->session->userdata('projects_error');
      if ($this->session->userdata('projects_error') != '')
         $this->session->set_userdata('projects_error', '');

      $admin['group'] = $this->session->userdata('group');

      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');

      $rules['Words'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Words'] = 'Words';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');
      
      $project_list = array();
      
      if ($this->validation->run() == TRUE)
      {
         $data['search'] = TRUE;
         $project_list = $this->_search();
      }

      $admin['project_exists'] = (count($project_list) == 0) ? FALSE : TRUE;

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Projects');
      $data['submenu'] = get_submenu('Search Projects');
      $data['admin'] = $admin;
      $data['project_list'] = $project_list;

      return $this->load->view('cp/search/search', $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * processes the search form results
    */
   function _search()
   {
      $this->administrator->check_login();

      $this->load->model('Indexes');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

//      $exact = (isset($search['Exact'])) ? TRUE : FALSE;
      $exact = TRUE;

      $projects = $this->Indexes->search_projects($search, $exact);

      return $projects;
   }

}
?>