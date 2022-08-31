<?php

class Utilities extends Controller {

   function Utilities()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'faqs'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a search index from existing database entries
    *
    */
   function generate_index()
   {
      $this->load->model('Indexes');
      
      $read_db = $this->load->database('read', TRUE);
      $write_db = $this->load->database('write', TRUE);

      // get a list of all faqs
      $sql = 'SELECT ID FROM faqs_item';

      $query = $read_db->query($sql);
      $faqs = $query->result_array();
      
      // go through each faq and index
      $count = 1;
      foreach ($faqs AS $faq)
      {
         echo $count." Indexing faq ID = ".$faq['ID']."... ";
         $this->Indexes->update_search_index($faq['ID']);
         echo "done.<br>";
         $count++;
      }
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Generates the category tree for all sites with FAQs
    *
    */
   function generate_category_tree()
   {
      $read_db = $this->load->database('read', TRUE);
      $write_db = $this->load->database('write', TRUE);

      $this->load->model('Categories');
      
      // get a list of all sites that have FAQs
      $sql = 'SELECT DISTINCT SiteID '.
             'FROM faqs_category';
      
      $query = $read_db->query($sql);
      $sites = $query->result_array();
      
      foreach ($sites AS $site)
      {
         $site_id = $site['SiteID'];
         
         // find out if there is a root node
         if ( ! $this->Categories->root_node_exists($site_id))
         {
            $root = $this->Categories->get_category_root($site_id);

            // assign this root to the Parent ID of all
            $sql = 'SELECT * FROM faqs_category '.
                   'WHERE SiteID = "'.$site_id.'" '.
                   'AND FaqCode != "root" '.
                   'ORDER BY Sort';
         
            $query = $read_db->query($sql);
            $cats = $query->result_array();
            
            // set the Parent ID and Sort order
            $count = 1;
            foreach ($cats AS $cat)
            {
               $values = array();
               $values['ParentID'] = $root;
               $values['Sort'] = $count;
               $write_db->where('ID', $cat['ID']);
               $write_db->update('faqs_category', $values);
               $count++;
            }
            $this->Categories->rebuild_tree($site_id, $root, 1);
         }
         else
         {
            // we assume that the Parent and Sort fields are filled out
            $root = $this->Categories->get_category_root($site_id);
            $this->Categories->rebuild_tree($site_id, $root, 1);
         }
      
         echo '<p>The category tree for '.$site_id.' has been generated.</p>';
      }
      
      echo '<p>All done.</p>';
      
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * Imports FAQs from the old hcgPublic database
    *
    * NOTE: This probably will not work now as the database structure
    *   has changed considerably.
    *
    */
   function import()
   {
      $this->load->helper('text');
      $this->load->model('Sites');
      $this->load->model('Categories');
      $this->load->model('Items');
      
      // We want to look at each database entry and 
      //   1) create a new category if one is not already created, 
      //   2) add the FAQ to the category
      
      // start by establishing two database connectings
      $cb_db = $this->load->database('write', TRUE);
      $hcg_db = $this->load->database('hcg_write', TRUE);
      
      $query = $hcg_db->query('SELECT * FROM faqs');
      $old_faqs = $query->result_array();
      
      echo '<pre>';
      
      // start looping through the old records and processing them
      foreach ($old_faqs AS $oldie)
      {
         $new_category = array();
         $new_faq = array();
         
         // parse the faqlist for SiteID and FaqCode
         if (strpos($oldie['faqlist'], "_") == 0)
         {
            $oldie['faqlist'] .= '_untitled';
         }
         list($new_list['SiteID'], $new_list['FaqCode']) = explode('_', $oldie['faqlist'], 2);
         
         
         // see if the category already exists and create it if is doesn't
         $sql = 'SELECT * FROM faqs_category '.
                'WHERE SiteID = "'.$new_category['SiteID'].'" '.
                'AND FaqCode = "'.$new_category['FaqCode'].'"';
                
         $query = $cb_db->query($sql);
         
         if ($query->num_rows < 1)
         {
            $new_category['Name'] = ucfirst($new_category['FaqCode']);
            $new_category['CreatedDate'] = date('Y-m-d H:i:s');
            $new_category['CreatedBy'] = $this->session->userdata('username');
            
            echo 'New List: '; print_r($new_category);
            $cb_db->insert('faqs_category', $new_category);
            
            $new_faq['ListID'] = $cb_db->insert_id();
         }
         else
         {
            $category = $query->row_array();
            $new_faq['ListID'] = $category['ID'];
         }
         
         // now we turn to the FAQ record itself
         
         if ($oldie['shortquestion'] == $oldie['question'])
         {
            $new_faq['ShortQuestion'] = '';
         }
         else
         {
            $new_faq['ShortQuestion'] = ascii_to_entities(entities_to_ascii($oldie['shortquestion']));
         }
         $new_faq['Question'] = ascii_to_entities(entities_to_ascii($oldie['question']));
         $new_faq['Answer'] = ascii_to_entities(entities_to_ascii($oldie['answer']));
         $new_faq['FlagAsNew'] = ($oldie['flagasnew'] == 1) ? 1 : 0;
         $new_faq['Status'] = ($oldie['status'] == 1) ? 'active' : 'inactive';
         $new_faq['Sort'] = $oldie['position'];
         $new_faq['CreatedDate'] = date('Y-m-d H:i:s', strtotime(str_replace('-','',$oldie['datecreated'])));
         $new_faq['CreatedBy'] = 'japplega';
         $new_faq['RevisedDate'] = date('Y-m-d H:i:s', strtotime(str_replace('-','',$oldie['lastmodified'])));
         $new_faq['RevisedBy'] = 'japplega';
         
         echo 'New FAQ: '; print_r($new_faq);
         $cb_db->insert('faqs_item', $new_faq);
      }
      echo '</pre>';
      
      exit;
   }


}
?>