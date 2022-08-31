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
   $result[] = array('Link' => '/dbupload/index/',
                     'LinkText' => 'Database Upload',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/deploy/index/',
                     'LinkText' => 'Deploy',
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