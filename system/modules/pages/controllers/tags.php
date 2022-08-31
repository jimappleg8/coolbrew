<?php

class Pages_Tags extends Controller {

	function Pages_Tags()
	{
		parent::Controller();	
	}
	
   //-------------------------------------------------------------------------
   
   /**
    * Generates a submenu
    *
    */
   function left_menu()
   {
      // (string) The view name in case we want to override the default
      $page_url = $this->tag->param(1, '');

      // (int) The level that should be considered the root
      $root_level = $this->tag->param(2, 2);
      
      // (bool) Whether we want to highlight the entire menu path
      $hilitepath = $this->tag->param(3, TRUE);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(4, "left_menu");
      
      $this->load->model('Pages');

      if ($page_url != '')
      {
         $this->Pages->page_url = $page_url;
      }
         
      $page = $this->Pages->get_page_info();
      
      if ($page == FALSE)
      {
         echo "Error: left_menu: Page is not defined.";
         exit;
      }
      
      $path = $this->Pages->get_page_path();
      
      // determine the level of the current page
      $page['level'] = count($path) - 1;
      
      $menu = $this->Pages->get_page_tree(SITE_ID);

      // The level count tracks how many items are in each level
      // This is useful when determining "last" elements
      $level_count = array();
      for ($i=0; $i<6; $i++)
         $level_count[$i] = 0;
      
      for ($i=0; $i<count($menu); $i++)
      {
         // set the default values
         $menu[$i]['hilite'] = FALSE;
         $menu[$i]['display'] = FALSE;

         // is this node in the path?
         if (in_array($menu[$i]['ID'], $path) 
          && $menu[$i]['level'] > 0)
         {
            $menu[$i]['display'] = TRUE;
            if ($hilitepath == TRUE || $menu[$i]['ID'] == $page['ID'])
            {
               $menu[$i]['hilite'] = TRUE;
            }
         }

         // is this an upper-level node we want to display?
         if ($menu[$i]['level'] >= $root_level 
          && $menu[$i]['level'] < $page['level'] 
          && in_array($menu[$i]['ParentID'], $path))
         {
            $menu[$i]['display'] = TRUE;
         }
      
         // is this node a sibling of the current page?
         if ($menu[$i]['ParentID'] == $page['ParentID'] 
          && $menu[$i]['level'] >= $root_level)
         {
            $menu[$i]['display'] = TRUE;
         }

         // is this node a child of the current page?
         if ($menu[$i]['ParentID'] == $page['ID'] 
          && $menu[$i]['level'] >= $root_level)
         {
            $menu[$i]['display'] = TRUE;
         }
         
         // after all this, make sure it should display
         if ($menu[$i]['display'] == TRUE 
          && $menu[$i]['DisplayInMenu'] == 0)
         {
            $menu[$i]['display'] = FALSE;
         }

      }

//      echo "<pre>"; print_r($menu); echo "<pre>";
//      exit;

      if ($page_url != '')
         $this->Pages->reset_page_url();

      $data['menu'] = $menu;
      $data['root_level'] = $root_level;
      $data['level_count'] = $level_count;
   	
      return $this->load->view($tpl, $data, TRUE);
   }


   //-------------------------------------------------------------------------
   
