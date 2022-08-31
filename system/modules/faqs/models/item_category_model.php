<?php

class Item_category_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing
   
   // --------------------------------------------------------------------

   function Item_category_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
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

      $this->write_db->insert('faqs_item_category', $link);

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
      $this->write_db->delete('faqs_item_category');

      return TRUE;
   }


}

/* End of file item_category_model.php */
/* Location: ./system/modules/faqs/models/item_category_model.php */