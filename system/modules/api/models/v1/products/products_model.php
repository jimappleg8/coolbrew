<?php

class Products_model extends Model {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables
   
   var $product_fields = array(
      'p.ProductName',
      'p.SESFilename AS ProductCode',
      'p.PackageSize',
      'p.AvailableIn',
      'p.LongDescription',
      'p.Teaser',
      'p.Footnotes',
      'p.Ingredients',
      'p.SiteID',                         // replaced with id actually called
      'p.ProductID',
      'p.UPC',
      'p.UPC AS UPC12',                   // built later from UPC
      'p.Language',
      'p.FlagAsNew',
      'p.ProductGroup',
      'p.NutritionFacts',                 // replaced with HTML version
      'p.StoreSection',
      'p.StoreSectionPostfix',
      'p.StoreDetail',
      'p.LocatorCode',
      'p.Benefits',
      'p.KosherSymbol',
      'ko.SymbolFile AS KosherFile',
      'ko.SymbolWidth AS KosherWidth',
      'ko.SymbolHeight AS KosherHeight',
      'ko.SymbolAlt AS KosherAlt',
      'p.OrganicSymbol',
      'o.SymbolFile AS OrganicFile',
      'o.SymbolWidth AS OrganicWidth',
      'o.SymbolHeight AS OrganicHeight',
      'o.SymbolAlt AS OrganicAlt',
      'p.OrganicStatement',
      'p.AllNatural',
      'p.Gluten',
      'p.Alergens',
      'p.SpiceLevel',
      'p.FlavorDescriptor',
      'p.CaffeineHeight AS CaffeineAmount',
      'p.CaffeineAlt AS CaffeineStatement',
      'p.NutritionBlend',
      'p.Standardization',
      'p.Directions',
      'p.Warning',
      'p.ThumbFile',
      'p.ThumbWidth',
      'p.ThumbHeight',
      'p.ThumbAlt',
      'p.SmallFile',
      'p.SmallWidth',
      'p.SmallHeight',
      'p.SmallAlt',
      'p.LargeFile',
      'p.LargeWidth',
      'p.LargeHeight',
      'p.LargeAlt',
      'p.Featured',
      'p.FeatureFile',
      'p.FeatureWidth',
      'p.FeatureHeight',
      'p.FeatureAlt',
      'p.BeautyFile',
      'p.BeautyWidth',
      'p.BeautyHeight',
      'p.BeautyAlt',
      'p.MetaTitle',
      'p.MetaDescription',
      'p.MetaKeywords',
      'p.MetaMisc AS MetaAbstract',
      'p.MetaRobots',
      'p.SortOrder',
      'p.Status AS ProductStatus',
      'p.DiscontinueDate',
      'p.Replacements',
      'p.LastModifiedDate',
      'p.LastModifiedBy',
      'p.BenefitsDisplay',
      'p.SmartBenefits',
      'p.NutritionScorecard',
      'p.ProductType',
      'p.Verified',
//      'p.CaffeineFile',
//      'p.CaffeineWidth',
//      'p.MenuSubsection',
//      'p.Components',
//      'p.FilterID',
   );

