<?php

class Nutritionals extends Controller {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables

   function Nutritionals()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'products'));
      $this->load->helper(array('url', 'menu'));

      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('write', TRUE);
      $this->hcg_db = $this->load->database('hcg_write', TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a product's NLEA data
    *
    * Auditing: complete
    */
   function edit($site_id, $product_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
            
      $products['error_msg'] = $this->session->userdata('products_error');
      if ($this->session->userdata('products_error') != '')
         $this->session->set_userdata('products_error', '');

      $products['message'] = $this->session->userdata('product_message');
      if ($this->session->userdata('product_message') != '')
         $this->session->set_userdata('product_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);
      
      $old_values = $this->Products->get_nlea_data($product_id);
      
      $rules['TYPE'] = 'trim|required';
      $rules['SSIZE'] = 'trim';
      $rules['MAKE'] = 'trim';
      $rules['SERV'] = 'trim';
      $rules['COL1HD'] = 'trim';
      $rules['CAL'] = 'trim';
      $rules['FATCAL'] = 'trim';
      $rules['TFATQ'] = 'trim';
      $rules['TFATP'] = 'trim';
      $rules['SFATQ'] = 'trim';
      $rules['SFATP'] = 'trim';
      $rules['PFATQ'] = 'trim';
      $rules['MFATQ'] = 'trim';
      $rules['HFATQ'] = 'trim';
      $rules['CHOLQ'] = 'trim';
      $rules['CHOLP'] = 'trim';
      $rules['SODQ'] = 'trim';
      $rules['SODP'] = 'trim';
      $rules['POTQ'] = 'trim';
      $rules['POTP'] = 'trim';
      $rules['TCARBQ'] = 'trim';
      $rules['TCARBP'] = 'trim';
      $rules['DFIBQ'] = 'trim';
      $rules['DFIBP'] = 'trim';
      $rules['SFIBQ'] = 'trim';
      $rules['IFIBQ'] = 'trim';
      $rules['SUGQ'] = 'trim';
      $rules['OCARBQ'] = 'trim';
      $rules['PROTQ'] = 'trim';
      $rules['PROTP'] = 'trim';
      $rules['VITAP'] = 'trim';
      $rules['VITCQ'] = 'trim';
      $rules['VITCP'] = 'trim';
      $rules['CALCP'] = 'trim';
      $rules['IRONP'] = 'trim';
      $rules['VITDP'] = 'trim';
      $rules['VITEP'] = 'trim';
      $rules['VITKP'] = 'trim';
      $rules['THIAP'] = 'trim';
      $rules['RIBOP'] = 'trim';
      $rules['NIACP'] = 'trim';
      $rules['VITB6P'] = 'trim';
      $rules['FOLICP'] = 'trim';
      $rules['FOLATEP'] = 'trim';
      $rules['CHLORP'] = 'trim';
      $rules['VITB12P'] = 'trim';
      $rules['BIOTINP'] = 'trim';
      $rules['PACIDP'] = 'trim';
      $rules['PHOSP'] = 'trim';
      $rules['IODIP'] = 'trim';
      $rules['MAGNP'] = 'trim';
      $rules['ZINCP'] = 'trim';
      $rules['SELEP'] = 'trim';
      $rules['COPPP'] = 'trim';
      $rules['MANGP'] = 'trim';
      $rules['CHROMP'] = 'trim';
      $rules['MOLYP'] = 'trim';
      $rules['COL2HD'] = 'trim';
      $rules['CAL2'] = 'trim';
      $rules['FATCAL2'] = 'trim';
      $rules['TFATQ2'] = 'trim';
      $rules['TFATP2'] = 'trim';
      $rules['SFATQ2'] = 'trim';
      $rules['SFATP2'] = 'trim';
      $rules['PFATQ2'] = 'trim';
      $rules['MFATQ2'] = 'trim';
      $rules['HFATQ2'] = 'trim';
      $rules['CHOLQ2'] = 'trim';
      $rules['CHOLP2'] = 'trim';
      $rules['SODQ2'] = 'trim';
      $rules['SODP2'] = 'trim';
      $rules['POTQ2'] = 'trim';
      $rules['POTP2'] = 'trim';
      $rules['TCARBQ2'] = 'trim';
      $rules['TCARBP2'] = 'trim';
      $rules['DFIBQ2'] = 'trim';
      $rules['DFIBP2'] = 'trim';
      $rules['SFIBQ2'] = 'trim';
      $rules['IFIBQ2'] = 'trim';
      $rules['SUGQ2'] = 'trim';
      $rules['OCARBQ2'] = 'trim';
      $rules['PROTQ2'] = 'trim';
      $rules['PROTP2'] = 'trim';
      $rules['VITAP2'] = 'trim';
      $rules['VITCP2'] = 'trim';
      $rules['CALCP2'] = 'trim';
      $rules['IRONP2'] = 'trim';
      $rules['VITDP2'] = 'trim';
      $rules['VITEP2'] = 'trim';
      $rules['VITKP2'] = 'trim';
      $rules['THIAP2'] = 'trim';
      $rules['RIBOP2'] = 'trim';
      $rules['NIACP2'] = 'trim';
      $rules['VITB6P2'] = 'trim';
      $rules['FOLICP2'] = 'trim';
      $rules['FOLATEP2'] = 'trim';
      $rules['CHLORP2'] = 'trim';
      $rules['VITB12P2'] = 'trim';
      $rules['BIOTINP2'] = 'trim';
      $rules['PACIDP2'] = 'trim';
      $rules['PHOSP2'] = 'trim';
      $rules['IODIP2'] = 'trim';
      $rules['MAGNP2'] = 'trim';
      $rules['ZINCP2'] = 'trim';
      $rules['SELEP2'] = 'trim';
      $rules['COPPP2'] = 'trim';
      $rules['MANGP2'] = 'trim';
      $rules['CHROMP2'] = 'trim';
      $rules['MOLYP2'] = 'trim';
      $rules['STMT1'] = 'trim';
      $rules['STMT1Q'] = 'trim';
      $rules['STMT2'] = 'trim';
      $rules['STMT2Q'] = 'trim';
      $rules['PDV1'] = 'trim';
      $rules['PDV2'] = 'trim';
      $rules['PDVT'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['TYPE'] = 'Display Type';
      $fields['SSIZE'] = 'Serving Size';
      $fields['MAKE'] = 'Makes';
      $fields['SERV'] = 'Servings per Container';
      $fields['COL1HD'] = 'Heading, column 1 ';
      $fields['CAL'] = 'Calories, column 1';
      $fields['FATCAL'] = 'Calories from Fat, column 1';
      $fields['TFATQ'] = 'Total Fat, column 1';
      $fields['TFATP'] = 'Total Fat %, column 1';
      $fields['SFATQ'] = 'Saturated Fat, column 1';
      $fields['SFATP'] = 'Saturated Fat %, column 1';
      $fields['PFATQ'] = 'Polyunsaturated Fat, column 1';
      $fields['MFATQ'] = 'Monounsaturated Fat, column 1';
      $fields['HFATQ'] = 'Trans Fat, column 1';
      $fields['CHOLQ'] = 'Cholesterol, column 1';
      $fields['CHOLP'] = 'Cholesterol %, column 1';
      $fields['SODQ'] = 'Sodium, column 1';
      $fields['SODP'] = 'Sodium %, column 1';
      $fields['POTQ'] = 'Potasium, column 1';
      $fields['POTP'] = 'Potasium %, column 1';
      $fields['TCARBQ'] = 'Total Carb, column 1';
      $fields['TCARBP'] = 'Total Carb %, column 1';
      $fields['DFIBQ'] = 'Dietary Fiber, column 1';
      $fields['DFIBP'] = 'Dietary Fiber %, column 1';
      $fields['SFIBQ'] = 'Soluble Fiber, column 1';
      $fields['IFIBQ'] = 'Insoluble Fiber, column 1';
      $fields['SUGQ'] = 'Sugars, column 1';
      $fields['OCARBQ'] = 'Other Carbs, column 1';
      $fields['PROTQ'] = 'Protein, column 1';
      $fields['PROTP'] = 'Protein %, column 1';
      $fields['VITAP'] = 'Vitamin A %, column 1';
      $fields['VITCQ'] = 'Vitamin C, column 1';
      $fields['VITCP'] = 'Vitamin C %, column 1';
      $fields['CALCP'] = 'Calcium %, column 1';
      $fields['IRONP'] = 'Iron %, column 1';
      $fields['VITDP'] = 'Vitamin D %, column 1';
      $fields['VITEP'] = 'Vitamin E %, column 1';
      $fields['VITKP'] = 'Vitamin K %, column 1';
      $fields['THIAP'] = 'Thiamin %, column 1';
      $fields['RIBOP'] = 'Riboflavin %, column 1';
      $fields['NIACP'] = 'Niacin %, column 1';
      $fields['VITB6P'] = 'Vitamin B6 %, column 1';
      $fields['FOLICP'] = 'Folic Acid %, column 1';
      $fields['FOLATEP'] = 'Folate %, column 1';
      $fields['CHLORP'] = 'Chloride %, column 1';
      $fields['VITB12P'] = 'Vitamin B12 %, column 1';
      $fields['BIOTINP'] = 'Biotin %, column 1';
      $fields['PACIDP'] = 'Pantothenic Acid %, column 1';
      $fields['PHOSP'] = 'Phosphorus %, column 1';
      $fields['IODIP'] = 'Iodine %, column 1';
      $fields['MAGNP'] = 'Magnesium %, column 1';
      $fields['ZINCP'] = 'Zinc %, column 1';
      $fields['SELEP'] = 'Selenium %, column 1';
      $fields['COPPP'] = 'Copper %, column 1';
      $fields['MANGP'] = 'Manganese %, column 1';
      $fields['CHROMP'] = 'Chromium %, column 1';
      $fields['MOLYP'] = 'Molybdenum %, column 1';
      $fields['COL2HD'] = 'Heading, column 2 ';
      $fields['CAL2'] = 'Calories, column 2';
      $fields['FATCAL2'] = 'Calories from Fat, column 2';
      $fields['TFATQ2'] = 'Total Fat, column 2';
      $fields['TFATP2'] = 'Total Fat %, column 2';
      $fields['SFATQ2'] = 'Saturated Fat, column 2';
      $fields['SFATP2'] = 'Saturated Fat %, column 2';
      $fields['PFATQ2'] = 'Polyunsaturated Fat, column 2';
      $fields['MFATQ2'] = 'Monounsaturated, column 2';
      $fields['HFATQ2'] = 'Trans Fat, column 2';
      $fields['CHOLQ2'] = 'Cholesterol, column 2';
      $fields['CHOLP2'] = 'Cholesterol %, column 2';
      $fields['SODQ2'] = 'Sodium, column 2';
      $fields['SODP2'] = 'Sodium %, column 2';
      $fields['POTQ2'] = 'Potasium, column 2';
      $fields['POTP2'] = 'Potasium %, column 2';
      $fields['TCARBQ2'] = 'Total Carb, column 2';
      $fields['TCARBP2'] = 'Total Carb %, column 2';
      $fields['DFIBQ2'] = 'Dietary Fiber, column 2';
      $fields['DFIBP2'] = 'Dietary Fiber %, column 2';
      $fields['SFIBQ2'] = 'Soluble Fiber, column 2';
      $fields['IFIBQ2'] = 'Insoluble Fiber, column 2';
      $fields['SUGQ2'] = 'Sugars, column 2';
      $fields['OCARBQ2'] = 'Other Carbs, column 2';
      $fields['PROTQ2'] = 'Protein, column 2';
      $fields['PROTP2'] = 'Protein %, column 2';
      $fields['VITAP2'] = 'Vitamin A %, column 2';
      $fields['VITCP2'] = 'Vitamin C %, column 2';
      $fields['CALCP2'] = 'Calcium %, column 2';
      $fields['IRONP2'] = 'Iron %, column 2';
      $fields['VITDP2'] = 'Vitamin D %, column 2';
      $fields['VITEP2'] = 'Vitamin E %, column 2';
      $fields['VITKP2'] = 'Vitamin K %, column 2';
      $fields['THIAP2'] = 'Thiamin %, column 2';
      $fields['RIBOP2'] = 'Riboflavin %, column 2';
      $fields['NIACP2'] = 'Niacin %, column 2';
      $fields['VITB6P2'] = 'Vitamin B6 %, column 2';
      $fields['FOLICP2'] = 'Folic Acid %, column 2';
      $fields['FOLATEP2'] = 'Folate %, column 2';
      $fields['CHLORP2'] = 'Chloride %, column 2';
      $fields['VITB12P2'] = 'Vitamin B12 %, column 2';
      $fields['BIOTINP2'] = 'Biotin %, column 2';
      $fields['PACIDP2'] = 'Pantothenic Acid %, column 2';
      $fields['PHOSP2'] = 'Phosphorus %, column 2';
      $fields['IODIP2'] = 'Iodine %, column 2';
      $fields['MAGNP2'] = 'Magnesium %, column 2';
      $fields['ZINCP2'] = 'Zinc %, column 2';
      $fields['SELEP2'] = 'Selenium %, column 2';
      $fields['COPPP2'] = 'Copper %, column 2';
      $fields['MANGP2'] = 'Manganese %, column 2';
      $fields['CHROMP2'] = 'Chromium %, column 2';
      $fields['MOLYP2'] = 'Molybdenum %, column 2';
      $fields['STMT1'] = 'Display Not a Significant Source';
      $fields['STMT1Q'] = 'Not a Significant Source statement';
      $fields['STMT2'] = 'Display Prep Statement';
      $fields['STMT2Q'] = 'Prep Statement';
      $fields['PDV1'] = 'Display short % Daily Values';
      $fields['PDV2'] = 'Display long % Daily Values';
      $fields['PDVT'] = 'Display % Daily Values table';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      
      // We need to translate some of the field names from the database 
      // to a version that can be used in PHP class variables. Variables
      // starting with a number are not allowed.
      $db_to_new = array(
         '2CAL' => 'CAL2',
         '2FATCAL' => 'FATCAL2',
         '2TFATP' => 'TFATP2',
         '2SFATP' => 'SFATP2',
         '2CHOLP' => 'CHOLP2',
         '2SODP' => 'SODP2',
         '2POTP' => 'POTP2',
         '2TCARBP' => 'TCARBP2',
         '2DFIBP' => 'DFIBP2',
         '2PROTP' => 'PROTP2',
         '2VITAP' => 'VITAP2',
         '2VITCP' => 'VITCP2',
         '2CALCP' => 'CALCP2',
         '2IRONP' => 'IRONP2',
         '2VITDP' => 'VITDP2',
         '2VITB6P' => 'VITB6P2',
         '2FOLATEP' => 'FOLATEP2',
         '2VITB12P' => 'VITB12P2',
         '2VITEP' => 'VITEP2',
         '2THIAP' => 'THIAP2',
         '2RIBOP' => 'RIBOP2',
         '2PHOSP' => 'PHOSP2',
         '2MAGNP' => 'MAGNP2',
         '2NIACP' => 'NIACP2',
         '2ZINCP' => 'ZINCP2',
         '2FOLICP' => 'FOLICP2',
         '2CHLORP' => 'CHLORP2',
         '2BIOTINP' => 'BIOTINP2',
         '2PACIDP' => 'PACIDP2',
         '2IODIP' => 'IODIP2',
         '2SELEP' => 'SELEP2',
         '2COPPP' => 'COPPP2',
         '2MANGP' => 'MANGP2',
         '2CHROMP' => 'CHROMP2',
         '2MOLYP' => 'MOLYP2',
         '2VITKP' => 'VITKP2',
      );
      foreach ($db_to_new AS $key => $name)
      {
         $defaults[$name] = $defaults[$key];
         unset($defaults[$key]);
      }
      
      $defaults['COL1HD'] = ($defaults['COL1HD'] != '') ? $defaults['COL1HD'] : '% Daily Value*'; 
      $defaults['COL2HD'] = (trim($defaults['COL2HD']) != '') ? $defaults['COL2HD'] : 'With Milk'; 

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');
         
         $data['yesno'] = array('' => '', 'yes' => 'Yes', 'no'=> 'No');
         $data['types'] = array('' => '-- Choose a type --',
                                '0' => 'US Normal',
                                '1' => 'US Prepared',
                                '2' => 'US Baby Food',
                                '3' => 'US Baby Food Prepared',
                                '5' => 'CA Normal (English)',
                                '4' => 'CA Baby Food (English)',
                                '6' => 'CA Normal (French)',
                                '7' => 'CA Baby Food (French)');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
         $data['submenu'] = get_submenu($site_id, 'Products');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['product'] = $this->Products->get_product_data($product_id, $site_id);
         $data['products'] = $products; // errors and messages
         $data['product_id'] = $product_id;

         $this->load->vars($data);
   	
         return $this->load->view('nleas/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $product_id, $old_values);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit nlea form
    *
    * Auditing: complete
    */
   function _edit($site_id, $product_id, $old_values)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($product_id == 0)
      {
         show_error('_edit_nlea requires that a product ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // translate fields back to the ones used in the database.
      $new_to_db = array(
         'CAL2' => '2CAL',
         'FATCAL2' => '2FATCAL',
         'TFATP2' => '2TFATP',
         'SFATP2' => '2SFATP',
         'CHOLP2' => '2CHOLP',
         'SODP2' => '2SODP',
         'POTP2' => '2POTP',
         'TCARBP2' => '2TCARBP',
         'DFIBP2' => '2DFIBP',
         'PROTP2' => '2PROTP',
         'VITAP2' => '2VITAP',
         'VITCP2' => '2VITCP',
         'CALCP2' => '2CALCP',
         'IRONP2' => '2IRONP',
         'VITDP2' => '2VITDP',
         'VITB6P2' => '2VITB6P',
         'FOLATEP2' => '2FOLATEP',
         'VITB12P2' => '2VITB12P',
         'VITEP2' => '2VITEP',
         'THIAP2' => '2THIAP',
         'RIBOP2' => '2RIBOP',
         'PHOSP2' => '2PHOSP',
         'MAGNP2' => '2MAGNP',
         'NIACP2' => '2NIACP',
         'ZINCP2' => '2ZINCP',
         'FOLICP2' => '2FOLICP',
         'CHLORP2' => '2CHLORP',
         'BIOTINP2' => '2BIOTINP',
         'PACIDP2' => '2PACIDP',
         'IODIP2' => '2IODIP',
         'SELEP2' => '2SELEP',
         'COPPP2' => '2COPPP',
         'MANGP2' => '2MANGP',
         'CHROMP2' => '2CHROMP',
         'MOLYP2' => '2MOLYP',
         'VITKP2' => '2VITKP',
      );
      foreach ($new_to_db AS $key => $name)
      {
         $values[$name] = $values[$key];
         unset($values[$key]);
      }

      $tmp = $this->cb_db->where('ProductID', $product_id);
      $this->cb_db->update('pr_nlea', $values);
      $this->hcg_db->where('ProductID', $product_id);
      $this->hcg_db->update('pr_nlea', $values);
      
      $this->auditor->audit_update('pr_nlea', $tmp->ar_where, $old_values, $values);
      
      $product = $this->Products->get_product_data($product_id, $site_id);

      $this->session->set_userdata('product_message', 'The nutrition facts for '.$product['ProductName'].' have been updated.');

      $last_action = $this->session->userdata('last_action') + 1;

      redirect('nleas/edit/'.$site_id.'/'.$product_id.'/'.$last_action.'/');
   }

   
}
?>
