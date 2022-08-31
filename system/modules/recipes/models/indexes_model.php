<?php

class Indexes_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   /**
    * Weighting factors for indexed parts
    */
   var $body_weight = 1;
   var $title_weight = 2;
   var $keyword_weight = 2;

   // --------------------------------------------------------------------

   function Indexes_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Retrieve the index records for the specified recipe ID
    *
    * @param   int     The Recipe ID
    * @return  array   An array of index records
    *
    */
   function get_index_records($recipe_id)
   {
      $sql = 'SELECT * '.
             'FROM rcp_index '.
             'WHERE RecipeID = '.$recipe_id;
      $query = $this->read_db->query($sql);
      $indexes = $query->result_array();
      
      return $indexes;
   }

   // --------------------------------------------------------------------

   /**
    * Retrieve the index records that match a word search
    *
    * @param   string  The Site ID
    * @param   array   The words that are being searched for
    * @param   bool    Whether the match should be exact
    * @return  array   An array of index records
    *
    */
   function get_index_records_by_word($site_id, $words, $exact = FALSE)
   {
      $nb_words = count($words);
      
      // define the base query
      foreach ($words AS $word)
      {
         $sql_words[] = 'i.Word = "'.$word.'"';
      }
      $sql_wordlist = implode(' OR ', $sql_words);

      $sql = 'SELECT DISTINCT i.RecipeID, COUNT(*) AS nb, '.
               'SUM(Weight) AS total_weight, FlagAsNew '.
             'FROM rcp_index AS i, rcp_recipe AS r, rcp_recipe_site AS rs '.
             'WHERE i.RecipeID = r.ID '.
             'AND rs.RecipeID = r.ID '.
             'AND rs.SiteID = "'.$site_id.'" '.
             'AND ('.$sql_wordlist.') '.
             'GROUP BY i.RecipeID ';
      // AND query?
      if ($exact)
      {
         $sql .= 'HAVING nb = '.$nb_words.' ';
      }
      $sql .= 'ORDER BY nb DESC, total_weight DESC';

      $query = $this->read_db->query($sql);
      $indexes = $query->result_array();
      
      return $indexes;
   }

   // --------------------------------------------------------------------

   function update_search_index($recipe_id)
   {
      // delete existing search index entries about this recipe
      $this->write_db->where('RecipeID', $recipe_id);
      $this->write_db->delete('rcp_index');

      // create a new entry for each of the words of the question
      foreach ($this->get_words($recipe_id) as $word => $weight)
      {
         $index['RecipeID'] = $recipe_id;
         $index['Word'] = $word;
         $index['Weight'] = $weight;
         $this->write_db->insert('rcp_index', $index);
      }
   }
 
   // --------------------------------------------------------------------

   function get_words($recipe_id)
   {
      // get the main recipe record
      $sql = 'SELECT Title, Description, Directions, Keywords '.
             'FROM rcp_recipe '.
             'WHERE ID = '.$recipe_id;
      $query = $this->read_db->query($sql);
      $result = $query->row_array();
      
       // weight the Description accordingly
      $raw_text = str_repeat(' '.strip_tags($result['Description']), $this->body_weight);
 
     // weight the Directions accordingly
      $raw_text .= str_repeat(' '.strip_tags($result['Directions']), $this->body_weight);
 
      // weight the Title accordingly
      $raw_text .= str_repeat(' '.$result['Title'], $this->title_weight);

      // weight the Keywords accordingly
      $raw_text .= str_repeat(' '.strip_tags($result['Keywords']), $this->keyword_weight);

      // get the ingredient list for this recipe
      $sql = 'SELECT Name, ProductOne, ProductTwo '.
             'FROM rcp_ingredient '.
             'WHERE RecipeID = '.$recipe_id;
      $query = $this->read_db->query($sql);
      $result = $query->result_array();
      
      foreach ($result AS $ingred)
      {
         $ingred['Phrase'] = str_replace(array('{prod1}','{prod2}'), array('',''), $ingred['Name']);
         if ($ingred['ProductOne'] != 0)
         {
            $sql = 'SELECT ProductName '.
                   'FROM pr_product '.
                   'WHERE ProductID = '.$ingred['ProductOne'];
            $query = $this->read_db->query($sql);
            $prod = $query->row_array();
            $ingred['Phrase'] .= ' '.$prod['ProductName'];
         }
         if ($ingred['ProductTwo'] != 0)
         {
            $sql = 'SELECT ProductName '.
                   'FROM pr_product '.
                   'WHERE ProductID = '.$ingred['ProductTwo'];
            $query = $this->read_db->query($sql);
            $prod = $query->row_array();
            $ingred['Phrase'] .= ' '.$prod['ProductName'];
         }
         $raw_text .= str_repeat(' '.$ingred['Phrase'], $this->title_weight);
      }

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
