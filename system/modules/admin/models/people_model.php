<?php

class People_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function People_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Check for existance of user
    *
    * @access   public
    * @return   mixed   array or FALSE
    */
   function get_user_login($username, $password)
   {
      $sql = 'SELECT * FROM adm_person '.
             'WHERE Username = \''.$username.'\' '.
             'AND Password = \''.$password.'\'';

      $query = $this->read_db->query($sql);
      
      if ($query->num_rows() > 0)
      {
         return $query->row_array();
      }
      return FALSE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of all active companies and people
    *
    * @access   public
    * @return   array
    */
   function get_users_companies()
   {
      $sql = 'SELECT ID, CompanyName, Address1, Address2, City, State, '.
             'Zip, Country, WebAddress, OfficePhone, FaxPhone '.
             'FROM adm_company '.
             'ORDER BY CompanyName';
      
      $query = $this->read_db->query($sql);
      $company_list = $query->result_array();
      
      $sql = 'SELECT p.ID AS UserID, p.Username, p.FirstName, p.LastName, p.Email, '.
             'p.CompanyID, p.Title, p.OfficePhone, p.OfficePhoneExt, '.
             'p.MobilePhone, p.FaxPhone, p.HomePhone, p.IMName, p.IMService, '.
             'p.Gender, g.Name as GroupName '.
             'FROM adm_person AS p, adm_member AS m, adm_membership AS ms, adm_group AS g '.
             'WHERE p.Status <= 1 '.
             'AND m.Name = CONCAT(p.ID,"-person") '.
             'AND m.ID = ms.MemberID '.
             'AND g.ID = ms.GroupID '.
             'ORDER BY CompanyID, FirstName';
            
      $query = $this->read_db->query($sql);
      $people_list = $query->result_array();
      
      for ($i=0; $i<count($company_list); $i++)
      {
         $results[$i] = $company_list[$i];
         $results[$i]['people'] = array();
         foreach ($people_list AS $person)
         {
            if ($person['CompanyID'] == $company_list[$i]['ID'])
            {
               $results[$i]['people'][] = $person;
            }
         }
      }

//      echo "<pre>"; print_r($results); echo "</pre>";
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of companies and people who have access to the 
    *   specified site. It checks to see if the companies and users have 
    *   access to any resource/action pair for that site.
    *
    * @access   public
    * @return   array
    */
   function get_site_users_companies($site_id)
   {
      $resource = $site_id.'-site';
      $action = 'view';
      
      $resource_id = $this->tacl->get_id('resource', $resource);
      $action_id = $this->tacl->get_action_id($action, $resource);
      
      $sql = 'SELECT c.ID, c.CompanyName, c.Address1, c.Address2, '.
               'c.City, c.State, c.Zip, c.Country, c.WebAddress, '.
               'c.OfficePhone AS CoOfficePhone, c.FaxPhone AS CoFaxPhone, '.
               'p.ID AS UserID, p.Username, p.FirstName, p.LastName, p.Email, '.
               'p.Title, p.OfficePhone, p.OfficePhoneExt, '.
               'p.MobilePhone, p.FaxPhone, p.HomePhone, p.IMName, '.
               'p.IMService, p.Gender, g.Name as GroupName '.
             'FROM adm_person AS p '.
             'LEFT JOIN adm_company AS c '.
               'ON p.CompanyID = c.ID '.
             'LEFT JOIN adm_member AS m '.
               'ON m.Name = CONCAT(p.ID,"-person") '.
             'LEFT JOIN adm_membership AS ms '.
                'ON m.ID = ms.MemberID '.
             'LEFT JOIN adm_group AS g '.
               'ON g.ID = ms.GroupID '.
             'LEFT JOIN adm_permission AS pm '.
               'ON pm.MemberID = m.ID '.
             'LEFT JOIN adm_resource AS r '.
               'ON pm.ResourceID = r.ID '.
             'LEFT JOIN adm_action AS a '.
               'ON pm.ActionID = a.ID '.
             'WHERE p.Status <= 1 '.
             'AND r.ID = '.$resource_id.' '.
             'AND a.ID = '.$action_id.' '.
             'AND a.Enabled = 1 '.
             'AND r.Enabled = 1 '.
             'AND m.Enabled = 1 '.
             'AND pm.Enabled = 1 '.
             'AND pm.Access = 1 '.
             'ORDER BY c.CompanyName, p.FirstName';
      
      $query = $this->read_db->query($sql);
      $people_list = $query->result_array();
      
      $results = array();
      $company_fields = array(
         "ID" => "ID", 
         "CompanyName" => "CompanyName",
         "Address1" => "Address1",
         "Address2" => "Address2",
         "City" => "City",
         "State" => "State",
         "Zip" => "Zip",
         "Country" => "Country",
         "WebAddress" => "WebAddress",
         "CoOfficePhone" => "OfficePhone",
         "CoFaxPhone" => "FaxPhone",
      );
      $cnt = 0;
      for ($i=0, $p=count($people_list); $i<$p; $i++)
      {
         if ( ! isset($results[$cnt]))
         {
            $results[$cnt] = array();
            $company_id = $people_list[$i]['ID'];
            foreach ($company_fields AS $key => $value)
            {
               $results[$cnt][$value] = $people_list[$i][$key];
               unset($people_list[$i][$key]);
            }
            $results[$cnt]['people'] = array();
         }
         else
         {
            foreach ($company_fields AS $key => $value)
            {
               unset($people_list[$i][$key]);
            }
         }
         $results[$cnt]['people'][] = $people_list[$i];
         if ($i + 1 < $p && $people_list[$i+1]['ID'] != $company_id)
         {
            $cnt++;
         }
      }
      
//      echo "<pre>"; print_r($results); echo "</pre>"; exit;
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of user data for the supplied username
    *
    * @access   public
    * @return   array
    */
   function get_user_data($username)
   {
      $sql = 'SELECT * FROM adm_person '.
             'WHERE Username = "'.$username.'"';

      $query = $this->read_db->query($sql);
      $user = $query->row_array();
      
      return $user;
   }

   // --------------------------------------------------------------------

   /**
    * Returns username for the supplied user ID
    *
    * @access   public
    * @return   array
    */
   function get_username($user_id)
   {
      $sql = 'SELECT Username FROM adm_person '.
             'WHERE ID = '.$user_id;

      $query = $this->read_db->query($sql);
      $user = $query->row_array();
      
      return $user['Username'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns usercode for the supplied username
    *
    * @access   public
    * @return   array
    */
   function get_usercode($username)
   {
      $sql = 'SELECT ID FROM adm_person '.
             'WHERE Username = "'.$username.'"';

      $query = $this->read_db->query($sql);
      $user = $query->row_array();
      
      return $user['ID'].'-person';
   }

   // --------------------------------------------------------------------

   /**
    * Returns the group name for this usercode. It assumes that there is
    * just one group for each user.
    *
    * @access   public
    * @return   array
    */
   function get_user_group($usercode)
   {
      $sql = 'SELECT g.Name '.
             'FROM adm_membership AS ms, adm_group AS g, adm_member AS m '.
             'WHERE ms.MemberID = m.ID '.
             'AND ms.GroupID = g.ID '.
             'AND m.Name = "'.$usercode.'"';

      $query = $this->read_db->query($sql);
      $group = $query->row_array($sql);
         
      return $group['Name'];
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of groups for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_group_list()
   {
      $sql = 'SELECT Name '.
             'FROM adm_group '.
             'WHERE Enabled = 1';

      $query = $this->read_db->query($sql);
      $groups = $query->result_array();
      
      for ($i=0; $i<count($groups); $i++)
      {
         $results[$groups[$i]['Name']] = $groups[$i]['Name'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of companies for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_company_list()
   {
      $sql = 'SELECT ID, CompanyName '.
             'FROM adm_company';

      $query = $this->read_db->query($sql);
      $companies = $query->result_array();
      
      for ($i=0; $i<count($companies); $i++)
      {
         $results[$companies[$i]['ID']] = $companies[$i]['CompanyName'];
      }
      
      return $results;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of IM Services for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_im_services_list()
   {
      $result = array('' => '',
                      'AOL'    => 'AOL',
                      'MSN'    => 'MSN',
                      'ICQ'    => 'ICQ',
                      'Yahoo'  => 'Yahoo',
                      'Jabber' => 'Jabber',
                      'Skype'  => 'Skype',
                      'Google' => 'Google',
                     );
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of genders for use in forms
    *
    * @access   public
    * @return   array
    */
   function get_gender_list()
   {
      $result = array('' => '',
                      'M'    => 'Male',
                      'F'    => 'Female',
                     );
      return $result;
   }

}

?>