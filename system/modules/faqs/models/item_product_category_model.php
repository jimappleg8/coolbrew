<?php

class Item_product_category_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   // --------------------------------------------------------------------

   function Item_product_category_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns all Products IDs found for a given FAQ ID.
    *
    * @access   public
    * @param    int      The FAQ ID
    * @return   int
    */
   function get_all_category_links($faq_id, $answer_id)
   {
      $sql = 'SELECT CategoryID FROM faqs_item_product_category '.
             'WHERE FaqID = '.$faq_id.' '.
             'AND AnswerID = '.$answer_id;

      $query = $this->read_db->query($sql);
      $products = $query->result_array();
      
      $product_list = array();
      foreach ($products AS $product)
      {
         $product_list[] = $product['CategoryID'];
      }
      
      return $product_list;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a link between the specified FAQ and Category
    *
    * @access   public
    * @return   array
    */
   function insert_link($faq_id, $answer_id, $category_id)
   {
      $link['FaqID'] = $faq_id;
      $link['AnswerID'] = $answer_id;
      $link['CategoryID'] = $category_id;

      $this->write_db->insert('faqs_item_product_category', $link);

      return TRUE;
   }


   // --------------------------------------------------------------------

   /**
    * Deletes the link between the specified FAQ and Category
    *
    * @access   public
    * @return   array
    */
   function delete_link($faq_id, $answer_id, $category_id)
   {
      $link['FaqID'] = $faq_id;
      $link['AnswerID'] = $answer_id;
      $link['CategoryID'] = $category_id;

      $this->write_db->where($link);
      $this->write_db->delete('faqs_item_product_category');

      return TRUE;
   }


}

?>