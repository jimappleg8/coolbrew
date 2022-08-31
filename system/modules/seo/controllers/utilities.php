<?php

class Utilities extends Controller {

   function Utilities()
   {
      parent::Controller();
      $this->load->helper(array('url', 'menu'));
   }

   // -----------------------------------------------------------------------

   /**
    * Automatic processing of all site indexes for use as a cron job
    */
   function auto_index()
   {
      $this->load->database('read');
      $this->load->library('Site_index');
      
      // first, get a list of all current indexes
      $sql = 'SELECT * FROM seo_index '.
             'ORDER BY SiteID';
      $query = $this->db->query($sql);
      $indexes = $query->result_array();
      
      // these are sites that are currently having troubles
      $skip = array('bc','cbcms','eb','he','ic','lg','si','lf','so','wa');
      
      foreach ($indexes AS $index)
      {
         unset($index['ID']);
         unset($index['ExecutionTime']);
         unset($index['IndexedDate']);
         unset($index['IndexedBy']);
      
         if (! in_array($index['SiteID'], $skip))
         {
            $this->site_index->index($index);
            echo "Indexed: ".$index['RootURL']."\n";
         }
         else
         {
            echo "Skipped: ".$index['RootURL']."\n";
         }
      }
   }

}
?>
