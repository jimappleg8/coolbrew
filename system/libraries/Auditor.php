<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Cool Brew
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		Cool Brew
 * @author		Jim Applegate
 * @copyright	Copyright (c) 2007, The Hain Celestial Group, Inc.
 * @license		http://www.coolbrewcms.com/user_guide/license.html
 * @link		http://www.coolbrewcms.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Auditor Class
 *
 * Supports the aggregation of JavaScript and CSS information
 *
 * @package		Cool Brew
 * @subpackage	Libraries
 * @category	Auditor
 * @author		Jim Applegate
 * @link		http://www.coolbrewcms.com/user_guide/libraries/collector.html
 *
 * Based on concepts and code by Tony Marston:
 * Copyright 2003-2005 by A J Marston <http://www.tonymarston.net>
 * Copyright 2006-2008 by Radicore Software Limited <http://www.radicore.org>
 */
class CI_Auditor {

   var $sess_cookie = 'ci_session';
   var $dbname = '';
   var $trn_array;       // audit transaction data

   /**
    * Constructor
    *
    * @access   public
    */      
   function CI_Auditor()
   {
      $this->CI =& get_instance();
      $this->CI->load->library('session');
      $this->CI->load->database('read');
      
      $this->dbname = $this->CI->db->database;
      
      $this->sess_cookie = $this->CI->session->sess_cookie;
   }

   // --------------------------------------------------------------------
   
   /**
    * Add a record to the audit trail for a DELETE.
    *
    * @access  public
    * @param   string  the database table being modified
    * @param   mixed   either a "where" array or string
    * @param   array   an array of fields and values (old record data)
    */
   function audit_delete($tablename, $where, $oldarray)
   {
      $newarray = array();

      $this->audit_write($tablename, $where, $oldarray, $newarray);
      return;
   }

   // --------------------------------------------------------------------
   
   /**
    * Add a record to the audit trail for an INSERT.
    *
    * @access  public
    * @param   string  the database table being modified
    * @param   mixed   either a "where" array or string
    * @param   array   an array of fields and values (new record data)
    */
   function audit_insert($tablename, $where, $newarray)
   {
      $oldarray = array();

      $this->audit_write($tablename, $where, $oldarray, $newarray);
      return;
   }

   // --------------------------------------------------------------------
   
   /**
    * Add a record to the audit trail for an UPDATE.
    *
    * @access  public
    * @param   string  the database table being modified
    * @param   mixed   either a "where" array or string
    * @param   array   an array of fields and values (new record data)
    * @param   array   an array of fields and values (old record data)
    */
   function audit_update($tablename, $where, $oldarray, $newarray)
   {
      $this->audit_write($tablename, $where, $oldarray, $newarray);
      return;
   }

   // --------------------------------------------------------------------
   
