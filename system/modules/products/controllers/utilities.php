<?php

class Utilities extends Controller {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables
   
   var $position;  // the position marker for scraping a file
   
   var $k_translate = array(
       'Circle-U' => 'Circle U',
       'Kosher: Circle-U Dairy' => 'Circle U Dairy',
       'Kosher: Circle K Parve' => 'Circle K Parve',
       'USDA Organic Seal' => 'USDA Organic',
       'kosher symbol OU' => 'OU',
       'kosher symbol OU-dairy' => 'OU Dairy',
       'kosher symbol KOF-dairy' => 'KOF Dairy',
       'kosher symbol KOF-parve' => 'KOF Parve',
       'Star K' => 'Star K',
       'Scroll K Dairy' => 'Scroll K Dairy',
       'Star D' => 'Star D',
       'Flag K Dairy' => 'Flag K Dairy',
       'Circle U Parve Symbol' => 'Circle U Parve',
       'QAI Organic Symbol' => 'QAI Organic',
       'Circle K Dairy' => 'Circle K Dairy',
       );

   var $sites_use_db = array(
       'ab'   => 'no',  // albabotanica.com
       'ad'   => 'yes', // albadrinks.com
       'am'   => 'yes', // arrowheadmills.com
       'ao'   => 'no',  // avalonorganics.com
       'bo'   => 'yes', // bostonssnacks.com
       'cb'   => 'yes', // casbahnaturalfoods.com
       'cc'   => 'no',  // celestialseasoningscoffee.com
       'ck'   => 'no',  // celestialseasonings.ca
       'cs'   => 'yes', // celestialseasonings.com
       'csk'  => 'no',  // celestialseasoningskombucha.com
       'db'   => 'yes', // deboles.com
       'dbr'  => 'no',  // dailybread.ltd.uk
       'eb'   => 'yes', // earthsbest.com
       'ebd'  => 'no',  // earthsbestdiapers.com
       'ef'   => 'no',  // esteefoods.com
       'eg'   => 'yes', // ethnicgourmet.com
       'fb'   => 'no',  // freebirdchicken.com
       'ge'   => 'yes', // gardenofeatin.com
       'gfc'  => 'no',  // myglutenfreecafe.com
       'gfch' => 'yes', // glutenfreechoices.com
       'gn'   => 'no',  // grainsnoirs.com
       'hf'   => 'yes', // hainpurefoods.com
       'hv'   => 'yes', // healthvalley.com
       'hw'   => 'yes', // hollywoodoils.com
       'if'   => 'no',  // imaginefoods.com
       'jn'   => 'no',  // jason-natural.com
       'lb'   => 'yes', // littlebearfoods.com
       'lf'   => 'no',  // limafood.com
       'lg'   => 'yes', // low-gnutrition.com
       'mn'   => 'yes', // maranathafoods.com
       'mnd'  => 'no',  // mothernaturediapers.com
       'ms'   => 'yes', // mountainsun.com
       'msc'  => 'no',  // marthastewartclean.com
       'ns'   => 'yes', // nilespice.com
       'nsf'  => 'no',  // nspiredfoods.com
       'oc'   => 'no',  // ococos.com
       'pf'   => 'no',  // plainvillefarms.com
       'qh'   => 'no',  // queenhelene.com
       'rp'   => 'no',  // rosetto.com
       'si'   => 'yes', // spectrumingredients.com
       'so'   => 'no',  // spectrumorganics.com
       'ss'   => 'no',  // sunspire.com
       'tc'   => 'yes', // terrachips.com
       'tcd'  => 'no',  // tendercarediapers.com
       'tcw'  => 'no',  // tendercarewipes.com
       'td'   => 'yes', // tastethedream.com
       'ts'   => 'no',  // tushies.com
       'tt'   => 'no',  // tofutown.net
       'tw'   => 'no',  // tushieswipes.com
       'up'   => 'no',  // unpetroleum.com
       'wa'   => 'no',  // walnutacres.com
       'wb'   => 'yes', // westbrae.com
       'ws'   => 'yes', // westsoy.biz
       'yv'   => 'yes', // yvesveggie.com
       'zn'   => 'no',  // zianatural.com
      );

