<?php

class Meta {

   function Meta()
   {
   }

   // --------------------------------------------------------------------

   function get_meta_info($url)
   {
      $html = $this->get_url($url);
      
      if (strpos($html, "301 Moved") !== false)
         $html = $this->get_url($this->match('/href="(.*?)"/ms', $html, 1));

      $meta['PageTitle'] = '';
      $meta['MetaKeywords'] = null;
      $meta['MetaDescription'] = null;
      $meta['MetaAbstract'] = null;
      $meta['MetaRobots'] = '';

      preg_match('/<title>([^>]*)<\/title>/si', $html, $match );

      if (isset($match) && is_array($match) && count($match) > 0)
         $meta['PageTitle'] = strip_tags($match[1]);

      preg_match_all("|<meta[^>]+name=\"([^\"]*)\"[^>]". "+content=\"([^\"]*)\"[^>]+>|i", $html, $out, PREG_PATTERN_ORDER);

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

   // --------------------------------------------------------------------

   function normalize($str)
   {
      $str = str_replace('_', ' ', $str);
      $str = str_replace('.', ' ', $str);
      $str = preg_replace('/ +/', ' ', $str);
      return $str;
   }

   // --------------------------------------------------------------------

   function get_url($url, $username = null, $password = null)
   {
      $ch = curl_init();
      if ( ! is_null($username) && !is_null($password))
         curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' .  base64_encode("$username:$password")));
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      $html = curl_exec($ch);
      curl_close($ch);
      return $html;
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
}

?>