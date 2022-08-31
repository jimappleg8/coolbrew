<?php

class Site_domains_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Site_domains_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
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
    * Returns primary domain data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_primary_domain($site_id)
   {
      $sql = 'SELECT * '.
             'FROM adm_site_domain '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND PrimaryDomain = 1';

      $query = $this->read_db->query($sql);
      $domain = $query->row_array();
      
      return $domain;
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
    * Returns an array of all domain records
    *
    * @access   public
    * @return   array
    */
   function get_all_domains()
   {
      $sql = 'SELECT * '.
             'FROM adm_site_domain '.
             'ORDER BY Domain';

      $query = $this->read_db->query($sql);
      $domains = $query->result_array();
      
      return $domains;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of domain records linked to a vendor.
    *
    * @access   public
    * @return   array
    */
   function get_vendor_domains($vendor_id)
   {
      $sql = 'SELECT * '.
             'FROM adm_site_domain '.
             'WHERE RegistrarVendor = '.$vendor_id.' '.
             'OR DNSVendor = '.$vendor_id.' '.
             'ORDER BY Domain';

      $query = $this->read_db->query($sql);
      $domains = $query->result_array();
      
      return $domains;
   }

   // --------------------------------------------------------------------

   /**
    * Returns counts of all registrars by vendor
    *
    * @access   public
    * @return   array
    */
   function get_registrar_vendors()
   {
      $sql = 'SELECT v.ID, v.VendorName, '.
               'COUNT(sd.RegistrarVendor) AS Domains '.
             'FROM adm_vendor AS v '.
             'LEFT JOIN adm_site_domain AS sd '.
               'ON v.ID = sd.RegistrarVendor '.
             'WHERE v.Hidden = 0 '.
             'GROUP BY v.ID, v.VendorName '.
             'ORDER BY v.ID';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
      return $vendors;
   }

   // --------------------------------------------------------------------

   /**
    * Returns counts of all DNS Providers by vendor
    *
    * @access   public
    * @return   array
    */
   function get_dns_vendors()
   {
      $sql = 'SELECT v.ID, v.VendorName, '.
               'COUNT(sd.DNSVendor) AS Domains '.
             'FROM adm_vendor AS v '.
             'LEFT JOIN adm_site_domain AS sd '.
               'ON v.ID = sd.DNSVendor '.
             'WHERE v.Hidden = 0 '.
             'GROUP BY v.ID '.
             'ORDER BY v.ID';

      $query = $this->read_db->query($sql);
      $vendors = $query->result_array();
      
      return $vendors;
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
    * Returns domains list for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_domains_list($site_id = '')
   {
      if ($site_id == '')
      {
         $sql = 'SELECT ID, Domain '.
                'FROM adm_site_domain '.
                'WHERE PrimaryDomain = 0 '.
                'ORDER BY Domain';
      }
      else
      {
         $sql = 'SELECT ID, Domain '.
                'FROM adm_site_domain '.
                'WHERE (PrimaryDomain = 0 '.
                'OR (PrimaryDomain = 1 '.
                'AND SiteID = "'.$site_id.'")) '.
                'ORDER BY Domain';      
      }

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
    * Inserts a domain record
    *
    * @access   public
    * @return   null
    */
   function insert_site_domain($values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $this->write_db->insert('adm_site_domain', $values);
      $domain_id = $this->write_db->insert_id();
      
      $this->CI->auditor->audit_insert('adm_site_domain', '', $values);

      return $domain_id;
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a domain record
    *
    * @access   public
    * @return   null
    */
   function update_site_domain($domain_id, $values, $old_values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $tmp = $this->write_db->where('ID', $domain_id);
      $this->write_db->update('adm_site_domain', $values);

      $this->CI->auditor->audit_update('adm_site_domain', $tmp->ar_where, $old_values, $values);

      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Deletes a domain record
    *
    * @access   public
    * @return   null
    */
   function delete_site_domain($domain_id, $old_values)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->library('auditor');

      $this->write_db->where('ID', $domain_id);
      $this->write_db->delete('adm_site_domain');
      
      $this->CI->auditor->audit_delete('adm_site_domain', $this->write_db->ar_where, $old_values);

   }

}

?>