   var $brands = array(
       'ab' => 'Alba Botanica',  // albabotanica.com
       'ad' => 'Alba Drinks',  // albadrinks.com
       'am' => 'Arrowhead Mills',  // arrowheadmills.com
       'ao' => 'Avalon',  // avalonorganics.com
       'bo' => 'Boston\'s',  // bostonssnacks.com
       'cb' => 'Casbah',  // casbahnaturalfoods.com
       'cc' => 'Celestial Seasonings',  // celestialseasoningscoffee.com
       'ck' => 'Celestial Seasonings',  // celestialseasonings.ca
       'cs' => 'Celestial Seasonings',  // celestialseasonings.com
       'csk' => 'Celestial Seasonings',  // celestialseasoningskombucha.com
       'db' => 'DeBoles',  // deboles.com
       'dbr' => 'Daily Bread',  // dailybread.ltd.uk
       'dc' => 'Taste the Dream',  // tastethedream.ca
       'de' => 'Taste the Dream',  // tastethedream.eu
       'eb' => 'Earth\'s Best',  // earthsbest.com
       'ebd' => 'Earth\'s Best',  // earthsbestdiapers.com
       'ec' => 'Earth\'s Best',  // earthsbest.ca
       'ef' => 'Estee',  // esteefoods.com
       'eg' => 'Ethnic Gourmet',  // ethnicgourmet.com
       'fb' => 'Free Bird Chicken',  // freebirdchicken.com
       'ge' => 'Garden of Eatin\'',  // gardenofeatin.com
       'gfc' => 'Gluten Free Cafe',  // myglutenfreecafe.com
       'gfch' => 'Gluten Free Choices',  // glutenfreechoices.com
       'gn' => 'Grains Noirs',  // grainsnoirs.com
       'ha' => 'Harry\'s',  // harryssnacks.com
       'hc' => 'Hain Celestial Group',  // hain-celestial.com
       'hceu' => 'Hain Celestial Group',  // hain-celestial.eu
       'he' => 'Celestial Seasonings',  // herbalexpedition.com
       'hf' => 'Hain Pure Foods',  // hainpurefoods.com
       'hk' => 'Hain Celestial Group',  // hain-celestial.ca
       'hn' => 'Heather\'s Naturals',  // heathersnaturals.com
       'hs' => 'Hain Pure Snax',  // hainpuresnax.com
       'hu' => 'Hain Celestial Group',  // hain-celestial.co.uk
       'hv' => 'Health Valley',  // healthvalley.com
       'hvh' => 'Health Valley',  // healthvalleyhunt.com
       'hw' => 'Hollywood',  // hollywoodoils.com
       'ic' => 'Imagine Soups',  // imaginesoup.ca
       'if' => 'Imagine Soups',  // imaginefoods.com
       'jn' => 'Jason',  // jason-natural.com
       'kf' => 'Kineret',  // kineretfoods.com
       'lb' => 'Little Bear',  // littlebearfoods.com
       'lf' => 'Lima',  // limafood.com
       'lg' => 'Low-G',  // low-gnutrition.com
       'lm' => 'Linda McCartney',  // linda-mccartney.com
       'lmuk' => 'Linda McCartney',  // lindamccartneyfoods.co.uk
       'mn' => 'MaraNatha',  // maranathafoods.com
       'mnd' => 'Earth\'s Best',  // mothernaturediapers.com
       'ms' => 'Mountain Sun',  // mountainsun.com
       'msc' => 'Martha Stewart Clean',  // marthastewartclean.com
       'ns' => 'Nile Spice',  // nilespice.com
       'nsf' => 'nSpired',  // nspiredfoods.com
       'oc' => 'O\'Cocos',  // ococos.com
       'pf' => 'Plainville Farms',  // plainvillefarms.com
       'qh' => 'Queen Helene',  // queenhelene.com
       'rp' => 'Rosetto',  // rosetto.com
       'sa' => 'ShariAnn\'s',  // shariannsorganic.com
       'sb' => 'Shaman Beauty',  // shamanbeauty.com
       'sc' => 'Celestial Seasonings',  // saphara.ca
       'si' => 'Spectrum',  // spectrumingredients.com
       'so' => 'Spectrum',  // spectrumorganics.com
       'ss' => 'SunSpire',  // sunspire.com
       'st' => 'Celestial Seasonings',  // sapharatea.com
       'tc' => 'Terra Chips',  // terrachips.com
       'tcd' => 'Earth\'s Best',  // tendercarediapers.com
       'tcw' => 'Earth\'s Best',  // tendercarewipes.com
       'td' => 'Taste the Dream',  // tastethedream.com
       'ts' => 'Earth\'s Best',  // tushies.com
       'tt' => 'Tofu Town',  // tofutown.net
       'tw' => 'Earth\'s Best',  // tushieswipes.com
       'up' => 'Unpetroleum',  // unpetroleum.com
       'wa' => 'Walnut Acres',  // walnutacres.com
       'wb' => 'Westbrae',  // westbrae.com
       'ws' => 'WestSoy',  // westsoy.biz
       'yc' => 'Yves',  // yvesveggie.ca
       'yv' => 'Yves',  // yvesveggie.com
       'zn' => 'Zia',  // zianatural.com
      );

