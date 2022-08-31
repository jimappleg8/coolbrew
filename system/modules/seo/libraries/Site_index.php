<?php

// Based on script by Ilya S. Lyubiinsiy -- see copyright below
// Significantly modified and expanded by Jim Applegate
//
// -----------------------------------------------------------------------
// Copyright (C) 2005 Ilya S. Lyubinskiy. All rights reserved.
// Technical support: http://www.php-development.ru/
//
// YOU MAY NOT
// (1) Remove or modify this copyright notice.
// (2) Distribute this code, any part or any modified version of it.
//     Instead, you may link to the homepage of this code:
//     http://www.php-development.ru/javascripts/smart-forms.php.
//
// YOU MAY
// (1) Use this code or any modified version of it on your website.
// (2) Use this code as part of another product.
//
// NO WARRANTY
// This code is provided "as is" without warranty of any kind, either
// expressed or implied, including, but not limited to, the implied warranties
// of merchantability and fitness for a particular purpose. You expressly
// acknowledge and agree that use of this code is at your own risk.
// -----------------------------------------------------------------------

class Site_index {

   var $CI;
   var $username = null;
   var $password = null;
   var $proxy_host = "";    // proxy host to use
   var $proxy_port = "";    // proxy port to use

   var $links = array();
   var $referrers = array();
   
   var $execution_time = 0;
   var $number_parsed = 0;

   // -----------------------------------------------------------------------

   /**
    * constructor
    */
   function Site_index()
   {
      $this->CI =& get_instance();
      $this->CI->load->library('session');

      set_time_limit(0);
      error_reporting(E_ALL);
      ini_set("log_errors", 0);
      ini_set("display_errors", 1);
   }

   // -----------------------------------------------------------------------

   /**
    * Parse HTML
    *
    * @param   string  
    * @param   string  
    * @param   string  
    */
   function enclose($start, $end1, $end2)
   {
      return "$start((?:[^$end1]|$end1(?!$end2))*)$end1$end2";
   }

   // -----------------------------------------------------------------------

   /**
    * Parse HTML
    *
    * @param   string   the HTML to parse
    * @param   array    extracted meta data
    * @param   string   extracted text
    * @param   string   extracted anchors (links)
    * @return  void
    */
   function parse($html, &$meta, &$text, &$anchors)
   {
      $html = $this->_clean_html($html);
      
      $meta = $this->_get_meta($html);
      $text = $this->_get_text($html);
      $anchors = $this->_get_anchors($html);
   }
   
   // -----------------------------------------------------------------------

