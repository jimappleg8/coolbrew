<?php

class Indexes_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   /**
    * Weighting factors for indexed parts
    */
   var $question_weight = 1;
   var $answer_weight = 1;
   var $keyword_weight = 2;

   // --------------------------------------------------------------------

   function Indexes_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   function get_index_records($faq_id)
   {
      $sql = 'SELECT * '.
             'FROM faqs_index '.
             'WHERE FaqID = '.$faq_id;
      $query = $this->read_db->query($sql);
      $indexes = $query->result_array();
      
      return $indexes;
   }
 
   // --------------------------------------------------------------------

   function delete_index_records($faq_id)
   {
      $this->write_db->where('FaqID', $faq_id);
      $this->write_db->delete('faqs_index');
   }
 
   // --------------------------------------------------------------------

   function update_search_index($faq_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Answers');

      // delete existing search index entries about this faq
      $this->delete_index_records($faq_id);
      
      // get a list of Answers for this FAQ
      $answer_ids = $this->CI->Answers->get_answer_ids($faq_id);

      foreach ($answer_ids AS $answer_id)
      {
         // create a new entry for each of the words of the question
         foreach ($this->get_words($faq_id, $answer_id) as $word => $weight)
         {
            $index['FaqID'] = $faq_id;
            $index['AnswerID'] = $answer_id;
            $index['Word'] = $word;
            $index['Weight'] = $weight;
            $this->write_db->insert('faqs_index', $index);
         }
      }
   }
 
   // --------------------------------------------------------------------

   function get_words($faq_id, $answer_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Items');
      
      $faq = $this->CI->Items->get_faq_data($faq_id, $answer_id);

       // weight the Short Question accordingly
      $raw_text = str_repeat(' '.strip_tags($faq['ShortQuestion']), $this->question_weight);
 
     // weight the Question accordingly
      $raw_text .= str_repeat(' '.strip_tags($faq['Question']), $this->question_weight);
 
      // weight the Answer(s) accordingly
      $raw_text .= str_repeat(' '.strip_tags($faq['Answer']), $this->answer_weight);

      // weight the Keywords accordingly
      $raw_text .= str_repeat(' '.strip_tags($faq['Keywords']), $this->keyword_weight);

      // stem the resulting phrase
      $stemmed_words = $this->stem_phrase($raw_text);

      // unique words with weight
      $words = array_count_values($stemmed_words);

      return $words;
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
