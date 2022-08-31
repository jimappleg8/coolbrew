<?php

class Keywords_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   // --------------------------------------------------------------------

   function Keywords_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Adds search request to keywords database
    *
    * @access   public
    * @return   array
    */
   function insert_keywords($site_id, $keywords, $results)
   {
      if ($keywords == '')
      {
         return FALSE;
      }
      
      $this->CI =& get_instance();
      
      $this->CI->load->model('Indexes');

      $keyword['SiteID'] = $site_id;
      $keyword['Keyword'] = $keywords;

      /**
       * To create a unique identifier that has the flexibility of 
       * a stemmed index, we stem the phrase, sort the array so it is 
       * in a predictable order, and implode it into a single string.
       * This should create the same string regardless of the order people 
       * type in the keywords, whether they put in disposable words like 
       * "the", and whether there are variations in those words like plurals.
       */
      $keywords = str_replace('-', ' ', $keywords);
      $stem_array = $this->CI->Indexes->stem_phrase($keywords);
      sort($stem_array);
      $keyword['Stemword'] = implode('-', $stem_array);

      /**
       * We track whether any results were found so that we can supply a
       * list of useful searches. At the same time, we want to know if 
       * people are searching for something and not finding it. This
       * strategy should also eliminate issues with folks searching for
       * curse words or the like; they should not show up on popular 
       * searches even if they type it in 100 times.
       */
      $keyword['ResultsFound'] = (count($results) > 0) ? 1 : 0;
      $keyword['CreatedDate'] = date('Y-m-d H:i:s');
      
      $this->write_db->insert('faqs_keyword', $keyword);
      
      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Gets a list of popular searches for the specified Site ID. 
    * Optionally, you can specify a timeframe in days.
    *
    * @access   public
    * @return   array
    */
   function get_popular_searches($site_id, $days = 0, $limit = 5)
   {
      if ($days > 0)
      {
         $cutoff_timestamp = time() - ($days * 43200);
         $cutoff_date = date('Y-m-d H:i:s', $cutoff_timestamp);
      }
      
      $sql = 'SELECT DISTINCT Stemword, Keyword, COUNT(*) AS num '.
             'FROM faqs_keyword '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ResultsFound = 1 ';
     
     if ($days > 0)
     {
        $sql .= 'AND CreatedDate >= "'.$cutoff_date.'" ';
     }

     $sql .= 'GROUP BY Stemword '.
             'ORDER BY num DESC '.
             'LIMIT '.$limit;

      $query = $this->read_db->query($sql);
      $searches = $query->result_array();
      
      return $searches;
   }

}

?>