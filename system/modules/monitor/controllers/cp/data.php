<?php

class Data extends Controller {

   // --------------------------------------------------------------------

   function Data()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'monitor'));
      $this->load->helper(array('url', 'menu'));
   }

   // --------------------------------------------------------------------

   /**
    *
    */
   function index()
   {
   }

   // --------------------------------------------------------------------

   /**
    * Edits the specified link
    *
    */
   function edit($data_id, $this_action) 
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Reports');
      $this->load->model('Report_data');
      $this->load->model('Sites');

      $datum = $this->Report_data->get_report_data_data($data_id);
      $report_id = $datum['report_id'];

      $rules['amount'] = 'trim|required';
      $rules['source'] = 'trim';
      $this->validation->set_rules($rules);

      $fields['amount'] = 'Amount';
      $fields['source'] = 'Source';
      $this->validation->set_fields($fields);

      $defaults['amount'] = $datum['amount'];
      $defaults['source'] = $datum['source'];
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('monitor');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Monitor');
         $data['submenu'] = get_cp_submenu('Reports');
         $data['data_id'] = $data_id;
         $data['datum'] = $datum;
         $data['report_id'] = $report_id;
         $data['sites_lookup'] = $this->Sites->get_sites_lookup();
         $data['report'] = $this->Reports->get_report_data($report_id);

         $this->load->vars($data);
         return $this->load->view('cp/data/edit', NULL, TRUE);

      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($data_id, $report_id, $datum['site_id']);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit form
    *
    */
   function _edit($data_id, $report_id, $site_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['overridden'] = 1;
      $this->Reports->update_report($data_id, $values);
      
      $this->session->set_userdata('monitor_message', 'The datum has been updated.');

      redirect('cp/reports/update_data/'.$report_id.'#'.$site_id);
   }   


}