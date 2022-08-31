<?php

class Utilities extends Controller {

   var $aco = array();

   function Utilities()
   {
      parent::Controller();
      $this->load->library('session');

      $options = array('db' => 'read', 'prefix' => 'adm');
      $this->load->library('tacl', $options);

      $this->load->library('administrator', array('module_id' => 'admin'));
      $this->load->helper('url');
      
      include APPPATH().'/config/acl_admin.php';
      $this->aco['adm'] = $acl_admin;

      include APPPATH().'/config/acl_sites.php';
      $this->aco['sites'] = $acl_sites;
   }
   
   // --------------------------------------------------------------------
   
   /**
    * This is for placing TACL commands and running once
    *
    */
   function tacl()
   {
      $this->load->model('People');
      
      // codes for the initial launch of the website
      $user_array = array(
         'acotty'   => array('eg','hv','lm','rp'),
         'alofland' => array('if'),
         'apappas'  => array('td'),
         'asturm'   => array('eb'),
         'azimmer'  => array('hv'),
         'bhajart'  => array('jn','qh','zn'),
         'cavis'    => array('jn','qh','zn'),
         'cbyrne'   => array('ck','ec','hk','sc'),
         'csidman'  => array('gfch'),
         'dalperov' => array('td'),
         'dbittman' => array('cb','gfch','hv','if','ns'),
         'dfiore'   => array('tc'),
         'diannucc' => array('eg','if','lm','rp'),
         'dleroi'   => array('if','msc'),
         'dsirovat' => array('am','gfch','mn','nsf','oc','ss'),
         'dwoolwor' => array('eb','ge','tc'),
         'dyoung'   => array('if','hv'),
         'egiordan' => array('eb','eg','gfc','rp','td','ws'),
         'epoon'    => array('aotc','bo','ge','tc','ha','hf','lb'),
         'hkallawa' => array('ab','anp','ao','as','av','up'),
         'jbrown'   => array('db','ms'),
         'jcornell' => array('aotc','bo','ge','tc','ha','hf','lb'),
         'jdepippo' => array('fb','pf'),
         'jgentle'  => array('dbr','lmuk','hu'),
         'jharada'  => array('ck','ec','hk','ic','sc','dc','yc'),
         'jhudson'  => array('cs','csk'),
         'jjohnson' => array('hv'),
         'jscirica' => array('td'),
         'jstolte'  => array('bc','cc','cj','cs','fl','he','st'),
         'jtruong'  => array('eb','ge','tc'),
         'jwilbeck' => array('ge','hf','td','tc','ws'),
         'kbremer'  => array('eb','ebd','mn','mnd','nsf','oc','ss','tcd','tcw','ts','tw'),
         'kgallego' => array('bc','cc','cs','he','st'),
         'khaas'    => array('gfc'),
         'kharwood' => array('bc','cc','cj','cs','fl','he','st'),
         'kmcgown'  => array('eg','gfc','lm','rp'),
         'kquinn'   => array('ws'),
         'kvalenti' => array('mn','nsf','oc','ss'),
         'lgillies' => array('hk','ic'),
         'llopatin' => array('hv'),
         'lnolan'   => array('eb'),
         'lyang'    => array('td','ws'),
         'manthes'  => array('hc','hk','hu','hceu'),
         'mdesimon' => array('hv'),
         'mgoose'   => array('tt','ws','yv'),
         'mkreince' => array('am','db','eb','ebd','if','ms','mn','mnd','nsf','oc','ss','tahb','tcd','tcw','ts','tw','wa'),
         'mmiranda' => array('jn','qh','zn'),
         'mputman'  => array('am','aotc','cb','db','eb','ebd','ge','gfch','hf','hw','if','mn','mnd','ms','nsf','oc','qh','ss','tc','tcd','tcw','td','ts','tt','tw','wa','ws','yv'),
         'mvasu'    => array('ck','ec','hk','sc','yc'),
         'nessevel' => array('cs','csk'),
         'nventimi' => array('tt','ws','yv'),
         'nyin'     => array('jn','qh'),
         'rhaney'   => array('ab','anp','ao','as','av','up'),
         'rrein'    => array('am','db','hf','hw','wa'),
         'sblack'   => array('hf','so'),
         'sdavis'   => array('bc','cc','cj','cs','fl','he','st'),
         'sgalusha' => array('hn','jn','qh','zn','zg'),
         'skestenb' => array('eb','ebd','mn','mnd','nsf','oc','ss','tcd','tcw','ts','tw'),
         'tsalvado' => array('am','aotc','cb','db','eb','ebd','ge','gfch','hf','hw','if','mn','mnd','ms','nsf','oc','qh','ss','tc','tcd','tcw','td','ts','tt','tw','wa','ws','yv'),
         'tkenny'   => array('cs','csk'),
         'tstump'   => array('yc'),
         'vclark'   => array('cs','csk'),
      );
  
      foreach ($user_array AS $username => $site_array)
      {
         $usercode = $this->People->get_usercode($username);

         // get a list of sites the user currently has access to
         $now_auths = $this->tacl->authorizations('member', $usercode);
         
         $now_lookup = array();
         foreach ($now_auths AS $auth)
         {
            $now_lookup[$auth['ResourceName'].'|'.$auth['ActionName']] = $auth['Access'];
         }
         
         // add the user to a site if needed
         foreach ($site_array AS $site_id)
         {
            $resource = $site_id.'-site';
            $action = 'view';
            if ( ! $this->tacl->authorized_member($usercode, $action, $resource))
            {
               $this->tacl->add_permission('member', $usercode, $resource, $action);
               $did = '<span style="color:green;">ADDED';
            }
            else
            {
               $did = '<span style="color:gold;">STET';
            }

            $result = ($this->administrator->check_acl($resource, $action, $usercode)) ? 'ALLOWED' : 'DENIED';      
            $result2 = ($this->tacl->authorized_member($usercode, $action, $resource) == 'ALLOW') ? 'ALLOWED' : 'DENIED';

            echo $did.": Admin Check... ".$resource." | ".$action." &mdash; Access for ".$usercode.' ('.$username.') is '.$result."</span><br>";
            echo $did.": TACL Check... ".$resource." | ".$action." &mdash; Access for ".$usercode.' ('.$username.') is '.$result2."</span><br>";
      
            // add the user's company to a site if necessary
            $user = $this->People->get_user_data($username);
            $compcode = $user['CompanyID'].'-company';
            if ( ! $this->tacl->authorized_member($compcode, $action, $resource))
            {
               $this->tacl->add_permission('member', $compcode, $resource, $action);
            }
            
            // remove this site from the lookup array
            unset($now_lookup[$resource.'|'.$action]);
         }
         
         // remove the user from a site if needed
         foreach ($now_lookup AS $key => $access)
         {
            list($resource, $action) = explode('|', $key);
            list($site_id, $site) = explode('-', $resource);
            if ($site == 'site' && $action == 'view')
            {
               $this->tacl->remove_permission('member', $usercode, $resource, $action);

               $result = ($this->administrator->check_acl($resource, $action, $usercode)) ? 'ALLOWED' : 'DENIED';      
               $result2 = ($this->tacl->authorized_member($usercode, $action, $resource) == 'ALLOW') ? 'ALLOWED' : 'DENIED';

               echo '<span style="color:red;">REMOVED: Admin Check... '.$resource." | ".$action." &mdash; Access for ".$usercode.' ('.$username.') is '.$result."</span><br>";
               echo '<span style="color:red;">REMOVED: TACL Check... '.$resource." | ".$action." &mdash; Access for ".$usercode.' ('.$username.') is '.$result2."</span><br>";
            }
         }
      }
      
      exit;
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a list of site users that can be copied and pasted
    * into outlook.
    *
    */
   function email_list()
   {
      $this->load->database('read');
      
      $sql = 'SELECT FirstName, LastName, Email '.
             'FROM adm_person '.
             'WHERE Status = 1';
      $query = $this->db->query($sql);
      $list = $query->result_array();
      
      foreach ($list AS $item)
      {
         echo $item['FirstName'].' '.$item['LastName'].' &lt;'.$item['Email'].'&gt;<br />';
      }
      exit;
   }

}
?>