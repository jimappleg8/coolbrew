<?php

class Contactus_model extends Model {

   var $read_db;   // database object for reading (different per level)
   var $write_db;  // database object for writing (different per level)
   var $live_db;   // database on production server
   
   // --------------------------------------------------------------------

   function Contactus_model()
   {
      parent::Model();
      $this->read_db  = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
      $this->live_db  = $this->load->database('production', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Connects to the message database and returns the results.
    *
    */
   function get_message_data($message_id)
   {
      $sql = 'SELECT * '.
             'FROM wf_contactus '.
             'WHERE id = '.$message_id;
      
      $query = $this->live_db->query($sql);
      $message = $query->row_array();
      
      return $message;
   }

   // --------------------------------------------------------------------

   /**
    * Saves the form data to the contact us table
    *
    * @access   public
    * @return   array
    */
   function save_submision($fields, $site_id = '')
   {
      $site_id = ($site_id == '') ? SITE_ID : $site_id;
      
      foreach ($fields AS $key => $value)
      {
         $values[$key] = $this->input->post($key);
      }

      if ( ! isset($values['Marketing']))
      {
         $values['Marketing'] = 'NO';
      }

      if ( ! isset($values['Release']))
      {
         $values['Release'] = 'NO';
      }

      $values['form_id'] = $site_id.'_contactus';
      $values['submit_ts'] = time();

      // remove dashes and spaces from the ProductUPC
      if (isset($values['ProductUPC']) && is_numeric($values['ProductUPC']))
      {
         $values['ProductUPC'] = str_replace('-', '', $values['ProductUPC']);
         $values['ProductUPC'] = str_replace(' ', '', $values['ProductUPC']);
      }
      
      $this->write_db->insert('wf_contactus', $values);
      
      return $values;
   }

   // --------------------------------------------------------------------
   
   /**
    *
    */
   function get_messages($site_id, $filter, $offset = 0, $limit = 20)
   {
      $form_id = $site_id.'_contactus';
      
      $sql = 'SELECT * '.
             'FROM wf_contactus '.
             'WHERE form_id = "'.$form_id.'" '.
             'AND spam = 0 ';
      if ($filter != '')
      {
         $sql .= 'AND comment LIKE "%'.$filter.'%" ';
      }
      $sql .= 'ORDER BY submit_ts DESC '.
              'LIMIT '.$offset.', '.$limit;
      
      $query = $this->live_db->query($sql);
      $messages = $query->result_array();
      
      return $messages;   
   }
   
   // --------------------------------------------------------------------
   
   /**
    *
    */
   function get_spam_messages($site_id, $filter, $offset = 0, $limit = 20)
   {
      $form_id = $site_id.'_contactus';
      
      $sql = 'SELECT * '.
             'FROM wf_contactus '.
             'WHERE form_id = "'.$form_id.'" '.
             'AND spam = 1 ';
      if ($filter != '')
      {
         $sql .= 'AND comment LIKE "%'.$filter.'%" ';
      }
      $sql .= 'ORDER BY submit_ts DESC '.
              'LIMIT '.$offset.', '.$limit;
      
      $query = $this->live_db->query($sql);
      $messages = $query->result_array();
      
      return $messages;   
   }
   
    // --------------------------------------------------------------------
   
   /**
    *
    */
   function get_message_count($site_id, $filter)
   {
      $form_id = $site_id.'_contactus';

      $sql = 'SELECT ID '.
             'FROM wf_contactus '.
             'WHERE form_id = "'.$form_id.'" '.
             'AND spam = 0 ';
      if ($filter != '')
      {
         $sql .= 'AND Message LIKE "%'.$filter.'%" ';
      }
      
      $query = $this->live_db->query($sql);
      
      return $query->num_rows();
   }
   
    // --------------------------------------------------------------------
   
   /**
    *
    */
   function get_spam_count($site_id, $filter)
   {
      $form_id = $site_id.'_contactus';

      $sql = 'SELECT ID '.
             'FROM wf_contactus '.
             'WHERE form_id = "'.$form_id.'" '.
             'AND spam = 1 ';
      if ($filter != '')
      {
         $sql .= 'AND Message LIKE "%'.$filter.'%" ';
      }
      
      $query = $this->live_db->query($sql);
      
      return $query->num_rows();
   }
   
   // --------------------------------------------------------------------

   /**
    * Update the message record
    *
    */
   function update_message($message_id, $values)
   {
      $this->live_db->where('id', $message_id);
      $this->live_db->update('wf_contactus', $values);
      
      return TRUE;
   }


}



/* End of file contactus_model.php */
/* Location: ./system/modules/mailform/models/contactus_model.php */