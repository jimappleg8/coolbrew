<?php

class Indexes_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   // --------------------------------------------------------------------

   function Indexes_model()
   {
      parent::Model();
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
    * Converts a phrase into an array of stemmed words for indexing
    */
   function stem_phrase($phrase)
   {
      $this->load->library('Stemmer');
      
      // split into words
      $words = str_word_count(strtolower($phrase), 1);

      // ignore stop words
      $words = $this->remove_stop_words_from_array($words);

      // stem words
      $stemmed_words = array();
      foreach ($words as $word)
      {
         // ignore 1 and 2 letter words
         if (strlen($word) <= 2)
         {
            continue;
         }
         $stemmed_words[] = $this->stemmer->stem($word, TRUE);
      }
      return $stemmed_words;
   }

   // --------------------------------------------------------------------

   /**
    * Removes common words from a phrase before stemming.
    *
    * This list is not definitive; there may be a better list that we
    * can use, but this will do for now.
    *
    */
   function remove_stop_words_from_array($words)
   {
      $stop_words = array('i', 'me', 'my', 'myself', 'we', 'our', 'ours', 'ourselves', 'you', 'your', 'yours', 'yourself', 'yourselves', 'he', 'him', 'his', 'himself', 'she', 'her', 'hers', 'herself', 'it', 'its', 'itself', 'they', 'them', 'their', 'theirs', 'themselves', 'what', 'which', 'who', 'whom', 'this', 'that', 'these', 'those', 'am', 'is', 'are', 'was', 'were', 'be', 'been', 'being', 'have', 'has', 'had', 'having', 'do', 'does', 'did', 'doing', 'a', 'an', 'the', 'and', 'but', 'if', 'or', 'because', 'as', 'until', 'while', 'of', 'at', 'by', 'for', 'with', 'about', 'against', 'between', 'into', 'through', 'during', 'before', 'after', 'above', 'below', 'to', 'from', 'up', 'down', 'in', 'out', 'on', 'off', 'over', 'under', 'again', 'further', 'then', 'once', 'here', 'there', 'when', 'where', 'why', 'how', 'all', 'any', 'both', 'each', 'few', 'more', 'most', 'other', 'some', 'such', 'no', 'nor', 'not', 'only', 'own', 'same', 'so', 'than', 'too', 'very', );

      return array_diff($words, $stop_words);
   }

}

?>