   function Utilities()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'products'));
      $this->load->helper(array('url', 'menu', 'text'));

      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('write', TRUE);
      $this->hcg_db = $this->load->database('hcg_write', TRUE);
      
      $this->position = 0;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generate a report of products and what needs to be completed.
    *
    */
   function category_status()
   {
      $this->load->dbutil();
      $this->load->model('Sites');
      $this->load->model('Categories');
      
      $sql = 'SELECT ID FROM adm_site '.
             'WHERE Status = "active" '.
             'ORDER BY ID';       
      $query = $this->cb_db->query($sql);
      $sites = $query->result_array();
      
      $categories = array();
      foreach ($sites AS $site)
      {
         $categories[$site['ID']] = $this->Categories->get_category_tree($site['ID']);
      }
      
      foreach ($categories AS $category)
      {
         
      }
      
      echo '<pre>'; print_r($categories); echo '</pre>';
            
   }

   // --------------------------------------------------------------------
   
   /**
    * Generate a report of products and what needs to be completed.
    *
    */
   function product_status()
   {
      $this->load->dbutil();
      $this->load->helper('upc');
      $this->load->model('Sites');
      
      $sql = 'SELECT '.
               'p.ProductID, '.
               'p.UPC, '.
               'p.SiteID, '.
               'p.SiteID AS Brand, '.
               'p.ProductName, '.
               'p.PackageSize, '.
               'p.ProductGroup, '.
               'p.SiteID AS FeedsSite, '.
               'p.LongDescription AS Description, '.
               'p.Ingredients, '.
               'k.SymbolAlt AS Kosher, '.
               'o.SymbolAlt AS Organic, '.
               'p.ProductType, '.
               'n.SSIZE AS NutritionFacts '.
             'FROM pr_product AS p LEFT JOIN (pr_nlea AS n, pr_symbol AS k, pr_symbol AS o) '.
               'ON (p.ProductID = n.ProductID AND p.KosherSymbol = k.SymbolID AND p.OrganicSymbol = o.SymbolID) '.
             'WHERE p.Status != "discontinued" '.
             'AND p.Language = "en_US" '.
             'AND p.SiteID != "cf" AND p.SiteID != "ha" AND p.SiteID != "lm" '.
             'ORDER BY Brand ASC, ProductName ASC, ProductGroup DESC';
      $query1 = $this->cb_db->query($sql);
      $records = $query1->result_array();
      
      $entities = array('&#65279;', '&#199;', '&#205;', '&#8216;', '&#174;', '&amp;', '&#237;', '&#8482;', '&#153;', '&trade;', '&#180;', '&reg;', '&egrave;', '&eacute;', '&#233;', '&#232;', '&#8211;');
      $non_entities = array('', 'C', 'I', '\'', ' (R)', '&', 'i', ' TM', ' TM', ' TM', '\'', ' (R)', 'e', 'e', 'e', 'e', '-');
      
      // general processing loop for all products
      for ($i=0, $cnt=count($records); $i<$cnt; $i++)
      {
         if ($records[$i]['ProductGroup'] == 'master')
            $records[$i]['UPC'] = '(master)';
         else
            $records[$i]['UPC'] = getFullUPC($records[$i]['UPC']);

         $records[$i]['Brand'] = (isset($this->brands[$records[$i]['Brand']])) ? $this->brands[$records[$i]['Brand']] : 'unknown';
         $records[$i]['ProductName'] = str_replace($entities, $non_entities, $records[$i]['ProductName']);
         $records[$i]['FeedsSite'] = (isset($this->sites_use_db[$records[$i]['FeedsSite']])) ? $this->sites_use_db[$records[$i]['FeedsSite']] : 'unknown';
         $records[$i]['Description'] = ($records[$i]['Description'] != '') ? 'x' : '';
         $records[$i]['Ingredients'] = ($records[$i]['Ingredients'] != '') ? 'x' : '';
         $records[$i]['Kosher'] = (isset($this->k_translate[$records[$i]['Kosher']])) ? $this->k_translate[$records[$i]['Kosher']] : '';
         $records[$i]['Organic'] = (isset($this->k_translate[$records[$i]['Organic']])) ? $this->k_translate[$records[$i]['Organic']] : '';
         $records[$i]['NutritionFacts'] = ($records[$i]['NutritionFacts'] != '') ? 'x' : '';
      }


      // add a record at the top with field names
      $index = array();
      foreach ($records[0] AS $key => $value)
      {
         $index[0]["$key"] = "$key";
      }
      $records = array_merge($index, $records);

      // put the updated data back into the object
      $query1->result_array = $records;

      $today = date('Ymd');
      
      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=product_report_".$today.".csv");

      echo $this->dbutil->csv_from_result($query1, FALSE);
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Generate a list of all active categories by site ID
    */
   function generate_category_list()
   {
      $this->load->model('Categories');
      
      $sql = 'SELECT DISTINCT SiteID FROM pr_category ORDER BY SiteID';
      $query = $this->cb_db->query($sql);
      $sites = $query->result_array();
      
      $cats = array();
      $new_cats = array();
      
      foreach ($sites AS $site)
      {
         $categories[$site['SiteID']] = $this->Categories->get_category_tree($site['SiteID']);
      }
      
      foreach ($categories AS $site => $cats)
      {
         $new_cats[$site] = array();
         for ($i=0, $cnt=count($cats); $i<$cnt-1; $i++)
         {
            $new_cats[$site][$i]['CategoryName'] = $cats[$i+1]['CategoryName'];
            $new_cats[$site][$i]['Level'] = $cats[$i+1]['level'];
         }
      }
      
      echo '<pre>';
      foreach ($new_cats AS $site => $cats)
      {
         echo $site."\n";
         foreach ($cats AS $cat)
         {
            echo str_repeat(' ', ($cat['Level']-1)*3).$cat['CategoryName']."\n";
         }
         echo "\n";
      }
      echo '</pre>';
   }

   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Health Valley products
    */
   function update_health_valley_products()
   {
      $file = '/Users/japplega/Desktop/hv-products.csv';
      $columns = array('ProductID', 'UPC', 'SiteID', 'FilterID', 'Status', 'Verified', 'ProductName', 'Ingredients', 'LongDescription', 'Teaser', 'Benefits', 'AvailableIn', 'Footnotes', 'NutritionBlend', 'Standardization', 'Directions', 'Warning', 'AllNatural', 'Gluten', 'OrganicStatement', 'ThumbFile', 'ThumbWidth', 'ThumbHeight', 'ThumbAlt', 'SmallFile', 'SmallWidth', 'SmallHeight', 'SmallAlt', 'LargeFile', 'LargeWidth', 'LargeHeight', 'LargeAlt', 'NutritionFacts', 'KosherSymbol', 'OrganicSymbol', 'CaffeineFile', 'CaffeineWidth', 'CaffeineHeight', 'CaffeineAlt', 'StoreSection', 'StoreSectionPostfix', 'StoreDetail', 'LocatorCode', 'MenuSubsection', 'DiscontinueDate', 'Replacements', 'MetaTitle', 'LastModifiedDate', 'LastModifiedBy', 'MetaMisc', 'MetaDescription', 'MetaKeywords', 'MetaRobots', 'Components', 'ProductType', 'FlavorDescriptor', 'SortOrder', 'FlagAsNew', 'Featured', 'SpiceLevel', 'Allergens', 'FeatureFile', 'FeatureWidth', 'FeatureHeight', 'FeatureAlt', 'BeautyFile', 'BeautyWidth', 'BeautyHeight', 'BeautyAlt', 'PackageSize', 'ProductGroup', 'Language', 'SESFilename');
      $site_id = 'hv';
      
      $this->_update_products($file, $columns, $site_id);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Queen Helene products
    */
   function import_queen_helene_products()
   {
      $file = '/Users/japplega/Desktop/qh-products.csv';
      $columns = array('UPC', 'ProductName', 'PackageSize', 'CategoryID', 'ProductGroup');
      $site_id = 'qh';
      $options = array(
         'multiple-categories' => FALSE,
      );
      
      $this->_import_products($file, $columns, $site_id, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Alba Botanica products
    */
   function import_alba_botanica_products()
   {
      $file = '/Users/japplega/Desktop/alba-product-list.csv';
      $columns = array('ProductName', 'UPC', 'PackageSize', 'CategoryID', 'ProductGroup', 'Status');
      $site_id = 'ab';
      $options = array(
         'multiple-categories' => TRUE,
      );
      
      $this->_import_products($file, $columns, $site_id, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Avalon products
    */
   function import_avalon_products()
   {
      $file = '/Users/japplega/Desktop/avalon-product-list.csv';
      $columns = array('ProductName', 'UPC', 'PackageSize', 'CategoryID', 'ProductGroup', 'Status');
      $site_id = 'ao';
      $options = array(
         'multiple-categories' => TRUE,
      );
      
      $this->_import_products($file, $columns, $site_id, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Spectrum products
    */
   function import_spectrum_products()
   {
      $file = '/Users/japplega/Desktop/spectrum-product-list.csv';
      $columns = array('ProductName', 'UPC', 'PackageSize', 'CategoryID', 'ProductGroup', 'Status');
      $site_id = 'so';
      $options = array(
         'multiple-categories' => TRUE,
      );
      
      $this->_import_products($file, $columns, $site_id, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file of Spectrum products
    */
   function import_sensible_portions_products()
   {
      $file = '/Users/japplega/Desktop/sp_products.csv';
      $columns = array('ProductName', 'PackageSize', 'UPC', 'CategoryID', 'ProductGroup', 'Status');
      $site_id = 'sp';
      $options = array(
         'multiple-categories' => FALSE,
      );
      
      $this->_import_products($file, $columns, $site_id, $options);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file or Tabbed Text file of product info
    *  and add it to the products databases.
    */
   function _import_products($file, $columns, $site_id, $options = array())
   {
      // I have run into issues when I accidentally ran this from bolwebdev1
      if (SERVER_LEVEL != 'local')
      {
         echo "This script is designed to run on a local server only.";
         exit;
      }
      
      $this->load->helper(array('text','url'));
      $this->load->model('Products');
      $this->load->model('Product_categories');
      
      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');
      
      // set the default options
      $multi_cats = (isset($options['multiple-categories'])) ? $options['multiple-categories'] : FALSE;

      $row = 0;
      $handle = fopen($file, "r");

      while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
      {
         for ($i=0; $i<count($columns); $i++)
         {
            $products[$row][$columns[$i]] = $data[$i];
         }
         $products[$row]['UPC'] = str_replace(' ', '', $products[$row]['UPC']);
         $products[$row]['UPC'] = ltrim($products[$row]['UPC']);

         // I don't know why , but the first line has 3 extra chars in the front
         if ($columns[0] == 'UPC' && $row == 0)
         {
            $products[$row]['UPC'] = substr($products[$row]['UPC'], 3, 11);
         }
         else
         {
            $products[$row]['UPC'] = substr($products[$row]['UPC'], 0, 11);
         }
         $products[$row]['UPC'] = ($products[$row]['UPC'] != '0') ? $products[$row]['UPC'] : '';

         $products[$row]['ProductName'] = ascii_to_entities($products[$row]['ProductName']);
         $products[$row]['ProductName'] = str_replace('  ', ' ', $products[$row]['ProductName']);
         $products[$row]['SiteID'] = $site_id;
         $products[$row]['SESFilename'] = url_title(entities_to_ascii($products[$row]['ProductName']));
         $products[$row]['Language'] = (isset($products[$row]['Language'])) ? $products[$row]['Language'] : 'en_US';
         $products[$row]['Status'] = (isset($products[$row]['Status'])) ? $products[$row]['Status'] : 'active';
         
         if (isset($products[$row]['CategoryID']))
         {
            $categories = $products[$row]['CategoryID'];
            unset($products[$row]['CategoryID']);
         }
         
         $id = $this->Products->insert_product($products[$row]);
         
         if (isset($categories))
         {
            if ($multi_cats == TRUE)
            {
               $cats = explode('|', $categories);
               foreach ($cats AS $cat)
               {
                  $category['CategoryID'] = $cat;
                  $category['ProductID'] = $id;
                  $this->Product_categories->insert_product_category($category);
               }
            }
            else
            {
               $category['CategoryID'] = $categories;
               $category['ProductID'] = $id;
               $this->Product_categories->insert_product_category($category);
            }
         }
         
         echo $id.': '.$products[$row]['ProductName'].' ('.$products[$row]['UPC'].') created<br />';
         $row++;
      }
      fclose($handle);
      
//     echo '<pre>'; print_r($products); echo '</pre>';
      echo '<br />'.($row - 1).' records created.';
      exit;
   }
   
   
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file or Tabbed Text file of product info
    *  and update the products database
    *
    * At the moment, this function does not support adding or modifying 
    *  category information.
    */
   function _update_products($file, $columns, $site_id)
   {
      // I have run into issues when I accidentally ran this from bolwebdev1
      if (SERVER_LEVEL != 'local')
      {
         echo "This script is designed to run on a local server only.";
         exit;
      }
      
      $this->load->helper(array('text','url'));
      $this->load->model('Products');
      $this->load->model('Product_categories');
      
      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');
      
      $row = 0;
      $handle = fopen($file, "r");

      while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
      {
         for ($i=0; $i<count($columns); $i++)
         {
            // I don't know why, but the first line has 3 extra chars in the front
            if ($i == 0 && $row == 0)
            {
               $products[$row][$columns[$i]] = trim(substr($data[$i], 3));
            }
            else
            {
               $products[$row][$columns[$i]] = trim($data[$i]);
            }
         }
         
         // process the data
         $entity_list = array('ProductName', 'PackageSize', 'LongDescription', 'Teaser', 'Benefits', 'AvailableIn', 'Footnotes', 'Ingredients', 'NutritionBlend', 'Standardization', 'Directions', 'Warning', 'AllNatural', 'Gluten', 'OrganicStatement', 'Allergens', 'SpiceLevel', 'Replacements', 'ThumbAlt', 'SmallAlt', 'LargeAlt', 'FeatureAlt', 'BeautyAlt', 'MetaTitle', 'MetaDescription', 'MetaKeywords', 'MetaMisc', 'MetaRobots', 'FlavorDescriptor');
         foreach ($entity_list AS $field)
         {
            if (isset($products[$row][$field]))
            {
               $products[$row][$field] = ascii_to_entities($products[$row][$field]);
            }
         }
      
         $products[$row]['LastModifiedDate'] = date('Y-m-d H:i:s');
         $products[$row]['LastModifiedBy'] = $this->session->userdata('username');

         $products[$row]['UPC'] = str_replace(' ', '', $products[$row]['UPC']);
         $products[$row]['UPC'] = ltrim($products[$row]['UPC']);
         $products[$row]['UPC'] = ($products[$row]['UPC'] != '0') ? $products[$row]['UPC'] : '';
         
         // adds preceeding 0 if UPC is only 10 digits
         // This is because Excel has a way of deleting the leading 0.
         if (strlen($products[$row]['UPC']) == 10)
         {
            $products[$row]['UPC'] = '0'.$products[$row]['UPC'];
         }

         $products[$row]['ProductName'] = str_replace('  ', ' ', $products[$row]['ProductName']);
         $products[$row]['Language'] = (isset($products[$row]['Language'])) ? $products[$row]['Language'] : 'en_US';
         $products[$row]['Status'] = (isset($products[$row]['Status'])) ? $products[$row]['Status'] : 'active';
         
         // for new products, this may be set, but blank
         if (isset($products[$row]['SiteID']))
         {
            if ($products[$row]['SiteID'] == '')
            {
               $products[$row]['SiteID'] = $site_id;
            }
         }

         // if there is supposed to be an SESFilename and isn't, we want to generate it
         if (isset($products[$row]['SESFilename']))
         {
            if ($products[$row]['SESFilename'] == '')
            {
               $products[$row]['SESFilename'] = url_title(entities_to_ascii($products[$row]['ProductName']));
            }
         }
         
         // fixes issue with misspelling of Allergens
         if (isset($products[$row]['Allergens']))
         {
            $products[$row]['Alergens'] = $products[$row]['Allergens'];
            unset($products[$row]['Allergens']);
         }

         if (isset($products[$row]['CategoryID']))
         {
            $categories = $products[$row]['CategoryID'];
            unset($products[$row]['CategoryID']);
         }
         
         if ($products[$row]['ProductID'] != '')
         {
            $id = $products[$row]['ProductID'];
            $old_values = $this->Products->get_product_data($id, $site_id);
            $this->Products->update_product($id, $products[$row], $old_values);
            echo $id.': '.$products[$row]['ProductName'].' ('.$products[$row]['UPC'].') updated<br />';
         }
         else
         {
            $id = $this->Products->insert_product($products[$row]);
            echo $id.': '.$products[$row]['ProductName'].' ('.$products[$row]['UPC'].') created<br />';
         }
         $row++;
      }
      fclose($handle);
      
//     echo '<pre>'; print_r($products); echo '</pre>';
      echo '<br />'.($row - 1).' records processed.';
      exit;
   }
   
   
   // --------------------------------------------------------------------
   
   /**
    * Read File
    *
    * Opens the file specfied in the path and returns it as a string.
    *
    * @access	private
    * @param	string	path to file
    * @return	string
    */	
   function _read_file($file)
   {
      if ( ! file_exists($file))
      {
         return FALSE;
      }
   
      if (function_exists('file_get_contents'))
      {
         return file_get_contents($file);      
      }

      if ( ! $fp = @fopen($file, 'rb'))
      {
         return FALSE;
      }
      
      flock($fp, LOCK_SH);
   
      $data = '';
      if (filesize($file) > 0)
      {
         $data =& fread($fp, filesize($file));
      }

      flock($fp, LOCK_UN);
      fclose($fp);

      return $data;
   }

   // --------------------------------------------------------------------

   /**
    * Pulls product information from the Zia website and inserts it 
    *  into the products database tables.
    *
    * This is a one-time script and is no longer needed. I am keeping 
    *  just in case we want to do something similiar in the future.
    *
    */
   function get_zia_products()
   {
      // using PEAR HTTP_Request
      require 'HTTP/Request.php';
      
      $products = array();
      $product_ids = array(100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 
                           110, 113, 117, 118, 120, 122, 123, 124, 125, 127, 
                           129, 130, 131, 133, 135, 136, 137, 138, 139, 140, 
                           141, 154, 155, 156, 157, 159, 160, 161, 162, 163, 
                           164, 165, 167, 169, 170, 172, 174, 176, 180, 182, 
                           183, 184, 188, 197, 198, 199, 200, 201, 203, 204, 
                           205, 240, 247, 251, 252, 255, 256, 259, 260, 277, 
                           278, 279, 280, 281, 312, 313, 314, 315, 316, 328, 
                           330, 332, 333, 334, 335, 336, 337, 338);
//     $product_ids = array(120, 182);
      
      $cnt = 0;
      
      foreach ($product_ids AS $product_id)
      {

         // first get the main product page

         $request = "http://www.zianatural.com/shopping/product/detailmain.jsp".
                    "?itemID=".$product_id.
                    "&itemType=PRODUCT".
                    "&ProductID=".$product_id;
//        echo $request;
   
         $r = new HTTP_Request($request);

//        if ($this->config->item('proxy') != "")
//        {
//           $r->setProxy($this->config->item('proxy'), $this->config->item('proxy_port'));
//        }

         $response = $r->sendRequest();

         if (!PEAR::isError($response))
         {
            $page = $r->getResponseBody();
         }
         else
         {
            return "<br>Error Message: ".$response->getMessage();
         }
   
         // scrape the resulting page
         
//        echo '<pre>'.htmlentities($page).'</pre>';

         // start at a good position in the file
         $this->position = strpos($page, '<!-- Vert Detail Image -->');
   
         // find the small image filename
         $start_str = "/local/";
         $end_str = "\"";
         $products[$cnt]['SmallFile'] = $this->_get_substring($page, $start_str, $end_str);
         
         // find the product name
         $start_str = "<div class=colorsubheader>";
         $end_str = "</div>";
         $products[$cnt]['ProductName'] = $this->_get_substring($page, $start_str, $end_str);

         // find the product teaser
         $start_str = "<div class=colorheader>";
         $end_str = "</div>";
         $products[$cnt]['Teaser'] = $this->_get_substring($page, $start_str, $end_str);

         // find the product description
         $start_str = "<div class=default>";
         $end_str = "</div>";
         $products[$cnt]['LongDescription'] = $this->_get_substring($page, $start_str, $end_str);

         // find the large image filename
         $start_str = "&img=local/";
         $end_str = "'";
         $products[$cnt]['LargeFile'] = $this->_get_substring($page, $start_str, $end_str);
         
         if ($products[$cnt]['LargeFile'] == '')
         {
            // try a different path
            $start_str = "&img=/local/";
            $end_str = "'";
            $products[$cnt]['LargeFile'] = $this->_get_substring($page, $start_str, $end_str);
         }

         // find the item number
         // <td><span class=default>421</span></td>
         preg_match('/<td><span class=default>(\d+)<\/span><\/td>/', substr($page, $this->position), $matches);
         $products[$cnt]['Components'] = (isset($matches[1])) ? $matches[1] : '';


         // find the price
         $start_str = "<span class=color>$";
         $end_str = "</span>";
         $products[$cnt]['ProductType'] = $this->_get_substring($page, $start_str, $end_str, TRUE, 1);

         // see if there is a "more info" button
         // /shopping/product/moreinfo.jsp
         $start = strpos($page, "/shopping/product/moreinfo.jsp");
         $is_more_info = ($start > 0) ? TRUE : FALSE;

         // -----------------------------------------------------------------
         
         // now get the "more info" page

         if ($is_more_info == TRUE)
         {

            $request = "http://www.zianatural.com/shopping/product/moreinfo.jsp".
                       "?iProductID=".$product_id;
//           echo $request;
   
            $r = new HTTP_Request($request);

//           if ($this->config->item('proxy') != "")
//           {
//              $r->setProxy($this->config->item('proxy'), $this->config->item('proxy_port'));
//           }

            $response = $r->sendRequest();

            if (!PEAR::isError($response))
            {
               $page = $r->getResponseBody();
            }
            else
            {
               return "<br>Error Message: ".$response->getMessage();
            }
   
            // scrape the resulting page
            // There is enough variety in these pages that I just add the
            // contents to the Long Description and I will need to go through all
            // and copy/paste the info into the correct fields.
            
            $this->position = 0;
            
            // find the additional information
            $start_str = '<div class="default">';
            $end_str = "</div>";
            $products[$cnt]['LongDescription'] .= "\n\n".$this->_get_substring($page, $start_str, $end_str, NULL, NULL, TRUE);
         }
         
         $products[$cnt]['SESFilename'] = url_title($products[$cnt]['ProductName']);
         $products[$cnt]['ProductGroup'] = 'none';
         $products[$cnt]['Language'] = 'en_US';
         $products[$cnt]['SmallAlt'] = $products[$cnt]['ProductName'];
         $products[$cnt]['LargeAlt'] = $products[$cnt]['ProductName'];
         $products[$cnt]['MetaTitle'] = 'Zia Natural: '.$products[$cnt]['ProductName'];
         $products[$cnt]['Status'] = 'active';
         $products[$cnt]['SiteID'] = 'zn';  // still required for hcg_public
         
         // clean up encoding for all strings
         foreach ($products[$cnt] AS $key => $value)
         {
            if (is_string($value))
            {
               $products[$cnt][$key] = entities_to_ascii($value);
               $products[$cnt][$key] = ascii_to_entities($value);
            }
         }
         
         echo "<pre>"; print_r($products[$cnt]); echo "</pre>";
         
         $cnt++;
      }
      
      foreach ($products AS $values)
      {
         // save the main record
         $this->cb_db->insert('pr_product', $values);
         $this->hcg_db->insert('pr_product', $values);

         $product_id = $this->cb_db->insert_id();
         
         // add the Locator code (based on ProductID)
         $locator['LocatorCode'] = 'ZN'.$product_id;
         $this->cb_db->where('ProductID', $product_id);
         $this->cb_db->update('pr_product', $locator);
         $this->hcg_db->where('ProductID', $product_id);
         $this->hcg_db->update('pr_product', $locator);
      
         // add site record for CoolBrew
         $site['ProductID'] = $product_id;
         $site['SiteID'] = 'zn';
      
         $this->cb_db->insert('pr_product_site', $site);

      }
      exit;  // without this exit command, the function gets cycled through twice.
   }
   
   // --------------------------------------------------------------------

   function _get_substring($page, $start_str, $end_str, $inc_start = FALSE, $inc_start_amt = 0, $inc_end = FALSE, $inc_end_amt = 0)
   {
      $start_offset = ($inc_start == TRUE) ? strlen($start_str) - $inc_start_amt : strlen($start_str);
      $start = strpos($page, $start_str, $this->position) + $start_offset;

      if ($start == $start_offset)
         return '';

      $end_offset = ($inc_end == TRUE) ? strlen($end_str) + $inc_end_amt : 0; // this isn't right
      $end = strpos($page, $end_str, $start) + $end_offset;
      
      if ($end == $end_offset)
         return '';
         
      $this->position = $end;

      return trim(substr($page, $start, ($end-$start)));
   }
   
   // --------------------------------------------------------------------

   function strip_cr($str)
   {
      $str = str_replace(chr(13),' ',$str);
      $str = str_replace(chr(10),'',$str);
      return $str;
   }
   
}
?>
