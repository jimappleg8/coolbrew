<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ItemModel extends Model {

   var $_id = FALSE;
   var $feed_id;
   var $remote_id;
   var $link;
   var $title;
   var $created_time;
   var $updated_time;

   // ----------------------------------------------------------------------
   
   function ItemModel()
   {
      parent::Model();
      $this->load->database('read');
   }

   // ----------------------------------------------------------------------
   
   /**
    *
    *
    */
   function reset()
   {
      $this->_id = false;
      $this->feed_id = 0;
      $this->remote_id = '';
      $this->link = '';
      $this->title = '';
      $this->text = '';
      $this->created_time = localtime();
      $this->updated_time = localtime();
   }

   // ----------------------------------------------------------------------
   
   /**
    *
    *
    */
   function load($feed_id, $remote_id)
   {
      $this->db->where('FeedID', $feed_id);
      $this->db->where('RemoteID', $remote_id);
      $rs = $this->db->get('rss_item');

      if ($rs->num_rows() > 0)
      {
         $row = $rs->row_array();
         $this->_id = $row['ID'];
         $this->feed_id = $feed_id;
         $this->remote_id = $remote_id;
         $this->link = $row['Link'];
         $this->title = $row['Title'];
         $this->text = $row['Text'];
         $this->created_time = strtotime($row['CreatedTime']);
         $this->updated_time = strtotime($row['UpdatedTime']);
         return true;
      }
      else
      {
         $this->reset();
         $this->feed_id = $feed_id;
         $this->remote_id = $remote_id;
         return false;
      }
   }

   // ----------------------------------------------------------------------
   
   /**
    *
    *
    */
   function save()
   {
      $record['Link'] = $this->link;
      $record['Title'] = $this->title;
      $record['Text'] = $this->text;
      
      if ($this->_id !== false)
      {
         $record['UpdatedTime'] = date('Y-m-d H:i:s');
         $this->db->where('ID', $this->_id);
         $this->db->update('rss_item', $record);
      }
      else
      {
         $record['FeedID'] = $this->feed_id;
         $record['RemoteID'] = $this->remote_id;
         $record['CreatedTime'] = date('Y-m-d H:i:s');
         $record['UpdatedTime'] = date('Y-m-d H:i:s');
         $this->db->insert('rss_item', $record);
         $this->_id = $this->db->insert_id();
      }
   }

   // ----------------------------------------------------------------------
   
   /**
    *
    *
    */
   function get_items($offset, $num_per_page)
   {
      $sql = 'SELECT count(*) AS total FROM rss_item';
      $rs = $this->db->query($sql);
      if ($rs->num_rows() > 0)
      {
         $row = $rs->row_array();
         $total = $row['total'];
         $this->db->orderby('UpdatedTime', 'desc');
         $this->db->limit($num_per_page, $offset);
         $rs = $this->db->get('rss_item');
         return array('total' => $total, 
                      'items' => $rs->result_array());
      }
      else
      {
         return array('total' => 0, 
                      'items' => array());
      }
   }   
}

?>