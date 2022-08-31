<?php

class Dbupload extends Controller {

   // set data for all the pull-down menus
   var $servers = array(
      '' => '',
      'dev' => 'Development (WebDev1)',
      'test' => 'Test (WebDev1)',
      'stage' => 'Staging (WebDB1)',
      'live' => 'Live (WebDB1)',
      'intranet' => 'Intranet (Dolphins)',
   );

   var $tables = array(
      '' => '',
      'Coolbrew: Products' => array(
         'coolbrew_products:pr_all' => 'ALL Product tables',
         'coolbrew_products:pr_category' => 'pr_category',
         'coolbrew_products:pr_ingredient' => 'pr_ingredient',
         'coolbrew_products:pr_ingredient_link' => 'pr_ingredient_link',
         'coolbrew_products:pr_product' => 'pr_product',
         'coolbrew_products:pr_product_category' => 'pr_product_category',
         'coolbrew_products:pr_product_site' => 'pr_product_site',
         'coolbrew_products:pr_nlea' => 'pr_nlea',
         'coolbrew_products:pr_symbol' => 'pr_symbol',
      ),
      'Coolbrew: Recipes' => array(
         'coolbrew_recipes:rcp_all' => 'ALL Recipe tables',
         'coolbrew_recipes:rcp_category' => 'rcp_category',
         'coolbrew_recipes:rcp_index' => 'rcp_index',
         'coolbrew_recipes:rcp_ingredient' => 'rcp_ingredient',
         'coolbrew_recipes:rcp_nutritional' => 'rcp_nutritional',
         'coolbrew_recipes:rcp_nutritional_calories' => 'rcp_nutritional_calories',
         'coolbrew_recipes:rcp_recipe' => 'rcp_recipe',
         'coolbrew_recipes:rcp_recipe_category' => 'rcp_recipe_category',
         'coolbrew_recipes:rcp_recipe_site' => 'rcp_recipe_site',
      ),
   );

   // I'm hard-coding this as well because not all sites are relevant.
   var $sites = array(
      'all'  => 'All Sites',
      'ab'   => 'Alba Botanica',
      'ad'   => 'Alba Drinks',
      'am'   => 'Arrowhead Mills',
      'bo'   => 'Boston\'s',
      'cb'   => 'Casbah',
      'cs'   => 'Celestial Seasonings',
      'csks' => 'Celestial Shots',
      'db'   => 'DeBoles',
      'eb'   => 'Earth\'s Best US',
      'ef'   => 'Estee',
      'eg'   => 'Ethnic Gourmet',
      'ge'   => 'Garden of Eatin\'',
      'gguf' => 'GG Unique Fiber',
      'gfc'  => 'Gluten Free Cafe',
      'gfch' => 'Gluten Free Choices',
      'gg'   => 'Greek Gods Yogurt',
      'hc'   => 'Hain Celestial (Corporate)',
      'hf'   => 'Hain Pure Foods',
      'ha'   => 'Harry\'s',
      'hv'   => 'Health Valley',
      'hn'   => 'Heather\'s Naturals',
      'hw'   => 'Hollywood',
      'if'   => 'Imagine US',
      'jn'   => 'Jason Natural',
      'lm'   => 'Linda McCartney',
      'lb'   => 'Little Bear/Bearitos',
      'lg'   => 'Low-G',
      'mn'   => 'MaraNatha',
      'ms'   => 'Mountain Sun',
      'ns'   => 'Nile Spice',
      'rp'   => 'Rosetto',
      'sp'   => 'Sensible Portions',
      'so'   => 'Spectrum Organics',
      'ss'   => 'SunSpire',
      'tahb' => 'Take a Healthy Bite',
      'td'   => 'Taste the Dream',
      'tc'   => 'Terra Chips',
      'wa'   => 'Walnut Acres',
      'wb'   => 'Westbrae',
      'ws'   => 'WestSoy',
      'wst'  => 'WestSoy Tofu',
      'yv'   => 'Yves',
      'zn'   => 'Zia',
   );

   // --------------------------------------------------------------------

   function Dbupload()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'uplive'));
      $this->load->helper(array('url', 'menu', 'json'));
   }
	
   // --------------------------------------------------------------------

   function index()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');
      
      $defaults = $this->session->userdata('dbupload_defaults');
      $this->session->set_userdata('dbupload_defaults', '');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');

      $rules['Source'] = 'trim|required';
      $rules['Target'] = 'trim|required';
      $rules['Table'] = 'trim|required';
      $rules['Site'] = 'trim|required';
      $rules['Where'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Source'] = 'Source';
      $fields['Target'] = 'Target';
      $fields['Table'] = 'Table';
      $fields['Site'] = 'Site';
      $fields['Where'] = 'Where';

      $this->validation->set_fields($fields);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         
         // get data for the various pulldown lists
         $data['servers'] = $this->servers;
         $data['tables'] = $this->tables;
         $data['sites'] = $this->sites;

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_main_tabs('Uplive');
         $data['submenu'] = get_submenu('Database Upload');
         $data['admin'] = $admin; // errors and messages

         $this->load->vars($data);
   	
         return $this->load->view('dbupload/list', NULL, TRUE);
      }
      else
      {
         $this->_upload();
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the database upload form
    *
    */
   function _upload()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
         
      list($model, $table) = explode(':', $values['Table']);
      
      $this->load->model($model);
      $msg = call_user_func(array($this->$model, 'move_'.$table), $values['Source'], $values['Target'], $values['Site'], $values['Where']);

      $msg .= '<span style="color:#666;font-size:0.8em;">Uploaded: '.date('Y-m-d H:i:s').'</span><br />';

      $this->session->set_userdata('admin_message', $msg);

      $this->session->set_userdata('dbupload_defaults', $values);
      redirect('dbupload/index');
   }

   // --------------------------------------------------------------------
   
   /**
    * Performs a database upload without the form (like an API)
    * http://webadmin.hcgweb.net/admin/uplive.php/dbupload/api stage&Target=live&Table=coolbrew_products:pr_all&Site=cb&Where=
    *
    */
   function api()
   {
      $defaults = $this->session->userdata('dbupload_defaults');
      $this->session->set_userdata('dbupload_defaults', '');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');

      $rules['Source'] = 'trim|required';
      $rules['Target'] = 'trim|required';
      $rules['Table'] = 'trim|required';
      $rules['Site'] = 'trim|required';
      $rules['Where'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Source'] = 'Source';
      $fields['Target'] = 'Target';
      $fields['Table'] = 'Table';
      $fields['Site'] = 'Site';
      $fields['Where'] = 'Where';

      $this->validation->set_fields($fields);
      
      $this->validation->set_defaults($defaults);
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->get($key);
         
      list($model, $table) = explode(':', $values['Table']);
      
      $this->load->model($model);
      $msg = call_user_func(array($this->$model, 'move_'.$table), $values['Source'], $values['Target'], $values['Site'], $values['Where']);

      $msg .= 'Uploaded: '.date('Y-m-d H:i:s');

      $response['res'] = 0;
      $response['res_message'] = $msg;

      echo json_encode($response);
      exit;
   }

}
