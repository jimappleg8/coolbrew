<?php

class Lists extends Controller {

   function Lists()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'lists'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of all lists for this site
    *
    */
   function index($site_id)
   {
//      $this->check('Lists');
      
      $list['error_msg'] = $this->session->userdata('list_error');
      if ($this->session->userdata('list_error') != '')
         $this->session->set_userdata('list_error', '');

      $this->load->model('Sites');
      $this->load->model('Lists');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $list_list = $this->Lists->get_lists($site_id);

      $list['list_exists'] = (count($list_list) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('lists');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Lists');
      $data['submenu'] = get_submenu($site_id, 'Lists');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['list'] = $list;
      $data['list_list'] = $list_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('lists/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a list
    *
    */
   function add($site_id, $this_action) 
   {
//      $this->check('Lists');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['ListCode'] = 'trim|required';
      $rules['Name'] = 'trim|required';
      $rules['IsHTMLDefault'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ListCode'] = 'List Code';
      $fields['Name'] = 'Name';
      $fields['IsHTMLDefault'] = 'Is HTML Default';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('lists');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Lists');
         $data['submenu'] = get_submenu($site_id, 'Lists');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('lists/add', NULL, TRUE);
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
    * Processes the add list form
    *
    */
   function _add($site_id)
   {
      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;      
      $values['Name'] = ascii_to_entities($values['Name']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $this->db->insert('lists', $values);
      
      redirect("lists/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a list
    *
    */
   function edit($site_id, $list_id, $this_action) 
   {
//      $this->check('Lists');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Lists');

      $site = $this->Sites->get_site_data($site_id);
      $list = $this->Lists->get_list_data($list_id);

      $rules['ListCode'] = 'trim|required';
      $rules['Name'] = 'trim|required';
      $rules['IsHTMLDefault'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ListCode'] = 'List Code';
      $fields['Name'] = 'Name';
      $fields['IsHTMLDefault'] = 'Is HTML Default';

      $this->validation->set_fields($fields);

      $defaults = $list;
      $defaults['Name'] = entities_to_ascii($defaults['Name']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('lists');

         $data['last_action'] = $this->session->userdata('last_action') + 1;      
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Lists');
         $data['submenu'] = get_submenu($site_id, 'Lists');
         $data['list_id'] = $list_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('lists/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $list_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a list record
    *
    */
   function _edit($site_id, $list_id)
   {
      if ($list_id == 0)
      {
         show_error('_edit_list requires that a list ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['Name'] = ascii_to_entities($values['Name']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->db->where('ID', $list_id);
      $this->db->update('lists', $values);
      
      redirect("lists/index/".$site_id.'/');
   }


}
?>