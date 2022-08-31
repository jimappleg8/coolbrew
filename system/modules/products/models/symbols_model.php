<?php

class Symbols_model extends Model {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables

   function Symbols_model()
   {
      parent::Model();
      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('write', TRUE);
      $this->hcg_db = $this->load->database('hcg_write', TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Returns the record data for a given symbol ID
    *
    */
   function get_symbol_data($prod_id, $symbol) 
   {
      $sql = "SELECT pr_symbol.SymbolFile, pr_symbol.SymbolWidth, ".
             "pr_symbol.SymbolHeight, pr_symbol.SymbolAlt " .
             "FROM pr_product, pr_symbol " .
             "WHERE pr_product.ProductID = ".$prod_id." ".
             "AND pr_product.".$symbol." = pr_symbol.SymbolID";
      $query = $this->cb_db->query($sql);
      $result = $query->row_array();

      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of kosher symbols for use in a pulldown menu
    *
    * The array that is returned is not the standard one I would
    * return for a text-based list; it contains enough information
    * that I can construct an image-based pulldown menu in the 
    * display template.
    *
    * @returns   array
    */
   function get_kosher_list() 
   {
      $sql = 'SELECT * '.
             'FROM pr_symbol ' .
             'WHERE SymbolType = "kosher"';
      $query = $this->cb_db->query($sql);
      $result = $query->result_array();
      
      for ($i=0, $cnt=count($result); $i<$cnt; $i++)
      {
         $result[$i]['SymbolFile'] = str_replace('/images/site/', '', $result[$i]['SymbolFile']);
         $result[$i]['SymbolFile'] = str_replace('/images/', '', $result[$i]['SymbolFile']);
         $result[$i]['SymbolFile'] = 'http://resources.hcgweb.net/shared/symbols/'.$result[$i]['SymbolFile'];
      }

      return $result;
   }

   // --------------------------------------------------------------------

   /**
    * Returns an array of organic symbols for use in a pulldown menu
    *
    * The array that is returned is not the standard one I would
    * return for a text-based list; it contains enough information
    * that I can construct an image-based pulldown menu in the 
    * display template.
    *
    * @returns   array
    */
   function get_organic_list() 
   {
      $sql = 'SELECT * '.
             'FROM pr_symbol ' .
             'WHERE SymbolType = "organic"';
      $query = $this->cb_db->query($sql);
      $result = $query->result_array();

      for ($i=0, $cnt=count($result); $i<$cnt; $i++)
      {
         $result[$i]['SymbolFile'] = str_replace('/images/site/', '', $result[$i]['SymbolFile']);
         $result[$i]['SymbolFile'] = str_replace('/images/', '', $result[$i]['SymbolFile']);
         $result[$i]['SymbolFile'] = 'http://resources.hcgweb.net/shared/symbols/'.$result[$i]['SymbolFile'];
      }

      return $result;
   }

}

/* End of file symbols_model.php */
/* Location: ./system/modules/products/models/symbols_model.php */