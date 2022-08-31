<?php

class Jobs_people_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // temporary rewrite table as site is being restructured.
   var $rewrites = array(
      '/admin/index' => '/jobs/index',
      '/admin/locations' => '/locations/index',
      '/admin/categories' => '/categories/index',
      '/admin/companies' => '/companies/index',
      '/admin/people' => '/people/index',
      '/admin/export_applications' => '/resumes/export_applications',
   );


   // --------------------------------------------------------------------

   function Jobs_people_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Get an array of tab data based on what the user has rights to see
    *
    */
   function get_tabs($this_tab = '', $menu_name = 'main')
   {
      $username = $this->session->userdata('username');
      
      $sql = 'SELECT adm_menu.Link, adm_menu.LinkText, adm_menu.Position '.
             'FROM adm_menu LEFT JOIN jobs_people_menu '.
             'ON adm_menu.ID = jobs_people_menu.MenuID '.
             'WHERE adm_menu.ModuleID = \'jobs\' '.
             'AND adm_menu.MenuName = \''.$menu_name.'\' '.
             'AND jobs_people_menu.Username = \''.$username.'\' '.
             'AND jobs_people_menu.SiteID = \''.SITE_ID.'\' '.
             'ORDER BY adm_menu.Sort';

      $query = $this->read_db->query($sql);
      $rights = $query->result_array();
      
      for ($i=0; $i<count($rights); $i++)
      {
         if ($rights[$i]['LinkText'] == $this_tab)
         {
            $rights[$i]['Selected'] = TRUE;
         }
         else
         {
            $rights[$i]['Selected'] = FALSE;
         }
         // temporarily rewrite the link. Ultimately, this should be
         // changed in the database, but I decided to do this during
         // development.
         $rights[$i]['Link'] = $this->rewrites[$rights[$i]['Link']];
      }
      return $rights;
   }

   // --------------------------------------------------------------------
   
   /**
    * Check to see if access is allowed
    *
    */
   function check($section = '')
   {
      $this->load->helper('url');

      $username = $this->session->userdata('username');
      
      // first check if user is logged in
      if ($username == '')
      {
         $this->session->set_flashdata('return_url', $this->uri->uri_string());
         redirect('admin/login');
      }
      
      // if logged in, first check if person has rights to this module
      if ($section != '')
      {
         $sql = 'SELECT jobs_people.Username '.
                'FROM jobs_people, jobs_people_module '.
                'WHERE jobs_people.Username = jobs_people_module.Username '.
                'AND jobs_people_module.ModuleID = \'jobs\' '.
                'AND jobs_people_module.SiteID = \''.SITE_ID.'\'';

         $query = $this->read_db->query($sql);
      
         if ($query->num_rows() == 0)
         {
            redirect('admin/sorry');
         }
      }

      // then check if person has rights to the requested section
      if ($section != '')
      {
         $sql = 'SELECT adm_menu.LinkText '.
                'FROM adm_menu LEFT JOIN jobs_people_menu '.
                'ON adm_menu.ID = jobs_people_menu.MenuID '.
                'WHERE adm_menu.ModuleID = \'jobs\' '.
                'AND jobs_people_menu.Username = \''.$username.'\' '.
                'AND jobs_people_menu.SiteID = \''.SITE_ID.'\' '.
                'AND adm_menu.LinkText = \''.$section.'\'';

         $query = $this->read_db->query($sql);
      
         if ($query->num_rows() == 0)
         {
            redirect('admin/sorry');
         }
      }
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Delete a user. 
    *
    * The user is actually deleted from the people_module table because 
    * we do not want to delete them from all hcgWeb Portal modules and
    * there are likely to be historical references to the user as a job 
    * posting creator or recruiter.
    *
    */
   function delete_person($username)
   {
      $sql = "DELETE FROM jobs_people_module " . 
             "WHERE Username = '".$username."' ".
             "AND ModuleID = 'jobs'";
   
      $this->db->query($sql);
   }

}

?>