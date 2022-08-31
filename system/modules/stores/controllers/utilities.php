<?php

class Utilities extends Controller {

   // sites using an IRI store locator.
   // this should be dynamically generated from the database.
   var $iri_sites = array(
          "am",    // Arrowhead Mills
          "cs",    // Celestial Seasonings
          "db",    // DeBoles
          "eb",    // Earth's Best
          "ge",    // Garden of Eatin'
          "gfc",   // My Gluten Free Cafe
          "hf",    // Hain Pure Foods
          "hv",    // Health Valley
          "if",    // Imagine Foods
          "ms",    // Mountain Sun
          "rp",    // Rosetto Pasta
          "tc",    // Terra Chips
          "td",    // Taste The Dream
          "wb",    // Westbrae
          "yv",    // Yves Veggie US
          // listing multi-brand sites last helps reduce confusion
          "gfch",  // Gluten Free Choices
       );

   // sites using the Nielsen store locator.
   // this should be dynamically generated from the database.
   var $nielsen_sites = array(
          "ad",    // Alba Drinks
          "am",    // Arrowhead Mills
          "bo",    // Boston's Snacks
          "cb",    // Casbah
          "cs",    // Celestial Seasonings
          "db",    // DeBoles
          "eb",    // Earth's Best
          "ef",    // Estee
          "eg",    // Ethnic Gourmet
          "ge",    // Garden of Eatin'
          "gfc",   // My Gluten Free Cafe
          "hf",    // Hain Pure Foods
          "hv",    // Health Valley
          "hw",    // Hollywood
          "if",    // Imagine Foods
          "jn",    // Jason Natural
          "lb",    // Little Bear/Bearitos
          "ms",    // Mountain Sun
          "ns",    // Nile Spice
          "rp",    // Rosetto Pasta
          "tc",    // Terra Chips
          "td",    // Taste The Dream
          "wb",    // Westbrae
          "ws",    // WestSoy
          "yv",    // Yves Veggie US
          "zn",    // Zia Natural
          // listing multi-brand sites last helps reduce confusion
          "gfch",  // Gluten Free Choices
       );
    
