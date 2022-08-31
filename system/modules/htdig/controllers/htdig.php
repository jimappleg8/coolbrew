<?php

class Htdig extends Controller {

   function Htdig()
   {
      parent::Controller();   
   }
   
   function search_results()
   {
      global $_HCG_GLOBAL;
      
      // (string) the site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) the template to use
      $tpl = $this->tag->param(2, 'htdig_search_results');
            
      $errors = "";
      $max_pages = 10;
      
      if ($this->input->post('matchesperpage'))
      {
         $options['matchesperpage'] = $this->input->post('matchesperpage');
      }
      else
      {
         $options['matchesperpage'] = 10; // the default
      }
   
      if ($this->input->post('words'))
      {
         $options['words'] = $this->input->post('words');
      }
      else
      {
         $options['words'] = ""; // the default
         $errors = "You must specify a word to search for.";
      }
   
      if ($this->input->post('method'))
      {
         $options['method'] = $this->input->post('method');
      }
      else
      {
         $options['method'] = "and"; // the default
      }
   
      if ($this->input->post('sort'))
      {
         $options['sort'] = $this->input->post('sort');
      }
      else
      {
         $options['sort'] = "score"; // the default
      }
   
      if ($this->input->post('page'))
      {
         $options['page'] = $this->input->post('page');
      }
      else
      {
         $options['page'] = 1; // the default
      }
   
      $this->load->library('Search');
            
      $this->search->htdig_path = "/usr/bin";
      $this->search->htsearch_path = "/var/opt/httpd/cgi-bin";
      $this->search->configuration = "/etc/htdig/".$site_id.".conf";
      $this->search->database_directory = "/var/lib/htdig";
      $this->search->secure_search = 1;
   
      $words = ereg_replace("[ ]+","|",$options['words']);
   
      $results = array();
      
      $this->search->do_search($options['words'], $options, $results);
      
      // set how many pages will be listed
      $results['num_pages'] = ceil($results['MatchCount'] / $options['matchesperpage']);

      if ($results['num_pages'] > $max_pages)
      {
         $results['num_pages'] = $max_pages;
      }

      for($i=1; $i<=$results['num_pages']; $i++)
      {
         $results['page_loop'][$i-1] = $i;
      }
      
      $results['php_self'] = $_SERVER['PHP_SELF'];
      
//      echo "<pre>"; print_r($results); echo "</pre>";

      $data['results'] = $results;
      $data['options'] = $options;
      $data['errors'] = $errors;

      echo $this->load->view($tpl, $data, TRUE);
   }
   
}

?>