   /**
    * Parse URL
    *
    * @param   string  URL to parse
    */
   function url_parse($url)
   {
      $error_reporting = error_reporting(E_ERROR | E_PARSE);
      $url = parse_url($url);
      error_reporting($error_reporting);
      return $url;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract Scheme
    *
    * @param   string  URL from which to extract the scheme
    * @param   string  default scheme if not found
    */
   function url_scheme($url, $scheme = 'http')
   {
      if ( ! ($url = $this->url_parse($url))) return $scheme;
      return isset($url['scheme']) ? $url['scheme'] : $scheme;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract Host
    *
    * @param   string  URL from which to extract the host
    * @param   bool    convert host to lower case?
    * @param   mixed   whether the 'www' should be processed and how
    */
   function url_host($url, $lower = TRUE, $www = FALSE)
   {
      if ( ! ($url = $this->url_parse($url))) return '';

      $url = $lower ? strtolower($url['host']) : $url['host'];
      if ($www == 'append' && strpos($url, 'www.') !== 0)
      {
         return 'www.' . $url;
      }
      if ($www == 'strip'  && strpos($url, 'www.') === 0)
      {
         return substr($url, 4);
      }
      return $url;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract Path
    *
    * @param   string  URL from which to extract the path
    */
   function url_path($url)
   {
      if ( ! ($url = $this->url_parse($url))) return '';
      $url = isset($url['path']) ? explode('/', $url['path']) : Array();
      if (reset($url) === '') array_shift($url);
      if (end  ($url) === '' || strpos(end($url), '.') !== false) array_pop($url);
      return implode('/', $url);
   }

   // -----------------------------------------------------------------------

   /**
    * Extract filename
    *
    * @param   string  URL from which to extract the filename
    * @param   array   list of file conversions, e.g. array('/^index.\\w+$/' => '')
    */
   function url_file($url, $convert = array())
   {
      if( ! ($url = $this->url_parse($url))) return '';

      $url = isset($url['path']) ? end(explode('/', $url['path'])) : '';
      $url = (strpos($url, '.') !== false) ? $url : '';

      foreach ($convert as $i => $x)
      {
         $url = preg_replace($i, $x, $url);
      }
      return $url;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract file extension
    *
    * @param   string  URL from which to extract the file extension
    * @param   array   list of file conversions, e.g. array('/^index.\\w+$/' => '')
    */
   function url_ext($url, $convert = array())
   {
      if ( ! ($url = $this->url_parse($url))) return '';

      $url = isset($url['path']) ? end(explode('/', $url['path'])) : '';
      $url = (strpos($url, '.') !== false) ? end(explode('.', $url)) : '';

      foreach ($convert as $i => $x)
      {
         $url = preg_replace($i, $x, $url);
      }
      return $url;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract Query
    *
    * @param   string  URL from which to extract the query
    * @param   bool    whether to escape ampersands in the query
    * @param   array   names to be excluded from query
    */
   function url_query($url, $escape = FALSE, $exclude = array())
   {
      if ( ! ($url = $this->url_parse($url))) return '';
      if ( ! isset($url['query'])) return '';

      $url = preg_split('/(&(?!amp;)|&amp;)/', $url['query']);

      foreach ($url as $i => $x)
      {
         $x = explode('=', $x);
         if (in_array($x[0], $exclude))
         {
            unset($url[$i]);
         }
      }
      return implode($escape ? '&amp;' : '&', $url);
   }

   // -----------------------------------------------------------------------

   /**
    * Concat
    *
    * @param   
    * @param   
    */
   function url_concat($base, $rel)
   {
      $scheme = $this->url_scheme($base);
      $host = $this->url_host($base);
      $path = $this->url_path($base);

      if ($rel{0} == '/')
      {
         return "$scheme://$host$rel";
      }
      else if ($path === '')
      {
         return "$scheme://$host/$rel";
      }
      else
      {
         return "$scheme://$host/$path/$rel";
      }
   }

   // -----------------------------------------------------------------------

   /**
    * Normalize
    *
    * @param   string  URL to normalize
    * @param   string  scheme ('http' or 'https')
    * @param   mixed   whether the 'www' should be processed and how
    * @param   array   list of file conversions, e.g. array('/^index.\\w+$/' => '')
    * @param   bool    whether to escape ampersands in the query
    * @param   array   names to be excluded from query
    */
   function url_normalize($url, $scheme  = 'http', $www = FALSE, $convert = array(), $escape = FALSE, $exclude = array())
   {
      $scheme = $this->url_scheme($url, $scheme);
      $host = $this->url_host($url, FALSE, $www);
      $path = $this->url_path($url);
      $file = $this->url_file($url, $convert);
      $query = $this->url_query($url, $escape, $exclude);

      if ($scheme === '' || $host === '') return '';

      if ($path === '')
      {
         return "$scheme://$host/$file".($query ? "?$query" : "");
      }
      else
      {
         return "$scheme://$host/$path/$file" . ($query ? "?$query" : "");
      }
   }

   // --------------------------------------------------------------------

   function get_url($url)
   {
      $ch = curl_init();
      if ( ! is_null($this->username) && ! is_null($this->password))
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' .  base64_encode("$this->username:$this->password")));
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      if ($this->proxy_host != '')
      {
         curl_setopt($ch, CURLOPT_PROXY, "$this->proxy_host:$this->proxy_port");
      }
      $html = curl_exec($ch);
      curl_close($ch);
      return $html;
   }

   // -----------------------------------------------------------------------

   /**
    * Index Website
    * 
    */
   function index($config)
   {
      // reset all variables
      $this->links = array();
      $this->referrers = array();

      $time   = $this->microtime_float();
      $parsed = 0;
      
      $this->links[0]['URL'] = $config['RootURL'];
      
      $max = (integer)$config['MaxPages'];
      $www = $config['WwwTreatment'];
      
      $convert = array();
      if ($config['IndexTreatment'] == "strip" )
         $convert = array('/^index.\\w+$/' => '');
      if ($config['IndexTreatment'] == "append")
         $convert = array('/^$/' => $config['IndexAppend']);

      $exclude = preg_split('/\\s+/', $config['QueryExcludes']);
      $ext_parse = $config['ExternalTitles'];

      $extensions = preg_split('/\\s+/', $config['Extensions']);
      $extensions[] = '';

      $roots[0] = $this->links[0]['URL'];
      $roots[1] = $this->url_normalize($this->links[0]['URL'], 'http', 'append');
      $roots[2] = $this->url_normalize($this->links[0]['URL'], 'http', 'strip');
      $root_host = $this->url_parse($this->links[0]['URL']);
      $roots[3] = $root_host['scheme'].'://'.$root_host['host'];
      $roots[4] = $this->url_normalize($roots[3], 'http', 'append');
      $roots[5] = $this->url_normalize($roots[3], 'http', 'strip');
      
      for ($j=0; $j<count($this->links); $j++)
      {
         $this->links[$j]['URL'] = $this->url_normalize($this->links[$j]['URL'], 'http', $www, $convert, FALSE, $exclude);
      }

      for ($ind = 0; $ind < count($this->links); $ind++)
      {
         $this->links[$ind]['Title'] = '';
         $this->links[$ind]['MetaDescription'] = '';
         $this->links[$ind]['MetaKeywords'] = '';
         $this->links[$ind]['MetaAbstract'] = '';
         $this->links[$ind]['MetaRobots'] = '';
         $this->links[$ind]['Text'] = '';
         $this->links[$ind]['Location'] = '';
         $this->links[$ind]['Type'] = '';
         $this->links[$ind]['NewURL'] = '';

         if (trim($this->links[$ind]['URL']) === '')
         {
            unset($this->links[$ind]);
            continue;
         }

         // ----- Check URL -----

         $in_root = false;
         foreach ($roots as $i => $root)
         {
            $in_root = $in_root || strpos($this->links[$ind]['URL'], $root) === 0;
         }

         if ( ! $in_root)
         {
            if ( ! $ext_parse) continue;
         }

         if ( ! in_array($this->url_ext($this->links[$ind]['URL']), $extensions)) continue;

         // ----- Get Contents -----

         $html = $this->get_url($this->links[$ind]['URL']);
         
         // look for redirects and update link to real URL
         if (strpos($html, "301 Moved") !== false || strpos($html, "302 Moved") !== false || strpos($html, "302 Found") !== false)
         {
            $this->links[$ind]['NewURL'] = $this->match('/href="(.*?)"/ms', $html, 1);
            $html = $this->get_url($this->links[$ind]['NewURL']);
         }


         if ($html === false) continue;

         // ----- Parse URL -----

         $parsed++;

         $meta = array();
         $text = '';
         $anchors = '';
         $this->parse($html, $meta, $text, $anchors);

         $this->links[$ind]['Title'] = $meta['Title'];
         $this->links[$ind]['MetaDescription'] = $meta['MetaDescription'];
         $this->links[$ind]['MetaKeywords'] = $meta['MetaKeywords'];
         $this->links[$ind]['MetaAbstract'] = $meta['MetaAbstract'];
         $this->links[$ind]['MetaRobots'] = $meta['MetaRobots'];
         $this->links[$ind]['Text'] = $text;

         // ----- Extract Anchors -----

         if ( ! $in_root || ($max < count($this->links) && $max != 0)) continue;
      
         foreach ($anchors as $i => $x)
         {
            $x = preg_replace("/#.*/X", "", $x);
             
            // check for javascript by looking for :
            if ($x == '' || preg_match("/^(\\w)+:(?!\/\/)/X", $x))
               continue;

            if ( ! preg_match("/^(\\w)+:\/\//X", $x)) 
               $x = $this->url_concat($this->links[$ind]['URL'], $x);

            $x = $this->url_normalize($x, 'http', $www, $convert, FALSE, $exclude);
            
            // add this link to the array of referrers
            $found = FALSE;
            foreach ($this->referrers AS $referrer)
            {
               if (strtolower($referrer['referrer']) == strtolower($this->links[$ind]['URL']) && strtolower($referrer['link']) == strtolower($x))
                  $found = TRUE;
            }
            if ( ! $found)
               $this->referrers[] = array(
                  'referrer' => strtolower($this->links[$ind]['URL']),
                  'link' => strtolower($x),
               );

            // check if URL is already in list
            if (count($this->links) < $max || $max == 0)
            {
               $found = FALSE;
               foreach ($this->links AS $link)
               {
                  if (strtolower($link['URL']) == strtolower($x))
                     $found = TRUE;
               }
               if ( ! $found)
                  $this->links[]['URL'] = $x;
            }
         }
      }
      
      // ----- Add Location and Type information -----

      for ($j=0; $j<count($this->links); $j++)
      {
         if (trim($this->links[$j]['URL']) === '') continue;

         $in_root = false;
         foreach ($roots as $key => $root)
         {
            $in_root = $in_root || strpos($this->links[$j]['URL'], $root) === 0;
         }

         if ($in_root)
         {
            if (in_array($this->url_ext($this->links[$j]['URL']), $extensions))
            {
               $this->links[$j]['Location'] = 'internal';
               $this->links[$j]['Type'] = 'page';
            }
            else
            {
               $this->links[$j]['Location'] = 'internal';
               $this->links[$j]['Type'] = 'download';
            }
         }
         else
         {
            if (in_array($this->url_ext($this->links[$j]['URL']), $extensions))
            {
               $this->links[$j]['Location'] = 'external';
               $this->links[$j]['Type'] = 'page';
            }
            else 
            {
               $this->links[$j]['Location'] = 'external';
               $this->links[$j]['Type'] = 'download';
            }
         }
      }
      
      $this->number_parsed = $parsed;
      
      $this->execution_time = $this->microtime_float() - $time;

      $this->save_index($config);

      return TRUE;
   }

   // -----------------------------------------------------------------------

   /**
    * Saves the index results to the database
    * 
    */
   function save_index($config)
   {
      $this->CI->load->database('write');
      
      if ($this->index_exists($config['SiteID']))
      {
         // delete the previous saved referrers
         $sql = 'DELETE r '.
                'FROM seo_index AS i, seo_index_link AS il, seo_referrer AS r '.
                'WHERE i.ID = il.IndexID '.
                'AND i.SiteID = "'.$config['SiteID'].'" '.
                'AND il.ID = r.ReferrerID';
         $query = $this->CI->db->query($sql);

         // delete the previous saved index and links
         $sql = 'DELETE i, il '.
                'FROM seo_index AS i, seo_index_link AS il '.
                'WHERE i.ID = il.IndexID '.
                'AND i.SiteID = "'.$config['SiteID'].'"';
         $query = $this->CI->db->query($sql);
      }
      
      // save the current index
      $config['ExecutionTime'] = $this->execution_time;
      $config['WwwTreatment'] = ($config['WwwTreatment'] == FALSE) ? 'default' : $config['WwwTreatment'];
      $config['IndexedDate'] = date('Y-m-d H:i:s');
      $config['IndexedBy'] = $this->CI->session->userdata('username');
      
      $this->CI->db->insert('seo_index', $config);
      
      $id = $this->CI->db->insert_id();
      
//      echo "<pre>"; print_r($this->links); echo "</pre>"; exit;
      
      // insert each of the URLs into seo_index_link
      for ($j=0; $j<count($this->links); $j++)
      {
         $values = $this->links[$j];
         $values['IndexID'] = $id;
         $this->CI->db->insert('seo_index_link', $values);
         
         $link_id = $this->CI->db->insert_id();
         // add this link to the reverse-lookup array
         $link_array[strtolower($this->links[$j]['URL'])] = $link_id;
      }
      
      // now add links to the referrer table
      foreach ($this->referrers AS $referrer)
      {
         $refer['ReferrerID'] = $link_array[$referrer['referrer']];
         $refer['LinkID'] = $link_array[$referrer['link']];
         $this->CI->db->insert('seo_referrer', $refer);
      }
   }
   
   // -----------------------------------------------------------------------

   /**
    * Checks if an index is currently saved.
    * 
    */
   function index_exists($site_id)
   {
      $this->CI->load->database('read');
      
      $sql = 'SELECT * FROM seo_index '.
             'WHERE SiteID = "'.$site_id.'"';
      $query = $this->CI->db->query($sql);
      
      return ($query->num_rows() > 0) ? TRUE : FALSE;
   }

   // --------------------------------------------------------------------

   function match_all($regex, $str, $i = 0)
   {
      if (preg_match_all($regex, $str, $matches) === false)
         return false;
      else
         return $matches[$i];

   }

   // --------------------------------------------------------------------

   function match($regex, $str, $i = 0)
   {
      if (preg_match($regex, $str, $match) == 1)
         return $match[$i];
      else
         return false;
   }

   // --------------------------------------------------------------------

   /**
    * Simple function to replicate PHP 5 behaviour of microtime
    */
   function microtime_float()
   {
      list($usec, $sec) = explode(" ", microtime());
      $result = ((float)$usec + (float)$sec);
      return $result;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract links (anchors) from the supplied HTML
    *
    * @param   string  the HTML to parse
    * @return  array 
    */
   function _get_anchors($html)
   {
      $pstring1 = "'[^']*'";
      $pstring2 = '"[^"]*"';
      $pnstring = "[^'\">]";
      $pintag   = "(?:$pstring1|$pstring2|$pnstring)*";

      $panchor  = "<[a|area]+(?:\\s$pintag){0,1}>";
      $phref    = "href\\s*=[\\s'\"]*([^\\s'\">]*)";
   
      preg_match_all("/$panchor/iX", $html, $anchors);
      $anchors = $anchors[0];
      reset($anchors);
      while (list($i, $x) = each($anchors))
      {
         $anchors[$i] = preg_match("/$phref/iX", $x, $x) ? $x[1] : '';
      }
      $anchors = array_unique($anchors);

      return $anchors;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract text (strip out tags) from the supplied HTML
    *
    * @param   string  the HTML to parse
    * @return  string 
    */
   function _get_text($html)
   {
      $pstring1 = "'[^']*'";
      $pstring2 = '"[^"]*"';
      $pnstring = "[^'\">]";
      $pintag   = "(?:$pstring1|$pstring2|$pnstring)*";
   
      $text = preg_replace("/<$pintag>/iX",   " ", $html);
      $text = preg_replace("/\\s+|&nbsp;/iX", " ", $text);

      return $text;
   }

   // -----------------------------------------------------------------------

   /**
    * Extract meta data from the supplied HTML
    *
    * @param   string  the HTML to parse
    * @return  array 
    */
   function _get_meta($html)
   {
      $meta['Title'] = '';
      $meta['MetaKeywords'] = '';
      $meta['MetaDescription'] = '';
      $meta['MetaAbstract'] = '';
      $meta['MetaRobots'] = '';

      preg_match('/<title>([^>]*)<\/title>/si', $html, $match);

      if (isset($match) && is_array($match) && count($match) > 0)
         $meta['Title'] = strip_tags($match[1]);

      preg_match_all("/<meta[^>]+name=[\"|\'](.*)[\"|\'][^>]+". "content=[\"|\'](.*)[\"|\'][^>]?>/i", $html, $out, PREG_PATTERN_ORDER);

      for ($i=0;$i < count($out[1]);$i++)
      {
         // loop through the meta data - add your own tags here if you need
         if (strtolower($out[1][$i]) == "keywords") 
            $meta['MetaKeywords'] = $out[2][$i];
         if (strtolower($out[1][$i]) == "description")
            $meta['MetaDescription'] = $out[2][$i];
         if (strtolower($out[1][$i]) == "abstract")
            $meta['MetaAbstract'] = $out[2][$i];
         if (strtolower($out[1][$i]) == "robots")
            $meta['MetaRobots'] = $out[2][$i];
      }

      return $meta;      
   }

   // -----------------------------------------------------------------------

   /**
    * Removes scripts, comments and styles from HTML
    *
    * @param   string  the HTML to parse
    * @return  array 
    */
   function _clean_html_old($html)
   {
      $pstring1 = "'[^']*'";
      $pstring2 = '"[^"]*"';
      $pnstring = "[^'\">]";
      $pintag   = "(?:$pstring1|$pstring2|$pnstring)*";
      $pattrs   = "(?:\\s$pintag){0,1}";

      $pcomment = $this->enclose("<!--", "-", "->");
      $pscript  = $this->enclose("<script$pattrs>", "<", "\\/script>");
      $pstyle   = $this->enclose("<style$pattrs>", "<", "\\/style>");
      $pexclude = "(?:$pcomment|$pscript|$pstyle)";

      $html = preg_replace("/$pexclude/iX", " ", $html);

      return $html;
   }

   // -----------------------------------------------------------------------

   /**
    * Removes scripts, comments and styles from HTML
    *
    * @param   string  the HTML to parse
    * @return  array 
    */
   function _clean_html($html)
   {
      $search = array(
         "'<script[^>]*?>.*?</script>'si",   // strip out javascript
         "'<style[^>]*?>.*?</style>'si",     // strip out styles
         "'<!--(.|\s)*?-->'",                // strip out comments
      );

      $replace = array(
         "",
         "",
         "",
      );

      $text = preg_replace($search, $replace, $html);

      return $text;
   }


}

?>