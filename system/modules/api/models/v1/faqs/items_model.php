<?php

class Items_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   var $faq_fields = array(
      'f.ID AS FaqID',
      'a.ID AS AnswerID',
      'f.ShortQuestion',
      'f.Question',
      'a.Answer',
      'f.FlagAsNew',
      'f.Status',
      'f.Sort',
   );
   
   // --------------------------------------------------------------------

   function Items_model()
   {
      parent::Model();
      
      $this->load->helper('v1/resources');
   }

   // --------------------------------------------------------------------

   /**
    * Initializes the database connections based on the server level.
    *
    * @access   public
    * @param    string    The server level
    * @return   bool
    */
   function init_db($level)
   {
      // we use the "write" database because it points to a specific server
      // where the "read" database should stay "localhost" to balance load.
      $this->read_db = $this->load->database($level.'-write', TRUE);
      $this->write_db = $this->load->database($level.'-write', TRUE);

      return TRUE;  
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified faq ID
    *
    * @access   public
    * @return   array
    */
   function get_faq_data($faq_id, $answer_id)
   {
      $field_list = implode($this->faq_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM faqs_item AS f, faqs_site AS s, faqs_answer AS a '.
             'WHERE f.ID = s.FaqID '.
             'AND a.ID = s.AnswerID '.
             'AND f.ID = '.$faq_id.' '.
             'AND s.AnswerID = '.$answer_id;
      
      $query = $this->read_db->query($sql);
      $faq = $query->row_array();
      
      return $faq;
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of active faqs for the specified FAQ Code
    *
    * @access   public
    * @return   array
    */
   function get_faqs_in_category($faq_id)
   {
      $field_list = implode($this->faq_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM faqs_category AS c, faqs_item AS f, '.
               'faqs_item_category AS fc, faqs_answer AS a '.
             'WHERE c.ID = fc.CategoryID '.
             'AND f.ID = fc.FaqID '.
             'AND a.ID = fc.AnswerID '.
             'AND c.ID = "'.$faq_id.'" '.
             'AND f.Status = "active" '.
             'ORDER BY f.Sort';
             
      $query = $this->read_db->query($sql);
      $faqs = $query->result_array();

      return $faqs;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of FAQ records for all faqs in $site_id that
    *   are assigned to an FAQ category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_faqs_in_site($site_id, $include_pending = TRUE)
   {
      $field_list = implode($this->faq_fields, ', ');

      $sql = 'SELECT '.$field_list.' '.
             'FROM faqs_site AS s '.
             'INNER JOIN faqs_item AS f '.
               'ON s.FaqID = f.ID '.
             'INNER JOIN faqs_answer AS a '.
               'ON a.ID = s.AnswerID '.
             'WHERE s.SiteID = "'.$site_id.'" '.
             'AND (f.Status = "active"';
      $sql .= ($include_pending == TRUE) ? ' OR f.Status = "pending") ': ') ';
      $sql .= 'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $faq_list = $query->result_array();

//      echo "<pre>"; print_r($faq_list); echo "</pre>";
      
      return $faq_list;
   }

   // --------------------------------------------------------------------

   /**
    * Performs search for FAQs and returns array of FAQ results
    *
    * @access   public
    * @return   boolean
    */
   function search_faqs($search, $site_id, $exact = TRUE)
   {
      $this->CI =& get_instance();
      
      $this->CI->load->model('v1/faqs/Indexes');

      $faq_list = array();
      $matches = array();
      $match_cnt = 0;
      $no_matches = FALSE;
      $show_all = TRUE;
      
      if ($search['Words'] != '')
      {
         $show_all = FALSE;
         
         $words = array_values($this->CI->Indexes->stem_phrase($search['Words']));
         $nb_words = count($words);

         // define the base query
         foreach ($words AS $word)
            $sql_words[] = 'i.Word = "'.$word.'"';
         $sql_wordlist = implode(' OR ', $sql_words);

         $sql = 'SELECT DISTINCT i.FaqID, i.AnswerID, COUNT(*) AS nb, '.
                  'SUM(Weight) AS total_weight '.
                'FROM faqs_index AS i, faqs_item AS f, faqs_site AS s '.
                'WHERE i.FaqID = f.ID '.
                'AND f.ID = s.FaqID '.
                'AND s.AnswerID = i.AnswerID '.
                'AND s.SiteID = "'.$site_id.'" '.
                'AND f.Status = "active" '.
                'AND ('.$sql_wordlist.') '.
                'GROUP BY i.FaqID, i.AnswerID ';
         // AND query?
         if ($exact)
         {
            $sql .= 'HAVING nb = '.$nb_words.' ';
         }
         $sql .= 'ORDER BY nb DESC, total_weight DESC';

         $query = $this->read_db->query($sql);
         $word_matches = $query->result_array();
         foreach ($word_matches AS $wm)
         {
            $matches[$match_cnt][] = $wm['FaqID'].'-'.$wm['AnswerID'];
         }
         if ( ! empty($matches[$match_cnt]))
            $match_cnt++;
         else
            $no_matches = TRUE;
      }
      
      // if nothing was entered, show all rather than none
      if ($show_all == TRUE)
      {
         $sql = 'SELECT DISTINCT i.FaqID, i.AnswerID, COUNT(*) AS nb, '.
                  'SUM(Weight) AS total_weight '.
                'FROM faqs_index AS i, faqs_item AS f, faqs_site AS s '.
                'WHERE i.FaqID = f.ID '.
                'AND f.ID = s.FaqID '.
                'AND s.AnswerID = i.AnswerID '.
                'AND s.SiteID = "'.$site_id.'" '.
                'AND f.Status = "active" '.
                'GROUP BY i.FaqID, i.AnswerID '.
                'ORDER BY nb DESC, total_weight DESC';

         $query = $this->read_db->query($sql);
         $word_matches = $query->result_array();
         foreach ($word_matches AS $wm)
         {
            $matches[$match_cnt][] = $wm['FaqID'].'-'.$wm['AnswerID'];
         }      
      }

      // once we have our matches, 
      if ($exact && $no_matches == FALSE)
      {
         for ($i=0; $i<count($matches); $i++)
         {
            if ($i == 0)
            {
               $faq_list = $matches[$i];
            }
            else
            {
               $faq_list = array_intersect($matches[$i], $faq_list);
            }
         }
      }
      elseif ( ! $exact)
      {
         foreach ($matches AS $match_array)
         {
            $faq_list = array_merge($match_array, $faq_list);
         }
      }
      
      // get the faqs themselves
      $faqs = array();
      foreach ($faq_list AS $key => $item)
      {
         list($faq_id, $answer_id) = explode('-', $item);
         $faqs[] = $this->get_faq_data($faq_id, $answer_id);
      }

      return $faqs;
   }


}

/* End of file items_model.php */
/* Location: ./system/modules/api/models/v1/faqs/items_model.php */