   var $nlea_fields = array(
      'ProductID',
      'ProductID AS UPC',             // replaced with actual value
      'ProductID AS ProductCode',     // replaced with actual value
      'SiteID',
      'ProductName',
      'ProductFile',
      'TYPE',
      'SSIZE',
      'MAKE',
      'SERV',
      'COL1HD',
      'COL2HD',
      'CAL',
      '2CAL AS CAL2',
      'FATCAL',
      '2FATCAL AS FATCAL2',
      'TFATQ',
      'TFATP',
      'TFATQ2',
      '2TFATP AS TFATP2',
      'SFATQ',
      'SFATP',
      'SFATQ2',
      '2SFATP AS SFATP2',
      'PFATQ',
      'PFATQ2',
      'MFATQ',
      'MFATQ2',
      'HFATQ',
      'HFATQ2',
      'CHOLQ',
      'CHOLP',
      'CHOLQ2',
      '2CHOLP AS CHOLP2',
      'SODQ',
      'SODP',
      'SODQ2',
      '2SODP AS SODP2',
      'POTQ',
      'POTP',
      'POTQ2',
      '2POTP AS POTP2',
      'TCARBQ',
      'TCARBP',
      'TCARBQ2',
      '2TCARBP AS TCARBP2',
      'DFIBQ',
      'DFIBP',
      'DFIBQ2',
      '2DFIBP AS DFIBP2',
      'SFIBQ',
      'SFIBQ2',
      'IFIBQ',
      'IFIBQ2',
      'SUGQ',
      'SUGQ2',
      'OCARBQ',
      'OCARBQ2',
      'PROTQ',
      'PROTP',
      'PROTQ2',
      '2PROTP AS PROTP2',
      'VITAP',
      '2VITAP AS VITAP2',
      'VITCQ',
      'VITCP',
      '2VITCP AS VITCP2',
      'CALCP',
      '2CALCP AS CALCP2',
      'IRONP',
      '2IRONP AS IRONP2',
      'VITDP',
      '2VITDP AS VITDP2',
      'VITEP',
      '2VITEP AS VITEP2',
      'VITKP',
      '2VITKP AS VITKP2',
      'THIAP',
      '2THIAP AS THIAP2',
      'RIBOP',
      '2RIBOP AS RIBOP2',
      'NIACP',
      '2NIACP AS NIACP2',
      'VITB6P',
      '2VITB6P AS VITB6P2',
      'FOLICP',
      '2FOLICP AS FOLICP2',
      'FOLATEP',
      '2FOLATEP AS FOLATEP2',
      'CHLORP',
      '2CHLORP AS CHLORP2',
      'VITB12P',
      '2VITB12P AS VITB12P2',
      'BIOTINP',
      '2BIOTINP AS BIOTINP2',
      'PACIDP',
      '2PACIDP AS PACIDP2',
      'PHOSP',
      '2PHOSP AS PHOSP2',
      'IODIP',
      '2IODIP AS IODIP2',
      'MAGNP',
      '2MAGNP AS MAGNP2',
      'ZINCP',
      '2ZINCP AS ZINCP2',
      'SELEP',
      '2SELEP AS SELEP2',
      'COPPP',
      '2COPPP AS COPPP2',
      'MANGP',
      '2MANGP AS MANGP2',
      'CHROMP',
      '2CHROMP AS CHROMP2',
      'MOLYP',
      '2MOLYP AS MOLYP2',
      'STMT1',
      'STMT1Q',
      'STMT1Q AS STMT1Text',      // replaced with processed value
      'STMT2',
      'STMT2Q',
      'PDV1',
      'PDV2',
      'PDVT',
      'OverrideHTML',
      'sort',
   );
   
   // --------------------------------------------------------------------

