<?php

class Items extends Controller {

   function Items()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'lists'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of the list items
    *
    */
   function index($site_id, $list_id)
   {
//      $this->check('Lists');

      $list['error_msg'] = $this->session->userdata('list_error');
      if ($this->session->userdata('list_error') != '')
         $this->session->set_userdata('list_error', '');

      $this->load->helper('text');
      $this->load->model('Sites');
      $this->load->model('Lists');
      $this->load->model('Items');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $item_list = $this->Items->get_list_items($list_id);

      $list['item_exists'] = (count($item_list) == 0) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('lists');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Lists');
      $data['submenu'] = get_submenu($site_id, 'Lists');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['list_id'] = $list_id;
      $data['list'] = $list;
      $data['list_data'] = $this->Lists->get_list_data($list_id);
      $data['item_list'] = $item_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('items/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an item
    *
    */
   function delete($item_id, $this_action) 
   {
      $this->load->database('read');
      
      $sql = 'SELECT lists.SiteID, lists_item.ListID '.
             'FROM lists, lists_item '.
             'WHERE lists_item.ID = '.$item_id.' '.
             'AND lists.ID = lists_item.ListID';
      $query = $this->db->query($sql);
      $list = $query->row_array();
      
      $this->db->where('ID', $item_id);
      $this->db->delete('lists_item');
      
      redirect("items/index/".$list['SiteID'].'/'.$list['ListID'].'/');
   }

   // --------------------------------------------------------------------

   /**
    * Adds a list item
    *
    */
   function add($site_id, $list_id, $this_action) 
   {
//      $this->check('Lists');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Lists');
      
      $site = $this->Sites->get_site_data($site_id);
      $list_data = $this->Lists->get_list_data($list_id);

      $rules['SortKey'] = 'trim';
      $rules['Content'] = 'trim|required';
      $rules['IsHTML'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['SortKey'] = 'Sort Key';
      $fields['Content'] = 'Content';
      $fields['IsHTML'] = 'Is HTML';

      $this->validation->set_fields($fields);

      $defaults['IsHTML'] = $list_data['IsHTMLDefault'];
      
      $this->validation->set_defaults($defaults);

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
         $data['list_id'] = $list_id;
      
         $this->load->vars($data);
   	
         return $this->load->view('items/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id, $list_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add item form
    *
    */
   function _add($site_id, $list_id)
   {
      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['ListID'] = $list_id;      
      $values['SortKey'] = ascii_to_entities($values['SortKey']);
      $values['Content'] = ascii_to_entities($values['Content']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $this->db->insert('lists_item', $values);
      
      redirect("items/index/".$site_id.'/'.$list_id.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a list item
    *
    */
   function edit($site_id, $item_id, $this_action) 
   {
//      $this->check('Lists');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Items');

      $site = $this->Sites->get_site_data($site_id);
      $item = $this->Items->get_item_data($item_id);
      $list_id = $item['ListID'];

      $rules['SortKey'] = 'trim';
      $rules['Content'] = 'trim|required';
      $rules['IsHTML'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['SortKey'] = 'Sort Key';
      $fields['Content'] = 'Content';
      $fields['IsHTML'] = 'Is HTML';

      $this->validation->set_fields($fields);

      $defaults = $item;
      $defaults['SortKey'] = entities_to_ascii($defaults['SortKey']);
      $defaults['Content'] = entities_to_ascii($defaults['Content']);
      
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
         $data['item_id'] = $item_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('items/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $item_id, $list_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a list item record
    *
    */
   function _edit($site_id, $item_id, $list_id)
   {
      if ($item_id == 0)
      {
         show_error('_edit_item requires that a item ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SortKey'] = ascii_to_entities($values['SortKey']);
      $values['Content'] = ascii_to_entities($values['Content']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->db->where('ID', $item_id);
      $this->db->update('lists_item', $values);
      
      redirect("items/index/".$site_id.'/'.$list_id.'/');
   }


}
?>