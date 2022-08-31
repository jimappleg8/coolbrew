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
function get_vendors_submenu($this_item = '')
{
   $result[] = array('Link' => '/cp/vendors/index/',
                     'LinkText' => 'Vendors',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/vendor_services/index/',
                     'LinkText' => 'Services',
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