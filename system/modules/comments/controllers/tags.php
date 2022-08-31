<?php

class Comments_Tags extends Controller {

   function Comments_Tags()
   {
      parent::Controller();
      $this->load->helper(array('url', 'form'));
   }

   //-------------------------------------------------------------------------

   /**
    * creates a random comment
    */
   function random() 
   {
      // (string) The group code
      $group = $this->tag->param(1);
      
      // (string) The site ID
      $site_id = $this->tag->param(2, SITE_ID);
      
      // (string) The view name in case we want to override the default
      $tpl = $this->tag->param(3, "random");

      // get comment data
      $this->load->database('read');

      $sql = 'SELECT * '.
             'FROM comments AS c, comments_group AS cg '. 
             'WHERE cg.ID = c.GroupID '.
             'AND cg.GroupCode = "'.$group.'" '.
             'AND cg.SiteID = "'.$site_id.'" '.
             'AND c.Status = "active"';
      
      $query = $this->db->query($sql);
      $comments = $query->result_array();

      $max = count($comments) - 1;
      $comment = $comments[mt_rand(0, $max)];

      $data['comment'] = $comment;

      echo $this->load->view($tpl, $data, TRUE);
   }

}
?>
