<?php

// =========================================================================
// emploc.fns.php
// written by Jim Applegate
//
// =========================================================================

require_once 'template.class.php';
require_once 'mod_emploc/emploc.inc.php';
require_once 'mod_ldapauth/ldapauth.inc.php';
require_once 'mod_core/core.inc.php';


// ------------------------------------------------------------------------
// TAG: search_empdir
//
// ------------------------------------------------------------------------
function search_empdir($access_level)
{
   global $_HCG_GLOBAL;
   
   if (!empty($_HCG_GLOBAL['passed_vars'])) {
      extract($_HCG_GLOBAL['passed_vars'], EXTR_OVERWRITE);
   }
   
   $search['access_level'] = $access_level;
   $searchFilter = ""; 

   if (isset($searchType)) {

      if (!$givenName && !$sn && !$mail && !$title && !$telephonenumber && !$quick && !$groupname) {
         $search['error'] = "Error: At least one of the fields must be filled in.";
         $search['result_type'] = "error";

// Begin Group Search -------------------------------

      } elseif ($searchType == "g") { 
		
         // since there's just one field, we go straight to a search filter
         if ($groupname == "%%ALL%%") {
            $searchFilter = "(& (cn=*)(objectClass=mailGroup))";
         } else {
            $searchFilter = "(& (cn=*" . $groupname . "*)(objectClass=mailGroup))";
         }
                
         // We connect to the server and do an anonymous bind: 
         $linkIdentifier = connectBindServer();
         if ($linkIdentifier) {

			// define what attributes we want to get
			$attribs = array("cn","mail","dn","description");
            $resultEntries = searchDirectory($linkIdentifier, $searchFilter, $attribs);
            
			// assign results to variables for Smarty template
			if ($resultEntries) {
			   $search['result_type'] = "groups";

			   $noOfEntries = $resultEntries["count"];
			   for ($i = 0; $i < $noOfEntries; $i++) {
                  $search['cn'][$i] = $resultEntries[$i]["cn"][0];
                  $search['cn_url'][$i] = urlencode($search['cn'][$i]);
                  $search['description'][$i] = $resultEntries[$i]["description"][0];
                  $search['mail'][$i] = $resultEntries[$i]["mail"][0];
			   }

			} else {
			   $search['error'] = "No entries returned from the directory.";
               $search['result_type'] = "error";
			}
			closeConnection($linkIdentifier);
         } else {
            $search['error'] = "There was an error connecting to the LDAP directory.";
            $search['result_type'] = "error";
         }
		
// Begin Employee Search ------------------------------------
	
      } else {
    
         $logic = "&";
         $wildcard="*";
    
         if ($searchType == "q") {  // quick search
            $words = explode(" ", $quick);
            $j = 0;
            for ($i=0; $i<count($words); $i++) {
               if ($words[$i]!="") {
                  $q_words[$j] = $words[$i];
                  $j = $j + 1;
               }
            }
            $givenName = $q_words[0];
    		
            // if there's a second word, it's assumed to be a last name, and the
            // search is set to be first AND last name. Otherwise, all fields
            // are searched with an OR logic. Possible issue: some people may
            // want to enter last name first, and that won't work.
            if ($q_words[1]) {  
               $sn = $q_words[1];
               $logic="&";
            } else {
               $sn = $q_words[0];
               $mail = $q_words[0];
               $logic="|";
            }
         }

         if ($searchType == "f") {  // first letter search
            $wildcard="";
         }

         // if someone enters  single letter, make the starting letter
         if ((strlen($givenName) == 1) || (strlen($sn) == 1)) {
            $wildcard="";
         }
		
         // We create an associative array with the search criteria that we 
         // then use as an argument to create the search filter: 

         $searchCriteria = array(
            "givenName"       => $givenName,
            "sn"              => $sn,
            "telephonenumber" => $telephonenumber,
            "title"           => $title,
            "mail"            => $mail,
         );
         $searchFilter = createSearchFilter($searchCriteria, $logic, $wildcard);
                
         // We connect to the server and do an anonymous bind: 
         $linkIdentifier = connectBindServer();
         if ($linkIdentifier) {

			// define what attributes we want to get
			$attribs = array("cn","mail","dn","telephonenumber","title","uid");
            $resultEntries = searchDirectory($linkIdentifier, $searchFilter, $attribs);
            
			// assign results to variables for Smarty template
			if ($resultEntries) {
			   $search['result_type'] = "people";
			   
			   $noOfEntries = $resultEntries["count"];
			   for ($i = 0; $i < $noOfEntries; $i++) {
                  $search['uid'][$i] = $resultEntries[$i]["uid"][0];
                  $search['cn'][$i] = $resultEntries[$i]["cn"][0];
                  $search['telephonenumber'][$i] = $resultEntries[$i]["telephonenumber"][0];
                  $search['mail'][$i] = $resultEntries[$i]["mail"][0];
                  $search['title'][$i] = $resultEntries[$i]["title"][0];
               }


			} else {
			   $search['error'] = "No entries returned from the directory.";
               $search['result_type'] = "error";
			}
			closeConnection($linkIdentifier);
         } else {
            $search['error'] = "There was an error connecting to the LDAP directory.";
            $search['result_type'] = "error";
         }
      }
   } 

   $t = new HCG_Smarty;

   $t->assign("search", $search);
	
   $t->setTplPath("emploc_search.tpl");
   echo $t->fetch("emploc_search.tpl");
   
}


