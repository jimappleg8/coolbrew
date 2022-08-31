<?php

class Contact_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   // --------------------------------------------------------------------

   function Contact_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Saves the form data to the contact us table
    *
    * @access   public
    * @return   array
    */
   function insert_contact($fields, $site_id)
   {   
      foreach ($fields AS $key => $value)
      {
         $values[$key] = $this->input->post($key);
      }

      $values['SiteID'] = $site_id;
      $values['SubmitDate'] = date('Y-m-d h:i:s');

      $this->write_db->insert('eyelevel_contact', $values);
      
      return $values;
   }

}

/* End of file contact_model.php */
/* Location: ./system/modules/eyelevel/models/contact_model.php */