<?php

class Products_model extends Model {

   var $CI;

   var $read_db;      // database object for reading
   var $write_db;     // database object for writing
   var $hcg_write_db; // old hcgPublic database for writing

   // --------------------------------------------------------------------

   function Products_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
      $this->hcg_write_db = $this->load->database('hcg_write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of product category records and their associated products
    *  including a generated field indicating whether a particular product/
    * category combination has been added to the site.
    *
    * @access   public
    * @param    string    The site ID
    * @return   array
    */
   function get_products_tree($site_id, $faq_id, $answer_id)
   {
      $this->CI =& get_instance();

      $this->CI->load->model('Item_product');
      $this->CI->load->model('Item_product_category');

      // 1. get a list of all categories
      $categories = $this->get_category_tree($site_id);
      
      // remove the root node
      array_shift($categories);
      
      // get a list of product categories for this site that are linked
      $cat_links = $this->CI->Item_product_category->get_all_category_links($faq_id, $answer_id);

      // create a categories lookup array, and indicate links
      $cat_lookup = array();
      for ($i=0, $numcats=count($categories); $i<$numcats; $i++)
      {
         $cat_lookup[$categories[$i]['CategoryID']] = $i;
         
         // define the products array
         $categories[$i]['Products'] = array();

         if (in_array($categories[$i]['CategoryID'], $cat_links))
         {
            $categories[$i]['Assigned'] = 1;
         }
         else
         {
            $categories[$i]['Assigned'] = 0;
         }
      }

      // 2. get a list of all products for the site
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
      
      // get a list of products for this site that are linked
      $prod_links = $this->CI->Item_product->get_all_product_links($faq_id, $answer_id);

      // create a products lookup array and indicate links
      $prod_lookup = array();
      foreach ($products AS $product)
      {
         $prod_lookup[$product['ProductID']] = $product;

         if (in_array($product['ProductID'], $prod_links))
         {
            $prod_lookup[$product['ProductID']]['Assigned'] = 1;
         }
         else
         {
            $prod_lookup[$product['ProductID']]['Assigned'] = 0;
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
         if (isset($prod_lookup[$item['ProductID']]))
         {
            $categories[$cat_lookup[$item['CategoryID']]]['Products'][] = $prod_lookup[$item['ProductID']];
         }
      }
      
//      echo '<pre>'; print_r($categories); echo '</pre>'; exit;

      return $categories;
   }

   // --------------------------------------------------------------------

   /**
    * Returns a list of Product records for all products in $site_id that
    *   are assigned to the specified category.
    *
    * @access   public
    * @param    string    The site ID
    * @param    string    The category ID
    * @param    bool      Whether to include products with a status of pending
    * @return   array
    */
   function get_products_in_category($site_id, $category_id, $include_pending = TRUE)
   {
      $sql = 'SELECT p.ProductID, p.ProductName, p.ProductGroup, '.
                'c.CategoryID, p.PackageSize, p.UPC, p.Status '.
             'FROM pr_product AS p, pr_category AS c, '.
                'pr_product_category AS pc '.
             'WHERE c.SiteID = "'.$site_id.'" '.
             'AND pc.CategoryID = c.CategoryID '.
             'AND pc.CategoryID = '.$category_id.' '.
             'AND pc.ProductID = p.ProductID '.
             'AND c.Status LIKE "active" '.
             'AND (p.Status = "active" '.
             'OR p.Status = "partial"';
      $sql .= ($include_pending == TRUE) ? ' OR p.Status = "pending")': ')';
      $sql .= "ORDER BY p.ProductName ASC, p.ProductGroup DESC, p.PackageSize ASC";

      $query = $this->read_db->query($sql);
      $product_list = $query->result_array();
   
      return $product_list;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of the product categories tree
    *
    * @access   public
    * @param    int      The product ID
    * @return   array
    */
   function get_category_tree($site_id, $cat_code = 'root')
   {      
      // retrieve the left and right value of the "root" node
      $sql = 'SELECT Lft, Rgt FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "'.$cat_code.'"'; 
      $query = $this->read_db->query($sql);
      $row = $query->row_array();
      
      if ($query->num_rows() == 0)
      {
         $row = $this->create_category_root($site_id);
      }

      // start with an empty $right stack
      $right = array();

      // now, retrieve all descendants of the $root node
      
      $sql = 'SELECT CategoryID, CategoryName, Lft, Rgt '.
             'FROM pr_category '.
             'WHERE Lft BETWEEN '.$row['Lft'].' AND '.$row['Rgt'].' '.
             'AND SiteID = "'.$site_id.'" '.
             'ORDER BY Lft ASC';
      $query = $this->read_db->query($sql);
      $result = $query->result_array();

      // display each row
      for($i=0; $i<count($result); $i++)
      {
         // only check stack if there is one
         if (count($right) > 0)
         {
            // check if we should remove a node from the stack
            while ($right[count($right) - 1] < $result[$i]['Rgt'])
            {
               array_pop($right);
            }
         }
         // add level information to the data
         $result[$i]['level'] = count($right);
         
         // add this node to the stack
         $right[] = $result[$i]['Rgt'];

         // remove Lft and Rgt data
         unset($result[$i]['Rgt']);
         unset($result[$i]['Lft']);
      }
      
      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Creates a root node for a site's product categories
    *
    * @access   public
    * @param    string      The site ID
    * @return   array
    */
   function create_category_root($site_id)
   {
      // set the default values
      $category['SiteID'] = $site_id;
      $category['CategoryCode'] = 'root';
      $category['CategoryName'] = '';
      $category['CategoryDescription'] = '';
      $category['CategoryType'] = '';
      $category['Status'] = 'active';
      $category['CategoryParentID'] = 0;
      $category['CategoryOrder'] = 0;
      $category['CategoryText'] = '';
      $category['SESFilename'] = '';
      $category['MetaTitle'] = '';
      $category['MetaMisc'] = '';
      $category['MetaDescription'] = '';
      $category['MetaKeywords'] = '';
      $category['Language'] = '';
      $category['Lft'] = '1';
      $category['Rgt'] = '2';

      $this->write_db->insert('pr_category', $category);
      $this->hcg_write_db->insert('pr_category', $category);

      // get the root node and return it
      $sql = 'SELECT CategoryID FROM pr_category '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND CategoryCode = "root"'; 

      $query = $this->read_db->query($sql);
      $row = $query->row_array();

      return $row;
   }

}

?>