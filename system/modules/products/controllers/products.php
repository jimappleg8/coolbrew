<?php

class Products extends Controller {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables

   function Products()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'products'));
      $this->load->helper(array('url', 'menu'));

      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('read', TRUE);
      $this->hcg_db = $this->load->database('hcg_read', TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of this site's active products
    *
    */
   function index($site_id, $category_code = 'all')
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $admin['message'] = $this->session->userdata('products_message');
      if ($this->session->userdata('products_message') != '')
         $this->session->set_userdata('products_message', '');

      $admin['error_msg'] = $this->session->userdata('products_error');
      if ($this->session->userdata('products_error') != '')
         $this->session->set_userdata('products_error', '');
         
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->helper('upc');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      $site = $this->Sites->get_site_data($site_id);
      
//      $root = $this->Categories->get_category_root($site_id);
      $category_list = $this->Categories->get_active_category_tree($site_id);

      if ($category_code == 'all')
      {
         $category = array();
         $product_list = $this->Products->get_products_in_site($site_id);
         $nocat_list = $this->Products->get_nocat_products_in_site($site_id);
         $admin['limited'] = FALSE;
      }
      elseif ($category_code == 'none')
      {
         $category = array();
         $product_list = array();
         $nocat_list = $this->Products->get_nocat_products_in_site($site_id);
         $admin['limited'] = TRUE;
      }
      else
      {
         $category = $this->Categories->get_category_data_by_code($site_id, $category_code);
         $product_list = $this->Products->get_products_in_category($site_id, $category['ID']);
         $admin['limited'] = TRUE;
      }

      $admin['product_exists'] = (count($product_list) == 0 && count($nocat_list) == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('products');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
      $data['submenu'] = get_submenu($site_id, 'Products');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      $data['product_list'] = $product_list;
      $data['nocat_list'] = $nocat_list;
      $data['category'] = $category;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('products/list', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of this site's discontinued products
    *
    */
   function discontinued($site_id, $category_code = 'all')
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $products['error_msg'] = $this->session->userdata('products_error');
      if ($this->session->userdata('products_error') != '')
         $this->session->set_userdata('products_error', '');
         
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->helper('upc');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      $site = $this->Sites->get_site_data($site_id);
      
//      $root = $this->Categories->get_category_root($site_id);
      $category_list = $this->Categories->get_category_tree($site_id);

      if ($category_code == 'all')
      {
         $category = array();
         $product_list = $this->Products->get_discontinued_products_in_site($site_id);
         $nocat_list = $this->Products->get_nocat_discontinued_products_in_site($site_id);
         $products['limited'] = FALSE;
      }
      elseif ($category_code == 'none')
      {
         $category = array();
         $product_list = array();
         $nocat_list = $this->Products->get_nocat_discontinued_products_in_site($site_id);
         $products['limited'] = TRUE;
      }
      else
      {
         $category = $this->Categories->get_category_data_by_code($site_id, $category_code);
         $product_list = $this->Products->get_discontinued_products_in_category($site_id, $category['ID']);
         $products['limited'] = TRUE;
      }

      $products['product_exists'] = (count($product_list) == 0 && count($nocat_list) == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('products');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
      $data['submenu'] = get_discontinued_submenu($site_id, 'Products');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['products'] = $products;
      $data['product_list'] = $product_list;
      $data['nocat_list'] = $nocat_list;
      $data['category'] = $category;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('products/discontinued', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Copies a product record and it's associated NLEA record
    *
    * Auditing: complete
    */
   function copy($site_id, $product_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Products');
      $this->load->library('auditor');
      
      // Part 1: duplicate the product record
      
      $values = $this->Products->get_product_record($product_id);
      
      unset($values['ProductID']);
      unset($values['UPC']);
      
      $values['ProductName'] = 'Copy of '.$values['ProductName'];
      $values['LastModifiedDate'] = date('Y-m-d H:i:s');
      $values['LastModifiedBy'] = $this->session->userdata('username');
      
      $this->cb_db->insert('pr_product', $values);
      $this->hcg_db->insert('pr_product', $values);
      
      $new_id = $this->cb_db->insert_id();
      
      $values['LocatorCode'] = strtoupper($site_id).$new_id;

      $this->auditor->audit_insert('pr_product', '', $values);
      
      // update the Locator code (based on new ProductID)
      $locator['LocatorCode'] = $values['LocatorCode'];
      $this->cb_db->where('ProductID', $new_id);
      $this->cb_db->update('pr_product', $locator);
      $this->hcg_db->where('ProductID', $new_id);
      $this->hcg_db->update('pr_product', $locator);

      // Part 2: assign the new record to the site
      
      $site['ProductID'] = $new_id;
      $site['SiteID'] = $site_id;
      
      $this->cb_db->insert('pr_product_site', $site);

      $this->auditor->audit_insert('pr_product_site', '', $site);
      
      // Part 2: duplicate the associated NLEA record
      
      $nlea = $this->Products->get_nlea_data($product_id);
      
      $nlea['ProductID'] = $new_id;
      $nlea['ProductName'] = $values['ProductName'];
      
      $this->cb_db->insert('pr_nlea', $nlea);
      $this->hcg_db->insert('pr_nlea', $nlea);

      $this->auditor->audit_insert('pr_nlea', '', $nlea);

      redirect('products/index/'.$site_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a product
    *
    */
   function delete($site_id, $product_id, $this_action) 
   {
      $this->administrator->check_group('admin');

      $this->load->helper('text');
      $this->load->model('Products');
      
      // get the current record so we can display a status message
      $product = $this->Products->get_product_record($product_id);
      
      // delete the product record and associated records
      $this->Products->delete_product($product_id);
      
      $this->session->set_userdata('products_message', 'The product  "'.$product['ProductName'].'" has been deleted.');
      
      redirect("products/index/".$site_id);
   }

   // --------------------------------------------------------------------

   /**
    * Adds a product listing.
    *
    */
   function add($site_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $products['message'] = $this->session->userdata('product_message');
      if ($this->session->userdata('product_message') != '')
         $this->session->set_userdata('product_message', '');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Products');
      $this->load->model('Sites');
      $this->load->library('validation');
      
      $site = $this->Sites->get_site_data($site_id);
      
      $rules['UPC'] = 'trim'; //|required';
      $rules['Status'] = 'trim'; //|required';
      $rules['Language'] = 'trim'; //|required';
      $rules['ProductName'] = 'trim|required';
      $rules['LongDescription'] = 'trim'; //|required';
      $rules['Ingredients'] = 'trim'; //|required';

      $this->validation->set_rules($rules);

      $fields['UPC'] = 'UPC';
      $fields['Status'] = 'Status';
      $fields['Language'] = 'Language';
      $fields['ProductName'] = 'Product Name';
      $fields['LongDescription'] = 'Long Description';
      $fields['Ingredients'] = 'Ingredients';

      $this->validation->set_fields($fields);

      $defaults['Status'] = 'active';
      $defaults['Language'] = 'en_US';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');
      
         // get data for the various pulldown lists
         $data['statuses'] = array('discontinued' => 'discontinued', 
                                   'active' => 'active', 
                                   'pending' => 'pending', 
                                   'partial' => 'partial', 
                                   'inactive' => 'inactive');
         $data['languages'] = array('en_US' => 'en_US', 
                                    'en_CA' => 'en_CA', 
                                    'fr_CA' => 'fr_CA');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
         $data['submenu'] = get_submenu($site_id, 'Products');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['products'] = $products; // errors and messages
      
         $this->load->vars($data);
   	
         return $this->load->view('products/add', NULL, TRUE);
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
    * Processes the add product form
    *
    * Auditing: complete
    */
   function _add($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // the site ID is required for sites using hcgPublic
      $values['SiteID'] = $site_id;
      $values['ProductGroup'] = 'none';

      // process the form text (convert special characters and the like)
      $values['ProductName'] = ascii_to_entities($values['ProductName']);
      $values['LongDescription'] = ascii_to_entities($values['LongDescription']);
      $values['Ingredients'] = ascii_to_entities($values['Ingredients']);
      
      $product_id = $this->Products->insert_product($values);

      $last_action = $this->session->userdata('last_action') + 1;

      redirect("products/edit/".$site_id.'/'.$product_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Updates a product listing
    *
    * Auditing: complete
    */
   function edit($site_id, $product_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
            
      $admin['message'] = $this->session->userdata('product_message');
      if ($this->session->userdata('product_message') != '')
         $this->session->set_userdata('product_message', '');

      $admin['group'] = $this->session->userdata('group');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Products');
      $this->load->model('Symbols');
      $this->load->model('Sites');
      $this->load->model('Ingredients');
      $this->load->model('Nleas');
      $this->load->library(array('validation', 'auditor'));
      
      $site = $this->Sites->get_site_data($site_id);
      
      $nlea = $this->Nleas->get_nlea_data($product_id);
      $old_values = $this->Products->get_product_data($product_id, $site_id);
      
      $rules['UPC'] = 'trim';
      $rules['FilterID'] = 'trim';
      $rules['Status'] = 'trim';
      $rules['Language'] = 'trim';
      $rules['SESFilename'] = 'trim';
      $rules['Verified'] = 'trim';
      $rules['SortOrder'] = 'trim';
      $rules['FlagAsNew'] = 'trim';
      $rules['Featured'] = 'trim';
      $rules['ProductGroup'] = 'trim';
      $rules['ProductName'] = 'trim';
      $rules['PackageSize'] = 'trim';
      $rules['LongDescription'] = 'trim';
      $rules['Teaser'] = 'trim';
      $rules['Benefits'] = 'trim';
      $rules['AvailableIn'] = 'trim';
      $rules['Footnotes'] = 'trim';
      $rules['Ingredients'] = 'trim';
      $rules['NutritionBlend'] = 'trim';
      $rules['Standardization'] = 'trim';
      $rules['Directions'] = 'trim';
      $rules['Warning'] = 'trim';
      $rules['AllNatural'] = 'trim';
      $rules['Gluten'] = 'trim';
      $rules['OrganicStatement'] = 'trim';
      $rules['Alergens'] = 'trim';
      $rules['SpiceLevel'] = 'trim';
      $rules['FeatureFile'] = 'trim';
      $rules['FeatureWidth'] = 'trim';
      $rules['FeatureHeight'] = 'trim';
      $rules['FeatureAlt'] = 'trim';
      $rules['BeautyFile'] = 'trim';
      $rules['BeautyWidth'] = 'trim';
      $rules['BeautyHeight'] = 'trim';
      $rules['BeautyAlt'] = 'trim';
      $rules['ThumbFile'] = 'trim';
      $rules['ThumbWidth'] = 'trim';
      $rules['ThumbHeight'] = 'trim';
      $rules['ThumbAlt'] = 'trim';
      $rules['SmallFile'] = 'trim';
      $rules['SmallWidth'] = 'trim';
      $rules['SmallHeight'] = 'trim';
      $rules['SmallAlt'] = 'trim';
      $rules['LargeFile'] = 'trim';
      $rules['LargeWidth'] = 'trim';
      $rules['LargeHeight'] = 'trim';
      $rules['LargeAlt'] = 'trim';
      $rules['NutritionFacts'] = 'trim';
      $rules['KosherSymbol'] = 'trim';
      $rules['OrganicSymbol'] = 'trim';
      $rules['CaffeineFile'] = 'trim';
      $rules['CaffeineWidth'] = 'trim';
      $rules['CaffeineHeight'] = 'trim';
      $rules['CaffeineAlt'] = 'trim';
      $rules['StoreSection'] = 'trim';
      $rules['StoreSectionPostfix'] = 'trim';
      $rules['StoreDetail'] = 'trim';
      $rules['LocatorCode'] = 'trim';
      $rules['MenuSubsection'] = 'trim';
      $rules['DiscontinueDate'] = 'trim';
      $rules['Replacements'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaMisc'] = 'trim';
      $rules['MetaRobots'] = 'trim';
      $rules['Components'] = 'trim';
      $rules['ProductType'] = 'trim';
      $rules['FlavorDescriptor'] = 'trim';
      
      $rules['BenefitsDisplay'] = 'trim';
      $rules['SmartBenefits'] = 'trim';
      $rules['NSSodium'] = 'trim';
      $rules['NSFat'] = 'trim';
      $rules['NSFiber'] = 'trim';
      $rules['NSAntioxidants'] = 'trim';
      $rules['NSAntioxidantChoice'] = 'trim';
      $rules['NSCalories'] = 'trim';
      $rules['NSOther'] = 'trim';
      $rules['NSOtherChoice'] = 'trim';
      $rules['NSOtherQuantity'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['UPC'] = 'UPC';
      $fields['FilterID'] = 'Filter ID';
      $fields['Status'] = 'Status';
      $fields['Language'] = 'Language';
      $fields['SESFilename'] = 'SES Filename';
      $fields['Verified'] = 'Verified';
      $fields['SortOrder'] = 'Sort Order';
      $fields['FlagAsNew'] = 'Flag As New';
      $fields['Featured'] = 'Featured';
      $fields['ProductGroup'] = 'Product Group';
      $fields['ProductName'] = 'Product Name';
      $fields['PackageSize'] = 'Package Size';
      $fields['LongDescription'] = 'Long Description';
      $fields['Teaser'] = 'Teaser';
      $fields['Benefits'] = 'Benefits';
      $fields['AvailableIn'] = 'Available In';
      $fields['Footnotes'] = 'Footnotes';
      $fields['Ingredients'] = 'Ingredients';
      $fields['NutritionBlend'] = 'Nutrition Blend';
      $fields['Standardization'] = 'Standardization';
      $fields['Directions'] = 'Directions';
      $fields['Warning'] = 'Warning';
      $fields['AllNatural'] = 'All Natural';
      $fields['Gluten'] = 'Gluten';
      $fields['OrganicStatement'] = 'Organic Statement';
      $fields['Alergens'] = 'Alergens';
      $fields['SpiceLevel'] = 'Spice Level';
      $fields['FeatureFile'] = 'Feature File';
      $fields['FeatureWidth'] = 'Feature Width';
      $fields['FeatureHeight'] = 'Feature Height';
      $fields['FeatureAlt'] = 'Feature Alt';
      $fields['BeautyFile'] = 'Beauty File';
      $fields['BeautyWidth'] = 'Beauty Width';
      $fields['BeautyHeight'] = 'Beauty Height';
      $fields['BeautyAlt'] = 'Beauty Alt';
      $fields['ThumbFile'] = 'Thumb File';
      $fields['ThumbWidth'] = 'Thumb Width';
      $fields['ThumbHeight'] = 'Thumb Height';
      $fields['ThumbAlt'] = 'Thumb Alt';
      $fields['SmallFile'] = 'Small File';
      $fields['SmallWidth'] = 'Small Width';
      $fields['SmallHeight'] = 'Small Height';
      $fields['SmallAlt'] = 'Small Alt';
      $fields['LargeFile'] = 'Large File';
      $fields['LargeWidth'] = 'Large Width';
      $fields['LargeHeight'] = 'Large Height';
      $fields['LargeAlt'] = 'Large Alt';
      $fields['NutritionFacts'] = 'Nutrition Facts';
      $fields['KosherSymbol'] = 'Kosher Symbol';
      $fields['OrganicSymbol'] = 'Organic Symbol';
      $fields['CaffeineFile'] = 'Caffeine File';
      $fields['CaffeineWidth'] = 'Caffeine Width';
      $fields['CaffeineHeight'] = 'Caffeine Height';
      $fields['CaffeineAlt'] = 'Caffeine Alt';
      $fields['StoreSection'] = 'Store Category ID';
      $fields['StoreSectionPostfix'] = 'Store Category ID Postfix';
      $fields['StoreDetail'] = 'Store Product ID';
      $fields['LocatorCode'] = 'Locator Code';
      $fields['MenuSubsection'] = 'Menu Subsection';
      $fields['DiscontinueDate'] = 'Discontinue Date';
      $fields['Replacements'] = 'Replacements';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaMisc'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Robots';
      $fields['Components'] = 'Components';
      $fields['ProductType'] = 'Product Type';
      $fields['FlavorDescriptor'] = 'Flavor Descriptor';
      
      $fields['BenefitsDisplay'] = 'Benefit Display Options';
      $fields['SmartBenefits'] = 'Smart Benefits';
      $fields['NSSodium'] = 'Nutrition Scorecard Sodium';
      $fields['NSFat'] = 'Nutrition Scorecard Fat';
      $fields['NSFiber'] = 'Nutrition Scorecard Fiber';
      $fields['NSAntioxidants'] = 'Nutrition Scorecard Antioxidants';
      $fields['NSAntioxidantChoice'] = 'Nutrition Scorecard Antioxidant Choice';
      $fields['NSCalories'] = 'Nutrition Scorecard Calories';
      $fields['NSOther'] = 'Nutrition Scorecard Other';
      $fields['NSOtherChoice'] = 'Nutrition Scorecard Other Choice';
      $fields['NSOtherQuantity'] = 'Nutrition Scorecard Other Quantity';

      $this->validation->set_fields($fields);

      $defaults = $old_values;

      $defaults['ProductName'] = entities_to_ascii($defaults['ProductName']);
      $defaults['PackageSize'] = entities_to_ascii($defaults['PackageSize']);
      $defaults['LongDescription'] = entities_to_ascii($defaults['LongDescription']);
      $defaults['Teaser'] = entities_to_ascii($defaults['Teaser']);
      $defaults['Benefits'] = entities_to_ascii($defaults['Benefits']);
      $defaults['AvailableIn'] = entities_to_ascii($defaults['AvailableIn']);
      $defaults['Footnotes'] = entities_to_ascii($defaults['Footnotes']);
      $defaults['Ingredients'] = entities_to_ascii($defaults['Ingredients']);
      $defaults['NutritionBlend'] = entities_to_ascii($defaults['NutritionBlend']);
      $defaults['Standardization'] = entities_to_ascii($defaults['Standardization']);
      $defaults['Directions'] = entities_to_ascii($defaults['Directions']);
      $defaults['Warning'] = entities_to_ascii($defaults['Warning']);
      $defaults['AllNatural'] = entities_to_ascii($defaults['AllNatural']);
      $defaults['Gluten'] = entities_to_ascii($defaults['Gluten']);
      $defaults['OrganicStatement'] = entities_to_ascii($defaults['OrganicStatement']);
      $defaults['Alergens'] = entities_to_ascii($defaults['Alergens']);
      $defaults['SpiceLevel'] = entities_to_ascii($defaults['SpiceLevel']);
      $defaults['Replacements'] = entities_to_ascii($defaults['Replacements']);
      $defaults['FeatureAlt'] = entities_to_ascii($defaults['FeatureAlt']);
      $defaults['BeautyAlt'] = entities_to_ascii($defaults['BeautyAlt']);
      $defaults['ThumbAlt'] = entities_to_ascii($defaults['ThumbAlt']);
      $defaults['SmallAlt'] = entities_to_ascii($defaults['SmallAlt']);
      $defaults['LargeAlt'] = entities_to_ascii($defaults['LargeAlt']);
      $defaults['MetaTitle'] = entities_to_ascii($defaults['MetaTitle']);
      $defaults['MetaDescription'] = entities_to_ascii($defaults['MetaDescription']);
      $defaults['MetaKeywords'] = entities_to_ascii($defaults['MetaKeywords']);
      $defaults['MetaMisc'] = entities_to_ascii($defaults['MetaMisc']);
      $defaults['MetaRobots'] = entities_to_ascii($defaults['MetaRobots']);
      $defaults['FlavorDescriptor'] = entities_to_ascii($defaults['FlavorDescriptor']);
      
      if ($defaults['NutritionScorecard'] != '')
      {
         $ns_array = unserialize($defaults['NutritionScorecard']);
         $defaults['NSSodium'] = $ns_array[0];
         $defaults['NSFat'] = $ns_array[1];
         $defaults['NSFiber'] = $ns_array[2];
         $defaults['NSAntioxidants'] = $ns_array[3];
         $defaults['NSAntioxidantChoice'] = $ns_array[4];
         $defaults['NSCalories'] = $ns_array[5];
         $defaults['NSOther'] = $ns_array[6];
         $defaults['NSOtherChoice'] = $ns_array[7];
         $defaults['NSOtherQuantity'] = $ns_array[8];
      }
      else
      {
         $defaults['NSSodium'] = 0;
         $defaults['NSFat'] = 0;
         $defaults['NSFiber'] = 0;
         $defaults['NSAntioxidants'] = 0;
         $defaults['NSAntioxidantChoice'] = '';
         $defaults['NSCalories'] = 0;
         $defaults['NSOther'] = 0;
         $defaults['NSOtherChoice'] = '';
         $defaults['NSOtherQuantity'] = '';
      }

      $this->validation->set_defaults($defaults);
      
      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');
         $this->collector->append_js_file('product_sites');

         // get data for the various pulldown lists
         $data['statuses'] = array(
            'discontinued' => 'discontinued', 
            'active' => 'active', 
            'pending' => 'pending', 
            'partial' => 'partial', 
            'inactive' => 'inactive',
            );
         $data['languages'] = array(
            'en_US' => 'en_US', 
            'en_CA' => 'en_CA', 
            'fr_CA' => 'fr_CA',
            );
         $data['benefit_displays'] = array(
            'none' => 'Display any or all', 
            'Benefits' => 'Display Benefits field only',
            'SmartBenefits' => 'Display Smart Benefits only', 
            'NutritionScorecard' => 'Display Nutrition Scorecard only',
            );
         $vitap = ($nlea['VITAP']) ? $nlea['VITAP'].'%' : 'none';
         $vitcp = ($nlea['VITCP']) ? $nlea['VITCP'].'%' : 'none';
         $vitep = ($nlea['VITEP']) ? $nlea['VITEP'].'%' : 'none';
         $selep = ($nlea['SELEP']) ? $nlea['SELEP'].'%' : 'none';
         $data['antioxidants'] = array(
            '' => '-- select one --',
            'VITAP' => 'Vitamin A ('.$vitap.')',
            'VITCP' => 'Vitamin C ('.$vitcp.')',
            'VITEP' => 'Vitamin E ('.$vitep.')',
            'SELEP' => 'Selenium ('.$selep.')',
            );
         $data['kosher_symbols'] = $this->Symbols->get_kosher_list();
         $data['organic_symbols'] = $this->Symbols->get_organic_list();

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Products');
         $data['submenu'] = get_submenu($site_id, 'Products');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['product_sites'] = $this->Products->get_product_sites($product_id);
         $data['product'] = $old_values;
         $data['nlea'] = $nlea;
         $data['admin'] = $admin; // errors and messages
         $data['product_id'] = $product_id;
         $data['ingredient_list'] = $this->Ingredients->parse_ingredients($old_values['Ingredients']);

         $this->load->vars($data);
   	
         return $this->load->view('products/edit', NULL, TRUE);
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
    * Processes the edit product form
    *
    * Auditing: complete
    */
   function _edit($site_id, $product_id, $old_values)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($product_id == 0)
      {
         show_error('_edit_settings requires that a product ID be supplied.');
      }
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      // condense the Nutrition Scorecard into one field
      $values['NutritionScorecard'] = serialize(array(
         $values['NSSodium'],
         $values['NSFat'],
         $values['NSFiber'],
         $values['NSAntioxidants'],
         $values['NSAntioxidantChoice'],
         $values['NSCalories'],
         $values['NSOther'],
         $values['NSOtherChoice'],
         $values['NSOtherQuantity'],
         ));
      unset($values['NSSodium']);
      unset($values['NSFat']);
      unset($values['NSFiber']);
      unset($values['NSAntioxidants']);
      unset($values['NSAntioxidantChoice']);
      unset($values['NSCalories']);
      unset($values['NSOther']);
      unset($values['NSOtherChoice']);
      unset($values['NSOtherQuantity']);

      // process the form text (convert special characters and the like)
      $values['ProductName'] = ascii_to_entities($values['ProductName']);
      $values['PackageSize'] = ascii_to_entities($values['PackageSize']);
      $values['LongDescription'] = ascii_to_entities($values['LongDescription']);
      $values['Teaser'] = ascii_to_entities($values['Teaser']);
      $values['Benefits'] = ascii_to_entities($values['Benefits']);
      $values['AvailableIn'] = ascii_to_entities($values['AvailableIn']);
      $values['Footnotes'] = ascii_to_entities($values['Footnotes']);
      $values['Ingredients'] = ascii_to_entities($values['Ingredients']);
      $values['NutritionBlend'] = ascii_to_entities($values['NutritionBlend']);
      $values['Standardization'] = ascii_to_entities($values['Standardization']);
      $values['Directions'] = ascii_to_entities($values['Directions']);
      $values['Warning'] = ascii_to_entities($values['Warning']);
      $values['AllNatural'] = ascii_to_entities($values['AllNatural']);
      $values['Gluten'] = ascii_to_entities($values['Gluten']);
      $values['OrganicStatement'] = ascii_to_entities($values['OrganicStatement']);
      $values['Alergens'] = ascii_to_entities($values['Alergens']);
      $values['SpiceLevel'] = ascii_to_entities($values['SpiceLevel']);
      $values['Replacements'] = ascii_to_entities($values['Replacements']);
      $values['FeatureAlt'] = ascii_to_entities($values['FeatureAlt']);
      $values['BeautyAlt'] = ascii_to_entities($values['BeautyAlt']);
      $values['ThumbAlt'] = ascii_to_entities($values['ThumbAlt']);
      $values['SmallAlt'] = ascii_to_entities($values['SmallAlt']);
      $values['LargeAlt'] = ascii_to_entities($values['LargeAlt']);
      $values['MetaTitle'] = ascii_to_entities($values['MetaTitle']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);
      $values['MetaMisc'] = ascii_to_entities($values['MetaMisc']);
      $values['MetaRobots'] = ascii_to_entities($values['MetaRobots']);
      $values['FlavorDescriptor'] = ascii_to_entities($values['FlavorDescriptor']);
      
      $values['LastModifiedDate'] = date('Y-m-d H:i:s');
      $values['LastModifiedBy'] = $this->session->userdata('username');
      
      $this->Products->update_product($product_id, $values, $old_values);

      $this->session->set_userdata('product_message', $values['ProductName'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;
      redirect('products/edit/'.$site_id.'/'.$product_id.'/'.$last_action.'/');
   }

   
}
?>
