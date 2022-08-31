<?php

class Utilities extends Controller {

   function Utilities()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'pages'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of availble utilities
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $pages['message'] = $this->session->userdata('page_message');
      if ($this->session->userdata('page_message') != '')
         $this->session->set_userdata('page_message', '');

      $pages['error_msg'] = $this->session->userdata('page_error');
      if ($this->session->userdata('page_error') != '')
         $this->session->set_userdata('page_error', '');

      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Pages');
      
      $site = $this->Sites->get_site_data($site_id);
      
      // check status of menu file
      $data['menu_file_exists'] = file_exists(SERVERPATH. $site['DocRootDir']. '/inc/'. $site_id. '_menu.txt');
      $sql = 'SELECT * FROM pages_utility '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ImportedMenu = 1';
      $query = $this->db->query($sql);
      $data['utility'] = $query->row_array();

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('pages');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Pages');
      $data['submenu'] = get_submenu($site_id, 'Pages');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['pages'] = $pages;
      
      $this->load->vars($data);
   	
      return $this->load->view('utilities/index', NULL, TRUE);
   }

   // --------------------------------------------------------------------
   
   /**
    * Imports a menu file from hcg_public
    *
    */
   function import_menu($site_id, $dry_run = 1)
   {
      $this->load->helper('text');
      $this->load->library('meta');
      $this->load->model('Sites');
      $this->load->model('Pages');
      
      $inserts = array();
      $skips = array();
      $errors = array();
      
      $site = $this->Sites->get_site_data($site_id);
      
      // get the menu data from the file
      $menu_file = SERVERPATH. $site['DocRootDir']. '/inc/'. $site_id. '_menu.txt';

      $handle = fopen($menu_file, "r") or die ("Unable to open menu file");

      $count = 0;
      while ( ! feof($handle))
      {
         $line = fgets($handle, 1024);
         $line_array = explode("\t", $line);
         $parent = $line_array[0];
         $sec_id = $line_array[1];
         $sec_name = $line_array[2];
         $link = (isset($line_array[3])) ? $line_array[3] : '';
         $sort = (isset($line_array[4])) ? $line_array[4] : '';

         $menu_data[$count]['parent'] = trim($parent);
         $menu_data[$count]['sec_id'] = trim($sec_id);
         $menu_data[$count]['sec_name'] = entities_to_ascii(trim($sec_name));
         $menu_data[$count]['link'] = trim($link);
         $menu_data[$count]['sort'] = $count;
         $count = $count + 1;
      }
      
//      echo "<pre>"; print_r($menu_data); echo "</pre>";
      
      // get root ID and initialize the $parents array
      $parents = array();
      $parents['root'] = $this->Pages->get_site_root($site_id);
      unset($menu_data[0]);
      $id = $parents['root'];

      foreach ($menu_data AS $item)
      {
         // check for product entry
         // it is assumed that the sec_id would be "prod####"
         $prod = preg_match("/prod\d+/", $item['sec_id']);
         if ($prod == 1)
         {
            $skips[] = 'SKIPPED: '.$item['sec_id'].' ('.$item['sec_name'].')';
            continue;
         }

         $page['SiteID'] = $site_id;
         if (isset($parents[$item['parent']]))
         {
            $page['ParentID'] = $parents[$item['parent']];
         }
         else
         {
            echo "Parent not defined: ".$item['parent'];
            exit;
         }
         $page['PageName'] = $item['sec_id'];
         $page['MenuText'] = $item['sec_name'];
         
         // check if this is an external link
         $proto = strpos($item['link'], 'http://');
         $page['ExternalLink'] = ($proto === FALSE) ? 0 : 1;

         // check if this link contains "_blank"
         $newwin = strpos($item['link'], '" target="_blank');
         if ($newwin !== FALSE)
         {
            $page['NewWindow'] = 1;
            $page['URL'] = str_replace('" target="_blank', '', $item['link']);
         }
         else
         {
            $page['NewWindow'] = 0;
            $page['URL'] = $item['link'];
         }

         $page['DisplayInMenu'] = 1;
         $page['Sort'] = $item['sort'];

         // Extract the meta data from the live file
         if ($page['ExternalLink'] != 1)
         {
            $url = $site['Protocol'].$site['ActiveDomain'].$page['URL'];
            $meta = $this->meta->get_meta_info($url);
            $page['PageTitle'] = $meta['PageTitle'];
            $page['MetaDescription'] = $meta['MetaDescription'];
            $page['MetaKeywords'] = $meta['MetaKeywords'];
            $page['MetaAbstract'] = $meta['MetaAbstract'];
            $page['MetaRobots'] = $meta['MetaRobots'];
         }
         else
         {
            $page['PageTitle'] = '';
            $page['MetaDescription'] = NULL;
            $page['MetaKeywords'] = NULL;
            $page['MetaAbstract'] = NULL;
            $page['MetaRobots'] = '';
         }
         
         // check for product category
         // it is assumed that the sec_id would be "cat####"
         $cat = preg_match("/cat\d+/", $item['sec_id']);
         $page['ProductCategory'] = $cat;
         
         // attempt to get an updated page name from pr_category table
         if ($page['ProductCategory'] == 1)
         {
            $cat_id = (integer) str_replace('cat', '', $item['sec_id']);
            $sql = 'SELECT CategoryCode FROM pr_category '.
                   'WHERE CategoryID = '.$cat_id;
            $query = $this->db->query($sql);
            $result = $query->row_array();
            if (isset($result['CategoryCode']))
            {
               if ($result['CategoryCode'] != '')
               {
                  $page['PageName'] = $result['CategoryCode'];
               }
               else
               {
                  $page['PageName'] = url_title($page['MenuText']);
                  if ($dry_run == 0)
                  {
                     $this->db->where('CategoryID', $cat_id);
                     $this->db->update('pr_category', array('CategoryCode' => $page['PageName']));
                  }
                  else
                  {
                     $where = 'CategoryID = '.$cat_id;
                     echo $this->db->update_string('pr_category', array('CategoryCode' => $page['PageName']), $where);
                     echo "<br><br>";
                  }
               }
            }
            else
            {
               $errors[] = "WARNING: The specified product category (".$cat_id.") was not found in pr_category.";
            }
         }

         $page['CreatedDate'] = date('Y-m-d H:i:s');
         $page['CreatedBy'] = $this->session->userdata('username');
         
         if ($dry_run == 0)
         {
            $this->db->insert('pages', $page);
            $id = $this->db->insert_id();
         }
         else
         {
            echo $this->db->insert_string('pages', $page); 
            echo "<br><br>";
            $id++;
         }
         
         $parents[$item['sec_id']] = $id;

         $inserts[] = 'INSERTED: '.$page['MenuText'].' ('.$page['PageName'].')';
      }
      
      if ($dry_run == 0)
      {
         $this->Pages->rebuild_tree($site_id, $parents['root'], 1);
      }
      
      // mark that this menu file has been imported.
      $utility['SiteID'] = $site_id;
      $utility['ImportedMenu'] = 1;
      $utility['ImportedDate'] = date('Y-m-d H:i:s');
      $utility['ImportedBy'] = $this->session->userdata('username');
      if ($dry_run == 0)
      {
         $this->db->insert('pages_utility', $utility);
      }
      else
      {
         echo $this->db->insert_string('pages_utility', $utility);
         echo "<br><br>";
      }

      if ( ! empty($errors))
      {
         echo "ERRORS:<br>";
         foreach ($errors AS $error)
         {
            echo $error.'<br>';
         }
         echo "<br>";
      }

      if ( ! empty($inserts))
      {
         echo "INSERTS:<br>";
         foreach ($inserts AS $insert)
         {
            echo $insert.'<br>';
         }
         echo "<br>";
      }

      if ( ! empty($skips))
      {
         echo "INSERTS:<br>";
         foreach ($skips AS $skip)
         {
            echo $skip.'<br>';
         }
      }

   }
   


}
?>