<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ldap {

   var $ldap_host;
   var $ldap_port;
   var $ldap_base;

   /**
    * Class contructor
    */
   function Ldap($params = array('server' => 'default'))
   {
      $CI =& get_instance();
      $servers = $CI->config->item('ldap');
      
      $this->ldap_host = $servers[$params['server']]['host'];
      $this->ldap_port = $servers[$params['server']]['port'];
      $this->ldap_base = $servers[$params['server']]['base'];
      
   }

   // --------------------------------------------------------------------

   /**
    * Encapsulates the connection to the LDAP server and also the binding to the appropriate part of the DN tree:
    *
    * @access   private
    * @param    integer   
    * @return   void
    */
   function connect_bind_server($bindRDN = 0, $bindPassword = 0)
   {
      $linkIdentifier = ldap_connect($this->ldap_host, $this->ldap_port);

      // If no RDN and password is specified, we attempt an anonymous bind,
      // else we bind using the provided credentials: 
      if ($linkIdentifier)
      {
         if ( ! $bindRDN && ! $bindPassword)
         {
            if ( ! @ldap_bind($linkIdentifier))
            {
               return 0;
            }
         }
         else
         {
            if ( ! ldap_bind($linkIdentifier, $bindRDN, stripslashes($bindPassword)))
            {
               return 0;
            }
         }
      }
      else
      {
         return 0;
      }
      return $linkIdentifier;
   }

   // --------------------------------------------------------------------
   
   /**
    * Closes the connection to ldap
    *
    */
   function close_connection($linkIdentifier)
   {
      ldap_close($linkIdentifier);
   }

   // --------------------------------------------------------------------
   
   /**
    * This function given a link identifier obtained from the connectBindServer() function and the search filter created by createSearchFilter() performs a search on the directory: 
    *
    */
   function search_directory($linkIdentifier, $searchFilter, $attributes = array(0 => ""))
   {
      if ( ! $attributes[0])
      {
         $searchResult = ldap_search($linkIdentifier, $this->ldap_base, $searchFilter);
      }
      else
      {
         $searchResult = ldap_search($linkIdentifier, $this->ldap_base, $searchFilter, $attributes);
      }

      // We count the search results to see if we got any entries at all: 

      if (ldap_count_entries($linkIdentifier, $searchResult) <= 0)
      {
         return 0;
      }
      else
      {
         $resultEntries = ldap_get_entries($linkIdentifier, $searchResult);
         return $resultEntries;
      }
   }


}

?>