    var $initial_import = FALSE;   // used in the Walmart import script
    var $store_lookup;
    var $google_key;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Utilities()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'stores'));
      $this->load->helper('url');

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }
	
   // --------------------------------------------------------------------

   /**
    * Displays list of utilities that can be used.
    */
   function index()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $admin['error'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');

      $this->load->helper('menu');

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('stores');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Stores');
      $data['submenu'] = get_submenu('Utilities');
      $data['admin'] = $admin;
      
      $this->load->vars($data);
   	
      return $this->load->view('utilities/index', NULL, TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * This script queries the Nielsen database to get a list of all the
    *  stores it has in Oregon for the given product.
    *
    */
   function gather_oregon()
   {
   
      $this->load->model('Zipcodes');
      $this->load->model('Nielsen');
      
      $zips = $this->Zipcodes->zipcodes_in_state('OR');
      
          $search = array();
         $search['productid'] = '007487396200';
         $search['city'] = '';
         $search['state'] = 'OR';
         $search['searchradius'] = 20;
         $search['productfamilyid'] = "HNCL";
         $search['clientid'] = "69";
         $search['template'] = "default.xsl";
         $search['stores'] = "1";
         $search['storespagenum'] = "1";
         $search['storesperpage'] = "50";
         $search['etailers'] = "0";
         $search['producttype'] = "agg";
         $search['brand'] = 'ws';
         $search['sort'] = "Distance";
         
         $results = $this->Nielsen->get_store_list($search);

     echo '<pre>'; print_r($results); echo '</pre>'; exit;
      
      foreach ($zips AS $zip)
      {
   
         $search = array();
         $search['productid'] = '';
         $search['zip'] = $zip;
         $search['searchradius'] = 20;
         $search['productfamilyid'] = "HNCL";
         $search['clientid'] = "69";
         $search['template'] = "default.xsl";
         $search['stores'] = "1";
         $search['storespagenum'] = "1";
         $search['storesperpage'] = "50";
         $search['etailers'] = "0";
         $search['producttype'] = "agg";
         $search['brand'] = '';
         $search['sort'] = "Distance";
         
         $results = $this->Nielsen->get_store_list($search);
        
      }

   }
   
      
   // --------------------------------------------------------------------

   /**
    * Looks for duplicate entries in the database and displays both 
    *   records so the user can decide which record to delete.
    *
    */
   function find_duplicate()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $admin['error'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');

      $this->load->helper('menu');
      $this->load->helper(array('form', 'text'));
      $this->load->model('Messages');
      
      $sql = 'SELECT StoreName, '.
               'CONCAT(Address1,Address2,City,State,Zip) AS Address '.
             'FROM stores '.
             'GROUP BY Address '.
             'HAVING ( COUNT(CONCAT(Address1,Address2,City,State,Zip)) > 1 )';
      $query = $this->read_db->query($sql);
      $dups = $query->result_array();
      
      $data['total'] = count($dups);
      
      $data['stores'] = array();
      if ($data['total'] > 0)
      {
         $sql = 'SELECT * '.
                'FROM stores '.
                'WHERE CONCAT(Address1,Address2,City,State,Zip) = "'.$dups[0]['Address'].'"';
         $query = $this->read_db->query($sql);
         $data['stores'] = $query->result_array();
      }
      
      for ($i=0, $cnt=count($data['stores']); $i<$cnt; $i++)
      {
         $data['stores'][$i]['messages'] = $this->Messages->get_open_messages_by_store_id($data['stores'][$i]['StoreID']);
      }

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('stores');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Stores');
      $data['submenu'] = get_submenu('Utilities');
      $data['admin'] = $admin;
      

      $this->load->vars($data);
   	
      return $this->load->view('utilities/find_duplicate', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes the duplicate specified and redirect back to find_duplicate
    *   so the next item can be reviewed.
    *
    */
   function delete_duplicate($store_id)
   {
      $this->load->model('Stores');

      $this->Stores->delete_store($store_id);
      
      $this->session->set_userdata('admin_message', 'The duplicate store ('.$store_id.') has been deleted.');

      redirect('utilities/find_duplicate');
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Looks for records with no phone number and displays a simple form that 
    *  helps you correct it. It also allows you to change the store name and
    *  status since that is some of the info you might get in searching for
    *  the phone number.
    *
    */
   function missing_phone()
   {
      $this->load->model('Stores');
      
      if (isset($_POST['Phone']))
      {
         $values = array();
         $values['Phone'] = $_POST['Phone'];
         $values['status'] = $_POST['status'];
         $values['StoreName'] = $_POST['StoreName'];
         $this->Stores->update_store($_POST['StoreID'], $values);
      }

       // Find a record that has a missing phone number
      $store = $this->Stores->get_store_no_phone();
      
      if ($store)
      {
         echo '<pre>'; print_r($store); echo '</pre>';
         $search = $store['StoreName'].', '.$store['Address1'].', '.$store['City'].', '.$store['State'].' '.$store['Zip'];
         echo '<br />'.$search.' | <a href="http://www.google.com#q='.urlencode($search).'" target="_blank">Search Google</a>';
         echo ' | <a href="http://webadmin.hcgweb.net/admin/stores.php/stores/edit/'.$store['StoreID'].'/100" target="_blank">Store Admin</a>';
         echo '<br /><span style="color:red;">No Phone Number.</span>';
         echo '<form method="POST">';
         echo '<input type="hidden" name="StoreID" value="'.$store['StoreID'].'" />';
         echo '<br />Phone: <input type="text" name="Phone" />';
         echo '<br />Status: <input type="text" name="status" value="'.$store['status'].'" />';
         echo '<br />Store Name: <input type="text" size="60" name="StoreName" value="'.$store['StoreName'].'" />';
         echo '<br /><input type="submit" />';
         echo '</form>';
         exit;
      }
      echo '<br />No missing phone numbers found.';
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Closes any messages referring to Nielsen data since we cannot 
    *  update the data directly.
    *
    */
   function close_nielsen()
   {
      $this->load->model('Messages');

      $count = $this->Messages->close_nielsen_messages();
      
      $this->session->set_userdata('admin_message', $count.' messages were closed.');

      redirect('utilities/index');
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the UPC list supplied by Nielsen and compares it to the
    * products database.
    *
    */
   function process_nielsen_upcs()
   {
      // the script assumes that the source data has been imported into 
      // a table called stores_nielsen_tmp
      
      // First, we want to look at the smaller list and see how it matches
      // up with the larger. The CoolBrew list is smaller, so we will 
      // look at all entries in that list.
      
      $sql = 'SELECT ProductID, ProductName, UPC, Status, SiteID '.
             'FROM pr_product '.
             'WHERE ProductGroup != "master"';
      $query = $this->read_db->query($sql);
      $results = $query->result_array();
      
      $no_upc = array();

      foreach ($results AS $result)
      {
         if ($result['Status'] == 'partial')
         {
            $result['Status'] = 'active';
         }

         $upcA = '0'.$result['UPC']; // simply adding 0 in front
         
         if ($upcA != '0')
         {
            $sql = 'SELECT * FROM stores_nielsen_tmp '.
                   'WHERE UPC = "'.$upcA.'"';
            $q = $this->read_db->query($sql);
            
            // ideally, we will find the record right away
            $matched = FALSE;
            if ($q->num_rows() > 0)
            {
               $matched = TRUE;
               $update['UPCMatch'] = 'YES';
               $update['ProductID'] = $result['ProductID'];
               $update['ProductName'] = $result['ProductName'];
               $update['Status'] = $result['Status'];
               $update['SiteID'] = $result['SiteID'];
               $this->write_db->where('UPC', $upcA);
               $this->write_db->update('stores_nielsen_tmp', $update);
               echo $result['ProductName'].' ('.$upcA.') was updated.<br />';
            }
            else
            {
               // try it with just the last 10 digits
               $upcB = substr($result['UPC'], 1, 10);

               $sql = 'SELECT * FROM stores_nielsen_tmp '.
                   'WHERE UPC LIKE "%'.$upcB.'"';
               $q = $this->read_db->query($sql);
               if ($q->num_rows() > 0)
               {
                  $nielsen = $q->row_array();
                  echo '<pre>'; print_r($nielsen); echo '</pre>';
                  $matched = TRUE;
                  $update['UPCMatch'] = 'NO';
                  $update['ProductID'] = $result['ProductID'];
                  $update['ProductName'] = $result['ProductName'];
                  $update['Status'] = $result['Status'];
                  $update['SiteID'] = $result['SiteID'];
                  $this->write_db->where('UPC', $nielsen['UPC']);
                  $this->write_db->update('stores_nielsen_tmp', $update);
                  echo '<span style="color:#FF0;">'.$result['ProductName'].' ('.$nielsen['UPC'].') was updated.</span><br />';
               }
            }
            
            if ($matched == FALSE && $result['Status'] != 'discontinued' && $result['Status'] != 'inactive')
            {
               $insert['UPC'] = $upcA;
               $insert['UPCMatch'] = NULL;
               $insert['ProductID'] = $result['ProductID'];
               $insert['ProductName'] = $result['ProductName'];
               $insert['Status'] = $result['Status'];
               $insert['SiteID'] = $result['SiteID'];
               $this->write_db->insert('stores_nielsen_tmp', $insert);
               echo '<span style="color:#F00;">'.$result['ProductName'].' ('.$upcA.') was inserted.</span><br />';
            }
         }
         elseif ($result['Status'] != 'discontinued' && $result['Status'] != 'inactive')
         {
            $no_upc[] = $result;
         }
      }
      echo "These records do not have a UPC:<br />";
      echo '<pre>'; print_r($no_upc); echo '</pre>';
   }

   // --------------------------------------------------------------------

   /**
    * The brand lists are mostly screwed up, so this is an attempt to 
    * fix them. This worked and now we should not need this function.
    *
    */
   function clean_brands()
   {
      echo "Working...<br><pre>";

      // first, create a lookup table of SiteIDs
      $sql = 'SELECT * FROM adm_site_brand';
      $query = $this->read_db->query($sql);
      $results = $query->result_array();
      
      foreach ($results AS $result)
      {
         $sites[] = $result['SiteID'];
      }

      // get all store records
      $sql = 'SELECT * FROM stores '.
             'WHERE Brands != "" '.
             'OR NotBrands != ""';
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
      $cnt = 0;

      foreach ($stores AS $store)
      {
         $site_str = trim(strtolower($store['Brands']));
         $new_site_str = $site_str;
         if ($site_str != '')
         {
            $site_str = str_replace("", '', $site_str);
            $site_str = str_replace(" ", '', $site_str);
            // check if the string has commas
            if ( ! strpos($site_str, ','))
            {
               // if not, then split it into 2-character chunks.
               $site_array = array();
               for ($i=0; $i<strlen($site_str); $i=$i+2)
               {
                  $site_array[] = substr($site_str, $i, 2);
               }
               $new_site_str = implode(',', $site_array);
               echo str_pad($store['StoreID'], 5, '0', STR_PAD_LEFT).' | HOT | CV | '.$new_site_str."\n";
            }
            else
            {
               echo str_pad($store['StoreID'], 5, '0', STR_PAD_LEFT).' | HOT | OK | '.$site_str."\n";
            }
         }

         $notsite_str = trim(strtolower($store['NotBrands']));
         $new_notsite_str = $notsite_str;
         if ($notsite_str != '')
         {
            $notsite_str = str_replace("", '', $notsite_str);
            $notsite_str = str_replace(" ", '', $notsite_str);
           // check if the string has commas
            if ( ! strpos($notsite_str, ','))
            {
               // if not, then split it into 2-character chunks.
               $notsite_array = array();
               for ($i=0; $i<strlen($notsite_str); $i=$i+2)
               {
                  $notsite_array[] = substr($notsite_str, $i, 2);
               }
               $new_notsite_str = implode(',', $notsite_array);
               echo str_pad($store['StoreID'], 5, '0', STR_PAD_LEFT).' | NOT | CV | '.$new_notsite_str."\n";
            }
            else
            {
               echo str_pad($store['StoreID'], 5, '0', STR_PAD_LEFT).' | NOT | OK | '.$notsite_str."\n";
            }
         }
         $values['Brands'] = $new_site_str;
         $values['NotBrands'] = $new_notsite_str;
         $this->write_db->where('StoreID', $store['StoreID']);
         $this->write_db->update('stores', $values);
         echo $store['StoreID']." saved to database.\n\n";
      }
      echo "</pre>";
   }

   // --------------------------------------------------------------------

   /**
    * This is to generate the stores_brand table from the existing 
    * stores table. Later, we can delete the "Brands" and "NotBrands"
    * fields from the stores table.
    *
    */
   function build_tables()
   {
      echo "Working...<br>";

      // first, create a lookup table of SiteID => BrandID
      $sql = 'SELECT * FROM adm_site_brand';
      $query = $this->read_db->query($sql);
      $results = $query->result_array();
      
      foreach ($results AS $result)
      {
         $site_brand[$result['SiteID']] = $result['BrandID'];
      }

      // get all store records
      $sql = 'SELECT * FROM stores '.
             'WHERE Brands != "" '.
             'OR NotBrands != ""';
      $query = $this->read_db->query($sql);
      $stores = $query->result_array();
      
      $cnt = 0;

      foreach ($stores AS $store)
      {
         $site_list = array();
         $site_str = trim(strtolower($store['Brands']));
         $site_str = str_replace("", '', $site_str);
         if ($site_str != '')
            $site_list = explode(',', $site_str);

         $notsite_list = array();
         $notsite_str = trim(strtolower($store['NotBrands']));
         $notsite_str = str_replace("", '', $notsite_str);
         if ($notsite_str != '')
            $notsite_list = explode(',', $notsite_str);

         if (count($site_list) > 0)
         {
            foreach ($site_list AS $site)
            {
               if ( ! isset($site_brand[$site]))
               {
                  $errors[] = str_pad($store['StoreID'], 5, '0', STR_PAD_LEFT)." | Site ID \"".$site."\" is not in the array.<br>";
               }
               else
               {
                  $entry['StoreID'] = $store['StoreID'];
                  $entry['BrandID'] = $site_brand[$site];
                  $entry['Carried'] = 1;
                  // check if this record already exists
                  $sql = 'SELECT * FROM stores_brand '.
                         'WHERE StoreID = '.$entry['StoreID'].' '.
                         'AND BrandID = "'.$entry['BrandID'].'"';
                  $query = $this->read_db->query($sql);
                  if ($query->num_rows() > 0)
                  {
                     $errors[] = str_pad($entry['StoreID'], 5, '0', STR_PAD_LEFT)." | There are conflicting entries for ".$entry['BrandID'].".<br>";
                  }
                  else
                  {
                    $this->write_db->insert('stores_brand', $entry);
                    $cnt++;
                    
                    echo str_pad($entry['StoreID'], 5, '0', STR_PAD_LEFT)." | Site ID \"".$site."\" was successfully used.<br>";
                  }
               }
            }
         }
         
         if (count($notsite_list) > 0)
         {
            foreach ($notsite_list AS $site)
            {
               if ( ! isset($site_brand[$site]))
               {
                  $errors[] = str_pad($store['StoreID'], 5, '0', STR_PAD_LEFT)." | Site ID \"".$site."\" is not in the array.<br>";
               }
               else
               {
                  $entry['StoreID'] = $store['StoreID'];
                  $entry['BrandID'] = $site_brand[$site];
                  $entry['Carried'] = 1;
                  // check if this record already exists
                  $sql = 'SELECT * FROM stores_brand '.
                         'WHERE StoreID = '.$entry['StoreID'].' '.
                         'AND BrandID = "'.$entry['BrandID'].'"';
                  $query = $this->read_db->query($sql);
                  if ($query->num_rows() > 0)
                  {
                     $errors[] = str_pad($entry['StoreID'], 5, '0', STR_PAD_LEFT)." | There are conflicting entries for ".$entry['BrandID'].".<br>";
                  }
                  else
                  {
                    $this->write_db->insert('stores_brand', $entry);
                    $cnt++;
                    
                    echo str_pad($entry['StoreID'], 5, '0', STR_PAD_LEFT)." | Site ID \"".$site."\" was successfully used.<br>";
                  }
               }
            }
         }
      }
      echo $cnt." records added.<br><br>Errors:<br>";
      foreach ($errors AS $error)
         echo $error;
      
   }

   // --------------------------------------------------------------------

   /**
    * Displays list of IRI data that can be generated.
    */
   function iri_index()
   {
      $admin['message'] = $this->session->userdata('admin_message');
      if ($this->session->userdata('admin_message') != '')
         $this->session->set_userdata('admin_message', '');

      $admin['error'] = $this->session->userdata('admin_error');
      if ($this->session->userdata('admin_error') != '')
         $this->session->set_userdata('admin_error', '');

      $this->load->helper('menu');

      $this->collector->append_css_file('admin');

      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Stores');
      $data['submenu'] = get_submenu('IRI Data Files');
      $data['admin'] = $admin;
      
      $data['site_list'] = '';
      $site_count = count($this->iri_sites);
      for ($i=0; $i<$site_count; $i++)
      {
         if ($i == $site_count - 1)
         {
            $data['site_list'] .= 'and '.$this->iri_sites[$i];
         }
         else
         {
            $data['site_list'] .= $this->iri_sites[$i].', ';
         }
      }

      $this->load->vars($data);
   	
      return $this->load->view('utilities/iri', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * This exports the brands product data to a CVS file that will work
    * with the IRI Store Locator system. This function generates the data
    * for the prod.cvs file.
    * 
    * This file has two parts, a UPC section and a Custom section:
    * 
    * The UPC section has five columns
    *    upc (10 digits)
    *    $site_id." ".ProductName
    *    UPC (literal)
    *    HNCL (literal)
    *    69 (literal)
    * 
    * The Custom section also has five columns
    *    LocatorCode
    *    $site_id." ".ProductName
    *    Custom (literal)
    *    HNCL (literal)
    *    69 (literal)
    */
   function iri_export_prod()
   {
      $sites = $this->iri_sites;
      $filename = "prod.csv";
   
      $eol = "\n";
      $errors = array();
      $line = "";
      
      $upcs = array();
   
      foreach ($sites as $site)
      {
         $sql = "SELECT p.UPC, p.ProductName, p.ProductGroup, p.LocatorCode ".
                "FROM pr_product AS p, pr_product_site AS ps " .
                "WHERE ps.SiteID LIKE \"".$site."\" ".
                "AND p.ProductID = ps.ProductID ".
                "AND p.Status NOT LIKE 'discontinued' ".
                "AND p.Status NOT LIKE 'inactive' ".
                "AND p.Status NOT LIKE 'pending' ".
                "AND p.LocatorCode NOT LIKE 'none'";
         $query = $this->read_db->query($sql);
         $prod_data = $query->result_array();
         
         // check to make sure we don't have duplicates
         for ($i=0; $i<count($prod_data); $i++)
         {
            if (in_array($prod_data[$i]['UPC'], $upcs))
            {
               $prod_data[$i]['duplicate'] = TRUE;
            }
            else
            {
               $prod_data[$i]['duplicate'] = FALSE;
               $upcs[] = $prod_data[$i]['UPC'];
            }
         }
         
         // generate UPC records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] != "master"
                && $prod_data[$i]['duplicate'] == FALSE)
            {
               if ($prod_data[$i]['UPC'] == '')
               {
                  $errors[] = $prod_data[$i]['ProductName']." does not have a UPC";
               }
               $line .= substr($prod_data[$i]['UPC'], 1).",";
               $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
               $line .= "UPC,HNCL,69".$eol;
            }
         }

         // generate Custom "master" records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "master"
                && $prod_data[$i]['duplicate'] == FALSE)
            {
               if ($prod_data[$i]['LocatorCode'] == '')
               {
                  $errors[] = $prod_data[$i]['ProductName']." does not have a Locator Code";
               }
               $line .= $prod_data[$i]['LocatorCode'].",";
               $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
               $line .= "Custom,HNCL,69".$eol;
            }
         }

         // generate Custom "none" records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "none"
                && $prod_data[$i]['duplicate'] == FALSE)
            {
               if ($prod_data[$i]['LocatorCode'] == '')
               {
                  $errors[] = $prod_data[$i]['ProductName']." does not have a Locator Code";
               }
               $line .= $prod_data[$i]['LocatorCode'].",";
               $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
               $line .= "Custom,HNCL,69".$eol;
            }
         }
      }
      
      $data['errors'] = $errors;
      $data['line'] = $line;
      
      $this->load->vars($data);
   	
      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);
      echo $this->load->view('utilities/iri_prod', NULL, TRUE);

      // NOTE: this isn't working because the index file is not reloading
      // to display and clear the error messages.
/*      if ( ! empty($errors))
      {
         $this->session->set_userdata('admin_error', 'There were errors generating the <b>prod.csv</b> file. Please view the file for details.');
      }
      else
      {
         $this->session->set_userdata('admin_message', 'The <b>prod.csv</b> file has been successfully generated.');
      }
*/
      exit;
   }


   // --------------------------------------------------------------------

   /**
    * This exports the brands product data to a CVS file that will work
    * with the IRI Store Locator system. This function generates the data
    * for the prod_rel.cvs file.
    * 
    * This file is pretty simple, just four columns:
    *   upc (10 digits)
    *   HNCL (literal)
    *   69 (literal)
    *   LocatorCode
    * 
    */
   function iri_export_prod_rel()
   {
      $sites = $this->iri_sites;
      $filename = "prod_rel.csv";

      $eol = "\n";
      $errors = array();
      $line = "";
   
      $upcs = array();
   
      foreach ($sites as $site)
      {
         $sql = "SELECT p.UPC, p.ProductName, p.ProductGroup, p.LocatorCode ".
                "FROM pr_product AS p, pr_product_site AS ps " .
                "WHERE ps.SiteID LIKE \"".$site."\" ".
                "AND p.ProductID = ps.ProductID ".
                "AND p.Status NOT LIKE 'discontinued' ".
                "AND p.Status NOT LIKE 'inactive' ".
                "AND p.Status NOT LIKE 'pending' ".
                "AND p.ProductGroup NOT LIKE 'master' ".
                "AND p.LocatorCode NOT LIKE 'none'";
         $query = $this->read_db->query($sql);
         $prod_data = $query->result_array();
   
         // check to make sure we don't have duplicates
         for ($i=0; $i<count($prod_data); $i++)
         {
            if (in_array($prod_data[$i]['UPC'], $upcs))
            {
               $prod_data[$i]['duplicate'] = TRUE;
            }
            else
            {
               $prod_data[$i]['duplicate'] = FALSE;
               $upcs[] = $prod_data[$i]['UPC'];
            }
         }
         
         // generate records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['duplicate'] == FALSE)
            {
               if ($prod_data[$i]['UPC'] == '')
               {
                  $errors[] = $prod_data[$i]['ProductName']." does not have a UPC";
               }
               $line .= substr($prod_data[$i]['UPC'], 1).",";
               $line .= "HNCL,69,";
               $line .= $prod_data[$i]['LocatorCode'].$eol;
            }
         }
      }

      $data['errors'] = $errors;
      $data['line'] = $line;
      
      $this->load->vars($data);
   	
      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);
      echo $this->load->view('utilities/iri_prod_rel', NULL, TRUE);

      // NOTE: this isn't working because the index file is not reloading
      // to display and clear the error messages.
/*      if ( ! empty($errors))
      {
         $this->session->set_userdata('admin_message', 'There were errors generating the <b>prod_rel.csv</b> file. Please view the file for details.');
      }
      else
      {
         $this->session->set_userdata('admin_message', 'The <b>prod_rel.csv</b> file has been successfully generated.');
      }
*/
      exit;
   }


   // --------------------------------------------------------------------

   /**
    * This exports the brands product data to a CVS file that will work
    * with the IRI Store Locator system. This function generates the data
    * for the upc_69.cvs file.
    * 
    * This file is very simple, just one column:
    *   upc (10 digits)
    */
   function iri_export_upc_69()
   {
      $sites = $this->iri_sites;
      $filename = "upc_69.csv";

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);

      $eol = "\n";
      $errors = array();
      $line = "";
   
      $upcs = array();
   
      foreach ($sites as $site)
      {
         $sql = "SELECT p.UPC, p.ProductName, p.ProductGroup, p.LocatorCode ".
                "FROM pr_product AS p, pr_product_site AS ps " .
                "WHERE ps.SiteID LIKE \"".$site."\" ".
                "AND p.ProductID = ps.ProductID ".
                "AND p.Status NOT LIKE 'discontinued' ".
                "AND p.Status NOT LIKE 'inactive' ".
                "AND p.Status NOT LIKE 'pending' ".
                "AND p.ProductGroup NOT LIKE 'master' ".
                "AND p.LocatorCode NOT LIKE 'none'";
         $query = $this->read_db->query($sql);
         $prod_data = $query->result_array();
   
         // check to make sure we don't have duplicates
         for ($i=0; $i<count($prod_data); $i++)
         {
            if (in_array($prod_data[$i]['UPC'], $upcs))
            {
               $prod_data[$i]['duplicate'] = TRUE;
            }
            else
            {
               $prod_data[$i]['duplicate'] = FALSE;
               $upcs[] = $prod_data[$i]['UPC'];
            }
         }
         
         // generate records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['duplicate'] == FALSE)
            {
               if ($prod_data[$i]['UPC'] == '')
               {
                  $errors[] = $prod_data[$i]['ProductName']." does not have a UPC";
               }
               $line .= substr($prod_data[$i]['UPC'], 1).$eol;
            }
         }
      }

      $data['errors'] = $errors;
      $data['line'] = $line;
      
      $this->load->vars($data);
   	
      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);
      echo $this->load->view('utilities/iri_upc_69', NULL, TRUE);

      // NOTE: this isn't working because the index file is not reloading
      // to display and clear the error messages.