// ------------------------------------------------------------------------
// TAG: user_profile
//
// ------------------------------------------------------------------------
function user_profile($uid, $access_level)
{

   $profile['uid'] = $uid;
   $profile['access_level'] = $access_level;

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

}

// ------------------------------------------------------------------------
// TAG: group_profile
//
// ------------------------------------------------------------------------
function group_profile($cn, $access_level)
{
   $profile['access_level'] = $access_level;

   // We connect to the server and do an anonymous bind: 
   $linkIdentifier = connectBindServer();
   if ($linkIdentifier) {

      // get group info
      $searchFilter = "(cn=" . $cn . ")";
      $attribs = array("dn","cn","description","mail","uniquemember","member");
      $resultEntries = searchDirectory($linkIdentifier, $searchFilter, $attribs);

      // assign results to variables for Smarty template
      if ($resultEntries) {
      
         $profile['dn'] = $resultEntries[0]['dn'][0];
         $profile['cn'] = $resultEntries[0]['cn'][0];
         $profile['description'] = $resultEntries[0]['description'][0];
         $profile['mail'] = $resultEntries[0]['mail'][0];
         
         $all_count = 0;
         if ($resultEntries[0]['uniquemember'][0]) {
            $noOfMemberEntries = $resultEntries[0]['uniquemember']["count"];
            for ($i = 0; $i < $noOfMemberEntries; $i++) {
               $start = "uid=";
               $end = ",";
               $profile['uniquemember'][$i] = get_string($resultEntries[0]['uniquemember'][$i], $start, $end);
               $profile['all_members'][$all_count] = $profile['uniquemember'][$i];
               $all_count = $all_count + 1;
            }
         }
         if ($resultEntries[0]['member'][0]) {
            $noOfMemberEntries = $resultEntries[0]['member']["count"];
            for ($i = 0; $i < $noOfMemberEntries; $i++) {
               $start = "uid=";
               $end = ",";
               $profile['member'][$i] = get_string($resultEntries[0]['member'][$i], $start, $end);
               $profile['all_members'][$all_count] = $profile['member'][$i];
               $all_count = $all_count + 1;
            }
         }
         
      } else {
         $profile['error'] = "The requested profile was not found.";
      }
      
      // construct search for all members' information
      $attribs = array("cn","mail","dn","telephonenumber","title","uid");
      $noOfMemberEntries = count($profile['all_members']);
      $searchFilter = "";
      for ($i = 0; $i < $noOfMemberEntries; $i++) {
         $searchFilter .= "(uid=" . $profile['all_members'][$i] . ")";
      }
      if ($noOfMemberEntries >= 2) {
         $searchFilter = "(| " . $searchFilter . ")";
      }
      $resultEntries = searchDirectory($linkIdentifier, $searchFilter, $attribs);

      // assign results to variables for Smarty template
      if ($resultEntries) {
         $noOfEntries = $resultEntries["count"];
         for ($i = 0; $i < $noOfEntries; $i++) {
            $profile['p_uid'][$i] = $resultEntries[$i]["uid"][0];
            $profile['p_cn'][$i] = $resultEntries[$i]["cn"][0];
            $profile['p_telephonenumber'][$i] = $resultEntries[$i]["telephonenumber"][0];
            $profile['p_mail'][$i] = $resultEntries[$i]["mail"][0];
            $profile['p_title'][$i] = $resultEntries[$i]["title"][0];
         }
      } else {
         $profile['error'] = "There was an error retrieving group list.";
      }
      
   } else {
      $profile['error'] = "There was an error connecting to the LDAP directory.";
   }

   $t = new HCG_Smarty;

   $t->assign("profile", $profile);
	
   $t->setTplPath("emploc_grpprofile.tpl");
   echo $t->fetch("emploc_grpprofile.tpl");

}


