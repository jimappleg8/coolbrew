<?php

class Company_model extends Model {

   var $CI;

   var $read_db;      // database object for reading
   var $write_db;     // database object for writing

   function Company_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
      
      $this->CI =& get_instance();

      $options = array('db' => 'write', 'prefix' => 'adm');
      $this->CI->load->library('tacl', $options);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of company data for the supplied company ID
    *
    * @access   public
    * @return   array
    */
   function get_company_data($company_id)
   {
      $sql = 'SELECT * FROM adm_company '.
             'WHERE ID = '.$company_id;

      $query = $this->read_db->query($sql);
      $user = $query->row_array();
      
      return $user;
   }

   // --------------------------------------------------------------------

   /**
    * Returns boolean indicating whether there are people assigned
    *  to the given company ID.
    *
    * @access   public
    * @return   array
    */
   function people_assigned_to_company($company_id)
   {
      $sql = 'SELECT ID FROM adm_person '.
             'WHERE CompanyID = '.$company_id;

      $query = $this->read_db->query($sql);
      
      if ($query->num_rows() > 0)
      {
         return TRUE;
      }
      
      return FALSE;
   }
   
   // --------------------------------------------------------------------

   /**
    * Deletes a company record
    *
    * @access   public
    * @return   array
    */
   function delete_company($company_id, $old_values)
   {
      $this->CI->load->library('auditor');

      // get the member ID for his company
      $usercode = $company_id.'-company';
      
      // delete the member record for this company
      // this also deletes any permissions for this member
      $this->tacl->remove_member($usercode);

      $tmp = $this->write_db->where('ID', $company_id);
      $this->write_db->delete('adm_company');

      $this->auditor->audit_delete('adm_company', $tmp->ar_where, $old_values);
   }
   
   // --------------------------------------------------------------------

   /**
    * Adds a new company record
    *
    * @access   public
    * @return   array
    */
   function insert_company($values)
   {
      $this->CI->load->library('auditor');
      
      $this->write_db->insert('adm_company', $values);
      $id = $this->write_db->insert_id();
      
      $this->auditor->audit_insert('adm_company', '', $values);
      
      // add company to member table
      $usercode = $id.'-company';
      $this->CI->tacl->create_member($usercode);      
      
      return $id;
   }

   // --------------------------------------------------------------------

   /**
    * Updates a company record
    *
    * @access   public
    * @return   array
    */
   function update_company($company_id, $values, $old_values)
   {
      $this->CI->load->library('auditor');
      
      $tmp = $this->write_db->where('ID', $company_id);
      $this->write_db->update('adm_company', $values);
      
      $this->auditor->audit_update('adm_company', $tmp->ar_where, $old_values, $values);
   }

}

?>