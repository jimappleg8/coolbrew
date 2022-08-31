<?php

class Iri_data extends Model {

   function Iri_data()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Return list of sites to be processed for IRI data.
    *
    * In the future, I'd like to get this data based on the site table 
    * and the preferences set in the Admin section.
    *
    * @access   public
    * @return   array
    */
   function adm_get_sites_array()
   {
      $sites = array("am",   // Arrowhead Mills
                     "cs",   // Celestial Seasonings
                     "eb",   // Earth's Best
                     "ge",   // Garden of Eatin'
                     "hf",   // Hain Pure Foods
                     "hs",   // Hain Pure Snax
                     "hv",   // Health Valley
                     "if",   // Imagine Foods
                     "ms",   // Mountain Sun
                     "rp",   // Rosetto Pasta
                     "tc",   // Terra Chips
                     "td",   // Taste The Dream
                     "wb",   // Westbrae
                    );
      return $sites;
   }
   
   // --------------------------------------------------------------------

   /**
    * Export the brands product data to a CVS file that will work with
    * the IRI Store Locator system. This function generates the data
    * for the prod.cvs file.
    * 
    * This file has two parts, a UPC section and a Custom section:
    * 
    * The UPC section has five columns
    *    upc (10 digits)
    *    $site_id." ".ProductName
    *    UPC (literal)
    *    HNCL (literal)
    *    69 (literal)
    * 
    * The Custom section also has five columns
    *    LocatorCode
    *    $site_id." ".ProductName
    *    Custom (literal)
    *    HNCL (literal)
    *    69 (literal)
    * 
    * @access   public
    * @param    string  The site ID whose products we are indexing
    * @return   void
    */
   function adm_export_prod($site_id = 'all')
   {
      if ($site_id == "all")
      {
         $sites = adm_get_sites_array();
         $filename = "prod.csv";
      }
      else
      {
         $sites = array($site_id);
         $filename = $site_id."_prod.csv";
      }
   
      $this->load->database('write');

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);

      $eol = "\n";
   
      foreach ($sites AS $site)
      {
         $sql = "SELECT UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND LocatorCode NOT LIKE 'none'";
         $query = $this->db->query($sql);
         $prod_data = $query->result_array();
   
         // generate UPC records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] != "master")
            {
               $line = "";
               $line .= substr($prod_data[$i]['UPC'], 1).",";
               $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
               $line .= "UPC,HNCL,69".$eol;
               echo $line;
            }
         }

