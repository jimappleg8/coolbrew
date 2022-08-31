<?php

/** 
 * Resources Helper
 *
 */

// ----------------------------------------------------------------------

/**
 * returns a default value if the supplied variable is empty
 *
 */
function rsrc_path($resource, $site_id, $file)
{
   if ($file == '')
   {
      return;
   }
   
   $resources = array (
      'press' => 'press/',
      'products-beauty' => 'products/beauty/',
      'products-categories' => 'products/categories/',
      'products-feature' => 'products/featured/',
      'products-large' => 'products/large/',
      'products-small' => 'products/small/',
      'products-thumb' => 'products/thumb/',
      'products-nutrition' => 'products/nutrition/',
      'recipes' => 'recipes/',
      'symbols' => 'symbols/',
   );
   
   // strip out any path already in the file
   $file = basename($file);
   
   if ( ! isset($resources[$resource]))
   {
      $resource = 'products-large';
   }

   $path = 'http://resources.hcgweb.net/'.
           $site_id.'/'.
           $resources[$resource].
           $file;

   return $path;
}

?>