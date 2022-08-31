<?php

class Locations extends Controller {

   function Locations()
   {
      parent::Controller();
      $this->load->model('Jobs_people');
      $this->load->library('session');
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of job locations
    *
    */
   function index() 
   {
      $this->Jobs_people->check('Locations');
      
      $location['error_msg'] = $this->session->userdata('jobs_error');
      if ($this->session->userdata('jobs_error') != '')
         $this->session->set_userdata('jobs_error', '');

      $this->load->database('write');

      $sql = "SELECT * FROM jobs_location " .
             "WHERE Status <= 1 ".
             "ORDER BY Country, LocationName";
      
      $query = $this->db->query($sql);
      $location_list = $query->result_array();

      $num_locs = count($location_list);
      $location['location_exists'] = ($num_locs == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('jobs_adm');

      $data['tabs'] = $this->Jobs_people->get_tabs('Locations');
      $data['location'] = $location;
      $data['location_list'] = $location_list;
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      
      $this->load->vars($data);
   	
      return $this->load->view('locations/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Deletes a location
    *
    */
   function delete($loc_id) 
   {
      $this->Jobs_people->check('Locations');
      
      $this->load->helper('url');

      $this->load->database('write');
      
      $sql = "SELECT * FROM jobs ".
             "WHERE LocationID = ".$loc_id." ".
             "AND Status <= 1";

      $query = $this->db->query($sql);
      $probs = $query->result_array();
   
      if (count($probs) == 0)
      {
         $sql = "UPDATE jobs_location " . 
                "SET Status = 2 " .
                "WHERE ID = ".$loc_id;

         $this->db->query($sql);
      }
      else
      {
         $sql = "SELECT LocationName FROM jobs_location " .
                "WHERE ID = ".$loc_id;
      
         $query = $this->db->query($sql);
         $row = $query->row_array();
   
         $this->session->set_userdata('jobs_error', "You cannot delete the location \"".$row['LocationName']."\" because there are active jobs that use it.<br>Please return to the Jobs tab and change those jobs to another location.");
      }
      redirect("locations/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Adds a location entry
    *
    */
   function add($this_action) 
   {
      $this->Jobs_people->check('Locations');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['LocationName'] = 'trim|required';
      $rules['City'] = 'trim';
      $rules['State'] = 'trim';
      $rules['Country'] = 'trim|required';
      $rules['ContactEmail'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['LocationName'] = 'Location Name';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Country'] = 'Country';
      $fields['ContactEmail'] = 'Contact Email';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Locations');
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('locations/add', NULL, TRUE);
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
    * Processes the add location form
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
      $values['LocationName'] = ascii_to_entities($values['LocationName']);
      $values['City'] = ascii_to_entities($values['City']);
      $values['State'] = ascii_to_entities($values['State']);
      $values['Country'] = ascii_to_entities($values['Country']);
      
      $values['SiteID'] = SITE_ID;
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->insert('jobs_location', $values);

      redirect("locations/index");
   }

   // --------------------------------------------------------------------
   
   /**
    * Updates a location entry
    *
    */
   function edit($loc_id, $this_action) 
   {
      $this->Jobs_people->check('Locations');
      
      $this->load->helper(array('form', 'url', 'text'));
      
      $this->load->library('validation');
      
      $rules['LocationName'] = 'trim|required';
      $rules['City'] = 'trim';
      $rules['State'] = 'trim';
      $rules['Country'] = 'trim|required';
      $rules['ContactEmail'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['LocationName'] = 'Location Name';
      $fields['City'] = 'City';
      $fields['State'] = 'State';
      $fields['Country'] = 'Country';
      $fields['ContactEmail'] = 'Contact Email';

      $this->validation->set_fields($fields);

      // get the data from current record
      $sql = 'SELECT * FROM jobs_location '.
             'WHERE ID = '.$loc_id.' ';
      $query = $this->db->query($sql);
      $defaults = $query->row_array();
      
      $defaults['LocationName'] = entities_to_ascii($defaults['LocationName']);
      $defaults['City'] = entities_to_ascii($defaults['City']);
      $defaults['State'] = entities_to_ascii($defaults['State']);
      $defaults['Country'] = entities_to_ascii($defaults['Country']);

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('jobs_adm');

         $data['tabs'] = $this->Jobs_people->get_tabs('Locations');
         $data['loc_id'] = $loc_id;
         $data['last_action'] = $this->session->userdata('last_action') + 1;

         $this->load->vars($data);
   	
         return $this->load->view('locations/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action + 1);
            $this->_edit($loc_id);
         }
      }
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Processes the edit location form
    *
    */
   function _edit($loc_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      // process the form text (convert special characters and the like)
      $values['LocationName'] = ascii_to_entities($values['LocationName']);
      $values['City'] = ascii_to_entities($values['City']);
      $values['State'] = ascii_to_entities($values['State']);
      $values['Country'] = ascii_to_entities($values['Country']);
      
      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');
      
      $this->load->database('write');
      
      $this->db->where('ID', $loc_id);
      $this->db->update('jobs_location', $values);

      redirect("locations/index");
   }


}

?>