<?php

class Earthsbest extends Controller {

   function Earthsbest()
   {
      parent::Controller();	
   }
	
   // -------------------------------------------------------------------------

   function uplive()
   {
      $this->load->library('Dbtools');
      $this->load->helper('email');
      
      $today = date('Ymd');
      $msg = '';
      $output = '';
      
      // back up the live database on bolwebdb1
      $live_backup = '/var/opt/httpd/eb-data/drupal_earthsbest_'.$today.'.sql';
      $cmd = 'mysqldump -u root -p --default-character-set=latin1 drupal_earthsbest > '.$live_backup;
      exec("ssh mysql-master '".$cmd."'", $output);
      
      echo '<pre>'; print_r($output); echo '</pre>'; exit;

      $this->load->database('live');
      
      $this->_send_query("use drupal_earthsbest_stage");
      $query = $this->_send_query("show tables");
      echo '<pre>'; print_r($myarray); echo '</pre>'; exit;
      $this->_send_query("source /var/opt/httpd/eb-data/earthsbest.sql");
      $this->_send_query("source /var/opt/httpd/eb-data/genpure.sql");

      // upload the files from staging to the live server
      exec("cd /var/opt/httpd/");
      exec("uplive -d ebdocs/");
      
      // move coolbrew product data live for API
      
      $this->load->model('Coolbrew_products');
      $this->Coolbrew_products->move_pr_category('stage', 'live', 'eb');
      $this->Coolbrew_products->move_pr_nlea('stage', 'live', 'eb');
      $this->Coolbrew_products->move_pr_product('stage', 'live', 'eb');
      $this->Coolbrew_products->move_pr_product_category('stage', 'live', 'eb');
      $this->Coolbrew_products->move_pr_product_site('stage', 'live', 'eb');
      $this->Coolbrew_products->move_pr_symbol('stage', 'live', 'eb');

      // move coolbrew FAQ data live for API
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_answer', '');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_category', 'SiteID = "eb"');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_index', '');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_item', '');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_item_category', '');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_item_product', '');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_item_product_category', '');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_keyword', 'SiteID = "eb"');
      $this->dbtools->dbCopyWhere('stage', 'live', 'coolbrew_stage', 'coolbrew', 'faqs_site', 'SiteID = "eb"');

      
      $msg .= "The EB site appears to have deployed successfully.\n";
      $subject = "The EB deployment suceeded";
      send_message($hostname, $subject, $msg);
      echo $msg;
      exit;
   }
   
   // -------------------------------------------------------------------------

   function _send_query($sql)
   {
      if (FALSE === $result = $this->db->query($sql))
      {
         $msg .= "Unable to query database\n";
         $msg .= mysql_error();
         $subject = "The EB deployment failed";
         send_message($hostname, $subject, $msg);
         echo $msg;
         exit;
      }
      return $result;
   }
   
}

// end of uplive/controllers/earthsbest.php