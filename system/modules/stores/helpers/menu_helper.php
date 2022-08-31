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
function get_submenu($this_item = '')
{
   $result[] = array('Link' => '/stores/index/',
                     'LinkText' => 'Search',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/messages/index/',
                     'LinkText' => 'Messages',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/utilities/index/',
                     'LinkText' => 'Utilities',
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