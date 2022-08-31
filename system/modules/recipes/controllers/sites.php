<?php

class Sites extends Controller {

   function Sites()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'recipes'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Lists the sites for the given recipe
    *
    */
   function index($site_id, $recipe_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Recipe_sites');
      
      $sites = $this->Recipe_sites->get_sites($recipe_id);
      $recipes['site_exists'] = (count($sites) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['sites'] = $sites;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;
      $data['recipe_id'] = $recipe_id;

      $this->load->vars($data);
   	
      echo $this->load->view('sites/list', NULL, TRUE);
      exit;
   }

   // --------------------------------------------------------------------

   /**
    * AJAX Adds a site to a recipe
    *
    */
   function add($site_id, $recipe_id, $mysite_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->helper(array('form', 'text'));    
      $this->load->model('Recipe_sites');
      
      $this->Recipe_sites->insert_recipe_site($recipe_id, $mysite_id);

      $sites = $this->Recipe_sites->get_sites($recipe_id);
      $recipes['site_exists'] = (count($sites) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['sites'] = $sites;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;
      $data['recipe_id'] = $recipe_id;

      $this->load->vars($data);
   	
      echo $this->load->view('sites/list', NULL, TRUE);
      exit;
   }
   
   // --------------------------------------------------------------------

   /**
    * AJAX Removes a site from a recipe
    *
    */
   function delete($site_id, $recipe_id, $mysite_id) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));    
      $this->load->model('Recipe_sites');
      
      $this->Recipe_sites->delete_recipe_site($recipe_id, $mysite_id);

      $sites = $this->Recipe_sites->get_sites($recipe_id);
      $recipes['site_exists'] = (count($sites) > 0) ? TRUE : FALSE;
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['sites'] = $sites;
      $data['recipes'] = $recipes;
      $data['site_id'] = $site_id;
      $data['recipe_id'] = $recipe_id;

      $this->load->vars($data);
   	
      echo $this->load->view('sites/list', NULL, TRUE);
      exit;
   }

}

/* End of file sites.php */
/* Location: ./system/modules/recipes/controllers/sites.php */