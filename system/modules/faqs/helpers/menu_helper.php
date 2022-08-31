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
   $result[] = array('Link' => '/sites/faqs/index/'.$site_id.'/',
                     'LinkText' => 'FAQs',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/sites/categories/index/'.$site_id.'/',
                     'LinkText' => 'Categories',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/sites/shared/manage/'.$site_id.'/',
                     'LinkText' => 'Manage Shared FAQs',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/sites/search/index/'.$site_id.'/',
                     'LinkText' => 'Search FAQs',
                     'Position' => 'left',
                    );
//   $result[] = array('Link' => '/admin/permissions/'.$site_id.'/',
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

// --------------------------------------------------------------------

/**
 * Get array for the main submenus
 *
 * @access   public
 * @params   string   the site id to use in links
 * @params   string   the current item to show as selected
 * @returns  array   
 */
function get_main_submenu($this_item = '')
{
   $result[] = array('Link' => '/cp/faqs/index/',
                     'LinkText' => 'Shared FAQs',
                     'Position' => 'left',
                    );
   $result[] = array('Link' => '/cp/categories/index/',
                     'LinkText' => 'Categories',
                     'Position' => 'left',
                    );
//   $result[] = array('Link' => '/admin/permissions/'.$site_id.'/',
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