   /**
    * Generates a submenu using an included menu file
    * To make older sites compatible until a complete conversion can happen.
    *
    */
   function left_menu_inc()
   {
      // (string) The view name in case we want to override the default
      $item_id = $this->tag->param(1, '');

      // (int) The level that should be considered the root
      $root_level = $this->tag->param(2, 2);
      
      // (bool) Whether we want to highlight the entire menu path
      $hilitepath = $this->tag->param(3, FALSE);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(4, "left_menu_inc");
      
      $menu = $this->_getMenuDataFlat();
   
      // we start with level 1 as the level of the actual item, then we
      // reverse levels later...
      $level = 1;
      $item = $item_id;
      $direct_line = array();
      $next_item = '';
      while ($item != "")
      {
         foreach ($menu as $key => $row)
         {
            // mark all the sub items of the requested menu item. Each time
            // through the loop, this gets all the children, peers, and
            // parents in this branch of the tree, up to the root level.
            if ($menu[$key]['parent'] == $item)
            {
               $menu[$key]['level'] = $level;
               $menu[$key]['use'] = true;
            }
            // somewhere in this process we'll find the desired menu item.
            // when we do, we make a note of it's position in the array.
            if ($menu[$key]['sec_id'] == $item_id)
            {
               $item_key = $key;
               $menu[$key]['hilite'] = true;
            }
            // get the parent for next time through 'while' loop
            if ($menu[$key]['sec_id'] == $item)
            {
               $direct_line[] = $key;
               $next_item = $menu[$key]['parent'];
            }
         }
         $item = $next_item;
         $level = $level + 1;
      }
   
      $count = 0;
      $level_count = array();
      $menu_data = array();
      for ($i=0; $i<6; $i++)
      {
         $level_count[$i] = 0;
      }
      foreach ($menu as $key => $row)
      {
         if ( ! empty($menu[$key]['level']))
         {
            // reverse level numbers...
            $menu[$key]['level'] = $level - $menu[$key]['level'];
            // ... and turn off items below desired root level
            // except for the direct line to the root.
            if (($menu[$key]['level'] < $root_level)
             && (!(in_array($key, $direct_line))))
            {
               $menu[$key]['use'] = false;
            }
            if (in_array($key, $direct_line) && $hilitepath == true)
            {
               $menu[$key]['hilite'] = true;
            }
         }
         // create a new array with just the needed elements
         if ($menu[$key]['use'] == true)
         {
            if ($key == $item_key)
            {
               $menu_data[$count]['this_page'] = true;
            }
            else
            {
               $menu_data[$count]['this_page'] = false;
            }
            $menu_data[$count]['sec_name'] = $menu[$key]['sec_name'];
            $menu_data[$count]['link'] = $menu[$key]['link'];
            $menu_data[$count]['level'] = $menu[$key]['level'];
            $menu_data[$count]['hilite'] = $menu[$key]['hilite'];
            $level_count[$menu[$key]['level']]++;
            $count = $count + 1;
         }
      }
   
//      echo "<pre>"; print_r($menu_data); echo "</pre>";
   
      $data['menu_table'] = $menu_data;
      $data['level_count'] = $level_count;
   	
      return $this->load->view($tpl, $data, TRUE);
   }
   
   // ------------------------------------------------------------------------
   // _getMenuDataFlat()
   //
   // ------------------------------------------------------------------------
   
   function _getMenuDataFlat()
   {
      $menu_file = DOCPATH.'/inc/'.SITE_ID.'_menu.txt';

      $handle = fopen($menu_file, "r") or die ("Unable to open menu file");

      $count = 0;
      while ( ! feof($handle))
      {
         $line = fgets($handle, 1024);
         $line_array = explode("\t", $line);
         $parent = $line_array[0];
         $sec_id = (isset($line_array[1])) ? $line_array[1] : '';
         $sec_name = (isset($line_array[2])) ? $line_array[2] : '';
         $link = (isset($line_array[3])) ? $line_array[3] : '';
         $sort = (isset($line_array[4])) ? $line_array[4] : '';
   
         $menu_data[$count]['parent'] = trim($parent);
         $menu_data[$count]['sec_id'] = trim($sec_id);
         $menu_data[$count]['sec_name'] = trim($sec_name);
         $menu_data[$count]['link'] = trim($link);
         $menu_data[$count]['sort'] = trim($sort);
         $menu_data[$count]['level'] = "";
         $menu_data[$count]['use'] = false;
         $menu_data[$count]['hilite'] = false;
         $count = $count + 1;
      }
   //   echo "<pre>"; print_r($menu_data); echo "</pre>";
      return $menu_data;
   }
   
   //-------------------------------------------------------------------------

   /**
    * returns a hierarchical list of pages for the site
    * 
    */
   function site_map()
   {
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(1, "site_map");

      $this->load->model('Pages');

      $map = $this->Pages->get_page_tree(SITE_ID, 'root');

      $data['site_data'] = $map;
   	
      return $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------
   
   /**
    * Returns the data for a particular page
    * 
    */
   function page_info()
   {
      // (string) the URL for the page to look up
      $page_url = $this->tag->param(1, '');
      
      $this->load->model('Pages');
      
      if ($page_url != '')
      {
         $this->Pages->page_url = $page_url;
      }

      $page = $this->Pages->get_page_info();
      
      if ($page == FALSE)
      {
         echo "Error: page_info: Page is not defined.";
         exit;
      }
      
      $path = $this->Pages->get_page_path();
      for ($i=0; $i<count($path); $i++)
      {
         $item = $this->Pages->get_page_data($path[$i]);
//         $new_path[$i] = $item['PageName'];
         $new_path[$i]['PageName'] = $item['PageName'];
         $new_path[$i]['URL'] = $item['URL'];
         $new_path[$i]['MenuText'] = $item['MenuText'];
      }
      
      $results= $page;
      $results['Path'] = $new_path;

      return $results;

   }

}
?>