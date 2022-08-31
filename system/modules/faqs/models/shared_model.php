<?php

/*
This model is not directly related to a database table, but it makes 
logical sense that it be it's own model as it serves as a data type of sorts.
*/

class Shared_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Shared_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of shared FAQ records and their associated answers
    *  including a generated field indicating whether a particular FAQ/
    *  Answer combination has been added to the site.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_shared_faqs($site_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Answers');

      // first, get a list of all shared FAQs (i.e. from the site 'shared').
      // The use of MIN() and GROUP BY is to ensure that we get just
      // one entry per FAQ even if there are multiple answers
      $sql = 'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
               'f.Status, MIN(a.Answer) '.
             'FROM faqs_item AS f, faqs_site AS s, faqs_answer AS a '.
             'WHERE s.SiteID = "shared" '.
             'AND f.ID = s.FaqID '.
             'AND a.ID = s.AnswerID '.
             'AND ( '.
               'f.Status = "active" '.
               'OR f.Status = "pending" '.
             ') '.
             'GROUP BY f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                'f.Status '.
             'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $shared_faqs = $query->result_array();

      // now, get a list of FAQs for this site that are shared
      $sql = 'SELECT t.* '.
             'FROM faqs_site AS s, faqs_site AS t '.
             'WHERE s.FaqID = t.FaqID '.
             'AND s.AnswerID = t.AnswerID '.
             'AND s.SiteID = "shared" '.
             'AND t.SiteID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      $assigns = $query->result_array();
      
      // create lookup arrays
      $assigned_faqs = array();
      $assigned_answers = array();
      foreach ($assigns AS $assign)
      {
         $assigned_faqs[] = $assign['FaqID'];
         $assigned_answers[] = $assign['AnswerID'];
      }
      
      // now, attach the answers for each FAQ
      for($i=0, $faq_cnt=count($shared_faqs); $i<$faq_cnt; $i++)
      {
         unset($shared_faqs[$i]['MIN(a.Answer)']);
         $shared_faqs[$i]['Assigned'] = in_array($shared_faqs[$i]['ID'], $assigned_faqs);
         $shared_faqs[$i]['Answers'] = $this->CI->Answers->get_answers($shared_faqs[$i]['ID']);
         for ($j=0, $ans_cnt=count($shared_faqs[$i]['Answers']); $j<$ans_cnt; $j++)
         {
            $shared_faqs[$i]['Answers'][$j]['Assigned'] = in_array($shared_faqs[$i]['Answers'][$j]['ID'], $assigned_answers);
         }
      }
      
//      echo "<pre>"; print_r($shared_faqs); echo "</pre>"; exit;

      return $shared_faqs;

   }

   // --------------------------------------------------------------------

   /**
    * Returns a lookup array of faqs that are shared for the specified site.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_shared_faqs_lookup($site_id)
   {
      $sql = 'SELECT t.* '.
             'FROM faqs_site AS s, faqs_site AS t '.
             'WHERE s.FaqID = t.FaqID '.
             'AND s.AnswerID = t.AnswerID '.
             'AND s.SiteID = "shared" '.
             'AND t.SiteID = "'.$site_id.'"';
      
      $query = $this->read_db->query($sql);
      $assigns = $query->result_array();
      
      // create lookup arrays
      $assigned_faqs = array();
      foreach ($assigns AS $assign)
      {
         $assigned_faqs[] = $assign['FaqID'];
      }

      return $assigned_faqs;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a lookup array of faqs that are shared for the specified site.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function faq_is_shared($faq_id)
   {
      $sql = 'SELECT FaqID '.
             'FROM faqs_site '.
             'WHERE FaqID = '.$faq_id.' '.
             'AND SiteID = "shared"';
      
      $query = $this->read_db->query($sql);
      
      return ($query->num_rows() > 0) ? TRUE : FALSE;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of FAQ records for all faqs in $site_id that
    *   are assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_faqs_in_site($site_id, $include_pending = TRUE)
   {
      // The use of MIN() and GROUP BY is to ensure that we get just
      // one entry per FAQ even if there are multiple answers
      $sql = 'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                'c.ID AS CategoryID, f.Status, MIN(a.Answer) '.
             'FROM faqs_item AS f, faqs_category AS c, '.
                'faqs_item_category AS fc, faqs_site AS s, faqs_answer AS a '.
             'WHERE c.SiteID = "'.$site_id.'" '.
             'AND f.ID = s.FaqID '.
             'AND c.SiteID = s.SiteID '.
             'AND a.ID = s.AnswerID '.
             'AND fc.CategoryID = c.ID '.
             'AND fc.FaqID = f.ID '.
             'AND (f.Status = "active"';
      $sql .= ($include_pending == TRUE) ? ' OR f.Status = "pending") ': ') ';
      $sql .= 'GROUP BY f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                'c.ID, f.Status '.
              'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $faq_list = $query->result_array();
   
      return $faq_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of FAQ records for all faqs in $site_id that
    *   are NOT assigned to a category.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_nocat_faqs_in_site($site_id)
   {
      // first, get a list of all products for the site.
      // The use of MIN() and GROUP BY is to ensure that we get just
      // one entry per FAQ even if there are multiple answers
      $sql = 'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
               'f.Status, MIN(a.Answer) '.
             'FROM faqs_item AS f, faqs_site AS s, faqs_answer AS a '.
             'WHERE s.SiteID = "'.$site_id.'" '.
             'AND f.ID = s.FaqID '.
             'AND a.ID = s.AnswerID '.
             'AND ( '.
               'f.Status = "active" '.
               'OR f.Status = "pending" '.
             ') '.
             'GROUP BY f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                'f.Status '.
             'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $nocat_faqs = $query->result_array();
      
      // now, get a list of all categories for these products
      $sql = 'SELECT fc.FaqID '.
             'FROM faqs_item_category AS fc '.
               'JOIN faqs_category AS c ON fc.CategoryID = c.ID '.
             'WHERE c.SiteID = "'.$site_id.'"';
   
      $query = $this->read_db->query($sql);
      $nocat_cats = $query->result_array();
      
      // create a lookup array of products with categories
      foreach ($nocat_cats AS $cat)
      {
         $cat_lookup[$cat['FaqID']] = $cat['FaqID'];
      }
      
      // now, create the final list by removing products that have a category
      $nocat_list = array();
      foreach ($nocat_faqs AS $faq)
      {
         if ( ! isset($cat_lookup[$faq['ID']]))
         {
            $nocat_list[] = $faq;
         }
      }

      return $nocat_list;
   }

   // --------------------------------------------------------------------

   /**
    * Updates an existing FAQ record
    *
    * @access   public
    * @param    array     The values to be inserted
    * $param    string    the site ID
    * @return   boolean
    */
   function update_faq($faq_id, $values)
   {
      // update the main FAQ record
      $this->write_db->where('ID', $faq_id);
      $this->write_db->update('faqs_item', $values);
      
      return TRUE;
   }


}

?>