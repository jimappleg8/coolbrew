<?php

class Faqs_Tags extends Controller {

	function Faqs_Tags()
	{
		parent::Controller();
		$this->load->helper(array('text','typography','url'));
	}
	
   //-------------------------------------------------------------------------
   
   /**
    * Generates a list of FAQs for the given FAQ code (or category).
    * 
    * Supplies information to the template that allows the template designer
    *   to create either links to individual FAQ pages or a complete list
    *   of FAQs with anchor links for the same page.
    *
    */
   function faq_list() 
   {
      // (string) The faq code
      $faq_code = $this->tag->param(1);
      
      // (string) The site ID
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(3, "faq_list");
      
      $this->load->model('Items');
      
      $faqs = $this->Items->get_faqs_by_code($faq_code, $site_id);
      
      // make sure ShortQuestion has a value
      for ($i=0, $total_faqs = count($faqs); $i<$total_faqs; $i++)
      {
         if (trim($faqs[$i]['ShortQuestion']) == '')
         {
            $faqs[$i]['ShortQuestion'] = $faqs[$i]['Question'];
         }
      }

      $data['faqs'] = $faqs;
            
      echo $this->load->view($tpl, $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Generates a list of all FAQs for the given Site ID.
    * 
    * Supplies information to the template that allows the template designer
    *   to create either links to individual FAQ pages or a complete list
    *   of FAQs with anchor links for the same page.
    *
    */
   function multicategory_list() 
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "multicategory_list");
      
      $this->load->helper('text');
      $this->load->model('Sites');
      $this->load->model('Categories');
      $this->load->model('Items');
      $this->load->model('Shared');
      
      $category_list = $this->Categories->get_category_tree($site_id);

      $category = array();
      $faq_list = $this->Items->get_faqs_in_site($site_id);
      
      // make sure ShortQuestion has a value
      for ($i=0, $total_faqs=count($faq_list); $i<$total_faqs; $i++)
      {
         if (trim($faq_list[$i]['ShortQuestion']) == '')
         {
            $faq_list[$i]['ShortQuestion'] = $faq_list[$i]['Question'];
         }
      }
      
//      echo '<pre>'; print_r($faq_list); echo '</pre>'; exit;

      $data['site_id'] = $site_id;
      $data['category_list'] = $category_list;
      $data['faq_list'] = $faq_list;
//      $data['shared'] = $this->Shared->get_shared_faqs_lookup($site_id);
      
      echo $this->load->view($tpl, $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * FAQ detail page
    *
    */
   function detail($faq_id, $answer_id) 
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "detail");

      $this->load->model('Items');
      
      $faq = $this->Items->get_faq_data($faq_id, $answer_id);
      
      // make sure ShortQuestion has a value
      if (trim($faq['ShortQuestion']) == '')
      {
         $faq['ShortQuestion'] = $faq['Question'];
      }

      $data['faq'] = $faq;
      
      echo $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * generates an FAQs search form
    */
   function search()
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);

      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "search");

      // (string) the results template name
      $result_tpl = $this->tag->param(3, 'search_results');

      // (string) the results template name
      $action = $this->tag->param(4, 'search.php');

      // (string) the number of days to pull popular searches
      $pop_days = $this->tag->param(5, 0);

      // (string) the number of popular searches to list
      $pop_limit = $this->tag->param(6, 5);

      // (string) the template file to use with popular searches
      $pop_tpl = $this->tag->param(7, 'popular_searches');

      $this->load->helper('form');
      $this->load->library('validation');

      $rules['Words'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Words'] = 'Words';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      $this->collector->prepend_css_file('faqs-tags');
      
      $data['popular_searches'] = $this->popular_searches(array('site_id' => $site_id, 'days' => $pop_days, 'limit' => $pop_limit, 'tpl' => $pop_tpl, 'action' => $action));
      
      $data['action'] = $action;
      
      if ($this->validation->run() == FALSE)
      {
         return $this->load->view($tpl, $data, TRUE);
      }
      else
      {
         $data['search']['Words'] = $this->input->post('Words');

         $data['faqs'] = $this->_search($site_id);

         return $this->load->view($result_tpl, $data, TRUE);
      }
   }

   //-------------------------------------------------------------------------

   /**
    * processes the search form results
    */
   function _search($site_id)
   {
      $this->load->model('Items');
      $this->load->model('Keywords');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $search[$key] = $this->input->post($key);

//      $exact = (isset($search['Exact'])) ? TRUE : FALSE;
      $exact = TRUE;
       
      $faqs = $this->Items->search_faqs($search, $site_id, $exact);

      // make sure each ShortQuestion has a value
      for ($i=0, $total_faqs = count($faqs); $i<$total_faqs; $i++)
      {
         if (trim($faqs[$i]['ShortQuestion']) == '')
         {
            $faqs[$i]['ShortQuestion'] = $faqs[$i]['Question'];
         }
      }

      // add search request to keywords database
      $this->Keywords->insert_keywords($site_id, $search['Words'], $faqs);

      return $faqs;
   }

   //-------------------------------------------------------------------------

   /**
    * Display a list of the most popular searches made to date
    */
   function popular_searches($params = array())
   {
      // I want to be able to call this function as a local function
      // so I needed some way to pass along the tag variables...
      if (empty($params))
      {
         // (string) The site ID
         $site_id = $this->tag->param(1, SITE_ID);

         // (integer) The number of days back to go (0 = all results)
         $days = $this->tag->param(2, 0);

         // (integer) The number of search strings to return
         $limit = $this->tag->param(3, 5);

         // (string) The view name in case we want to override the default
         $tpl = $this->tag->param(4, 'popular_searches');

         // (string) the results template name
         $action = $this->tag->param(5, 'search.php');

         // the styles are only needed if this is a stand-alone tag
         $this->collector->prepend_css_file('faqs-tags');
      }
      else
      {
         $site_id = (isset($params['site_id'])) ? $params['site_id'] : SITE_ID;
         $days = (isset($params['days'])) ? $params['days'] : 0;
         $limit = (isset($params['limit'])) ? $params['limit'] : 5;
         $tpl = (isset($params['tpl'])) ? $params['tpl'] : 'popular_searches';
         $action = (isset($params['tpl'])) ? $params['action'] : 'search.php';
      }

      $this->load->model('Keywords');

      $data['action'] = $action;
      $data['searches'] = $this->Keywords->get_popular_searches($site_id, $days, $limit);
      
      return $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * Display a list of randomly choosen FAQs from the site.
    */
   function random_faqs()
   {
      // (string) The number of FAQs to return
      $quantity = $this->tag->param(1, 3);

      // (string) The site ID
      $site_id = $this->tag->param(2, SITE_ID);

      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(3, "search");

   }
   
}
?>