   /**
    * Add a record to the audit trail for an INSERT, UPDATE or DELETE.
    *
    * @access  private
    * @param   string  the database table being modified
    * @param   mixed   either a "where" array or string
    * @param   array   an array of fields and values (new record data)
    * @param   array   an array of fields and values (old record data)
    */
   function audit_write($tablename, $where, $oldarray, $newarray)
   {
      if (is_array($where))
      {
         // assumes the array is in the form produced by the active record class
         $where = implode(" ", $where);
      }
      
      // get name of calling function (task)
      $backtrace = debug_backtrace();
//      echo "<pre>"; print_r($backtrace); echo "</pre>";
      $bt_type = ($backtrace[2]['type'] == '') ? ':' : $backtrace[2]['type'];
      // The next line seems like a hack, but in some cases the 
      // file parameter is not listed in the backtrace. It seems
      // to be because the method is called within the same file,
      // so I go back one level and get the file in those cases.
      // It seems to behave inconsistently, though.
      $bt_file_base = (isset($backtrace[2]['file'])) ? $backtrace[2]['file'] : $backtrace[1]['file'];
      $bt_file = str_replace(BASEPATH.'modules', '', $bt_file_base);
      $task = $bt_file.':'.$backtrace[2]['class'].$bt_type.$backtrace[2]['function'];

      // first time only, get details from audit_session
      if ( ! $this->CI->session->userdata('session_number'))
      {
         // create a new audit_session record
         $ssn_data['Username']  = $this->CI->session->userdata('username');
         $ssn_data['SessionDate'] = date('Y-m-d');
         $ssn_data['SessionTime'] = date('h:i:s');

         $this->CI->db->insert('audit_session', $ssn_data);
         
         $ssn_data['SessionID'] = $this->CI->db->insert_id();
         $this->CI->session->set_userdata('session_number', $ssn_data['SessionID']);
      }
      else
      {
         $ssn_data['SessionID'] = $this->CI->session->userdata('session_number');
      }

      // first time only, get details from audit_transaction
      if (empty($this->trn_array))
      {
         $this->trn_array['SessionID'] = $ssn_data['SessionID'];
         
         // obtain the next value for TranSeqNo
         $sql = 'SELECT max(TranSeqNo) FROM audit_transaction '.
                'WHERE SessionID = '.$this->trn_array['SessionID'];
         $query = $this->CI->db->query($sql);
         $result = $query->row_array();
         $this->trn_array['TranSeqNo'] = $result['max(TranSeqNo)'] + 1;

         // fill in other data
         $this->trn_array['TransactionDate'] = date('Y-m-d');
         $this->trn_array['TransactionTime'] = date('h:i:s');
         $this->trn_array['Task'] = $task;

         $this->CI->db->insert('audit_transaction', $this->trn_array);
      }

      // now create the audit_table record

      $session_id  = $this->trn_array['SessionID'];
      $tran_seq_no = $this->trn_array['TranSeqNo'];

      // obtain the next value for TableSeqNo
      $sql = 'SELECT max(TableSeqNo) FROM audit_table '.
             'WHERE SessionID = "'.$session_id.'" '.
             'AND TranSeqNo = '.$tran_seq_no;
      $query = $this->CI->db->query($sql);
      $result = $query->row_array();

      $fieldarray['TableSeqNo'] = $result['max(TableSeqNo)'] + 1;
      $fieldarray['SessionID'] = $session_id;
      $fieldarray['TranSeqNo'] = $tran_seq_no;
      $fieldarray['BaseName']  = $this->dbname;
      $fieldarray['TableName'] = $tablename;
      $fieldarray['PKey'] = $where;

      // add this record to the database
      $this->CI->db->insert('audit_table', $fieldarray);


      // lastly create audit_field records as needed

      if ( ! empty($newarray))
      {
         // look for new fields with empty/null values
         foreach ($newarray as $item => $value)
         {
            if (strlen($value) == 0)
            {
               if ( ! array_key_exists($item, $oldarray))
               {
                  // value does not exist in $oldarray, so remove from $newarray
                  unset ($newarray[$item]);
               }
            }
         }
         // remove entry from $oldarray which does not exist in $newarray
         foreach ($oldarray as $item => $value)
         {
            if ( ! array_key_exists($item, $newarray))
            {
               unset ($oldarray[$item]);
            }
         }
      }
      
      // if this is an update, remove the fields that didn't change
      if (( ! empty($newarray)) && ( ! empty($oldarray)))
      {
         foreach ($oldarray as $item => $value)
         {
            if ($newarray[$item] == $oldarray[$item])
            {
               unset ($oldarray[$item]);
               unset ($newarray[$item]);
            }
         }
      }

      $table_seq_no = $fieldarray['TableSeqNo'];
      $fieldarray = array();

      foreach ($oldarray as $field_id => $old_value)
      {
         $fieldarray['SessionID']  = $session_id;
         $fieldarray['TranSeqNo']  = $tran_seq_no;
         $fieldarray['TableSeqNo'] = $table_seq_no;
         $fieldarray['FieldID']    = $field_id;
         $fieldarray['OldValue']   = $old_value;
         if (array_key_exists($field_id, $newarray))
         {
            $fieldarray['NewValue'] = $newarray[$field_id];
            // remove matched entry from $newarray
            unset($newarray[$field_id]);
         }
         else
         {
            $fieldarray['NewValue'] = '';
         }
         $this->CI->db->insert('audit_field', $fieldarray);
      }

      // process any unmatched details remaining in $newarray
      foreach ($newarray as $field_id => $new_value)
      {
         $fieldarray['SessionID']   = $session_id;
         $fieldarray['TranSeqNo']  = $tran_seq_no;
         $fieldarray['TableSeqNo'] = $table_seq_no;
         $fieldarray['FieldID']     = $field_id;
         $fieldarray['OldValue']    = '';
         $fieldarray['NewValue']    = $new_value;

         $this->CI->db->insert('audit_field', $fieldarray);
      }

      return;
   }
    
} // end class

?>
