<?php

/** 
 * Menu Helper
 *
 */

/**
 * Get array for the Control Panel submenus
 *
 * @access   public
 * @params   string   the site id to use in links
 * @params   string   the current item to show as selected
 * @returns  array   
 */
function get_cp_submenu($this_item = '')
{
   $result[] = array('Link' => '/cp/servers/index',
                     'LinkText' => 'Servers',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/analytics/index',
                     'LinkText' => 'Analytics',
                     'Position' => 'left',
                    );

   $result[] = array('Link' => '/cp/reports/index',
                     'LinkText' => 'Reports',
                     'Position' => 'left',
                    );

   for ($i=0; $i<count($result); $i++)
   {
      if ($result[$i]['LinkText'] == $this_item)
      {
         $result[$i]['Selected'] = TRUE;
      }
      else
      {
         $result[$i]['Selected'] = FALSE;
      }
   }

   return $result;
}

// ----------------------------------------------------------------------

/**
 * Get array for the Site submenus
 *
 * @access   public
 * @params   string   the site id to use in links
 * @params   string   the current item to show as selected
 * @returns  array   
 */
function get_sites_submenu($site_id, $this_item = '')
{
   $result[] = array('Link' => '/sites/analytics/index/'.$site_id.'/',
                     'LinkText' => 'Analytics',
                     'Position' => 'left',
                    );

   for ($i=0; $i<count($result); $i++)
   {
      if ($result[$i]['LinkText'] == $this_item)
      {
         $result[$i]['Selected'] = TRUE;
      }
      else
      {
         $result[$i]['Selected'] = FALSE;
      }
   }

   return $result;
}


?>