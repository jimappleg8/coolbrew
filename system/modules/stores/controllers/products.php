<?php

class Products extends Controller {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Products()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'stores'));
      $this->load->helper(array('url', 'menu'));

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Lists the products for the given store
    *
    */
   function index($store_id)
   {
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Stores_product');
      
      $products_carried = $this->Stores_product->get_products($store_id, 1);
      $admin['products_carried_exist'] = (count($products_carried) > 0) ? TRUE : FALSE;
      $products_not_carried = $this->Stores_product->get_products($store_id, 0);
      $admin['products_not_carried_exist'] = (count($products_not_carried) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['products_carried'] = $products_carried;
      $data['products_not_carried'] = $products_not_carried;
      $data['store_id'] = $store_id;
      $data['admin'] = $admin;

      $this->load->vars($data);
   	
      echo $this->load->view('products/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes an product link
    *
    */
   function delete($store_id, $product_id)
   {
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Stores_product');
      
      $this->Stores_product->delete_store_product($store_id, $product_id);
      
      $this->index($store_id);
   }

   // --------------------------------------------------------------------

   /**
    * Adds an ingredient
    *
    * Auditing: complete
    */
   function add($store_id) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Stores_product');
      $this->load->library(array('validation', 'auditor'));
      
      $rules['ProductSiteID'] = 'trim|required';
      $rules['ProductID'] = 'trim|required';
      $rules['Carried'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['ProductSiteID'] = 'Product SiteID';
      $fields['ProductID'] = 'Product ID';
      $fields['Carried'] = 'Carried';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         $this->_add($store_id);
      }

      $this->index($store_id);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the add product form
    *
    * Auditing: complete
    */
   function _add($store_id)
   {
      $fields = $this->validation->_fields;
      unset($fields['ProductSiteID']);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['StoreID'] = $store_id;
      $values['Source'] = 'administrator';

      $this->Stores_product->insert_store_product($values);
                  
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Lists linked products and allows editing of list
    */
   function edit($store_id, $this_action) 
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Sites');
      $this->load->model('Stores');
      $this->load->model('Stores_product');
      $this->load->model('Messages');
      $this->load->model('Api');
      $this->load->library(array('validation', 'auditor'));
      
      $store = $this->Stores->get_store_data($store_id);

      $products_carried = $this->Stores_product->get_products($store_id, 1);
      $admin['products_carried_exist'] = (count($products_carried) > 0) ? TRUE : FALSE;
      $products_not_carried = $this->Stores_product->get_products($store_id, 0);
      $admin['products_not_carried_exist'] = (count($products_not_carried) > 0) ? TRUE : FALSE;

      $rules['Products'] = 'trim';
      $rules['ProductSiteID'] = 'trim';
      $rules['ProductID'] = 'trim';
      $rules['Carried'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Products'] = 'Products list';
      $fields['ProductSiteID'] = 'Product Site ID';
      $fields['ProductID'] = 'Product ID';
      $fields['Carried'] = 'Carried';

      $this->validation->set_fields($fields);

      $defaults['Carried'] = 1;

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('stores');

      $map['Name'] = $store['StoreName'];
      $map['Address1'] = $store['Address1'];
      $map['Address2'] = $store['Address2'];
      $map['City'] = $store['City'];
      $map['State'] = $store['State'];
      $map['Zip'] = $store['Zip'];
      $map['Phone'] = $store['Phone'];

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Stores');
      $data['submenu'] = get_submenu('Search');
      $data['admin'] = $admin;
      $data['store_id'] = $store_id;
      $data['map_api_key'] = $this->Api->get_map_key();
      $data['site_list'] = $this->Sites->get_product_sites_list();
      $data['store'] = $map;
      $data['messages'] = $this->Messages->get_open_messages_by_store_id($store_id);
         
      // get the results of the products template
      $mydata['last_action'] = $this->session->userdata('last_action') + 1;
      $mydata['products_carried'] = $products_carried;
      $mydata['products_not_carried'] = $products_not_carried;
      $mydata['admin'] = $admin;
      $mydata['store_id'] = $store_id;
      $this->load->vars($mydata);
      $data['products'] =  $this->load->view('products/list', NULL, TRUE);

      $this->load->vars($data);
   	
      return $this->load->view('products/edit', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Builds a select list of products based on given site ID.
    *
    */
   function ajax_products($site_id)
   {
      $this->load->model('Products');
      
      $data['cats'] = $this->Products->get_product_category_list($site_id);
      $data['product_id'] = '';

      $this->load->vars($data);
   	
      echo $this->load->view('products/ajax_products', NULL, TRUE);
      exit;
   }


}
?>
