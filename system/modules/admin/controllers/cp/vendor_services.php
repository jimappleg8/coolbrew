<?php

class Vendor_services extends Controller {

   function Vendor_services()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper(array('url', 'menu'));

   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of the vendor services
    *
    */
   function index()
   {
      $admin['message'] = $this->session->userdata('site_message');
      if ($this->session->userdata('site_message') != '')
         $this->session->set_userdata('site_message', '');

      $this->load->model('Vendor_services');
      
      // the first time, rebuild the tree
      $this->Vendor_services->rebuild_tree(1, 1);

      $service_list = $this->Vendor_services->get_service_tree();
      
      $admin['service_exists'] = (count($service_list) < 0) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('admin-styles');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
      $data['submenu'] = get_vendors_submenu('Categories');
      $data['admin'] = $admin;
      $data['service_list'] = $service_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/vendor_services/list', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes a vendor service
    *
    */
   function delete($service_id, $this_action) 
   {
      $this->load->model('Vendor_services');

      $this->Vendor_services->delete_service($service_id);

      redirect('cp/vendor_services/index/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a product service
    *
    */
   function add($parent, $sort, $this_action) 
   {
      $this->load->helper(array('form', 'text'));
      $this->load->model('Vendor_services');
      $this->load->library(array('validation'));
      
      $rules['Name'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['SortOrder'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Name'] = 'Service Name';
      $fields['Description'] = 'Service Description';
      $fields['SortOrder'] = 'Service Order';

      $this->validation->set_fields($fields);

      $defaults['SortOrder'] = $sort;
      $defaults['Status'] = 'active';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');
         
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
         $data['submenu'] = get_vendors_submenu('Categories');
         $data['parent'] = $parent;
         $data['sort'] = $sort;
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/vendor_services/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($parent, $sort);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add service form
    *
    * Auditing: incomplete
    */
   function _add($parent, $sort)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      $values['ParentID'] = $parent;
      $values['Name'] = ascii_to_entities($values['Name']);
      $values['Description'] = ascii_to_entities($values['Description']);

      $service_id = $this->Vendor_services->insert_service($values);

      // And rebuild the tree so it is up-to-date
      $this->Vendor_services->rebuild_tree(1, 1);

      redirect('cp/vendor_services/index/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a vendor service
    *
    */
   function edit($service_id, $this_action) 
   {
      $this->load->helper(array('form', 'text'));
      $this->load->model('Vendor_services');
      $this->load->library('validation');
      
      $old_values = $this->Vendor_services->get_service_data($service_id);

      $rules['Name'] = 'trim|required';
      $rules['Description'] = 'trim';
      $rules['SortOrder'] = 'trim';
      $rules['ParentID'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Name'] = 'Service Name';
      $fields['Description'] = 'Service Description';
      $fields['SortOrder'] = 'Service Order';
      $fields['ParentID'] = 'Parent ID';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      $defaults['Name'] = entities_to_ascii($defaults['Name']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('admin-styles');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('All Vendors');
         $data['submenu'] = get_vendors_submenu('Services');
         $data['service_id'] = $service_id;
         $data['parents'] = $this->Vendor_services->get_parent_list($service_id);
      
         $this->load->vars($data);
   	
         return $this->load->view('cp/vendor_services/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($service_id, $old_values);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a service record
    *
    * Auditing: incomplete
    */
   function _edit($service_id, $old_values)
   {
      if ($service_id == 0)
      {
         show_error('_edit_service requires that a service ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['Name'] = ascii_to_entities($values['Name']);
      $values['Description'] = ascii_to_entities($values['Description']);

      // update the edited service
      $this->Vendor_services->update_service($service_id, $values);
      
      // Since we can change the SortOrder, rebuild the tree
      $this->Vendor_services->rebuild_tree(1, 1);

      redirect('cp/vendor_services/index/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Rearranges service items up and down
    *
    * @return void
    */
   function move($cat_id, $direction, $this_action) 
   {
      $this->load->model('Vendor_services');

      // detect if the page is just being reloaded
      if ($this_action > $this->session->userdata('last_action'))
      {
         $this->session->set_userdata('last_action', $this_action);

         $this->Vendor_services->update_service_sort_order($cat_id, $direction);
      
         // And rebuild the tree so it is up-to-date
         $this->Vendor_services->rebuild_tree(1, 1);
      }
      redirect('cp/vendor_services/index/');
   }

   
}
?>
