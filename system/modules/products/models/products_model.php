<?php

class Products_model extends Model {

   var $category_tree = array();
   
   var $cb_read_db;  // database object for coolbrew tables
   var $cb_write_db;  // database object for coolbrew tables
   var $hcg_write_db;  // database object for hcg_public tables

   function Products_model()
   {
      parent::Model();
      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_read_db = $this->load->database('read', TRUE);
      $this->cb_write_db = $this->load->database('write', TRUE);
      $this->hcg_write_db = $this->load->database('hcg_write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a basic product record for a given product ID
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_product_record($prod_id)
   {
      $sql = 'SELECT * FROM pr_product '.
             'WHERE ProductID = '.$prod_id;
      $query = $this->cb_read_db->query($sql);
      $product = $query->row_array();
      
      return $product;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns a complete product record for a given product ID
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_product_data($prod_id, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
     
      // will only find the product if it is assigned to this site ID
      $sql = 'SELECT p.*, ps.SiteID AS SiteID '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = '.$prod_id.' '.
             'AND p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'"';
      $query = $this->cb_read_db->query($sql);
      $product = $query->row_array();
      
      if ($query->num_rows() < 1)
         return FALSE;
   
      $product['error'] = 0;
      $product['error_msg'] = "";
      
      if ($product['Status'] == "discontinued")
      {
         $product['error'] = 1;
         $product['error_msg'] .= "This product has been discontinued. ";
      }
      
      if ( ! empty($product['KosherSymbol']))
      {
         $kosher_sym = $this->get_symbol_data($prod_id, "KosherSymbol");
         $product['KosherFile'] = $kosher_sym['SymbolFile'];
         $product['KosherWidth'] = $kosher_sym['SymbolWidth'];
         $product['KosherHeight'] = $kosher_sym['SymbolHeight'];
         $product['KosherAlt'] = $kosher_sym['SymbolAlt'];
      }

      if ( ! empty($product['OrganicSymbol']))
      {
         $organic_sym = $this->get_symbol_data($prod_id, "OrganicSymbol");
         $product['OrganicFile'] = $organic_sym['SymbolFile'];
         $product['OrganicWidth'] = $organic_sym['SymbolWidth'];
         $product['OrganicHeight'] = $organic_sym['SymbolHeight'];
         $product['OrganicAlt'] = $organic_sym['SymbolAlt'];
      }
   
      $this->load->helper('upc');
      $product['UPC12'] = getFullUPC($product['UPC']);

      return $product;
   }

   // --------------------------------------------------------------------

   /**
    * Returns product ID for a given product given it's code
    *
    * @access   public
    * @param    string    The product code
    * @param    string    The site ID
    * @return   array
    */
   function get_product_id_by_code($prod_code, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.SESFilename = "'.$prod_code.'" '.
             'AND p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'"';
      $query = $this->cb_read_db->query($sql);
      $result = $query->row_array();

      return $result['ProductID'];     
   }

   // --------------------------------------------------------------------

   /**
    * Returns product ID for a given product given it's 11-digit UPC
    *
    * @access   public
    * @param    string    The product code
    * @param    string    The site ID
    * @return   array
    */
   function get_product_id_by_upc($upc)
   {
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p '.
             'WHERE p.UPC = "'.$upc.'" '.
             'AND p.ProductGroup != "master"';
      $query = $this->cb_read_db->query($sql);
      $result = $query->row_array();

      return $result['ProductID'];     
   }

   // --------------------------------------------------------------------

   /**
    * Returns product metadata for a given product ID
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_product_metadata($prod_id)
   {
      $sql = 'SELECT ProductName, MetaTitle, MetaDescription, '.
               'MetaMisc, MetaKeywords, MetaRobots '.
             'FROM pr_product '.
             'WHERE ProductID = '.$prod_id;
      $query = $this->cb_read_db->query($sql);
      $results = $query->row_array();
   
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_products_in_site($site_id, $include_pending = TRUE)
   {
      $sql = 'SELECT p.*, c.CategoryID '.
             'FROM pr_product AS p, pr_category AS c, '.
                'pr_product_category AS pc, pr_product_site AS s '.
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND p.ProductID = s.ProductID '.
             'AND s.SiteID = c.SiteID '.
             'AND s.SiteID = "'.$site_id.'" '.
             'AND c.Status LIKE "active" '.
             'AND (p.Status LIKE "active" '.
             'OR p.Status LIKE "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $sql .= "ORDER BY p.SortOrder ASC, p.ProductName ASC, p.ProductGroup DESC, p.PackageSize ASC";
      $query = $this->cb_read_db->query($sql);
      $product_list = $query->result_array();
   
      return $product_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_discontinued_products_in_site($site_id)
   {
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
                'c.CategoryID, p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p, pr_category AS c, '.
                'pr_product_category AS pc, pr_product_site AS s '.
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND p.ProductID = s.ProductID '.
             'AND s.SiteID = c.SiteID '.
             'AND s.SiteID = "'.$site_id.'" '.
             'AND c.Status LIKE "active" '.
             'AND (p.Status LIKE "discontinued" '.
             'OR p.Status LIKE "inactive") '.
             'ORDER BY p.ProductName ASC, p.ProductGroup DESC, p.PackageSize ASC';
      $query = $this->cb_read_db->query($sql);
      $product_list = $query->result_array();
   
      return $product_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are assigned to the specified category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    string    The category ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_products_in_category($site_id, $category_id, $include_pending = TRUE)
   {
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
                'c.CategoryID, p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p, pr_category AS c, '.
                'pr_product_category AS pc, pr_product_site AS s '.
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND p.ProductID = s.ProductID '.
             'AND s.SiteID = c.SiteID '.
             'AND pc.CategoryID = '.$category_id.' '.
             'AND s.SiteID = "'.$site_id.'" '.
             'AND c.Status LIKE "active" '.
             'AND (p.Status LIKE "active" '.
             'OR p.Status LIKE "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $sql .= "ORDER BY p.ProductName ASC, p.ProductGroup DESC, p.PackageSize ASC";
      $query = $this->cb_read_db->query($sql);
      $product_list = $query->result_array();
   
      return $product_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are assigned to the specified category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    string    The category ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_discontinued_products_in_category($site_id, $category_id, $include_pending = TRUE)
   {
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
                'c.CategoryID, p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p, pr_category AS c, '.
                'pr_product_category AS pc, pr_product_site AS s '.
             'WHERE pc.CategoryID = c.CategoryID '.
             'AND pc.ProductID = p.ProductID '.
             'AND p.ProductID = s.ProductID '.
             'AND s.SiteID = c.SiteID '.
             'AND pc.CategoryID = '.$category_id.' '.
             'AND s.SiteID = "'.$site_id.'" '.
             'AND c.Status LIKE "active" '.
             'AND (p.Status LIKE "discontinued" '.
             'OR p.Status LIKE "inactive") '.
             'ORDER BY p.ProductName ASC, p.ProductGroup DESC, p.PackageSize ASC';
      $query = $this->cb_read_db->query($sql);
      $product_list = $query->result_array();
   
      return $product_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are NOT assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_nocat_products_in_site($site_id)
   {
   /*
      // this SQL works in MySQL 5.x but not in 4.x

      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
             'p.PackageSize, p.UPC, p.Status '.
             'FROM ( '.
               'pr_product AS p '.
               'JOIN pr_product_site AS ps ON p.ProductID = ps.ProductID '.
             ') '.
             'LEFT JOIN ( '.
               'pr_product_category AS pc '.
               'JOIN pr_category AS c ON pc.CategoryID = c.CategoryID '.
             ') ON ( p.ProductID = pc.ProductID '.
             'AND ps.SiteID = c.SiteID ) '.
             'WHERE pc.CategoryID IS NULL '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND ( '.
               'p.Status = "active" '.
               'OR p.Status = "partial" '.
               'OR p.Status = "pending" '.
             ') '.
             'ORDER BY p.ProductName ASC , p.ProductGroup DESC';
      */
      
      // first, get a list of all products for the site
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
             'p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p '.
               'JOIN pr_product_site AS ps ON p.ProductID = ps.ProductID '.
             'WHERE ps.SiteID = "'.$site_id.'" '.
             'AND ( '.
               'p.Status = "active" '.
               'OR p.Status = "partial" '.
               'OR p.Status = "pending" '.
             ') '.
             'ORDER BY p.ProductName ASC , p.ProductGroup DESC, p.PackageSize ASC';

      $query = $this->cb_read_db->query($sql);
      $nocat_prods = $query->result_array();
      
      // now, get a list of all categories for these products
      $sql = 'SELECT pc.ProductID '.
             'FROM pr_product_category AS pc '.
               'JOIN pr_category AS c ON pc.CategoryID = c.CategoryID '.
             'WHERE c.SiteID = "'.$site_id.'"';
   
      $query = $this->cb_read_db->query($sql);
      $nocat_cats = $query->result_array();
      
      // create a lookup array of products with categories
      foreach ($nocat_cats AS $cat)
      {
         $cat_lookup[$cat['ProductID']] = $cat['ProductID'];
      }
      
      // now, create the final list by removing products that have a category
      $nocat_list = array();
      foreach ($nocat_prods AS $prod)
      {
         if ( ! isset($cat_lookup[$prod['ProductID']]))
         {
            $nocat_list[] = $prod;
         }
      }

      return $nocat_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are NOT assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_nocat_discontinued_products_in_site($site_id)
   {
   /*
      // this SQL works in MySQL 5.x but not in 4.x

      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
             'p.PackageSize, p.UPC, p.Status '.
             'FROM ( '.
               'pr_product AS p '.
               'JOIN pr_product_site AS ps ON p.ProductID = ps.ProductID '.
             ') '.
             'LEFT JOIN ( '.
               'pr_product_category AS pc '.
               'JOIN pr_category AS c ON pc.CategoryID = c.CategoryID '.
             ') ON ( p.ProductID = pc.ProductID '.
             'AND ps.SiteID = c.SiteID ) '.
             'WHERE pc.CategoryID IS NULL '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND ( '.
               'p.Status = "active" '.
               'OR p.Status = "partial" '.
               'OR p.Status = "pending" '.
             ') '.
             'ORDER BY p.ProductName ASC , p.ProductGroup DESC';
      */
      
      // first, get a list of all products for the site
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
             'p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p '.
               'JOIN pr_product_site AS ps ON p.ProductID = ps.ProductID '.
             'WHERE ps.SiteID = "'.$site_id.'" '.
             'AND (p.Status = "discontinued" '.
             'OR p.Status = "inactive") '.
             'ORDER BY p.ProductName ASC , p.ProductGroup DESC, p.PackageSize ASC';

      $query = $this->cb_read_db->query($sql);
      $nocat_prods = $query->result_array();
      
      // now, get a list of all categories for these products
      $sql = 'SELECT pc.ProductID '.
             'FROM pr_product_category AS pc '.
               'JOIN pr_category AS c ON pc.CategoryID = c.CategoryID '.
             'WHERE c.SiteID = "'.$site_id.'"';
   
      $query = $this->cb_read_db->query($sql);
      $nocat_cats = $query->result_array();
      
      // create a lookup array of products with categories
      foreach ($nocat_cats AS $cat)
      {
         $cat_lookup[$cat['ProductID']] = $cat['ProductID'];
      }
      
      // now, create the final list by removing products that have a category
      $nocat_list = array();
      foreach ($nocat_prods AS $prod)
      {
         if ( ! isset($cat_lookup[$prod['ProductID']]))
         {
            $nocat_list[] = $prod;
         }
      }

      return $nocat_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of ProductIDs for all products in $site_id.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_prod_ids_in_site($site_id, $include_pending = TRUE)
   {
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $query = $this->cb_read_db->query($sql);
      $raw_list = $query->result_array();
   
      for ($i=0; $i<count($raw_list); $i++)
      {
         $id_list[$i] = $raw_list[$i]['ProductID'];
      }
   
      return $id_list;
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
   function get_prod_ids_in_category($cat_id, $include_pending = TRUE)
   {
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p, pr_product_category AS pc '.
             'WHERE pc.CategoryID = '.$cat_id.' '.
             'AND pc.ProductID = p.ProductID '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $sql .= "ORDER BY p.ProductID ASC";
      $query = $this->cb_read_db->query($sql);
      $raw_list = $query->result_array();
   
      for ($i=0; $i<count($raw_list); $i++)
      {
         $id_list[$i] = $raw_list[$i]['ProductID'];
      }
   
      return $id_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of ProductIDs for all products assigned to $site_id 
    * that have $bool_field set to 1. Since the field is specified as a 
    * variable, it makes this useful for all boolean fields.
    *
    * @access   public
    * @param    string    The boolean field name
    * @param    string    The site ID
    * @return   array
    */
   function get_prod_ids_per_field($bool_field, $site_id)
   {
      $sql = 'SELECT p.ProductID '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID = "'.$site_id.'" '.
             'AND '.$bool_field.' = 1 '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial" '.
             'OR p.Status = "pending")';
      $query = $this->cb_read_db->query($sql);
      $raw_list = $query->result_array();
   
      for ($i=0; $i<count($raw_list); $i++)
      {
         $id_list[$i] = $raw_list[$i]['ProductID'];
      }
   
      return $id_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of sites this product is associated with
    *
    */
   function get_product_sites($prod_id) 
   {
      $sql = 'SELECT SiteID '.
             'FROM pr_product_site '.
             "WHERE ProductID = ".$prod_id;
      $query = $this->cb_read_db->query($sql);
      $sites = $query->result_array();
      
      foreach ($sites AS $site)
         $results[] = $site['SiteID'];

      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of products for use in online forms
    *
    */
   function get_product_list($site_id)
   {
     $sql = 'SELECT p.ProductName, p.LocatorCode, p.UPC, '.
               'p.PackageSize, p.ProductGroup '.
             'FROM pr_product AS p, pr_product_site AS ps '.
             'WHERE p.ProductID = ps.ProductID '.
             'AND ps.SiteID LIKE "'.$site_id.'" '.
             'AND p.Status NOT LIKE "discontinued" '.
             'AND p.Status NOT LIKE "inactive" '.
             'ORDER BY p.ProductName';
      $query = $this->cb_read_db->query($sql);
      $products = $query->result_array();
      
      for ($i=0, $cnt=count($products); $i<$cnt; $i++)
      {
         $products[$i]['UPC'] = '0'.$products[$i]['UPC'];
         if ($products[$i]['ProductGroup'] == 'master')
         {
            $products[$i]['ProductName'] = $products[$i]['ProductName'].' - master';
         }
         elseif ($products[$i]['ProductGroup'] != 'none')
         {
            $products[$i]['ProductName'] = $products[$i]['ProductName'].' - '.$products[$i]['PackageSize'];
         }
      }
      return $products;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the record data for a given symbol ID
    *
    */
   function get_symbol_data($prod_id, $symbol) 
   {
      $sql = "SELECT pr_symbol.SymbolFile, pr_symbol.SymbolWidth, ".
             "pr_symbol.SymbolHeight, pr_symbol.SymbolAlt " .
             "FROM pr_product, pr_symbol " .
             "WHERE pr_product.ProductID = ".$prod_id." ".
             "AND pr_product.".$symbol." = pr_symbol.SymbolID";
      $query = $this->cb_read_db->query($sql);
      $result = $query->row_array();

      return $result;
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
      $sql = "SELECT * FROM pr_nlea " .
             "WHERE ProductID = ".$product_id;
      $query = $this->cb_read_db->query($sql);
      $nlea = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         // create a new record for this product
         $sql = 'SELECT ProductID, SiteID, ProductName '.
                'FROM pr_product '.
                'WHERE ProductID = '.$product_id;
         $query = $this->cb_read_db->query($sql);
         $values = $query->row_array();
         $values['TYPE'] = '0';

         $this->cb_write_db->insert('pr_nlea', $values);
         $this->hcg_write_db->insert('pr_nlea', $values);

         $sql = "SELECT * FROM pr_nlea " .
                "WHERE ProductID = ".$product_id;
         $query = $this->cb_read_db->query($sql);
         $nlea = $query->row_array();
      }
      return $nlea;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the HTML Nutrition Facts for the given Product ID.
    *
    */
   function nutrition_facts($prod_id, $display_hd = false)
   {
      $this->load->helper('nlea');
     
      $nutfacts = $this->get_nlea_data($prod_id);
            
      // see if the info is overridden
      if ($nutfacts['TYPE'] == 8)
      {
         return $nutfacts['OverrideHTML'];
      }
      
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
      $tpl = "nutrition_facts_" . $nutfacts['TYPE'];
      
      $data['nutfacts'] = $nutfacts;
      
      return $this->load->view($tpl, $data, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the product's relevant page data.
    *
    * This is designed to mimic the results of the pages.page_info tag
    * for product pages.
    *
    * @access   public
    * @return   array
    */
   function get_product_page_info($prod_id, $site_id = '')
   {
      $site_id = $site_id == '' ? SITE_ID : $site_id;
      
      // pull up the equivalent fields n the products database
      $sql = 'SELECT ProductName AS MenuText, MetaTitle AS PageTitle, '.
              'MetaDescription, MetaMisc AS MetaAbstract, MetaKeywords, '.
              'MetaRobots, SESFilename AS PageName '.
             'FROM pr_product '.
             'WHERE ProductID = '.$prod_id;
      $query = $this->cb_read_db->query($sql);
      $page = $query->row_array();
      
      return $page;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new Product record
    *
    * This assumes that the SiteID is a supplied field and adds the 
    *  pr_product_site record as well.
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   int       The newly generated Product ID
    */
   function insert_product($values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // first, insert the main product record
      $this->cb_write_db->insert('pr_product', $values);
      $this->hcg_write_db->insert('pr_product', $values);

      $product_id = $this->cb_write_db->insert_id();
      
      $site['ProductID'] = $product_id;
      $site['SiteID'] = $values['SiteID'];
      
      $this->cb_write_db->insert('pr_product_site', $site);
      
      $this->CI->auditor->audit_insert('pr_product', '', $values);
      $this->CI->auditor->audit_insert('pr_product_site', '', $site);
      
      return $product_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates an existing Product record
    *
    * @access   public
    * @param    int       The product ID of the record being updated
    * @param    array     The values to be inserted
    * @param    array     The values of the existing record
    * @return   boolean
    */
   function update_product($product_id, $values, $old_values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // update both databases
      $tmp = $this->cb_write_db->where('ProductID', $product_id);
      $this->cb_write_db->update('pr_product', $values);
      $this->hcg_write_db->where('ProductID', $product_id);
      $this->hcg_write_db->update('pr_product', $values);

      $this->CI->auditor->audit_update('pr_product', $tmp->ar_where, $old_values, $values);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes the specified product ID
    *
    * This will actually delete the entire product and all its associated
    *   records. This is available only to admin users
    *
    * This will delete the product from all sites; to remove a product from
    *   a site, use the product edit screen to remove the site assignment.
    *
    * @access   public
    * @param    integer   The product ID to be deleted
    * @return   boolean
    */
   function delete_product($product_id)
   {
      // delete all references to this category in pr_product_category
      $this->cb_write_db->where('ProductID', $product_id);
      $this->cb_write_db->delete('pr_product_category');
      $this->hcg_write_db->where('ProductID', $product_id);
      $this->hcg_write_db->delete('pr_product_category');
      
      // delete the product's NLEA record
      $this->cb_write_db->where('ProductID', $product_id);
      $this->cb_write_db->delete('pr_nlea');
      $this->hcg_write_db->where('ProductID', $product_id);
      $this->hcg_write_db->delete('pr_nlea');
      
      // delete all references to this category in pr_product_site
      // this table exists only on the CoolBrew side.
      $this->cb_write_db->where('ProductID', $product_id);
      $this->cb_write_db->delete('pr_product_site');

      // delete the actual product record
      $this->cb_write_db->where('ProductID', $product_id);
      $this->cb_write_db->delete('pr_product');
      $this->hcg_write_db->where('ProductID', $product_id);
      $this->hcg_write_db->delete('pr_product');

      return TRUE;
   }

}

/* End of file product_model.php */
/* Location: ./system/modules/products/models/product_model.php */