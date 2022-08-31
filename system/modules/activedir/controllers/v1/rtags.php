<?php

class Rtags extends Controller {

   function Rtags()
   {
      parent::Controller();
      $this->load->helper(array('url', 'form', 'text', 'typography'));
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Redirect to the search function
    *
    */
   function index()
   {
      $this->load->helper('url');
      redirect('v1/rtags/search/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Searches the Active Directory for users
    *
    */
   function quickSearch()
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('Rtag');
      $this->load->library('validation');
      
      // (string) The action URL
      $action = $this->rtag->param('action');
      
      $rules['searchType'] = 'trim';
      $rules['quick'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['searchType'] = 'Search Type';
      $fields['quick'] = 'Quick Search';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      $data['action'] = $action;
      
      $this->load->vars($data);
   	
      return $this->load->view('rtags/v1/quick', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Searches the Active Directory for users
    *
    */
   function search()
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('Rtag');
      $this->load->library('validation');
      
      // (string) The action URL
      $action = $this->rtag->param('action');

      $rules['searchType'] = 'trim';
      $rules['givenName'] = 'trim';
      $rules['sn'] = 'trim';
      $rules['title'] = 'trim';
      $rules['telephonenumber'] = 'trim';
      $rules['mail'] = 'trim';
      $rules['quick'] = 'trim';
      $rules['groupname'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['searchType'] = 'Search Type';
      $fields['givenName'] = 'First Name';
      $fields['sn'] = 'Last Name';
      $fields['title'] = 'Title';
      $fields['telephonenumber'] = 'Telephone Number';
      $fields['mail'] = 'Email Address';
      $fields['quick'] = 'Quick Search';
      $fields['groupname'] = 'Group Name';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == TRUE)
      {
         if ($this->input->post('searchType') == 'g')
         {
            $search = $this->_search_groups();
         }
         else
         {
            $search = $this->_search_users();
         }
//         echo "<pre>"; print_r($search); echo "</pre>";
      }
      else
      {
         $search['result_type'] = '';
         $search['formsize'] = 'short';
      }

      $data['action'] = $action;
      $data['search'] = $search;
      
      $this->load->vars($data);
   	
      return $this->load->view('rtags/v1/search', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the search form for users
    *
    * These are stored in the Active Directory
    *
    */
   function _search_users()
   {
      $options = array(
         'domain_controllers' => array('bowdc02.hvntdom.hain-celestial.com'),
         'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
         'ad_username'        => 'CN=Data Warehouse',
         'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
         'ad_password'        => 'd8awar3z',
         );
      $this->load->library('ad_ldap', $options);
      
      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      if (! $values['givenName'] && ! $values['sn'] && ! $values['mail'] && ! $values['title'] && ! $values['telephonenumber'] && ! $values['quick'])
      {
         $search['error'] = "Error: At least one of the fields must be filled in.";
         $search['result_type'] = "error";
      }
      else
      {
         $logic = "&";
         $wildcard="*";
 
         if ($values['searchType'] == "q")  // quick search
         {
            $words = explode(" ", $values['quick']);
            $j = 0;
            for ($i=0; $i<count($words); $i++)
            {
               if ($words[$i] != "")
               {
                  $q_words[$j] = $words[$i];
                  $j++;
               }
            }
            $values['givenName'] = $q_words[0];
       
            // if there's a second word, it's assumed to be a last name, and the
            // search is set to be first AND last name. Otherwise, all fields
            // are searched with an OR logic. Possible issue: some people may
            // want to enter last name first, and that won't work.
            if (isset($q_words[1]))
            {  
               $values['sn'] = $q_words[1];
            $logic="&";
            }
            else
            {
               $values['sn'] = $q_words[0];
               $values['mail'] = $q_words[0];
               $logic="|";
            }
         }

         if ($values['searchType'] == "f")  // first letter search
         {
            $wildcard="";
         }

         // if someone enters  single letter, make the starting letter
         if ((strlen($values['givenName']) == 1) || (strlen($values['sn']) == 1))
         {
            $wildcard="";
         }
   
         // We create an associative array with the search criteria that we 
         // then use as an argument to create the search filter: 
         $searchCriteria = array(
            "givenname"       => $values['givenName'],
            "sn"              => $values['sn'],
            "description"     => $values['title'],
            "mail"            => $values['mail'],
            "telephonenumber" => $values['telephonenumber'],
         );
         
//         echo "<pre>"; print_r($searchCriteria); echo "</pre>";
         
         // create the search filter
         $noOfFieldsSet = 0;
         $searchFilterA = '(objectClass=user)(samaccounttype='. ADLDAP_NORMAL_ACCOUNT .')(objectCategory=person)';
         $searchFilterB = '';
         foreach ($searchCriteria AS $key => $value)
         {
            if ($value)
            {
               $searchFilterB .= "(".$key."=".$wildcard.$value."*)";
               ++$noOfFieldsSet;
            }
         }
         // We perform a logical AND  or OR (depending on $logic) on all
         // specified search criteria to create the final search filter: 
         if ($logic == "&")
         {
            $searchFilter = "(".$logic." ".$searchFilterA.$searchFilterB.")";
         }
         else // logic = OR
         {
            $searchFilter = "(& ".$searchFilterA."(".$logic." ".$searchFilterB."))";
         }
         
//         echo $searchFilter."<br>";
         
         // define what attributes we want to get
         $attribs = array("displayname", "samaccountname", "mail", "telephonenumber", "description", "physicaldeliveryofficename");
         $resultEntries = $this->ad_ldap->search_directory($searchFilter, $attribs);
         
         // assign results to variables for Smarty template
         if ($resultEntries['count'] > 0)
         {
            $search['result_type'] = "people";
         
            for ($i=0; $i<$resultEntries['count']; $i++)
            {
               foreach ($attribs AS $attr)
               {
                  if (isset($resultEntries[$i][$attr][0]))
                  {
                     $search['results'][$i][$attr] = $resultEntries[$i][$attr][0];
                  }
                  else
                  {
                     $search['results'][$i][$attr] = '';
                  }
               }
            }
         }
         else
         {
            $search['error'] = "No entries returned from the directory.";
            $search['result_type'] = "error";
         }
         $this->ad_ldap->close_connection();
      }

      return $search;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the search form for groups.
    *
    * These are stored in the LDAP directory
    *
    */
   function _search_groups()
   {
      $options = array(
         'domain_controllers' => array('capitals.hvntdom.hain-celestial.com'),
         'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
         'ad_username'        => 'CN=Data Warehouse',
         'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
         'ad_password'        => 'd8awar3z',
         );
      $this->load->library('ad_ldap', $options);
      
      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      if (! $values['groupname'])
      {
         $search['error'] = "Error: you must enter a group name.";
         $search['result_type'] = "error";
      }
      else
      {
         // since there's just one field, we go straight to a search filter
         $searchFilter = '(objectClass=group)(samaccounttype='. ADLDAP_DISTRIBUTION_GROUP .')(objectCategory=group)';
         if ($values['groupname'] == "%%ALL%%")
         {
            $searchFilter .= "(cn=*)";
         }
         else
         {
            $searchFilter .= "(cn=*".$values['groupname']."*)";
         }
         $searchFilter = "(& ".$searchFilter.")";

//         echo $searchFilter."<br>";
         
         // define what attributes we want to get
         $attribs = array("cn", "mail", "description");
         $resultEntries = $this->ad_ldap->search_directory($searchFilter, $attribs);
            
         // assign results to variables for Smarty template
         if ($resultEntries['count'] > 0)
         {
            $search['result_type'] = "groups";
               
            for ($i=0; $i<$resultEntries["count"]; $i++)
            {
               foreach ($attribs AS $attr)
               {
                  if (isset($resultEntries[$i][$attr][0]))
                  {
                     $search['results'][$i][$attr] = $resultEntries[$i][$attr][0];
                  }
                  else
                  {
                     $search['results'][$i][$attr] = '';
                  }
               }
               $search['results'][$i]['cn_url'] = urlencode($search['results'][$i]['cn']);
            }
         }
         else  
         {
            $search['error'] = "No entries returned from the directory.";
            $search['result_type'] = "error";
         }
         $this->ad_ldap->close_connection();
      }
      return $search;
   }

   // --------------------------------------------------------------------
   
   /**
    * Processes the search form for groups.
    *
    * These are stored in the LDAP directory
    *
    */
   function user_profile($uid)
   {
   
      $profile['uid'] = $uid;

      $options = array(
         'domain_controllers' => array('bowdc2.hvntdom.hain-celestial.com'),
         'base_dn'            => 'DC=hvntdom, DC=hain-celestial, DC=com',
         'ad_username'        => 'CN=Data Warehouse',
         'account_suffix'     => ',OU=Service Accounts, OU=Boulder-Celestial, OU=hvntdom, DC=hvntdom, DC=hain-celestial, DC=com',
         'ad_password'        => 'd8awar3z',
         );
      $this->load->library('ad_ldap', $options);

      $results = $this->ad_ldap->user_info($uid);
      
      echo "<pre>"; print_r($results); echo "</pre>";
      exit;
/*
      // We connect to the server and do an anonymous bind: 
   $linkIdentifier = connectBindServer();
   if ($linkIdentifier) {

      // get the user's profile data
      $searchFilter = "(uid=" . $uid . ")";
      $attribs = array("dn","cn","title","mail","telephonenumber","facsimiletelephonenumber","l","manager","jpegphoto");
      $resultEntries = searchDirectory($linkIdentifier, $searchFilter, $attribs);

      // get groups the user is a member of
      $groupSearchFilter = "(| (uniqueMember=*uid=" . $uid . ",*)(member=*uid=" . $uid . ",*))";
      $grpattribs = array("dn","cn");
      $groupResultEntries = searchDirectory($linkIdentifier, $groupSearchFilter, $grpattribs);

      // get Common name of Manager
      $managerSearchFilter = "(uid=" . $resultEntries[0]["manager"][0] . ")";
      $manattribs = array("cn");
      $managerResults = searchDirectory($linkIdentifier, $managerSearchFilter, $manattribs);

      // assign results to variables for Smarty template
      if ($resultEntries) {
	
         $profile['photo_path'] = getJpegphoto ($linkIdentifier, $resultEntries[0]["dn"], $uid);

         foreach ($attribs as $attrib_key) {
            $profile[$attrib_key] = $resultEntries[0][$attrib_key][0];
         }
         
         if ($groupResultEntries) {
            $noOfGroupEntries = $groupResultEntries["count"];
            for ($i = 0; $i < $noOfGroupEntries; $i++) {
               $profile['group_cn'][$i] = $groupResultEntries[$i]["cn"][0];
               $profile['group_cn_url'][$i] = urlencode($groupResultEntries[$i]["cn"][0]);
            }
         }
         
         $profile['manager_cn'] = $managerResults[0]["cn"][0];
         
      } else {
         $profile['error'] = "The requested profile was not found.";
      }
      
   } else {
      $profile['error'] = "There was an error connecting to the LDAP directory.";
   }
   
   $t = new HCG_Smarty;

   $t->assign("profile", $profile);
	
   $t->setTplPath("emploc_profile.tpl");
   echo $t->fetch("emploc_profile.tpl");
*/
   }

}
?>