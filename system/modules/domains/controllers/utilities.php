<?php

class Utilities extends Controller {

   var $columns = array(
      'DomainName',
      'Brand',
      'Extension',
      'Country',
      'RegistrationDate',
      'RegistryExpiryDate',
      'PaidUntilDate',
      'BusinessUnit',
      'Status',
      'DNSType',
      'TransferLock',
      'RegProfileName',
      'RegFirstName',
      'RegLastName',
      'RegOrganization',
      'RegAddress',
      'RegAddress2',
      'RegCity',
      'RegStateProvince',
      'RegPostalCode',
      'RegCountry',
      'RegEmail',
      'RegPhone',
      'RegFax',
      'AdminProfileName',
      'AdminFirstName',
      'AdminLastName',
      'AdminOrganization',
      'AdminAddress',
      'AdminAddress2',
      'AdminCity',
      'AdminStateProvince',
      'AdminPostalCode',
      'AdminCountry',
      'AdminEmail',
      'AdminPhone',
      'AdminFax',
      'TechProfileName',
      'TechFirstName',
      'TechLastName',
      'TechOrganization',
      'TechAddress',
      'TechAddress2',
      'TechCity',
      'TechStateProvince',
      'TechPostalCode',
      'TechCountry',
      'TechEmail',
      'TechPhone',
      'TechFax',
      'IDNTranslation',
      'LocalLanguage',
      'DNS1',
      'DNS2',
      'DNS3',
      'DNS4',
      'Field1',
      'Field2'
   );

   // --------------------------------------------------------------------

   function Utilities()
   {
      parent::Controller();	
      $this->load->library('session');

      $this->load->library('administrator', array('module_id' => 'domains'));
      $this->load->helper('url');

   }
	
   // --------------------------------------------------------------------
   