// ------------------------------------------------------------------------
// TAG: edit_profile
//
// ------------------------------------------------------------------------
function edit_profile($uid)
{
   global $_HCG_GLOBAL;
   global $valid_user; // session variable
   global $valid_passwd; // session variable
   
   if (!empty($_HCG_GLOBAL['passed_vars'])) {
      extract($_HCG_GLOBAL['passed_vars'], EXTR_OVERWRITE);
   }

   $profile['display_form'] = true;
   $save_changes = false;
   $profile['uid'] = $uid;
   $attribs = array("cn", "ou", "telephonenumber", "facsimiletelephonenumber", "title", "manager", "l");

   // SECTION 1: process the form

   if ($mod_action == "save") {

      $profile['display_form'] = false;
      $save_changes = true;

      // decode the transfered variables
      $transfer=stripslashes($transfer_var);
      $transfer=urldecode($transfer);
      $transfer=unserialize($transfer);
	
      // check to make sure manager's name is a valid uid
      if (!empty($manager)) {
         $linkIdentifier = connectBindServer();
         if ($linkIdentifier) {
            $searchFilter = "(|(uid=" . $manager . ")(cn=" . $manager . "))";
            $resultEntries = searchDirectory($linkIdentifier, $searchFilter, array("uid"));
            if (!$resultEntries) {
               $error['manager'] = "Manager's user id is not valid. Please try again.";
               foreach ($attribs as $attrib_key) {
                  $profile[$attrib_key] = $$attrib_key;
               }
               $profile['display_form'] = true;
               $save_changes = false;
            } else {
               $manager = $resultEntries[0]["uid"][0];
            }
            closeConnection($linkIdentifier);
         } else {
            $error['results'] = "Could not verify manager's uid. There was an error connecting to the LDAP directory";
            $profile['display_form'] = false;
            $save_changes = false;
         }
      }
   } else {
      $mod_action = "edit";
   }
   
   // SECTION 2: save the changes to the form

   if ($save_changes == true) {
	
      $ldap_dn = 'uid='.$uid.',ou=People,'.$_HCG_GLOBAL['ldap_base'];
      $admin_dn = 'uid='.$valid_user.',ou=People,'.$_HCG_GLOBAL['ldap_base'];
	
      $linkIdentifier = connectBindServer($admin_dn, $valid_passwd);
	
      if ($linkIdentifier) {
	
         $attribs_array = $attribs;
         unset ($attribs_array[0]); // this deletes the "cn" variable
         unset ($attribs_array[1]); // this deletes the "ou" variable
         unset ($attribs_array[6]); // this deletes the "l" variable
		
         // start by deleting new blank fields
         foreach ($attribs_array as $del_key) {
            if (isset ($transfer[$del_key]) && (empty($$del_key))) {
               $delEntry[$del_key] = $transfer[$del_key];
            }
         }
         if (!empty($delEntry)) {
            $del_results = ldap_mod_del($linkIdentifier, $ldap_dn, $delEntry);
         } else {
            $del_results = true;
         }
	
         // next add newly filled-in fields
         foreach ($attribs_array as $add_key) {
            if (!isset ($transfer[$add_key]) && (!empty($$add_key))) {
               $addEntry[$add_key] = $$add_key;
            }
         }
         if (!empty($addEntry)) {
            $add_results = ldap_mod_add($linkIdentifier, $ldap_dn, $addEntry);
         } else {
            $add_results = true;
         }
	
         // finally, modify existing fields
         foreach ($attribs_array as $mod_key) {
            if (isset ($transfer[$mod_key]) && (!empty($$mod_key))) {
               $modEntry[$mod_key] = $$mod_key;
            }
         }
         if (!empty($modEntry)) {
            $mod_results = ldap_modify($linkIdentifier, $ldap_dn, $modEntry);
         } else {
            $mod_results = true;
         }
			
         if ($del_results && $del_results && $del_results) {
            $error['results'] = "Changes were saved successfully.";
         } else {
            $error['results'] = "Your changes were NOT saved:<br>";
            if ($del_results == false) {
               $error['results'] .= "Unable to delete new blank attributes.<br>";
            }
            if ($add_results == false) {
               $error['results'] .= "Unable to add new attributes.<br>";
            }
            if ($mod_results == false) {
               $error['results'] .= "Unable to modify changed attributes.<br>";
            }
         }
		
         closeConnection($linkIdentifier);
		
      } else {
         $error['results'] = "Your changes were NOT saved:<br>There was an error connecting to the LDAP directory";
      }
   }

   // SECTION 3: get the variables to display in the form and send to template

   if ($mod_action == "edit") {
	
      //use the uid passed as a variable to make the search
      $searchFilter = "(uid=" . $uid . ")";
		
      $linkIdentifier = connectBindServer();
      if ($linkIdentifier) {
		
         $resultEntries = searchDirectory($linkIdentifier, $searchFilter, $attribs);
         if ($resultEntries) {

            // eliminate any empty attribute variables
            // This assumes that an empty field is the same as a
            // non-existant attribute which I don't check directly.
            foreach ($attribs as $attrib_key) {
               if (!empty($resultEntries[0][$attrib_key][0])) {
                  $transfer[$attrib_key] = $resultEntries[0][$attrib_key][0];
                  $profile[$attrib_key] = $resultEntries[0][$attrib_key][0];
               }
            }
            closeConnection($linkIdentifier);
         } else {
            $error['general'] = "The requested profile was not found.";
            $profile['display_form'] == false;
         }
      } else {
         $error['general'] = "There was an error connecting to the LDAP directory.";
         $profile['display_form'] == false;
      }
   }

   // SECTION 4: display form with variables placed inside

   //encode attribute results to pass along to save part
   $profile['transfer_var'] = serialize($transfer);
   $profile['transfer_var'] = urlencode($profile['transfer_var']);

   // set up office pull-down menu 
   $profile['office_array'] = array("", "Melville", "Boulder", "Irwindale", "Moonachie", "Herford", "Shreveport", "Vancouver", "Modesto", "Rogers", "Remote User");

   // if there's an existing entry that doesn't match this list, 
   // add it to the list and set an error message:
   if (!in_array($transfer["l"], $profile['office_array'])) {
      $profile['office_array'][count($profile['office_array'])] = $transfer["l"];
      $error['location'] = "This location value is not valid. Please change.";
   }
   $profile['office_selected'] = $transfer["l"];


   $t = new HCG_Smarty;

   $t->assign("profile", $profile);
   $t->assign("error", $error);
	
   $t->setTplPath("emploc_editprofile.tpl");
   echo $t->fetch("emploc_editprofile.tpl");

}


?>