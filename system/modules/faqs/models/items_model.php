<?php

class Items_model extends Model {

   var $CI;

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   // --------------------------------------------------------------------

   function Items_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns array of active faqs for the specified FAQ Code
    *
    * @access   public
    * @return   array
    */
   function get_faqs_by_code($faq_code, $site_id = '')
   {
      $site_id = ($site_id == '') ? SITE_ID : $site_id;

      $sql = 'SELECT c.ID AS CategoryID, c.SiteID, c.FaqCode, '.
               'c.Name AS CategoryName, f.ID AS FaqID, f.ShortQuestion, '.
               'f.Question, f.FlagAsNew, f.Status, f.Sort, f.Keywords, '.
               'a.Answer, a.ID AS AnswerID '.
             'FROM faqs_category AS c, faqs_item AS f, '.
               'faqs_item_category AS fc, faqs_answer AS a '.
             'WHERE c.ID = fc.CategoryID '.
             'AND f.ID = fc.FaqID '.
             'AND a.ID = fc.AnswerID '.
             'AND c.SiteID = "'.$site_id.'" '.
             'AND c.FaqCode = "'.$faq_code.'" '.
             'AND f.Status = "active" '.
             'ORDER BY f.Sort';
             
      $query = $this->read_db->query($sql);
      $faqs = $query->result_array();

      return $faqs;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified faq ID
    *
    * @access   public
    * @return   array
    */
   function get_faq_record($faq_id)
   {
      $sql = 'SELECT * FROM faqs_item '.
             'WHERE ID = '.$faq_id;

      $query = $this->read_db->query($sql);
      $faq = $query->row_array();

      return $faq;
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
      $sql = 'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
               'f.Status, f.Sort, f.Keywords, a.Answer, a.ID AS AnswerID '.
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
      $sql =  'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                 'fc.CategoryID, f.Status, a.Answer, a.ID AS AnswerID '.
              'FROM faqs_site AS s '.
              'INNER JOIN faqs_item AS f '.
                'ON s.FaqID = f.ID '.
              'INNER JOIN faqs_answer AS a '.
                'ON a.ID = s.AnswerID '.
              'INNER JOIN faqs_item_category AS fc '.
                'ON fc.FaqID = f.ID '.
                'AND fc.AnswerID = s.AnswerID '.
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
    * Returns a multi-dimensional list of product categories, products 
    *   and the FAQ records assigned to them.
    *
    * @access   public
    * @param    string    The site ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_product_faqs_in_site($site_id, $include_pending = TRUE)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Products');

      // 1. get a list of all products for the site
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
             'p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p '.
               'JOIN pr_product_site AS ps ON p.ProductID = ps.ProductID '.
             'WHERE ps.SiteID = "'.$site_id.'" '.
             'AND ( '.
               'p.Status = "active" '.
               'OR p.Status = "partial" '.
               'OR p.Status = "pending" '.
             ') '.
             'ORDER BY p.ProductName ASC , p.ProductGroup DESC, p.PackageSize ASC';

      $query = $this->read_db->query($sql);
      $products = $query->result_array();
      
      // get a list of FAQs that are assigned to products
      $sql =  'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                 'ip.ProductID, f.Status, '.
                 'a.Answer, a.ID AS AnswerID '.
              'FROM faqs_site AS s '.
              'INNER JOIN faqs_item AS f '.
                'ON s.FaqID = f.ID '.
              'INNER JOIN faqs_answer AS a '.
                'ON a.ID = s.AnswerID '.
              'INNER JOIN faqs_item_product AS ip '.
                'ON ip.FaqID = f.ID '.
                'AND ip.AnswerID = s.AnswerID '.
              'WHERE s.SiteID = "'.$site_id.'" '.
              'AND (f.Status = "active"';
      $sql .= ($include_pending == TRUE) ? ' OR f.Status = "pending") ': ') ';
      $sql .= 'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $faq_list = $query->result_array();

      // create a products lookup array and attach FAQs
      $prod_lookup = array();
      for ($i=0, $num=count($products); $i<$num; $i++)
      {
         $products[$i]['FAQs'] = array();

         foreach ($faq_list AS $faq)
         {
            if ($faq['ProductID'] == $products[$i]['ProductID'])
            {
               $products[$i]['FAQs'][] = $faq;
            }
         }
         
         $prod_lookup[$products[$i]['ProductID']] = $products[$i];
      }

      // 2. get a list of all categories
      $categories = $this->CI->Products->get_category_tree($site_id);
      
      // remove the root node
      array_shift($categories);
      
      // get a list of FAQs that are assigned to product categories
      $sql =  'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                 'ipc.CategoryID, f.Status, '.
                 'a.Answer, a.ID AS AnswerID '.
              'FROM faqs_site AS s '.
              'INNER JOIN faqs_item AS f '.
                'ON s.FaqID = f.ID '.
              'INNER JOIN faqs_answer AS a '.
                'ON a.ID = s.AnswerID '.
              'INNER JOIN faqs_item_product_category AS ipc '.
                'ON ipc.FaqID = f.ID '.
                'AND ipc.AnswerID = s.AnswerID '.
              'WHERE s.SiteID = "'.$site_id.'" '.
              'AND (f.Status = "active"';
      $sql .= ($include_pending == TRUE) ? ' OR f.Status = "pending") ': ') ';
      $sql .= 'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $faq_list = $query->result_array();

      // create a categories lookup array, and indicate links
      $cat_lookup = array();
      for ($i=0, $num=count($categories); $i<$num; $i++)
      {
         $cat_lookup[$categories[$i]['CategoryID']] = $i;
         
         // define the product and FAQ arrays
         $categories[$i]['Products'] = array();
         $categories[$i]['FAQs'] = array();

         foreach ($faq_list AS $faq)
         {
            if ($faq['CategoryID'] == $categories[$i]['CategoryID'])
            {
               $categories[$i]['FAQs'][] = $faq;
            }
         }
      }

      // 3. get a list that associates categories with products
      $sql = 'SELECT pc.ProductID, pc.CategoryID '.
             'FROM pr_product_category AS pc '.
               'JOIN pr_category AS c ON pc.CategoryID = c.CategoryID '.
             'WHERE c.SiteID = "'.$site_id.'"';
   
      $query = $this->read_db->query($sql);
      $prod_cats = $query->result_array();

      // and join the data sets together
      foreach ($prod_cats AS $item)
      {
         $categories[$cat_lookup[$item['CategoryID']]]['Products'] = array();
         if (isset($prod_lookup[$item['ProductID']]))
         {
            $categories[$cat_lookup[$item['CategoryID']]]['Products'][] = $prod_lookup[$item['ProductID']];
         }
      }
      
      // mark the items that should be displayed
      for ($i=0, $num=count($categories); $i<$num; $i++)
      {
         $categories[$i]['display'] = ( ! empty($categories[$i]['FAQs'])) ? TRUE : FALSE;

         for ($j=0, $num=count($categories[$i]['Products']); $j<$num; $j++)
         {
            if ( ! empty($categories[$i]['Products'][$j]['FAQs']))
            {
               $categories[$i]['Products'][$j]['display'] = TRUE;
               $categories[$i]['display'] = TRUE;
               // loop backward through the categories and get all parents
            }
         }
      }
      
//      echo '<pre>'; print_r($categories); echo '</pre>'; exit;

      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of FAQ records for all faqs in $site_id that
    *   are NOT assigned to a category, product or product category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_nocat_faqs_in_site($site_id, $include_pending = TRUE)
   {
      $sql =  'SELECT f.ID, f.ShortQuestion, f.Question, f.FlagAsNew, '.
                 'fc.CategoryID, ip.ProductID, '.
                 'ipc.CategoryID AS ProductCategoryID, f.Status, '.
                 'a.Answer, a.ID AS AnswerID '.
              'FROM faqs_site AS s '.
              'INNER JOIN faqs_item AS f '.
                'ON s.FaqID = f.ID '.
              'INNER JOIN faqs_answer AS a '.
                'ON a.ID = s.AnswerID '.
              'LEFT JOIN faqs_item_category AS fc '.
                'ON fc.FaqID = f.ID '.
                'AND fc.AnswerID = s.AnswerID '.
              'LEFT JOIN faqs_item_product AS ip '.
                'ON ip.FaqID = f.ID '.
                'AND ip.AnswerID = s.AnswerID '.
              'LEFT JOIN faqs_item_product_category AS ipc '.
                'ON ipc.FaqID = f.ID '.
                'AND ipc.AnswerID = s.AnswerID '.
              'WHERE s.SiteID = "'.$site_id.'" '.
              'AND (fc.CategoryID IS NULL '.
                'AND ip.ProductID IS NULL '.
                'AND ipc.CategoryID IS NULL) '.
              'AND (f.Status = "active"';
      $sql .= ($include_pending == TRUE) ? ' OR f.Status = "pending") ': ') ';
      $sql .= 'ORDER BY f.Sort ASC';

      $query = $this->read_db->query($sql);
      $nocat_list = $query->result_array();

//      echo "<pre>"; print_r($nocat_list); echo "</pre>";

      return $nocat_list;
   }

   // --------------------------------------------------------------------
   
   /**
    * Creates a new FAQ record
    *
    * This is for site-specific FAQs and not shared ones.
    *
    * @access   public
    * @param    array     The values to be inserted
    * $param    string    the site ID
    * @return   boolean
    */
   function insert_faq($values)
   {
      $site['SiteID'] = $values['SiteID'];
      unset($values['SiteID']);
      
      $answer['Answer'] = $values['Answer'];
      $answer['Note'] = $values['Note'];
      unset($values['Answer']);
      unset($values['Note']);
      
      // first, insert the main FAQ record
      $this->write_db->insert('faqs_item', $values);
      $faq_id = $this->write_db->insert_id();
      
      // next, insert the answer record
      $answer['CreatedDate'] = $values['CreatedDate'];
      $answer['CreatedBy'] = $values['CreatedBy'];
      $this->write_db->insert('faqs_answer', $answer);
      $answer_id = $this->write_db->insert_id();
      
      // finally, insert the site record
      $site['FaqID'] = $faq_id;
      $site['AnswerID'] = $answer_id;
      $this->write_db->insert('faqs_site', $site);
      
      return $faq_id;
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
      $this->CI =& get_instance();
      
      $this->CI->load->model('Answers');

      $site_id = $values['SiteID'];
      unset($values['SiteID']);
      
      $answer['Answer'] = $values['Answer'];
      unset($values['Answer']);

      // get the AnswerID
      $answer_ids = $this->CI->Answers->get_answer_ids($faq_id);
      $answer_id = $answer_ids[0];
      
      // first, update the main FAQ record
      $this->write_db->where('ID', $faq_id);
      $this->write_db->update('faqs_item', $values);
      
      // then update the answer record
      $answer['RevisedDate'] = $values['RevisedDate'];
      $answer['RevisedBy'] = $values['RevisedBy'];
      $this->write_db->where('ID', $answer_id);
      $this->write_db->update('faqs_answer', $answer);

      return TRUE;
   }

   // --------------------------------------------------------------------

   /**
    * Deletes the specified faq ID
    *
    * This will actually delete the entire FAQ and all its answers.
    * To remove a shared FAQ from a specific site, use a different function.
    *
    * @access   public
    * @return   boolean
    */
   function delete_faq($faq_id)
   {
      // delete all references to this category in faqs_item_category
      $this->write_db->where('FaqID', $faq_id);
      $this->write_db->delete('faqs_item_category');
      
      // remove all associated answers
      $sql = 'DELETE a '.
             'FROM faqs_answer AS a, faqs_site AS s '.
             'WHERE s.FaqID = '.$faq_id.' '.
             'AND s.AnswerID = a.ID';
      $this->write_db->query($sql);

      // delete all references to this category in faqs_site
      $this->write_db->where('FaqID', $faq_id);
      $this->write_db->delete('faqs_site');

      // delete the index for this FAQ
      $this->write_db->where('FaqID', $faq_id);
      $this->write_db->delete('faqs_index');

      // delete the actual FAQ record
      $this->write_db->where('ID', $faq_id);
      $this->write_db->delete('faqs_item');

      return TRUE;
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
      
      $this->CI->load->model('Indexes');

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

?>