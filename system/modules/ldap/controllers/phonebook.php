<?php

class Phonebook extends Controller {

   function Phonebook()
   {
      parent::Controller();   
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Redirect to the search function
    *
    */
   function index()
   {
      $this->load->helper('url');
      redirect('phonebook/search/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Searches the Active Directory
    *
    */
   function search()
   {
      $this->load->helper(array('form', 'text'));
      
      $this->load->library('validation');
      
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
         $search = $this->_search();
         $search['formsize'] = 'long';
      }
      else
      {
         $search['result_type'] = '';
         $search['formsize'] = 'short';
      }

      $data['search'] = $search;
      
      $this->load->vars($data);
   	
      return $this->load->view('search', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the search form
    *
    */
   function _search()
   {
      $this->load->library('ldap');
      
      $fields = $this->validation->_fields;

      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $searchFilter = "";

      if (! $values['givenName'] && ! $values['sn'] && ! $values['mail'] && ! $values['title'] && ! $values['telephonenumber'] && ! $values['quick'] && ! $values['groupname'])
      {
         $search['error'] = "Error: At least one of the fields must be filled in.";
         $search['result_type'] = "error";
      }
      elseif ($values['searchType'] == "g")  // Begin Group Search --------
      {
         // since there's just one field, we go straight to a search filter
         if ($values['groupname'] == "%%ALL%%")
         {
            $searchFilter = "(& (cn=*)(objectClass=mailGroup))";
         }
         else
         {
            $searchFilter = "(& (cn=*" . $values['groupname'] . "*)(objectClass=mailGroup))";
         }
             
         // We connect to the server and do an anonymous bind: 
         $linkIdentifier = $this->ldap->connect_bind_server();
         if ($linkIdentifier)
         {
            // define what attributes we want to get
            $attribs = array("cn", "mail", "dn", "description");
            $resultEntries = $this->ldap->search_directory($linkIdentifier, $searchFilter, $attribs);
         
            // assign results to variables for Smarty template
            if ($resultEntries)
            {
               $search['result_type'] = "groups";

               $noOfEntries = $resultEntries["count"];
               for ($i = 0; $i < $noOfEntries; $i++)
               {
                  $search['cn'][$i] = $resultEntries[$i]["cn"][0];
                  $search['cn_url'][$i] = urlencode($search['cn'][$i]);
                  $search['description'][$i] = $resultEntries[$i]["description"][0];
                  $search['mail'][$i] = $resultEntries[$i]["mail"][0];
               }
            }
            else  
            {
               $search['error'] = "No entries returned from the directory.";
               $search['result_type'] = "error";
            }
            $this->ldap->close_connection($linkIdentifier);
         }
         else
         {
            $search['error'] = "There was an error connecting to the LDAP directory.";
            $search['result_type'] = "error";
         }
      }
      else  // Begin Employee Search ------------------------------------
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
                  $j = $j + 1;
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
            "givenName"       => $values['givenName'],
            "cn"              => '',
            "sn"              => $values['sn'],
            "title"           => $values['title'],
            "mail"            => $values['mail'],
            "employeenumber"  => '',
            "ou"              => '',
            "telephonenumber" => $values['telephonenumber'],
         );
         $searchFilter = $this->_createSearchFilter($searchCriteria, $logic, $wildcard);
             
         // We connect to the server and do an anonymous bind: 
         $linkIdentifier = $this->ldap->connect_bind_server();
         if ($linkIdentifier)
         {
            // define what attributes we want to get
            $attribs = array("cn","mail","dn","telephonenumber","title","uid");
            $resultEntries = $this->ldap->search_directory($linkIdentifier, $searchFilter, $attribs);
         
            // assign results to variables for Smarty template
            if ($resultEntries)
            {
               $search['result_type'] = "people";
         
               $noOfEntries = $resultEntries["count"];
               for ($i = 0; $i < $noOfEntries; $i++)
               {
                  foreach ($attribs AS $attr)
                  {
                     if (isset($resultEntries[$i][$attr][0]))
                     {
                        $search[$attr][$i] = $resultEntries[$i][$attr][0];
                     }
                     else
                     {
                        $search[$attr][$i] = '';
                     }
                  }
               }
            }
            else
            {
               $search['error'] = "No entries returned from the directory.";
               $search['result_type'] = "error";
            }
            $this->ldap->close_connection($linkIdentifier);
         }
         else
         {
            $search['error'] = "There was an error connecting to the LDAP directory.";
            $search['result_type'] = "error";
         }
      }

      return $search;
   }

   // --------------------------------------------------------------------
   
   /**
    * Given a search criteria string, this function creates a search
    * filter expression: 
    *
    */
   function _createSearchFilter($searchCriteria, $logic, $wildcard)
   {
       $noOfFieldsSet = 0;
       $searchFilter = '';
       if ($searchCriteria["givenName"])
       {
           $searchFilter .= "(givenName=" . $wildcard . 
                            $searchCriteria["givenName"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["cn"])
       {
           $searchFilter .= "(cn=" . $wildcard . $searchCriteria["cn"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["sn"])
       {
           $searchFilter .= "(sn=" . $wildcard . $searchCriteria["sn"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["title"])
       {
           $searchFilter .= "(title=" . $wildcard . $searchCriteria["title"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["mail"])
       {
           $searchFilter .= "(mail=" . $wildcard . $searchCriteria["mail"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["employeenumber"])
       {
           $searchFilter .= "(employeenumber=" . $wildcard .
                             $searchCriteria["employeenumber"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["ou"])
       {
           $searchFilter .= "(ou=" . $wildcard . $searchCriteria["ou"] . "*)";
           ++$noOfFieldsSet;
       }
   
       if ($searchCriteria["telephonenumber"])
       {
           $searchFilter .= "(telephonenumber=" . $wildcard .
                            $searchCriteria["telephonenumber"] . "*)";
           ++$noOfFieldsSet;
       }
   
       // We perform a logical AND  or OR (depending on $logic) on all
       // specified search criteria to create the final search filter: 
   
       if ($noOfFieldsSet >= 2)
       {
           $searchFilter = "(" . $logic . " " . $searchFilter . ")";
       }
       return $searchFilter;
   }
   
}
?>