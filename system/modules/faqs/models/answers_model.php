<?php

class Answers_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Answers_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a answer record
    *
    * @access   public
    * @param    string    The answer ID
    * @return   array
    */
   function get_answer_data($answer_id)
   {
      $sql = 'SELECT ID, Answer, Note '.
             'FROM faqs_answer '.
             'WHERE ID = '.$answer_id;

      $query = $this->read_db->query($sql);
      $answer = $query->row_array();
      
      return $answer;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of answer records for all faqs in $site_id
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_answers_in_site($site_id)
   {
      $sql = 'SELECT s.FaqID, a.ID, a.Answer, a.Note '.
             'FROM faqs_site AS s, faqs_answer AS a '.
             'WHERE s.SiteID = "'.$site_id.'" '.
             'AND a.ID = s.AnswerID';

      $query = $this->read_db->query($sql);
      $answer_list = $query->result_array();
      
      $answers = array();
      foreach ($answer_list AS $item)
      {
         $answers[$item['FaqID']][] = $item;
      }
      
//      echo "<pre>"; print_r($answers); echo "</pre>";
   
      return $answers;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of answers assocated with an FAQ. This is for use
    *   with the shared FAQs only.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_answers($faq_id)
   {
      $sql = 'SELECT a.ID, a.Answer, a.Note '.
             'FROM faqs_site AS s, faqs_answer AS a '.
             'WHERE s.SiteID = "shared" '.
             'AND a.ID = s.AnswerID '.
             'AND FaqID = '.$faq_id;

      $query = $this->read_db->query($sql);
      $answers = $query->result_array();
      
      return $answers;
   }

   // --------------------------------------------------------------------
   
   /**
    * Returns an array of answers IDs for the specified FAQ ID
    *
    * @access   public
    * @param    array     The faq ID
    * $param    string    the site ID
    * @return   array
    */
   function get_answer_ids($faq_id)
   {
      $sql = 'SELECT DISTINCT AnswerID '.
             'FROM faqs_site '.
             'WHERE FaqID = '.$faq_id;

      $query = $this->read_db->query($sql);
      $answers = $query->result_array();
      
      $ids = array();
      foreach ($answers AS $answer)
      {
         $ids[] = $answer['AnswerID'];
      }

      return $ids;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the FAQ ID for the specified answer ID
    *
    * @access   public
    * @param    string    The answer ID
    * @return   array
    */
   function get_answer_faq_id($answer_id)
   {
      $sql = 'SELECT FaqID '.
             'FROM faqs_site '.
             'WHERE AnswerID = '.$answer_id;

      $query = $this->read_db->query($sql);
      $faq = $query->row_array();
      
      return $faq['FaqID'];
   }

   // --------------------------------------------------------------------
   
   /**
    * Checks to see if this answer is active on any sites other than 
    *   the 'shared' pseudo-site.
    *
    * @access   public
    * @param    array     The answer ID
    * @return   array
    */
   function answer_is_being_used($answer_id)
   {
      $sql = 'SELECT SiteID FROM faqs_site '.
             'WHERE AnswerID = '.$answer_id.' '.
             'AND SiteID != "shared"';

      $query = $this->read_db->query($sql);
      
      if ($query->num_rows() == 0)
      {
         return FALSE;
      }
      
      return TRUE;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new answer record
    *
    * @access   public
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function insert_answer($values)
   {
      $site['SiteID'] = $values['SiteID'];
      unset($values['SiteID']);
      
      $site['FaqID'] = $values['FaqID'];
      unset($values['FaqID']);
      
      // first, insert the answer record
      $this->write_db->insert('faqs_answer', $values);
      $answer_id = $this->write_db->insert_id();
      
      // finally, insert the site record
      $site['AnswerID'] = $answer_id;
      $this->write_db->insert('faqs_site', $site);
      
      return $answer_id;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an existing answer record
    *
    * @access   public
    * $param    string    the answer ID
    * @param    array     The values to be inserted
    * @return   boolean
    */
   function update_answer($answer_id, $values)
   {
      $this->write_db->where('ID', $answer_id);
      $this->write_db->update('faqs_answer', $values);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes the specified answer.
    *
    * @access   public
    * @return   boolean
    */
   function delete_answer($answer_id)
   {
      // delete all references to this answer in faqs_site
      $this->write_db->where('AnswerID', $answer_id);
      $this->write_db->delete('faqs_site');

      // delete the actual answer record
      $this->write_db->where('ID', $answer_id);
      $this->write_db->delete('faqs_answer');

      return TRUE;
   }

}

?>