/*      if ( ! empty($errors))
      {
         $this->session->set_userdata('admin_message', 'There were errors generating the <b>upc_69.csv</b> file. Please view the file for details.');
      }
      else
      {
         $this->session->set_userdata('admin_message', 'The <b>upc_69.csv</b> file has been successfully generated.');
      }
*/
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * This generates a report of all the products being supported by IRI
    * for the store locator.
    */
   function iri_export_report()
   {
      $this->load->model('Sites');
      
      $sites = $this->iri_sites;
      $filename = "iri_report.csv";
   
      $eol = "\n";
      $errors = array();

      $count1['total'] = 0;
      $count2['total'] = 0;
   
      $upcs = array();
   
      foreach ($sites as $site)
      {
   
         $brand = $this->Sites->get_brand_name($site);
   
         $brandname[$site] = $brand.' ('.$site.')';

         $sql = "SELECT p.ProductID, p.UPC, p.ProductName, ".
                  "p.ProductGroup, p.LocatorCode ".
                "FROM pr_product AS p, pr_product_site AS ps ".
                "WHERE ps.SiteID LIKE \"".$site."\" ".
                "AND p.ProductID = ps.ProductID ".
                "AND p.Status NOT LIKE 'discontinued' ".
                "AND p.Status NOT LIKE 'inactive' ".
                "AND p.Status NOT LIKE 'pending' ".
                "AND p.LocatorCode NOT LIKE 'none'";
         $query = $this->read_db->query($sql);
         $prod_data = $query->result_array();
   
         // check to make sure we don't have duplicates
         for ($i=0; $i<count($prod_data); $i++)
         {
            if (in_array($prod_data[$i]['UPC'], $upcs))
            {
               $prod_data[$i]['duplicate'] = TRUE;
            }
            else
            {
               $prod_data[$i]['duplicate'] = FALSE;
               $upcs[] = $prod_data[$i]['UPC'];
            }
         }
         
         // generate UPC records
         $count1[$site] = 0;
         $line1[$site] = "";
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] != "master"
                && $prod_data[$i]['duplicate'] == FALSE)
            {
               $line1[$site] .= substr($prod_data[$i]['UPC'], 1).",";
               if ($prod_data[$i]['ProductGroup'] != "none")
               {
                  $line1[$site] .= "\"* ";
               }
               else
               {
                  $line1[$site] .= "\"";
               }
               $line1[$site] .= str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
               $line1[$site] .= $eol;
               $count1[$site]++;
            }
         }

         // generate Custom "master" records
         $count2[$site] = 0;
         $line2[$site] = "";
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "master"
                && $prod_data[$i]['duplicate'] == FALSE)
            {
               $line2[$site] .= $prod_data[$i]['LocatorCode'].",";
               $line2[$site] .= "\"".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
               $line2[$site] .= $eol;
               $line2[$site] .= "\"(Group containing these products: ";
               for ($j=0; $j<count($prod_data); $j++)
               {
                  if ($prod_data[$j]['ProductGroup'] == $prod_data[$i]['ProductID'])
                  {
                     $line2[$site] .= substr($prod_data[$j]['UPC'], 1)." ";
                  }
               }
               $line2[$site] .= ")\"".$eol;
               $count2[$site]++;
            }
         }

         // generate Custom "none" records
         $line3[$site] = "";
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "none"
                && $prod_data[$i]['duplicate'] == FALSE)
            {
               $line3[$site] .= $prod_data[$i]['LocatorCode'].",";
               $line3[$site] .= "\"".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
               $line3[$site] .= $eol;
               $count2[$site]++;
            }
         }
         $count1['total'] = $count1['total'] + $count1[$site];
         $count2['total'] = $count2['total'] + $count2[$site];
     }
   
      $data['sites'] = $sites;
      $data['errors'] = $errors;
      $data['brandname'] = $brandname;
      $data['product_count'] = $count1;
      $data['group_count'] = $count2;
      $data['line1'] = $line1;
      $data['line2'] = $line2;
      $data['line3'] = $line3;
      
      $this->load->vars($data);
   	
      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);
      echo $this->load->view('utilities/iri_report', NULL, TRUE);

      // NOTE: this isn't working because the index file is not reloading
      // to display and clear the error messages.
/*      if ( ! empty($errors))
      {
         $this->session->set_userdata('admin_message', 'There were errors generating the <b>iri_report.csv</b> file. Please view the file for details.');
      }
      else
      {
         $this->session->set_userdata('admin_message', 'The <b>iri_report.csv</b> file has been successfully generated.');
      }
*/
      exit;
   }


} // END Class

?>