<?php

class Recipe_sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Recipe_sites_model()
   {
      parent::Model();

      // this table is used only by CoolBrew, so we don't have to 
      //  mess with hcgPublic tables.
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_sites($recipe_id)
   {
      $sql = 'SELECT rs.SiteID, sd.Domain '.
             'FROM rcp_recipe_site AS rs, adm_site AS s, adm_site_domain AS sd '.
             'WHERE rs.RecipeID = '.$recipe_id.' '.
             'AND s.ID = rs.SiteID '.
             'AND s.ID = sd.SiteID '.
             'AND sd.PrimaryDomain = 1 '.
             'ORDER BY sd.Domain';
      $query = $this->db->query($sql);
      $sites = $query->result_array();
      
      return $sites;
   }

   // --------------------------------------------------------------------
   
   /**
    * Inserts a link record
    *
    * @access   public
    * @return   null
    */
   function insert_recipe_site($recipe_id, $site_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $values['RecipeID'] = $recipe_id;
      $values['SiteID'] = $site_id;

      $this->write_db->insert('rcp_recipe_site', $values);
      
      $this->CI->auditor->audit_insert('rcp_recipe_site', '', $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes a link record
    *
    */
   function delete_recipe_site($recipe_id, $site_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      // delete the link record
      $values['RecipeID'] = $recipe_id;
      $values['SiteID'] = $site_id;
      $this->write_db->delete('rcp_recipe_site', $values);
      
      $this->CI->auditor->audit_delete('rcp_recipe_site', $this->write_db->ar_where, $values);

      // delete this recipe from any of this site's categories
      $sql = 'SELECT rc.CategoryID, rc.RecipeID '.
             'FROM rcp_category AS c, rcp_recipe_category AS rc '.
             'WHERE c.ID = rc.CategoryID '.
             'AND rc.RecipeID = '.$recipe_id.' '.
             'AND c.SiteID = "'.$site_id.'"';
      $query = $this->write_db->query($sql);
      $cats = $query->result_array();
      
      foreach ($cats AS $cat)
      {
         $this->write_db->delete('rcp_recipe_category', $cat);
      
         $this->CI->auditor->audit_delete('rcp_recipe_category', $this->write_db->ar_where, $cat);
      }

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes all link records for all sites
    *
    */
   function delete_all_recipe_sites($recipe_id)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');
      
      $sites = $this->get_sites($recipe_id);
      
      foreach ($sites AS $site)
      {
         // delete the link record
         $values['RecipeID'] = $recipe_id;
         $values['SiteID'] = $site['SiteID'];
         $this->write_db->delete('rcp_recipe_site', $values);
      
         $this->CI->auditor->audit_delete('rcp_recipe_site', $this->write_db->ar_where, $values);

         // delete this recipe from any of this site's categories
         $sql = 'SELECT rc.CategoryID, rc.RecipeID '.
                'FROM rcp_category AS c, rcp_recipe_category AS rc '.
                'WHERE c.ID = rc.CategoryID '.
                'AND rc.RecipeID = '.$recipe_id.' '.
                'AND c.SiteID = "'.$site['SiteID'].'"';
         $query = $this->write_db->query($sql);
         $cats = $query->result_array();
      
         foreach ($cats AS $cat)
         {
            $this->write_db->delete('rcp_recipe_category', $cat);
      
            $this->CI->auditor->audit_delete('rcp_recipe_category', $this->write_db->ar_where, $cat);
         }
      }

      return TRUE;
   }

}

/* End of file recipe_sites_model.php */
/* Location: ./system/modules/recipes/models/recipe_sites_model.php */