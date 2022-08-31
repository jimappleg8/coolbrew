<?php

class Indexes_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   /**
    * Weighting factors for indexed parts
    */
   var $group_weight = 1;
   var $project_weight = 2;
   var $story_weight = 2;
   var $client_weight = 1;
   var $heat_weight = 1;
   var $notes_weight = 1;

   // --------------------------------------------------------------------

   function Indexes_model()
   {
      parent::Model();

      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   function get_index_records($project_id)
   {
      $sql = 'SELECT * '.
             'FROM project_index '.
             'WHERE ProjectID = '.$project_id;
      $query = $this->read_db->query($sql);
      $indexes = $query->result_array();
      
      return $indexes;
   }
 
   // --------------------------------------------------------------------

   function rebuild_index()
   {
      $sql = 'SELECT ID FROM projects';
      $query = $this->read_db->query($sql);
      $projects = $query->result_array();
      
      foreach ($projects AS $project)
      {
         $this->update_project_index($project['ID']);
      }
      
      $sql = 'SELECT ID, ProjectID FROM projects_story';
      $query = $this->read_db->query($sql);
      $stories = $query->result_array();
      
      foreach ($stories AS $story)
      {
         $this->update_story_index($story['ProjectID'], $story['ID']);
      }

      return TRUE;
   }
 
   // --------------------------------------------------------------------

   function delete_project_index_records($project_id)
   {
      $this->write_db->where('ProjectID', $project_id);
      $this->write_db->where('StoryID', 0);
      $this->write_db->delete('projects_index');
   }
 
   // --------------------------------------------------------------------

   function delete_story_index_records($story_id)
   {
      $this->write_db->where('StoryID', $story_id);
      $this->write_db->delete('projects_index');
   }
 
   // --------------------------------------------------------------------

   function update_project_index($project_id)
   {
      // delete existing search index entries about this project
      $this->delete_project_index_records($project_id);
      
      // create a new entry for each of the words of the project
      foreach ($this->get_project_words($project_id) as $word => $weight)
      {
         $index['ProjectID'] = $project_id;
         $index['StoryID'] = 0;
         $index['Word'] = $word;
         $index['Weight'] = $weight;
         $this->write_db->insert('projects_index', $index);
      }
   }
 
   // --------------------------------------------------------------------

   function update_story_index($project_id, $story_id)
   {
      // delete existing search index entries about this project
      $this->delete_story_index_records($story_id);
      
      // create a new entry for each of the words of the project
      foreach ($this->get_story_words($story_id) as $word => $weight)
      {
         $index['ProjectID'] = $project_id;
         $index['StoryID'] = $story_id;
         $index['Word'] = $word;
         $index['Weight'] = $weight;
         $this->write_db->insert('projects_index', $index);
      }
   }
 
   // --------------------------------------------------------------------

   function get_project_words($project_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Projects');
      
      $project = $this->CI->Projects->get_project_data($project_id);

       // weight the Group Name (domain) accordingly
      $raw_text = str_repeat(' '.strip_tags($project['GroupName']), $this->group_weight);
 
      // weight the Project Name accordingly
      $raw_text .= str_repeat(' '.strip_tags($project['ProjectName']), $this->project_weight);
 
      // stem the resulting phrase
      $stemmed_words = $this->stem_phrase($raw_text);

      // unique words with weight
      $words = array_count_values($stemmed_words);

      return $words;
   }

   // --------------------------------------------------------------------

   function get_story_words($story_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Stories');
      
      $story = $this->CI->Stories->get_story_data($story_id);

       // weight the Story Description accordingly
      $raw_text = str_repeat(' '.strip_tags($story['Description']), $this->story_weight);
 
     // weight the Question accordingly
      $raw_text .= str_repeat(' '.strip_tags($story['Client']), $this->client_weight);
 
      // weight the Answer(s) accordingly
      $raw_text .= str_repeat(' '.strip_tags($story['HeatID']), $this->heat_weight);

      // weight the Keywords accordingly
      $raw_text .= str_repeat(' '.strip_tags($story['Notes']), $this->notes_weight);

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
      $words = str_word_count(strtolower($phrase), 1, '0123456789');
      
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
         // we want to index HEAT ticket numbers
         if (is_numeric($word))
         {
            $stemmed_words[] = $word;
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
   
   // --------------------------------------------------------------------

   /**
    * Returns data for the specified Project/Story
    *
    * @access   public
    * @return   array
    */
   function get_project_data($project_id, $story_id)
   {
      if ($story_id != 0)  // result is a story
      {
         $sql = 'SELECT p.ID AS ProjectID, p.GroupName, p.ProjectName, s.* '.
                'FROM projects AS p, projects_story AS s '.
                'WHERE p.ID = s.ProjectID '.
                'AND p.ID = '.$project_id.' '.
                'AND s.ID = '.$story_id;
      }
      else  // result is a project only
      {
         $sql = 'SELECT p.ID AS ProjectID, p.GroupName, p.ProjectName '.
                'FROM projects AS p '.
                'WHERE p.ID = '.$project_id;
      }
      
      $query = $this->read_db->query($sql);
      $project = $query->row_array();
      
      return $project;
   }

   // --------------------------------------------------------------------

   /**
    * Performs search for projects and returns array of project results
    *
    * @access   public
    * @return   array
    */
   function search_projects($search, $exact = TRUE)
   {
      $project_list = array();
      $matches = array();
      $no_matches = FALSE;
      
      if ($search['Words'] != '')
      {
         $words = array_values($this->stem_phrase($search['Words']));
         $nb_words = count($words);
         
         // =============================
         //  first, find projects only
         // =============================

         // define the base query
         foreach ($words AS $word)
         {
            $sql_words[] = 'i.Word = "'.$word.'"';
         }
         $sql_wordlist = implode(' OR ', $sql_words);

         $sql = 'SELECT DISTINCT i.ProjectID, i.StoryID, COUNT(*) AS nb, '.
                  'SUM(Weight) AS total_weight '.
                'FROM projects_index AS i, projects AS p '.
                'WHERE i.ProjectID = p.ID '.
                'AND i.StoryID = 0 '.
                'AND ('.$sql_wordlist.') '.
                'GROUP BY i.ProjectID ';
         if ($exact)
         {
            $sql .= 'HAVING nb = '.$nb_words.' ';
         }
         $sql .= 'ORDER BY nb DESC, total_weight DESC';
         
         $query = $this->read_db->query($sql);
         $word_matches = $query->result_array();
         foreach ($word_matches AS $wm)
         {
            $matches[] = $wm['ProjectID'].'-'.$wm['StoryID'];
         }

         // =============================
         //  then, find stories
         // =============================

         // define the base query
         foreach ($words AS $word)
         {
            $sql_words[] = 'i.Word = "'.$word.'"';
         }
         $sql_wordlist = implode(' OR ', $sql_words);

         $sql = 'SELECT DISTINCT i.ProjectID, i.StoryID, COUNT(*) AS nb, '.
                  'SUM(Weight) AS total_weight '.
                'FROM projects_index AS i, projects AS p, projects_story AS s '.
                'WHERE i.ProjectID = p.ID '.
                'AND p.ID = s.ProjectID '.
                'AND s.ID = i.StoryID '.
                'AND ('.$sql_wordlist.') '.
                'GROUP BY i.ProjectID, i.StoryID ';
         if ($exact)
         {
            $sql .= 'HAVING nb = '.$nb_words.' ';
         }
         $sql .= 'ORDER BY nb DESC, total_weight DESC';

         $query = $this->read_db->query($sql);
         $word_matches = $query->result_array();
         foreach ($word_matches AS $wm)
         {
            $matches[] = $wm['ProjectID'].'-'.$wm['StoryID'];
         }
      }
      
      if (empty($matches))
      {
         $no_matches = TRUE;
      }
         
      // once we have our matches, 
/*      if ($exact && $no_matches == FALSE)
      {
         for ($i=0; $i<count($matches); $i++)
         {
            if ($i == 0)
            {
               $project_list = $matches[$i];
            }
            else
            {
               $project_list = array_intersect($matches[$i], $project_list);
            }
         }
      }
      elseif ( ! $exact)
      {
         foreach ($matches AS $match_array)
         {
            $project_list = array_merge($match_array, $project_list);
         }
      }
*/
      $project_list = $matches;
      
      // get the projects themselves
      $projects = array();
      foreach ($project_list AS $key => $item)
      {
         list($project_id, $story_id) = explode('-', $item);
         $projects[] = $this->get_project_data($project_id, $story_id);
      }

      return $projects;
   }


}

?>
