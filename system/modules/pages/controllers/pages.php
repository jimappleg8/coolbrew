<?php

class Pages extends Controller {

   function Pages()
   {
      parent::Controller();
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'pages'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // ====================================================================
   //  LISTS
   // ====================================================================
   
   /**
    * Generates a listing of the entire page system
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $page['error_msg'] = $this->session->userdata('page_error');
      if ($this->session->userdata('page_error') != '')
         $this->session->set_userdata('page_error', '');

      $this->load->model('Sites');
      $this->load->model('Pages');
      
      $site = $this->Sites->get_site_data($site_id);
      
      // the first time, rebuild the tree
//      $root = $this->Pages->get_site_root($site_id);
//      $this->Pages->rebuild_tree($site_id, $root, 1);
      
      $page_list = $this->Pages->get_page_tree($site_id, 'root');

      $page['page_exists'] = (count($page_list) == 1) ? FALSE : TRUE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('pages');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Pages');
      $data['submenu'] = get_submenu($site_id, 'Pages');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['page'] = $page;
      $data['page_list'] = $page_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('pages/list', NULL, TRUE);
   }

   // ====================================================================
   //  DELETE
   // ====================================================================

   /**
    * Deletes a page from the list
    *
    */
   function delete($site_id, $page_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->model('Pages');
      $this->load->database('read');
      
      $page = $this->Pages->get_page_data($page_id);

      // delete the page record itself
      $sql = 'DELETE FROM pages '. 
             'WHERE ID = '.$page_id;
      $this->db->query($sql);
      
      // get a list of this page's children
      $sql = 'SELECT ID FROM pages '.
             'WHERE ParentID = '.$page['ID'].' '.
             'ORDER BY Sort';
      $query = $this->db->query($sql);
      $children = $query->result_array();
      
      // get a list of categories whose CategoryOrder will need to be adjusted
      $sql = 'SELECT ID, Sort FROM pages '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND Sort > '.$page['Sort'].' '.
             'AND ParentID = '.$page['ParentID'];
      $query = $this->db->query($sql);
      $belows = $query->result_array();
      
      // change the parent IDs of children to this page's parent ID
      for ($i=0; $i<count($children); $i++)
      {
         $values['ParentID'] = $page['ParentID'];
         $values['Sort'] = $page['Sort'] + $i;
         $this->db->where('ID', $children[$i]['ID']);
         $this->db->update('pages', $values);
      }
      
      $offset = count($children) - 1;
      foreach ($belows AS $below)
      {
         $values['Sort'] = $below['Sort'] + $offset;
         $this->db->where('ID', $below['ID']);
         $this->db->update('pages', $values);
      }

      // rebuild the tree
      $root = $this->Pages->get_site_root($site_id);
      $this->Pages->rebuild_tree($site_id, $root, 1);

      redirect('pages/index/'.$site_id.'/');
   }

   // ====================================================================
   //  ADD
   // ====================================================================

   /**
    * Adds a page item
    *
    */
   function add($site_id, $parent, $sort, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      
      $this->load->library('validation');
      
      $this->load->model('Sites');
      $this->load->model('Pages');
      
      $site = $this->Sites->get_site_data($site_id);

      $rules['PageName'] = 'trim|required';
      $rules['MenuText'] = 'trim|required';
      $rules['URL'] = 'trim';
      $rules['ExternalLink'] = 'trim';
      $rules['NewWindow'] = 'trim';
      $rules['DisplayInMenu'] = 'trim';
      $rules['ProductCategory'] = 'trim';
      $rules['Content'] = 'trim';
      $rules['PageTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaAbstract'] = 'trim';
      $rules['MetaRobots'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['PageName'] = 'Page Name';
      $fields['MenuText'] = 'Menu Text';
      $fields['URL'] = 'URL';
      $fields['ExternalLink'] = 'External Link';
      $fields['NewWindow'] = 'New Window';
      $fields['DisplayInMenu'] = 'Display In Menu';
      $fields['ProductCategory'] = 'Product Category';
      $fields['Content'] = 'ContentContent';
      $fields['PageTitle'] = 'Page Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaAbstract'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Robots';

      $this->validation->set_fields($fields);

      $defaults['DisplayInMenu'] = 1;
      $defaults['MetaRobots'] = 'index,follow';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('pages');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Pages');
         $data['submenu'] = get_submenu($site_id, 'Pages');
         $data['site_id'] = $site_id;
         $data['parent'] = $parent;
         $data['sort'] = $sort;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('pages/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_add($site_id, $parent, $sort);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add page form
    *
    */
   function _add($site_id, $parent, $sort)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $this->load->database('read');
      
      // update needed sort fields to make room for insert
      $sql = 'SELECT ID, Sort FROM pages '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ParentID = '.$parent.' '.
             'AND Sort >= '.$sort;
      $query = $this->db->query($sql);
      $sort_list = $query->result_array();
      
      if ($query->num_rows() > 0)
      {
         foreach($sort_list AS $item)
         {
            $item['Sort'] = $item['Sort'] + 1;
            $this->db->where('ID', $item['ID']);
            $this->db->update('pages', $item);
         }
      }

      // Now, insert the record
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;      
      $values['ParentID'] = $parent;
      $values['Sort'] = $sort;
      $values['Content'] = ascii_to_entities($values['Content']);
      $values['PageTitle'] = ascii_to_entities($values['PageTitle']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);
      $values['MetaAbstract'] = ascii_to_entities($values['MetaAbstract']);
      $values['MetaRobots'] = ascii_to_entities($values['MetaRobots']);
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $this->db->insert('pages', $values);
      
      // And rebuild the tree so it is up-to-date
      $root = $this->Pages->get_site_root($site_id);
      $this->Pages->rebuild_tree($site_id, $root, 1);

      redirect("pages/index/".$site_id.'/');
   }

   // ====================================================================
   //  EDIT
   // ====================================================================
   
   /**
    * Updates a page item
    *
    */
   function edit($site_id, $page_id, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
      
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      $this->load->model('Sites');
      $this->load->model('Pages');

      $site = $this->Sites->get_site_data($site_id);
      $page = $this->Pages->get_page_data($page_id);

      $rules['PageName'] = 'trim|required';
      $rules['MenuText'] = 'trim|required';
      $rules['URL'] = 'trim';
      $rules['ExternalLink'] = 'trim';
      $rules['NewWindow'] = 'trim';
      $rules['DisplayInMenu'] = 'trim';
      $rules['ProductCategory'] = 'trim';
      $rules['Content'] = 'trim';
      $rules['PageTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaAbstract'] = 'trim';
      $rules['MetaRobots'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['PageName'] = 'Page Name';
      $fields['MenuText'] = 'Menu Text';
      $fields['URL'] = 'URL';
      $fields['ExternalLink'] = 'External Link';
      $fields['NewWindow'] = 'New Window';
      $fields['DisplayInMenu'] = 'Display In Menu';
      $fields['ProductCategory'] = 'Product Category';
      $fields['Content'] = 'Content';
      $fields['PageTitle'] = 'Page Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaAbstract'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Robots';

      $this->validation->set_fields($fields);

      $defaults = $page;
      $defaults['Content'] = entities_to_ascii($defaults['Content']);
      $defaults['PageTitle'] = entities_to_ascii($defaults['PageTitle']);
      $defaults['MetaDescription'] = entities_to_ascii($defaults['MetaDescription']);
      $defaults['MetaKeywords'] = entities_to_ascii($defaults['MetaKeywords']);
      $defaults['MetaAbstract'] = entities_to_ascii($defaults['MetaAbstract']);
      $defaults['MetaRobots'] = entities_to_ascii($defaults['MetaRobots']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('pages');

         $data['last_action'] = $this->session->userdata('last_action') + 1;      
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Pages');
         $data['submenu'] = get_submenu($site_id, 'Pages');
         $data['page_id'] = $page_id;
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('pages/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $page_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Updates a page record
    *
    */
   function _edit($site_id, $page_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($page_id == 0)
      {
         show_error('_edit_page requires that a page ID be supplied.');
      }

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['Content'] = ascii_to_entities($values['Content']);
      $values['PageTitle'] = ascii_to_entities($values['PageTitle']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);
      $values['MetaAbstract'] = ascii_to_entities($values['MetaAbstract']);
      $values['MetaRobots'] = ascii_to_entities($values['MetaRobots']);
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->db->where('ID', $page_id);
      $this->db->update('pages', $values);
      
      redirect("pages/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Rearranges page items up and down
    *
    * @return void
    */
   function move($site_id, $page_id, $direction, $this_action) 
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      // detect if the page is just being reloaded
      if ($this_action > $this->session->userdata('last_action'))
      {
         $this->session->set_userdata('last_action', $this_action);

         $this->load->model('Pages');

         $row = $this->Pages->get_page_data($page_id);
         
         // determine how many children are on this level
         $sql = 'SELECT ID '.
                'FROM pages '.
                'WHERE ParentID = '.$row['ParentID'].' '.
                'AND SiteID = \''.$row['SiteID'].'\'';
   
         $query = $this->db->query($sql);
         $parent = $query->result_array();
         $children = $query->num_rows();
      
         if ($direction == "dn" && $row['Sort'] < $children)
         {
            $sql = 'UPDATE pages '.
                   'SET Sort = '.$row['Sort'].' '.
                   'WHERE Sort = '.($row['Sort'] + 1).' '.
                   'AND ParentID = '.$row['ParentID'].' '.
                   'AND SiteID = \''.$row['SiteID'].'\'';
            $query = $this->db->query($sql);

            $sql = 'UPDATE pages '.
                   'SET Sort = '.($row['Sort'] + 1).' '.
                   'WHERE ID = '.$page_id;
            $query = $this->db->query($sql);

         }
         elseif ($direction == "up" && $row['Sort'] > 1)
         {
            $sql = 'UPDATE pages '.
                   'SET Sort = '.$row['Sort'].' '.
                   'WHERE Sort = '.($row['Sort'] - 1).' '.
                   'AND ParentID = '.$row['ParentID'].' '.
                   'AND SiteID = \''.$row['SiteID'].'\'';
            $query = $this->db->query($sql);

            $sql = 'UPDATE pages '.
                   'SET Sort = '.($row['Sort'] - 1).' '.
                   'WHERE ID = '.$page_id;
            $query = $this->db->query($sql);
         }

      // And rebuild the tree so it is up-to-date
      $root = $this->Pages->get_site_root($site_id);
      $this->Pages->rebuild_tree($site_id, $root, 1);
      }
      redirect("pages/index/".$site_id.'/');
   }
   

}
?>