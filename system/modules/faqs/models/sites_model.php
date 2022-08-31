<?php

class Sites_model extends Model {

   var $read_db;   // database object for reading
   var $write_db;  // database object for writing

   function Sites_model()
   {
      parent::Model();
      $this->read_db = $this->load->database('read', TRUE);
      $this->write_db = $this->load->database('write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified site ID
    *
    * @access   public
    * @return   array
    */
   function get_site_data($site_id)
   {
      $sql = 'SELECT adm_site.ID AS SiteID, adm_site.Description, adm_site_domain.Domain, adm_site_domain.ID AS DomainID, adm_brand.Name AS BrandName '.
             'FROM adm_site, adm_site_domain, adm_site_brand, adm_brand ' .
             'WHERE adm_site.ID = \''.$site_id.'\' '.
             'AND adm_site.ID = adm_site_domain.SiteID '.
             'AND adm_site.ID = adm_site_brand.SiteID '.
             'AND adm_brand.ID = adm_site_brand.BrandID '.
             'AND adm_site_domain.PrimaryDomain = 1';
      
      $query = $this->read_db->query($sql);
      $site = $query->row_array();

      return $site;
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a link between the specified Site, Shared FAQ and Answer
    *
    * @access   public
    * @return   array
    */
   function insert_link($site_id, $faq_id, $answer_id)
   {
      $link['SiteID'] = $site_id;
      $link['FaqID'] = $faq_id;
      $link['AnswerID'] = $answer_id;

      $this->write_db->insert('faqs_site', $link);

      return TRUE;
   }


   // --------------------------------------------------------------------

   /**
    * Deletes the link between the specified Site, Shared FAQ and Answer
    *
    * @access   public
    * @return   array
    */
   function delete_link($site_id, $faq_id, $answer_id)
   {
      $link['SiteID'] = $site_id;
      $link['FaqID'] = $faq_id;
      $link['AnswerID'] = $answer_id;

      $this->write_db->where($link);
      $this->write_db->delete('faqs_site');

      return TRUE;
   }

}

?>