   /**
    * System to pull in a CSV file exported from NameConsole2 and 
    *  update the domains database with the information there.
    *
    */
   function import_domains()
   {
      $this->load->helper(array('text','url'));
      $this->load->model('Adm_site_domains');
      
      // you need to make sure your file is saved as UTF-8
      setlocale(LC_ALL, 'en_US.UTF-8');
      
      $file = '/Users/japplega/Desktop/export.csv';
      $columns = $this->columns;
      $csc_vendor_id = 22;
      $hcg_vendor_id = 6;
      $unknown_vendor_id = 12;
      $csc_import_date = date('Y-m-d h:i:s');
      
      $doms = $this->Adm_site_domains->get_all_domains();
      
      // create a lookup array
      $lookup = array();
      foreach ($doms AS $dom)
      {
         $lookup[$dom['Domain']] = $dom;
      }
      
//      echo '<pre>'; print_r($lookup); echo '</pre>'; exit;

      $row = 0;
      $handle = fopen($file, "r");
      
      $new_domains = array();

      while (($data = fgetcsv($handle, 2000, ",")) !== FALSE)
      {
         // skip the row with the headers
         if ($row == 0)
         {
           $row++;
           continue;
         }
         
         for ($i=0; $i<count($columns); $i++)
         {
            $domains[$row][$columns[$i]] = $data[$i];
         }
         $domains[$row]['DomainName'] = strtolower($domains[$row]['DomainName']);
         $domains[$row]['CSCImport'] = $csc_import_date;
         $domains[$row]['RegistrationDate'] = date('Y-m-d', strtotime($domains[$row]['RegistrationDate']));
         $domains[$row]['RegistryExpiryDate'] = date('Y-m-d', strtotime($domains[$row]['RegistryExpiryDate']));
         $domains[$row]['PaidUntilDate'] = date('Y-m-d', strtotime($domains[$row]['PaidUntilDate']));
         
         // -------------------------------------------
         // update records already in the database
         // -------------------------------------------
         
         if (isset($lookup[$domains[$row]['DomainName']]))
         {
            $messages = array();
            $values = array();
            
            $values = $domains[$row];
            $domain_id = $lookup[$domains[$row]['DomainName']]['ID'];
            $old_values = $lookup[$domains[$row]['DomainName']];
            
            unset($values['DomainName']);
            
            if ($lookup[$domains[$row]['DomainName']]['RegistrarVendor'] != $csc_vendor_id)
            {
               $values['RegistrarVendor'] = $csc_vendor_id;
               $messages[] = 'RegistrarVendor updated to "CSC Corporate Domains".';
            }
            $this->Adm_site_domains->update_site_domain($domain_id, $values, $old_values);
            echo 'updating '.$domains[$row]['DomainName'];
            $this->print_messages($messages);
            unset($lookup[$domains[$row]['DomainName']]);
            $row++;
         }
         else
         {
            $new_domains[] = $domains[$row];
         }
      }
      fclose($handle);
      
      echo '<br />'.($row - 1).' domains updated.<br /><br />';
      
      // -------------------------------------------
      // add new records to the database
      // -------------------------------------------
         
      foreach ($new_domains AS $new_domain)
      {
         $new_domain['Domain'] = $new_domain['DomainName'];
         unset($new_domain['DomainName']);
         $new_domain['SiteID'] = '';
         $new_domain['NotRegistered'] = 0;
         $new_domain['RegistrarVendor'] = $csc_vendor_id;
         $new_domain['DNSVendor'] = ($new_domain['DNS1'] == 'ns1.ctea.com') ? 6 : 13;
         $new_domain['PrimaryDNSIsSetUp'] = 0;
         $new_domain['RegistrarShouldBePrimary'] = 1;
         $new_domain['DNSShouldBePrimary'] = 1;
         $new_domain['Notes'] = '';

         $this->Adm_site_domains->insert_site_domain($new_domain);

         echo 'adding '.$new_domain['Domain'].'<br />';
      }
      echo '<br />'.(count($new_domains)).' domains added.<br /><br />';
      
      // -----------------------------------------------------------
      // report on any domains in the database but not the CSV file
      // -----------------------------------------------------------
         
      foreach ($lookup AS $old_domain)
      {
         $messages = array();
         if ($old_domain['RegistrarVendor'] == $csc_vendor_id)
         {
            $messages[] = 'Warning: RegistrarVendor is currently set to "CSC Corporate Domains".';
         }
         echo 'leaving '.$old_domain['Domain'];
         $this->print_messages($messages);
      }
      echo '<br />'.(count($lookup)).' non-CSC domains noted.';

      exit;
   }
   
   // --------------------------------------------------------------------
   
   function print_messages($messages)
   {
      if ( ! empty($messages))
      {
         echo '<ul style="margin-top:0; margin-bottom:0;">';
         foreach ($messages AS $message)
         {
            echo '<li><span style="color:red;">'.$message.'</span></li>';
         }
         echo '</ul>';
      }
      else
      {
         echo '<br />';
      }
   }

   // --------------------------------------------------------------------
   
