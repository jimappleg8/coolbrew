<?php

class Products_Tags extends Controller {

   function Products_Tags()
   {
      parent::Controller();
      $this->load->helper('url');
   }
   
   /**
    * Displays detailed information about a product
    *
    * NOTE: this function does not check to see if the requested product
    * is discontinued. It returns the "status" field, however, and the
    * product_detail template can check the status and respond accordingly.
    *
    */
   function detail($cat_id, $prod_id = '')
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "detail");
      
      // (boolean) whether to return the results rather than echo them
      $return = $this->tag->param(3, FALSE);

      // check and see if there were two parameters. If not, assume that 
      // the supplied variable is the product ID
      if ($prod_id == '')
      {
         $prod_id = $cat_id;
         $cat_id = '';
      }
      
      $this->load->helper('number');
      $this->load->model('Categories');
      $this->load->model('Products');
      
      // convert the product code to an ID number
      if ( ! is_numeric($prod_id))
      {
         $prod_id = $this->Products->get_product_id_by_code($prod_id, $site_id);
      }
      $product = $this->Products->get_product_data($prod_id, $site_id);

      // parse the ingredient list and added links
      $this->load->model ('Ingredients');
//      $product['LinkedIngredients'] = $this->Ingredients->get_product_ingredient_data($site_id, $product['Ingredients']);
      $product['Ingredients'] = $this->Ingredients->clean_ingredients($product['Ingredients']);

      if ($cat_id == '')
      {
         // if the category isn't supplied, assume there is only one
         $category = $this->Categories->get_first_category($prod_id, $site_id);
      }
      else
      {
         $category = $this->Categories->get_category_data($cat_id, $site_id);
      }

      $nutfacts = $this->Products->nutrition_facts($prod_id);
      $nlea = $this->Products->get_nlea_data($prod_id);
      
      $data['product'] = $product;
      $data['nutfacts'] = $nutfacts;
      $data['nlea'] = $nlea;
      if ( ! empty($category))
      {
         $data['category'] = $category;
      }
   	
      if ($return) {
         return $this->load->view($tpl, $data, TRUE);
      }
      else
      {
         echo $this->load->view($tpl, $data, TRUE);
      }
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Displays a product's nutrition facts
    *
    */
   function nutrition_facts($cat_id, $prod_id = '')
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // check and see if there were two parameters. If not, assume that 
      // the supplied variable is the product ID
      if ($prod_id == '')
      {
         $prod_id = $cat_id;
         $cat_id = '';
      }
      
      $this->load->model('Products');
      
      // convert the product code to an ID number
      if ( ! is_numeric($prod_id))
      {
         $prod_id = $this->Products->get_product_id_by_code($prod_id, $site_id);
      }

      echo $this->Products->nutrition_facts($prod_id);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Displays a product's nutrition facts
    *
    */
   function caffeine_meter($prod_id)
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      $this->load->model('Products');
      
      // convert the product code to an ID number
      if ( ! is_numeric($prod_id))
      {
         $prod_id = $this->Products->get_product_id_by_code($prod_id, $site_id);
      }

      $data['product'] = $this->Products->get_product_data($prod_id, $site_id);

      echo $this->load->view('caffeine_meter', $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Returns the meta data for a given product
    *
    */
   function metadata($prod_id)
   {
      $this->load->model('Products');
      
      return $this->Products->get_product_metadata($prod_id);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Returns the data for a random product
    *
    */
   function random_product()
   {
      // (string) The boolean field by which to limit the selection
      $bool_field = $this->tag->param(1, 'none');

      // (string) The category by which to limit the selection
      $cat_id = $this->tag->param(2, 0);
      
      // (string) The site ID
      $site_id = $this->tag->param(3, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(4, "random_product");
            
      $this->load->model('Categories');
      $this->load->model('Products');

      if ($cat_id == 0)
      {
         if ($bool_field == "none")
         {
            $product_list = $this->Products->get_prod_ids_in_site($site_id);
         }
         else
         {
            $product_list = $this->Products->get_prod_ids_per_field($bool_field, $site_id);
         }
      }
      else
      {
         $product_list = $this->Categories->get_prod_ids_in_category($cat_id);
      }
      
      // random code taken from The PHP Cookbook, p.469
      $m = 1000000;
      $prod_id = $product_list[((mt_rand(1,$m * count($product_list))-1)/$m)];
      
      $product = $this->Products->get_product_data($prod_id, $site_id);
      $category = $this->Categories->get_first_category($prod_id, $site_id);
      
   //   echo "<pre>"; print_r($product); echo "</pre>";

      $data['product'] = $product;
      $data['category'] = $category;
   	
      echo $this->load->view($tpl, $data, TRUE);

   }
   
   //-------------------------------------------------------------------------
   
   /**
    * generates a list of products based on their category
    *
    */
   function category_list()
   {
      // (int) The category ID
      $cat_id = $this->tag->param(1, '');

      // (string) The site ID
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(3, "category_list");

      if ($cat_id == '')
      {
         // the category ID is being passed via the URL
         $cat_id = $this->uri->segment(3);
      }
      
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->model('Sites');

      $category = $this->Categories->get_category_data($cat_id, $site_id);
      
      $prod_list = $this->Categories->get_prod_ids_in_category($category['CategoryID']);
      
      // go through the list and build data structure
      $count = 0;
      $items = array();
      foreach ($prod_list as $prod_id) {
         $items[$count] = $this->Products->get_product_data($prod_id, $site_id);
         $count++;
      }
      
      foreach($items as $item) {
         $sortAux[] = $item['SortOrder'];
      }
      array_multisort($sortAux, SORT_ASC, $items);
   
      $data['site'] = $this->Sites->get_site_data($site_id);
      $data['site']['BaseURL'] = 'http://'.$data['site']['Domain'];

      $data['category'] = $category;
      $data['items'] = $items;
   	
      echo $this->load->view($tpl, $data, TRUE);

   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Displays a list of other products in the same category as the 
    * specified product.
    *
    */
   function related_list($cat_id, $prod_id = '')
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "related_list");
      
      // check and see if there were two parameters. If not, assume that 
      // the supplied variable is the product ID
      if ($prod_id == '')
      {
         $prod_id = $cat_id;
         $cat_id = '';
      }
      
      $this->load->model('Categories');
      $this->load->model('Products');
      
      // convert the product code to an ID number
      if ( ! is_numeric($prod_id))
      {
         $prod_id = $this->Products->get_product_id_by_code($prod_id, $site_id);
      }
      $product = $this->Products->get_product_data($prod_id, $site_id);

      if ($cat_id == '')
      {
         // if the category isn't supplied, assume there is only one
         $category = $this->Categories->get_first_category($prod_id, $site_id);
      }
      else
      {
         $category = $this->Categories->get_category_data($cat_id, $site_id);
      }
      
      $prod_list = $this->Categories->get_prod_ids_in_category($category['CategoryID']);
      
      // go through the list and build data structure
      $count = 0;
      foreach ($prod_list as $prod_id)
      {
         if ($prod_id != $product['ProductID'])
         {
            $items[$count] = $this->Products->get_product_data($prod_id, $site_id);
            $count++;
         }
      }
      
      if ($items[0]['SortOrder'] > 0) {
         foreach($items as $item) {
            $sortAux[] = $item['SortOrder'];
         }
         array_multisort($sortAux, SORT_ASC, $items);
      }
   
//      echo "<pre>"; print_r($items); echo "</pre>";
      
      $data['product'] = $product;
      $data['category'] = $category;
      $data['items'] = $items;
   	
      echo $this->load->view($tpl, $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Displays a category page divided by the category's sub-categories.
    *
    */
   function sub_category_list($cat_code)
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "sub_category_list");
      
      $this->load->model('Categories');
      $this->load->model('Products');
      $this->load->database('read');
      
      // -----------------------------------------------------
      
      $root = $this->Categories->get_category_id($cat_code, $site_id);

      // retrieve the left and right value of the $root node
      $sql = 'SELECT Lft, Rgt FROM pr_category '.
             'WHERE CategoryID = '.$root;
      $query = $this->db->query($sql);
      $row = $query->row_array();

      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      // excluding any inactive categories.
      $sql = 'SELECT * '.
             'FROM pr_category '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'AND Status LIKE "active" '.
             'ORDER BY Lft ASC';
      $query = $this->db->query($sql);
      $cat_list = $query->result_array();

      // assign a level to each result
      for ($i=0; $i<count($cat_list); $i++)
      {
         // only check stack if there is one
         if (count($right) > 0)
         {
            // check if we should remove a node from the stack
            while ($right[count($right) - 1] < $cat_list[$i]['Rgt'])
            {
               array_pop($right);
            }
         }
         $cat_list[$i]['level'] = count($right);

         // add this node to the stack
         $right[] = $cat_list[$i]['Rgt'];
      }

      // -----------------------------------------------------
   
      if (count($cat_list) == 0)
      {
         echo "This is an inactive category.";
      }
   
      // get all data for all products assigned to each category
      $count = 0;
      $max_level = 0;
      for ($i=1; $i<count($cat_list); $i++)
      {
         $prod_list = $this->Categories->get_prod_ids_in_category($cat_list[$i]['CategoryID']);
   
         // go through the list and build data structure
         foreach ($prod_list as $prod_id) {
            $items[$count] = $this->Products->get_product_data($prod_id);
            $items[$count]['CatID'] = $cat_list[$i]['CategoryID'];
            if ($cat_list[$i]['level'] > $max_level)
            {
               $max_level = $cat_list[$i]['level'];
            }
            $count++;
         }
      }
   
//      echo "<pre>"; print_r($items); echo "</pre>";
   
      $data['max_level'] = $max_level;
      $data['cat_list'] = $cat_list;
      $data['items'] = $items;
   	
      echo $this->load->view($tpl, $data, TRUE);   
   }
   
   //-------------------------------------------------------------------------
   
   
   /**
    * Shows a list of all categories and the products assigned to them. There
    *   is a mechanism to exclude some categories if desired. Categories are
    *   defined by their category code.
    */
   function all_category_list()
   {      
      // (array) list of categories not to include in primary list
      $cat_list = $this->tag->param(2, array());
      
      // (string) The site ID if you want to list products from a different site
      $site_id = $this->tag->param(3, SITE_ID);

      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(4, "all_category_list");
      
      $this->load->model('Categories');
      $this->load->model('Products');
      
      // the first time, rebuild the tree
      $root = $this->Categories->get_category_root($site_id);
      $this->Categories->rebuild_tree($site_id, $root, 1);

      $category_list = $this->Categories->get_category_tree($site_id);

      $product_list = $this->Products->get_products_in_site($site_id);
   
      $data['product_list'] = $product_list;
      $data['category_list'] = $category_list;
      
      $this->load->vars($data);
   	
      return $this->load->view($tpl, NULL, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Displays a list of ingredient items.
    * 
    */
   function ingredient_list()
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "ingredient_list");
      
      $this->load->model('Ingredients');
      
      $this->collector->prepend_css_file('products-tags');
      
      $ingredients = $this->Ingredients->get_ingredient_list($site_id);

      $data['ingredients'] = $ingredients;

      return $this->load->view($tpl, $data, TRUE);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Displays ingredient detail
    * 
    */
   function ingredient_detail($ingredient_code)
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(2, "ingredient_detail");
      
      $this->load->model('Ingredients');
      
      $this->collector->prepend_css_file('products-tags');
      
      $ingredient = $this->Ingredients->get_ingredient_data_by_code($site_id, $ingredient_code);

      $data['ingredient'] = $ingredient;

      return $this->load->view($tpl, $data, TRUE);
   }

   //-------------------------------------------------------------------------
   
   /**
    * Converts category id string to displayable title
    * 
    * @params  string  the category name
    */
   function category_id_title($cat_id)
   {
      return ucwords(str_replace("_", " ", $cat_id));
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Returns the brand name given a site ID. If no siteID is given, it
    * uses the current site.
    * 
    */
   function get_brand_name()
   {
      $site_id = $this->tag->param(1, SITE_ID);
      
      $this->load->model('Sites');
      
      return $this->Sites->get_brand_name($site_id);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Returns an array containing the category's immediate parent and 
    *  siblings for use in a menu.
    * 
    */
   function get_category_family()
   {
      // (int or string) The category ID or Code
      $cat_id = $this->tag->param(1, '');

      $site_id = $this->tag->param(2, SITE_ID);
      
      $this->load->model('Categories');
      
      return $this->Categories->get_immediate_family($cat_id, $site_id);
   }
   
   //-------------------------------------------------------------------------
   
   /**
    * Returns the page data for a particular product page
    *
    * This is equivalent to the pages.page_info tag, but it pulls the 
    * data from the products database.
    * 
    */
   function page_info($cat_code, $prod_id = '')
   {
      // (string) The site ID
      $site_id = $this->tag->param(1, SITE_ID);
      
      // check and see if there were two parameters. If not, assume that 
      // the supplied variable is the product ID
      if ($prod_id == '')
      {
         $prod_id = $cat_code;
         $cat_code = '';
      }
      
      $this->load->model('Categories');
      $this->load->model('Products');
      
      // convert the product code to an ID number
      if ( ! is_numeric($prod_id))
      {
         $prod_id = $this->Products->get_product_id_by_code($prod_id, $site_id);
      }
      $page = $this->Products->get_product_page_info($prod_id, $site_id);


      if ($cat_code == '')
      {
         // if the category isn't supplied, assume there is only one
         $category = $this->Categories->get_first_category($prod_id, $site_id);
      }
      else
      {
         $category = $this->Categories->get_category_data($cat_code, $site_id);
      }
      $cat_code = $category['CategoryCode'];

      $path = $this->Categories->get_category_page_path($cat_code, $site_id);

      $next = count($path);
      
      $path[$next]['PageName'] = $page['PageName'];
      $path[$next]['URL'] = '';
      $path[$next]['MenuText'] = $page['MenuText'];
      
      $results= $page;
      $results['Path'] = $path;

      return $results;

   }

  
}
   ?>