         // generate Custom "master" records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "master")
            {
               $line = "";
               $line .= $prod_data[$i]['LocatorCode'].",";
               $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
               $line .= "Custom,HNCL,69".$eol;
               echo $line;
            }
         }

         // generate Custom "none" records
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "none")
            {
               $line = "";
               $line .= $prod_data[$i]['LocatorCode'].",";
               $line .= "\"".strtoupper($site)." ".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\",";
               $line .= "Custom,HNCL,69".$eol;
               echo $line;
            }
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Export the brands product data to a CVS file that will work with
    * the IRI Store Locator system. This function generates the data
    * for the prod_rel.cvs file.
    *
    * This file is pretty simple, just four columns:
    *   upc (10 digits)
    *   HNCL (literal)
    *   69 (literal)
    *   LocatorCode
    *
    * @access   public
    * @param    string  The site ID whose products we are indexing
    * @return   array
    */
   function adm_export_prod_rel($site_id = 'all')
   {
      if ($site_id == "all")
      {
         $sites = adm_get_sites_array();
         $filename = "prod_rel.csv";
      }
      else
      {
         $sites = array($site_id);
         $filename = $site_id."_prod_rel.csv";
      }

      $this->load->database('write');

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);

      $eol = "\n";
   
      foreach ($sites as $site)
      {
         $sql = "SELECT UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND ProductGroup NOT LIKE 'master' ".
                "AND LocatorCode NOT LIKE 'none'";
         $query = $this->db->query($sql);
         $prod_data = $query->result_array();
   
         // generate records
         for ($i=0; $i<count($prod_data); $i++)
         {
            $line = "";
            $line .= substr($prod_data[$i]['UPC'], 1).",";
            $line .= "HNCL,69,";
            $line .= $prod_data[$i]['LocatorCode'].$eol;
            echo $line;
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Export the brands product data to a CVS file that will work with
    * the IRI Store Locator system. This function generates the data
    * for the upc_69.cvs file.
    * 
    * This file is very simple, just one column:
    *   upc (10 digits)
    *
    * @access   public
    * @param    string  The site ID whose products we are indexing
    * @return   array
    */
   function adm_export_upc_69($site_id = 'all')
   {
      if ($site_id == "all")
      {
         $sites = adm_get_sites_array();
         $filename = "upc_69.csv";
      }
      else
      {
         $sites = array($site_id);
         $filename = $site_id."_upc_69.csv";
      }

      $this->load->database('write');

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);

      $eol = "\n";
   
      foreach ($sites as $site)
      {
         $sql = "SELECT UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND ProductGroup NOT LIKE 'master' ".
                "AND LocatorCode NOT LIKE 'none'";
         $query = $this->db->query($sql);
         $prod_data = $query->result_array();
   
         // generate records
         for ($i=0; $i<count($prod_data); $i++)
         {
            $line = "";
            $line .= substr($prod_data[$i]['UPC'], 1).$eol;
            echo $line;
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Generate a report of all the products being supported by IRI
    * for the store locator.
    *
    * @access   public
    * @param    string  The site ID
    * @return   array
    */
   function adm_export_iri_report($site_id)
   {
      if ($site_id == "all")
      {
         $sites = adm_get_sites_array();
         $filename = "iri_report.csv";
      }
      else
      {
         $sites = array($site_id);
         $filename = $site_id."_iri_report.csv";
      }
   
      $this->load->database('write');

      header("Content-Type: text/plain");
      header("Content-Disposition: attachment; filename=".$filename);

      $eol = "\n";

      $count1['total'] = 0;
      $count2['total'] = 0;
   
      foreach ($sites as $site)
      {
         $sql = 'SELECT adm_brand.Name AS BrandName '.
                'FROM adm_site, adm_site_brand, adm_brand ' .
                'WHERE adm_site.ID = \''.$site.'\' '.
                'AND adm_site.ID = adm_site_brand.SiteID '.
                'AND adm_brand.ID = adm_site_brand.BrandID';
         $query = $this->db->query($sql);
         $brand = $query->row_array();

         $brandname[$site] = $brand['BrandName'];

         $sql = "SELECT ProductID, UPC, ProductName, ProductGroup, LocatorCode ".
                "FROM pr_product " .
                "WHERE SiteID LIKE \"".$site."\" ".
                "AND Status NOT LIKE 'discontinued' ".
                "AND LocatorCode NOT LIKE 'none'";
         $query = $this->db->query($sql);
         $prod_data = $query->result_array();
   
         // generate UPC records
         $count1[$site] = 0;
         $line1[$site] = "";
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] != "master")
            {
               $line1[$site] .= substr($prod_data[$i]['UPC'], 1).",";
               if ($prod_data[$i]['ProductGroup'] != "none")
               {
                  $line1[$site] .= "\"* ";
               }
               else
               {
                  $line1[$site] .= "\"";
               }
               $line1[$site] .= str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
               $line1[$site] .= $eol;
               $count1[$site]++;
            }
         }

         // generate Custom "master" records
         $count2[$site] = 0;
         $line2[$site] = "";
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "master")
            {
               $line2[$site] .= $prod_data[$i]['LocatorCode'].",";
               $line2[$site] .= "\"".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
               $line2[$site] .= $eol;
               $line2[$site] .= "\"(Group containing these products: ";
               for ($j=0; $j<count($prod_data); $j++)
               {
                  if ($prod_data[$j]['ProductGroup'] == $prod_data[$i]['ProductID'])
                  {
                     $line2[$site] .= substr($prod_data[$j]['UPC'], 1)." ";
                  }
               }
               $line2[$site] .= ")\"".$eol;
               $count2[$site]++;
            }
         }

         // generate Custom "none" records
         $line3[$site] = "";
         for ($i=0; $i<count($prod_data); $i++)
         {
            if ($prod_data[$i]['ProductGroup'] == "none")
            {
               $line3[$site] .= $prod_data[$i]['LocatorCode'].",";
               $line3[$site] .= "\"".str_replace("\"", "\"\"", $prod_data[$i]['ProductName'])."\"";
               $line3[$site] .= $eol;
               $count2[$site]++;
            }
         }
         $count1['total'] = $count1['total'] + $count1[$site];
         $count2['total'] = $count2['total'] + $count2[$site];
      }
   
      echo "IRI Store Locator Product Report".$eol;
      echo "Generated ".date('Y-m-d').$eol;
      echo $eol;
      echo "Summary,Individual UPCs,Product Groups".$eol;
      foreach ($sites as $site)
      {
         echo $brandname[$site].":,".$count1[$site].",".$count2[$site].$eol;   
      }
      echo "TOTAL:,".$count1['total'].",".$count2['total'].$eol;
      echo $eol;
      echo $eol;   
   
      foreach ($sites as $site)
      {
         echo $brandname[$site].$eol;
         echo $eol;
         echo "Individual UPCs: ".$count1[$site].$eol;
         echo "Product Groups: ".$count2[$site].$eol;
         echo $eol;
         echo $brandname[$site]." Individual UPCs:".$eol;
         echo "*part of a multi-sku product group".$eol;
         echo $eol;
         echo $line1[$site];
         echo $eol;
         echo $brandname[$site]." Product Groups:".$eol;
         echo $eol;
         echo $line2[$site];
         echo $line3[$site];
         echo $eol;
         echo $eol;
      }
   }
   
   
}

?>