   function process_named()
   {
      $this->load->library('Bind8');
      $this->load->model('Adm_site_domains');
      
      $doms = $this->Adm_site_domains->get_all_domains();
      
      // create a lookup array
      $lookup = array();
      foreach ($doms AS $dom)
      {
         $lookup[$dom['Domain']] = $dom;
      }
      
      $this->bind8->NAMEDCONF = '/Users/japplega/Desktop/named.conf';
      $this->bind8->named();
      
      $hosts = $this->bind8->HOSTS;
      
      $row = 0;
      foreach ($hosts AS $domain)
      {
         if ($domain == 'localhost')
         {
           continue;
         }
         
         // -------------------------------------------
         // update records already in the database
         // -------------------------------------------
         
         if (isset($lookup[$domain]))
         {
            $messages = array();
            $values = array();
            
            $domain_id = $lookup[$domain]['ID'];
            $old_values = $lookup[$domain];
            
            if ($lookup[$domain]['PrimaryDNSIsSetUp'] != 1)
            {
               $values['PrimaryDNSIsSetUp'] = 1;
               $messages[] = 'PrimaryDNSIsSetUp updated to "yes".';
               $this->Adm_site_domains->update_site_domain($domain_id, $values, $old_values);
            }
            echo 'updating '.$domain;
            $this->print_messages($messages);
            unset($lookup[$domain]);
            $row++;
         }
         else
         {
            $unknown_domains[] = $domain;
         }
      }

      echo '<br />'.($row - 1).' domains updated.<br /><br />';
      
       // -----------------------------------------------------------
      // list any domains marked as being managed by vendor DNS
      // -----------------------------------------------------------
      
      $row = 0;
      foreach ($lookup AS $old_values)
      {
         if ($old_values['DNSShouldBePrimary'] == 0)
         {
            echo 'DNS for '.$old_values['Domain'].' is being managed elsewhere.<br />';
            unset($lookup[$old_values['Domain']]);
            $row++;
         }
      }
      echo '<br />'.$row.' domains being managed elsewhere.<br /><br />';

      // -----------------------------------------------------------
      // make sure any domains not in named.conf are marked as such
      // and create list for adding to named.conf
      // -----------------------------------------------------------
         
      foreach ($lookup AS $old_values)
      {
         $messages = array();
         $values = array();
         
         $domain_id = $old_values['ID'];
         
         if ($old_values['PrimaryDNSIsSetUp'] == 1)
         {
            $values['PrimaryDNSIsSetUp'] = 0;
            $messages[] = 'PrimaryDNSIsSetUp updated to "no".';
            $this->Adm_site_domains->update_site_domain($domain_id, $values, $old_values);
         }
         echo 'zone "'.$old_values['Domain'].'"{ type master; file ""; };';
         $this->print_messages($messages);
      }
      echo '<br />'.(count($lookup)).' domains not found in named.conf.<br /><br />';

      // -------------------------------------------
      // list any unknown domains in named.conf
      // -------------------------------------------
         
      foreach ($unknown_domains AS $unknown)
      {
         echo 'unknown: '.$unknown.'<br />';
      }
      echo '<br />'.(count($unknown_domains)).' unknown domains in named.conf.<br /><br />';

      exit;
   }

   // --------------------------------------------------------------------
   
   function test_whois()
   {
      $this->load->library('Whois');

      $data = $this->whois->Lookup('spectrumnaturals.ca');
      
      echo '<pre>'; print_r($data); echo '</pre>';
      exit;
   }

   // --------------------------------------------------------------------
   
   /**
    * Uses saved whois data from CSC to look for errors in other
    *  domain settings. For example, if a domain is marked as having
    *  hcgWeb as the DNS vendor, but our name servers are not listed
    *  it will point that out.
    *
    */
   function detect_errors()
   {
      $this->load->helper(array('text','url'));
      $this->load->model('Adm_site_domains');
      
      $csc_vendor_id = 22;
      $hcg_vendor_id = 6;
      $unknown_vendor_id = 13;

      $doms = $this->Adm_site_domains->get_all_domains();
      
      foreach ($doms as $domain)
      {
         // check if DNS vendor is set incorrectly
         if ( ! strpos($domain['DNS1'], 'ctea.com') && $domain['DNSVendor'] == $hcg_vendor_id)
         {
            echo $domain['Domain']." -- HCG name servers not detected (DNSVendor is set to hcgWeb)<br />";
         }

         if (strpos($domain['DNS1'], 'ctea.com') && $domain['DNSVendor'] != $hcg_vendor_id)
         {
            echo $domain['Domain'].' -- HCG name servers detected (DNSVendor is NOT set to hcgWeb)<br />';
         }
      }
      echo '<br />Script complete.';
      exit;
   }


} // END Class

?>