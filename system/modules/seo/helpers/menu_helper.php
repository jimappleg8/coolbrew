<?php

/** 
 * Menu Helper
 *
 */

/**
 * Get array for the submenus
 *
 * @access   public
 * @params   string   the site id to use in links
 * @params   string   the current item to show as selected
 * @returns  array   
 */
function get_submenu($site_id, $this_item = '')
{
   $result[] = array('Link' => '/links/index/'.$site_id.'/',
                     'LinkText' => 'Link Tools',
                     'Position' => 'left',
                    );
//   $result[] = array('Link' => '/utilities/index/'.$site_id.'/',
//                     'LinkText' => 'Utilities',
//                     'Position' => 'left',
//                    );
//   $result[] = array('Link' => '/pages/permissions/'.$site_id.'/',
//                     'LinkText' => 'Permissions',
//                     'Position' => 'right',
//                    );

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