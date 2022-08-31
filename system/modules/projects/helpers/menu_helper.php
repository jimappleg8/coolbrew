<?php

/** 
 * Menu Helper
 *
 */

// --------------------------------------------------------------------

/**
 * Get array for the main submenus
 *
 * @access   public
 * @params   string   the site id to use in links
 * @params   string   the current item to show as selected
 * @returns  array   
 */
function get_submenu($this_item = '')
{
   $result[] = array('Link' => '/cp/sprints/index/',
                     'LinkText' => 'This Sprint',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/backlogs/index/',
                     'LinkText' => 'Project Backlog',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/support/index/',
                     'LinkText' => 'Vendor Support',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/projects/index/',
                     'LinkText' => 'All Projects',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/search/index/',
                     'LinkText' => 'Search Projects',
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