   function Products_model()
   {
      parent::Model();
      
      $this->load->helper('v1/resources');
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      // this module is read only, but for simplicity when copying
      // code from any of the other models, I have left the double
      // database loads.
      $this->cb_db = $this->load->database($level.'-write', TRUE);
      $this->hcg_db = $this->load->database($level.'-hcg_write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns product ID for a given product given it's code
    *
    * @access   public
    * @param    string    The product code
    * @param    string    The site ID
    * @return   int
    */
   function get_product_id_by_code($prod_code, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.SESFilename = "'.$prod_code.'" '.
             'AND p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'"';
      $query = $this->cb_db->query($sql);
      $result = $query->row_array();

      return (isset($result['ProductID'])) ? $result['ProductID'] : '';   
   }

   // --------------------------------------------------------------------

   /**
    * Returns product ID for a given product given it's 11-digit UPC
    *
    * @access   public
    * @param    string    The product code
    * @param    string    The site ID
    * @return   int
    */
   function get_product_id_by_upc($upc, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.UPC = "'.$upc.'" '.
             'AND p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND p.ProductGroup != "master"';
      $query = $this->cb_db->query($sql);
      $result = $query->row_array();

      return (isset($result['ProductID'])) ? $result['ProductID'] : '';     
   }

   // --------------------------------------------------------------------

   /**
    * Returns a limited product record for a given product ID.
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_product_data($prod_id, $site_id)
   {
      $field_list = implode($this->product_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM pr_product AS p '.
             'LEFT JOIN pr_symbol AS ko '.
               'ON ko.SymbolID = p.KosherSymbol '.
             'LEFT JOIN pr_symbol AS o '.
               'ON o.SymbolID = p.OrganicSymbol '.
             'WHERE p.ProductID = '.$prod_id;
      $query = $this->cb_db->query($sql);
      $product = $query->row_array();
      
      if ($query->num_rows() < 1)
         return FALSE;
         
      $product = $this->_complete_product_detail($product, $site_id);
   
      return $product;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the record data for a given symbol ID
    *
    */
   function get_symbol_data($symbol_id) 
   {
      $sql = 'SELECT SymbolFile, SymbolWidth, SymbolHeight, SymbolAlt '.
             'FROM pr_symbol '.
             'WHERE SymbolID = '.$symbol_id;
      $query = $this->cb_db->query($sql);
      $result = $query->row_array();

      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_products_in_site($site_id, $include_pending = TRUE)
   {
      $field_list = implode($this->product_fields, ', ');
      
      $sql = 'SELECT '.$field_list.' '.
             'FROM (pr_product AS p, pr_product_site AS ps) '.
             'LEFT JOIN pr_symbol AS ko '.
               'ON ko.SymbolID = p.KosherSymbol '.
             'LEFT JOIN pr_symbol AS o '.
               'ON o.SymbolID = p.OrganicSymbol '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $sql .= ' ORDER BY SortOrder ASC, ProductName ASC';
      
//      echo $sql; exit;
      
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
      
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $products[$i] = $this->_complete_product_detail($products[$i], $site_id);
      }

      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an alphabetical list of products for use in online forms
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_product_list($site_id, $use_groups = FALSE)
   {
      $sql = 'SELECT p.ProductName, p.SESFilename AS ProductCode, '.
               'p.ProductID, p.UPC, p.PackageSize, p.ProductGroup '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" ';
      if ($use_groups == TRUE)
      {
         $sql .= 'AND (p.ProductGroup LIKE "master" '.
                 'OR p.ProductGroup LIKE "none") ';
      }
      else
      {
         $sql .= 'AND p.ProductGroup NOT LIKE "master" ';
      }
             
      $sql .= 'ORDER BY p.ProductName';
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
      
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         if ($products[$i]['ProductGroup'] != 'none' && $use_groups == FALSE)
         {
            $products[$i]['ProductName'] = $products[$i]['ProductName'].' '.$products[$i]['PackageSize'];
         }
         unset($products[$i]['ProductGroup']);
         unset($products[$i]['PackageSize']);
      }
      
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of ProductIDs for all products that are assigned 
    *   to the specified category.
    *
    * @access   public
    * @param    string    The category ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_products_in_category($cat_id, $site_id, $include_pending = TRUE)
   {
      $field_list = implode($this->product_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM (pr_product AS p, pr_product_category AS pc) '.
             'LEFT JOIN pr_symbol AS ko '.
               'ON ko.SymbolID = p.KosherSymbol '.
             'LEFT JOIN pr_symbol AS o '.
               'ON o.SymbolID = p.OrganicSymbol '.
             'WHERE pc.CategoryID = '.$cat_id.' '.
             'AND pc.ProductID = p.ProductID '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $sql .= ' ORDER BY SortOrder ASC, ProductName ASC';
      $query = $this->cb_db->query($sql);
      $products = $query->result_array();
   
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $products[$i] = $this->_complete_product_detail($products[$i], $site_id);
      }
   
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the nlea data given a product ID. If the NLEA record
    * doesn't exist, it is created.
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_nlea_data($product_id)
   {
      $this->load->helper('v1/nlea');
      
      $field_list = implode($this->nlea_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM pr_nlea '.
             'WHERE ProductID = '.$product_id;
      $query = $this->cb_db->query($sql);
      $nlea = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         // create a new record for this product
         $sql = 'SELECT ProductID, SiteID, ProductName '.
                'FROM pr_product '.
                'WHERE ProductID = '.$product_id;
         $query = $this->cb_db->query($sql);
         $values = $query->row_array();
         $values['TYPE'] = '0';

         $this->cb_db->insert('pr_nlea', $values);
         $this->hcg_db->insert('pr_nlea', $values);

         $sql = "SELECT * FROM pr_nlea " .
                "WHERE ProductID = ".$product_id;
         $query = $this->cb_db->query($sql);
         $nlea = $query->row_array();
      }
      
      $nlea['STMT1Text'] = build_stmt1($nlea['STMT1Q']);
      
      return $nlea;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the HTML Nutrition Facts for the given Product ID.
    *
    */
   function nutrition_facts($prod_id, $site_id, $display_hd = false)
   {
      $this->load->helper('v1/nlea');
     
      $nutfacts = $this->get_nlea_data($prod_id);
      
      // check first if this is an override...
      if ($nutfacts['TYPE'] == 8)
      {
         return $nutfacts['OverrideHTML'];
      }
      
      // ...if not, then generate it.
            
      // calculate the total number of table rows
      // for now, we just set it at 100 and it works
      $nutfacts['total_rows'] = 100;
      
      // build copy for STMT1 if applicable
      if (strtoupper($nutfacts['STMT1']) == "YES")
      {
         $nutfacts['STMT1Q'] = build_stmt1($nutfacts['STMT1Q']);
      }
      
      $nutfacts['display_hd'] = $display_hd;

      $nutfacts['TYPE'] = ($nutfacts['TYPE'] == '') ? '0' : $nutfacts['TYPE'];
      $tpl = "v1/products/nutrition_facts_" . $nutfacts['TYPE'];
      
      $data['nutfacts'] = $nutfacts;
      
      return $this->load->view($tpl, $data, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Fills in the product details that are not directly in the database
    *
    */
   function _complete_product_detail($product, $site_id)
   {
      if ( ! is_array($product))
         return FALSE;
   
      if ( ! empty($product['KosherSymbol']))
      {
         $product['KosherFile'] = rsrc_path('symbols', $site_id, $product['KosherFile']);
      }
      else
      {
         $product['KosherFile'] = '';
         $product['KosherWidth'] = '';
         $product['KosherHeight'] = '';
         $product['KosherAlt'] = '';
      }
      unset($product['KosherSymbol']);

      if ( ! empty($product['OrganicSymbol']))
      {
         $product['OrganicFile'] = rsrc_path('symbols', $site_id, $product['OrganicFile']);
      }
      else
      {
         $product['OrganicFile'] = '';
         $product['OrganicWidth'] = '';
         $product['OrganicHeight'] = '';
         $product['OrganicAlt'] = '';
      }
      unset($product['OrganicSymbol']);
      
      // clean up image values
      $images_array = array('Beauty', 'Feature', 'Thumb', 'Small', 'Large');
      foreach ($images_array AS $image)
      {
         $product[$image.'File'] = rsrc_path('products-'.strtolower($image), $site_id, $product[$image.'File']);
         $product[$image.'Width'] = ($product[$image.'Width'] != 0) ? $product[$image.'Width'] : '';
         $product[$image.'Height'] = ($product[$image.'Height'] != 0) ? $product[$image.'Height'] : '';
         
         if ($product[$image.'Alt'] == '' && $product[$image.'File'] != '')
         {
            $product[$image.'Alt'] = $product['ProductName'];
         }
      }
      
      // expand the Nutrition Scorecard data
      $nlea = $this->get_nlea_data($product['ProductID']);
      $ns_array = array();
      if ($product['NutritionScorecard'] != '')
      {
         $ns_array = unserialize($product['NutritionScorecard']);
      }
      $product['NSSodium'] = ( ! empty($ns_array)) ? $ns_array[0] : 0;
      $sodq = ($nlea['SODQ']) ? $nlea['SODQ'] : '0mg';
      $sodp = ($nlea['SODP']) ? $nlea['SODP'].'%' : '0%';
      $product['NSSodiumQuantity'] = $sodq.'|'.$sodp;
      $product['NSFat'] = ( ! empty($ns_array)) ? $ns_array[1] : 0;
      $tfatq = ($nlea['TFATQ']) ? $nlea['TFATQ'] : '0g';
      $tfatp = ($nlea['TFATP']) ? $nlea['TFATP'].'%' : '0%';
      $product['NSFatQuantity'] = $tfatq.'|'.$tfatp;
      $product['NSFiber'] = ( ! empty($ns_array)) ? $ns_array[2] : 0;
      $dfibq = ($nlea['DFIBQ']) ? $nlea['DFIBQ'] : '0g';
      $dfibp = ($nlea['DFIBP']) ? $nlea['DFIBP'].'%' : '0%';
      $product['NSFiberQuantity'] = $dfibq.'|'.$dfibp;
      $product['NSAntioxidants'] = ( ! empty($ns_array)) ? $ns_array[3] : 0;
      $product['NSAntioxidantChoice'] = ( ! empty($ns_array)) ? $ns_array[4] : '';
      if ($product['NSAntioxidantChoice'] != '')
      {
         $anti = $product['NSAntioxidantChoice'];
         $product['NSAntioxidantQuantity'] = ($nlea[$anti]) ? $nlea[$anti].'%' : '0%';
      }
      else
      {
         $product['NSAntioxidantQuantity'] = '';
      }
      $product['NSCalories'] = ( ! empty($ns_array)) ? $ns_array[5] : 0;
      $product['NSCaloriesQuantity'] = ($nlea['CAL']) ? $nlea['CAL'] : 0;
      $product['NSOther'] = ( ! empty($ns_array)) ? $ns_array[6] : 0;
      $product['NSOtherChoice'] = ( ! empty($ns_array)) ? $ns_array[7] : '';
      $product['NSOtherQuantity'] = ( ! empty($ns_array)) ? $ns_array[8] : '';
      unset($product['NutritionScorecard']);
   
      $this->load->helper('v1/upc');
      $product['UPC12'] = getFullUPC($product['UPC']);
      
      return $product;
   }

}

/* End of file products_model.php */
/* Location: ./system/modules/api/models/v1/products_model.php */