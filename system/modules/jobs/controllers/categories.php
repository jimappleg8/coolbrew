<?php

class Categories extends Controller {

   function Categories()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of job categories
    *
    */
   function index() 
   {
      $this->Jobs_people->check('Categories');
      
      $category['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');

      $this->load->database('write');

      $sql = "SELECT * FROM jobs_category " .
             "WHERE Status <= 1 ".
             "ORDER BY CategoryName";
      
      $query = $this->db->query($sql);
      $category_list = $query->result_array();

      $num_cats = count($category_list);
      $category['category_exists'] = ($num_cats == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('jobs_adm');

      $data['tabs'] = $this->Jobs_people->get_tabs('Categories');
      $data['category'] = $category;
      $data['category_list'] = $category_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('categories/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Deletes a category
    *
    */
   function delete($cat_id) 
   {
      $this->Jobs_people->check('Categories');
      
      $this->load->helper('url');

      $this->load->database('write');
      
      $sql = "SELECT * FROM jobs ".
             "WHERE CategoryID = ".$cat_id." ".
             "AND Status <= 1";

      $query = $this->db->query($sql);
      $probs = $query->result_array();
   
      if (count($probs) == 0)
      {
         $sql = "UPDATE jobs_category " . 
                "SET Status = 2 " .
                "WHERE ID = ".$cat_id;

         $this->db->query($sql);
      }
      else
      {
         $sql = "SELECT CategoryName FROM jobs_category " .
                "WHERE ID = ".$cat_id;
      
         $query = $this->db->query($sql);
         $row = $query->row_array();
   
         $this->session->set_userdata('jobs_error', "You cannot delete the category \"".$row['CategoryName']."\" because there are active jobs that use it.<br>Please return to the Jobs tab and change those jobs to another category.");
      }
      redirect("categories/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a category entry
    *
    */
   function add($this_action) 
   {
      $this->Jobs_people->check('Categories');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['CategoryName'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['CategoryName'] = 'Category Name';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Categories');
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('categories/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_add();
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the add category form
    *
    */
   function _add()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      if ($values['Status'] == '')
         $values['Status'] = 1;
      
      // process the form text (convert special characters and the like)
      $values['CategoryName'] = ascii_to_entities($values['CategoryName']);
      
      $values['SiteID'] = SITE_ID;
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->insert('jobs_category', $values);

      redirect("categories/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a category entry
    *
    */
   function edit($cat_id, $this_action) 
   {
      $this->Jobs_people->check('Categories');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['CategoryName'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['CategoryName'] = 'Category Name';

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs_category '.
             'WHERE ID = '.$cat_id.' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['CategoryName'] = entities_to_ascii($defaults['CategoryName']);

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Categories');
         $data['cat_id'] = $cat_id;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('categories/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($cat_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit category form
    *
    */
   function _edit($cat_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['CategoryName'] = ascii_to_entities($values['CategoryName']);
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->where('ID', $cat_id);
      $this->db->update('jobs_category', $values);

      redirect("categories/index");
   }


}

?>