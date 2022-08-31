<?php

class Sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Sites_model()
   {
      parent::Model();
      $this->load->library('session');

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of sites for the current user. If the user is an admin
    *   the full list is returned.
    *
    * @access   public
    * @param    string    group the user is a member of.
    * @return   array
    */
   function get_sites()
   {
      $group = $this->session->userdata('group');
      $usercode = $this->session->userdata('usercode');
      
      $sql = 'SELECT s.ID AS SiteID, s.DevVendorURL, s.DevVendorName, '.
               's.DevURL, s.StageURL, s.LiveURL, sd.Domain, s.Status, '. 
               's.Description, v.VendorName AS HostingVendor '.
             'FROM adm_site AS s '.
             'LEFT JOIN adm_site_domain AS sd '.
               'ON s.ID = sd.SiteID '.
             'LEFT JOIN adm_site_vendor AS sv '.
               'ON s.ID = sv.SiteID '.
             'LEFT JOIN adm_vendor AS v '.
               'ON sv.VendorID = v.ID '.
             'WHERE sd.PrimaryDomain = 1 '.
             'AND s.Status = "active" '.
             'AND sv.ServiceID = 4 '.  // Hosting
             'AND sv.Status = "current" '.
             'ORDER BY sd.Domain';
      $query = $this->read_db->query($sql);
      $all_sites = $query->result_array();

      $sql = 'SELECT s.ID AS SiteID, sd.Domain, s.Status, '.
               's.Description, v.VendorName AS HostingVendor '.
             'FROM adm_site AS s '.
             'LEFT JOIN adm_site_domain AS sd '.
               'ON s.ID = sd.SiteID '.
             'LEFT JOIN adm_site_vendor AS sv '.
               'ON s.ID = sv.SiteID '.
             'LEFT JOIN adm_vendor AS v '.
               'ON sv.VendorID = v.ID '.
             'LEFT JOIN adm_resource AS r '.
               'ON r.Name = CONCAT(s.ID,"-site") '.
             'LEFT JOIN adm_permission AS pm '.
               'ON r.ID = pm.ResourceID '.
             'LEFT JOIN adm_member AS m '.
               'ON pm.MemberID = m.ID '.
             'LEFT JOIN adm_action AS a '.
               'ON pm.ActionID = a.ID '.
             'WHERE sd.PrimaryDomain = 1 '.
             'AND s.Status = "active" '.
             'AND sv.ServiceID = 4 '.  // Hosting
             'AND sv.Status = "current" '.
             'AND m.name = "'.$usercode.'" '.
             'AND a.Name = "view" '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND m.Enabled = 1 '.
             'AND pm.Enabled = 1 '.
             'AND pm.Access = 1 '.
             'ORDER BY sd.Domain';
      $query = $this->read_db->query($sql);
      $my_sites = $query->result_array();
      
      // create lookup array
      foreach($my_sites AS $my_site)
      {
         $my_site_lookup[$my_site['SiteID']] = '';
      }

      $sites = array();
      if ($group == 'admin')
      {
         foreach($all_sites AS $all_site)
         {
            $all_site['FullAccess'] = TRUE;
            $sites[] = $all_site;
         }
      }
      else
      {
         foreach($all_sites AS $all_site)
         {
            if (isset($my_site_lookup[$all_site['SiteID']]))
            {
               $all_site['FullAccess'] = TRUE;
            }
            else
            {
               $all_site['FullAccess'] = FALSE;
            }
            $sites[] = $all_site;
         }
      }

      return $sites;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of inactive sites.
    *
    * @access   public
    * @param    string    group the user is a member of.
    * @return   array
    */
   function get_inactive_sites()
   {
      $group = $this->session->userdata('group');
      $usercode = $this->session->userdata('usercode');
      
      $sql = 'SELECT s.ID AS SiteID, sd.Domain, s.Status, '.
               's.Description, v.VendorName AS HostingVendor '.
             'FROM adm_site AS s, adm_site_domain AS sd, '.
               'adm_site_vendor AS sv, adm_vendor AS v '.
             'WHERE s.ID = sd.SiteID '.
             'AND sd.PrimaryDomain = 1 '.
             'AND s.Status = "inactive" '.
             'AND sv.SiteID = s.ID '.
             'AND sv.VendorID = v.ID '.
             'AND sv.ServiceID = 4 '.  // Hosting
             'ORDER BY sd.Domain';
      $query = $this->read_db->query($sql);
      $all_sites = $query->result_array();

      $sql = 'SELECT s.ID AS SiteID, sd.Domain, s.Status, '.
               's.Description, v.VendorName AS HostingVendor '.
             'FROM adm_site AS s, adm_site_domain AS sd, '.
               'adm_site_vendor AS sv, adm_vendor AS v, adm_member AS m, '.
               'adm_permission AS pm, adm_resource AS r, adm_action AS a '.
             'WHERE s.ID = sd.SiteID '.
             'AND sd.PrimaryDomain = 1 '.
             'AND s.Status = "inactive" '.
             'AND sv.SiteID = s.ID '.
             'AND sv.VendorID = v.ID '.
             'AND sv.ServiceID = 4 '.  // Hosting
             'AND m.name = "'.$usercode.'" '.
             'AND m.ID = pm.MemberID '.
             'AND r.Name = CONCAT(s.ID,"-site") '.
             'AND r.ID = pm.ResourceID '.
             'AND a.Name = "view" '.
             'AND a.ID = pm.ActionID '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND m.Enabled = 1 '.
             'AND pm.Enabled = 1 '.
             'AND pm.Access = 1 '.
             'ORDER BY sd.Domain';
      $query = $this->read_db->query($sql);
      $my_sites = $query->result_array();
      
      // create lookup array
      foreach($my_sites AS $my_site)
      {
         $my_site_lookup[$my_site['SiteID']] = '';
      }

      $sites = array();
      if ($group == 'admin')
      {
         foreach($all_sites AS $all_site)
         {
            $all_site['FullAccess'] = TRUE;
            $sites[] = $all_site;
         }
      }
      else
      {
         foreach($all_sites AS $all_site)
         {
            if (isset($my_site_lookup[$all_site['SiteID']]))
            {
               $all_site['FullAccess'] = TRUE;
            }
            else
            {
               $all_site['FullAccess'] = FALSE;
            }
            $sites[] = $all_site;
         }
      }

      return $sites;
   }

   // --------------------------------------------------------------------

   /**
    * Returns multi-dimensional array of sites ordered by the brand for 
    *   the current user. If the user is an admin the full list is returned.
    *
    * @access   public
    * @return   array
    */
   function get_sites_by_brand()
   {
      $group = $this->session->userdata('group');
      $usercode = $this->session->userdata('usercode');

      $sql = 'SELECT s.ID AS SiteID, s.DevURL, s.StageURL, s.LiveURL, '.
                'sd.Domain, b.Name, b.ID AS BrandID '.
             'FROM adm_site AS s, adm_brand AS b, adm_site_domain AS sd, '.
               'adm_site_brand AS sb '.
             'WHERE s.ID = sb.SiteID '.
             'AND s.Status = "active" '.
             'AND s.ID = sd.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1 '.
             'ORDER BY b.Name, sd.Domain';
 
 /*
      // this restricts the list according to access rights
      
      $sql = 'SELECT s.ID AS SiteID, sd.Domain, b.Name, b.ID AS BrandID '.
             'FROM adm_site AS s, adm_brand AS b, adm_site_domain AS sd, '.
               'adm_site_brand AS sb, adm_member AS m, adm_action AS a, '.
               'adm_permission AS pm, adm_resource AS r '.
             'WHERE s.ID = sb.SiteID '.
             'AND s.ID = sd.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1 '.
             'AND m.Name = "'.$usercode.'" '.
             'AND m.ID = pm.MemberID '.
             'AND r.Name = CONCAT(s.ID,"-site") '.
             'AND r.ID = pm.ResourceID '.
             'AND a.Name = "view" '.
             'AND a.ID = pm.ActionID '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND m.Enabled = 1 '.
             'AND pm.Enabled = 1 '.
             'AND pm.Access = 1 '.
             'ORDER BY b.Name, sd.Domain';
*/

      $query = $this->read_db->query($sql);
      $brands = $query->result_array();
       
//      echo "$sql<pre>"; print_r($brands); echo "</pre>";
//      exit;

      return $brands;
   }

   // --------------------------------------------------------------------

   /**
    * Returns multi-dimensional array of sites in the same brand as Site ID
    *
    * @access   public
    * @return   array
    */
   function get_sites_in_same_brand($site_id)
   {
      $brand_id = $this->get_brand_id($site_id);

      $sql = 'SELECT adm_site.ID AS SiteID, adm_site_domain.Domain, '.
               'adm_brand.Name, adm_brand.ID AS BrandID '.
             'FROM adm_site, adm_brand, adm_site_domain, adm_site_brand '.
             'WHERE adm_brand.ID = "'.$brand_id.'" '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID '.
             'AND adm_site_domain.PrimaryDomain = 1 '.
             'ORDER BY adm_brand.Name, adm_site_domain.Domain';
      
      $query = $this->read_db->query($sql);
      $brands = $query->result_array();
       
//      echo "$sql<pre>"; print_r($brands); echo "</pre>";
//      exit;

      return $brands;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_site_data($site_id)
   {
      $sql = 'SELECT s.ID AS SiteID, s.Description, s.Region, s.Type, s.Status, '.
               's.RedirectSiteID, s.LaunchDate, s.DiscontinuedDate, '.
               's.DevVendorURL, s.DevVendorName, s.DevURL, s.StageURL, s.LiveURL, '.
               's.ProductLink, s.RecipeLink, s.RepositoryURL, s.AboutThisSite, '.
               'sd.Domain, sd.ID AS DomainID, b.Name AS BrandName, b.ID AS BrandID '.
             'FROM adm_site AS s, adm_site_domain AS sd, '.
               'adm_site_brand AS sb, adm_brand AS b ' .
             'WHERE s.ID = \''.$site_id.'\' '.
             'AND s.ID = sd.SiteID '.
             'AND s.ID = sb.SiteID '.
             'AND b.ID = sb.BrandID '.
             'AND sd.PrimaryDomain = 1';
      
      $query = $this->read_db->query($sql);
      $site = $query->row_array();

      return $site;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand name for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_brand_name($site_id)
   {
      $site = $this->get_site_data($site_id);
      return $site['BrandName'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns the brand ID for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_brand_id($site_id)
   {
      $site = $this->get_site_data($site_id);
      return $site['BrandID'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified domain ID
    *
    * @access   public
    * @return   array
    */
   function get_domain_data($domain_id)
   {
      $sql = 'SELECT * FROM adm_site_domain '.
             'WHERE ID = '.$domain_id;

      $query = $this->read_db->query($sql);
      $domain = $query->row_array();
      
      return $domain;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of modules for the specified Site ID
    *
    * @access   public
    * @return   array
    */
   function get_site_modules($site_id)
   {
      $sql = 'SELECT adm_module.ID, adm_module.Name, adm_site_module.SiteID '.
             'FROM adm_module LEFT JOIN adm_site_module '.
             'ON adm_site_module.ModuleID = adm_module.ID '.
             'AND adm_site_module.SiteID = "'.$site_id.'" '.
             'WHERE adm_module.Extends = "sites" '.
             'ORDER BY adm_module.Name';

      $query = $this->read_db->query($sql);
      $modules = $query->result_array();
      
      return $modules;
   }

   // --------------------------------------------------------------------

   /**
    * Returns multi-dimensional array of domains ordered by the
    * primary domain of each site.
    *
    * @access   public
    * @return   array
    */
   function get_domains_by_primary($site_id = '')
   {
      if ($site_id == '')
      {
         $sql = 'SELECT adm_site.ID, adm_site_domain.Domain, adm_brand.Name '.
                'FROM adm_site, adm_site_domain, adm_brand, adm_site_brand '.
                'WHERE adm_site.ID = adm_site_domain.SiteID '.
                'AND adm_site_brand.SiteID = adm_site_domain.SiteID '.
                'AND adm_site_brand.BrandID = adm_brand.ID '.
                'AND adm_site_domain.PrimaryDomain = 1 '.
                'ORDER BY adm_site_domain.Domain';
      }
      else
      {
         $sql = 'SELECT adm_site.ID, adm_site_domain.Domain, adm_brand.Name '.
                'FROM adm_site, adm_site_domain, adm_brand, adm_site_brand '.
                'WHERE adm_site.ID = "'.$site_id.'" '.
                'AND adm_site.ID = adm_site_domain.SiteID '.
                'AND adm_site_brand.SiteID = adm_site_domain.SiteID '.
                'AND adm_site_brand.BrandID = adm_brand.ID '.
                'AND adm_site_domain.PrimaryDomain = 1 '.
                'ORDER BY adm_site_domain.Domain';
      }
      
      $query = $this->read_db->query($sql);
      $p_domains = $query->result_array();
      
      for ($i=0; $i<count($p_domains); $i++)
      {
         $results[$p_domains[$i]['ID']] = array(
            'primary' => $p_domains[$i]['Domain'], 
            'brand' => $p_domains[$i]['Name']
         );
      }
      
      if ($site_id == '')
      {
         $sql = 'SELECT adm_site_domain.*, v1.VendorName AS RegistrarName, '.
                  'v2.VendorName AS DNSName '.
                'FROM adm_site_domain, adm_vendor AS v1, adm_vendor AS v2 '.
                'WHERE adm_site_domain.RegistrarVendor = v1.ID '.
                'AND adm_site_domain.DNSVendor = v2.ID '.
                'ORDER BY Domain';
      }
      else
      {
         $sql = 'SELECT adm_site_domain.*, v1.VendorName AS RegistrarName, '.
                  'v2.VendorName AS DNSName '.
                'FROM adm_site_domain, adm_vendor AS v1, adm_vendor AS v2 '.
                'WHERE SiteID = "'.$site_id.'" '.
                'AND adm_site_domain.RegistrarVendor = v1.ID '.
                'AND adm_site_domain.DNSVendor = v2.ID '.
                'ORDER BY Domain';
      }

      $query = $this->read_db->query($sql);
      $domains = $query->result_array();
         
      for ($i=0; $i<count($domains); $i++)
      {
         $results[$domains[$i]['SiteID']]['domains'][] = $domains[$i];
      }
 
//      echo "$sql<pre>"; print_r($results); echo "</pre>";
//      exit;

      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns multi-dimensional array of domains ordered by the
    * brand of each site.
    *
    * @access   public
    * @return   array
    */
   function get_domains_by_brand()
   {
      $sql = 'SELECT adm_site.ID, adm_site_domain.Domain, adm_brand.Name '.
             'FROM adm_site, adm_site_domain, adm_brand, adm_site_brand '.
             'WHERE adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site_brand.SiteID = adm_site_domain.SiteID '.
             'AND adm_site_brand.BrandID = adm_brand.ID '.
             'AND adm_site_domain.PrimaryDomain = 1 '.
             'ORDER BY adm_brand.Name, adm_site_domain.Domain';
      
      $query = $this->read_db->query($sql);
      $p_domains = $query->result_array();
      
      for ($i=0; $i<count($p_domains); $i++)
      {
         $results[$p_domains[$i]['ID']] = array(
            'primary' => $p_domains[$i]['Domain'], 
            'brand' => $p_domains[$i]['Name']
         );
      }
      
      $sql = 'SELECT adm_site_domain.*, v1.VendorName AS RegistrarName, '.
               'v2.VendorName AS DNSName '.
             'FROM adm_site_domain, adm_vendor AS v1, adm_vendor AS v2 '.
             'WHERE adm_site_domain.RegistrarVendor = v1.ID '.
             'AND adm_site_domain.DNSVendor = v2.ID '.
             'ORDER BY Domain';

      $query = $this->read_db->query($sql);
      $domains = $query->result_array();
         
      for ($i=0; $i<count($domains); $i++)
      {
         $results[$domains[$i]['SiteID']]['domains'][] = $domains[$i];
      }
 
//      echo "$sql<pre>"; print_r($results); echo "</pre>";
//      exit;

      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the total number of domains, by site if specified
    *
    * @access   public
    * @return   array
    */
   function get_domain_count($site_id = '')
   {
      if ($site_id == '')
      {
         $sql = 'SELECT * FROM adm_site_domain '.
                'ORDER BY Domain';
      }
      else
      {
         $sql = 'SELECT * FROM adm_site_domain '.
                'WHERE SiteID = "'.$site_id.'" '.
                'ORDER BY Domain';
      }

      $query = $this->read_db->query($sql);

      return $query->num_rows();
   }

   // --------------------------------------------------------------------

   /**
    * Returns brands list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_brands_list()
   {
      $sql = 'SELECT ID, Name '.
             'FROM adm_brand '.
             'ORDER BY Name';

      $query = $this->read_db->query($sql);
      $brands = $query->result_array();
         
      $results = array(''=>'');
      for ($i=0; $i<count($brands); $i++)
      {
         $results[$brands[$i]['ID']] = $brands[$i]['Name'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns domains list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_domains_list()
   {
      $sql = 'SELECT ID, Domain '.
             'FROM adm_site_domain '.
             'WHERE PrimaryDomain = 0 '.
             'ORDER BY Domain';

      $query = $this->read_db->query($sql);
      $domains = $query->result_array();
         
      $results = array(''=>'');
      for ($i=0; $i<count($domains); $i++)
      {
         $results[$domains[$i]['ID']] = $domains[$i]['Domain'];
      }
      
      return $results;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns sites list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_sites_list($include_multi = FALSE)
   {
      $sql = 'SELECT adm_site.ID, adm_site_domain.Domain '.
             'FROM adm_site, adm_site_domain '.
             'WHERE adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site_domain.PrimaryDomain = 1 '.
             'ORDER BY adm_site_domain.Domain';

      $query = $this->read_db->query($sql);
      $sites = $query->result_array();

      $results = array(''=>'-- choose a site --');
      if ($include_multi == TRUE)
      {
         $results['multi'] = 'Multiple Websites';
         $results['all'] = 'All Websites';
      }
      for ($i=0; $i<count($sites); $i++)
      {
         $results[$sites[$i]['ID']] = $sites[$i]['Domain'].' ('.$sites[$i]['ID'].')';
      }
      
      return $results;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns modules list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_modules_list()
   {
      $sql = 'SELECT ID, Name FROM adm_module '.
             'ORDER BY ID';

      $query = $this->read_db->query($sql);
      $modules = $query->result_array();

      $results = array('none'=>'none');
      for ($i=0; $i<count($modules); $i++)
      {
         $results[$modules[$i]['ID']] = $modules[$i]['ID'].' ('.$modules[$i]['Name'].')';
      }
      
      return $results;
   }
}

?>