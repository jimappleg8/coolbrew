<?php

class Nielsen_model extends Model {

   var $XML_LIST_ELEMENTS = array();
   
   var $url = 'http://www2.itemlocator.net/ils/locatorXML/?customer=hain';
   
   var $total_stores;
   var $total_pages;

   var $http_code = 200;
   var $error_dev_msg = '';
   var $error_usr_msg = '';
   var $error_more_info = '';

   // --------------------------------------------------------------------

   function Nielsen_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Prepares the API request for a Zip search
    */
   function get_store_list_by_zip($search)
   {
      $search['item'] = $search['upc'];
      unset($search['upc']);
      unset($search['latitude']);
      unset($search['longitude']);
      unset($search['city']);
      unset($search['state']);
      unset($search['count']);
      unset($search['sort']);
      unset($search['format']);
      unset($search['db-level']);
      unset($search['suppress-response-codes']);
      
      return $this->get_store_list($search);
   }
   
   // --------------------------------------------------------------------

   /**
    * Prepares the API request for a Latitude/Longitude search
    */
   function get_store_list_by_latlong($search)
   {
      $search['item'] = $search['upc'];
      $search['lat'] = $search['latitude'];
      $search['long'] = $search['longitude'];
      unset($search['upc']);
      unset($search['zip']);
      unset($search['latitude']);
      unset($search['longitude']);
      unset($search['city']);
      unset($search['state']);
      unset($search['count']);
      unset($search['sort']);
      unset($search['format']);
      unset($search['db-level']);
      unset($search['suppress-response-codes']);
      
      return $this->get_store_list($search);
   }
   
   // --------------------------------------------------------------------

   /**
    * Prepares the API request for a City/State search
    */
   function get_store_list_by_citystate($search)
   {
      $search['item'] = $search['upc'];
      unset($search['upc']);
      unset($search['zip']);
      unset($search['latitude']);
      unset($search['longitude']);
      unset($search['count']);
      unset($search['sort']);
      unset($search['format']);
      unset($search['db-level']);
      unset($search['suppress-response-codes']);
      
      return $this->get_store_list($search);
   }
   
   // --------------------------------------------------------------------

   /**
    * Connects to the Nielsen Product Locator and returns the results.
    *
    *  supplied values:
    *     item
    *     zip
    *     radius
    *     ip
    *
    */
   function get_store_list($search)
   {
      $post_array = array();
      
      foreach ($search AS $key => $value)
         $post_array[] = $key.'='.$value;
      
      // add the user's IP to the data sent
      $post_array['ip'] = $_SERVER['REMOTE_ADDR'];

      $post_vars = implode('&', $post_array);

      $r = curl_init($this->url);
      curl_setopt($r, CURLOPT_POST, 1);
      curl_setopt($r, CURLOPT_POSTFIELDS, $post_vars);
      curl_setopt($r, CURLOPT_FOLLOWLOCATION, 1);
      curl_setopt($r, CURLOPT_HEADER, 0);
      curl_setopt($r, CURLOPT_RETURNTRANSFER, 1);
      $xml = curl_exec($r);

      if ( ! curl_errno($r))
      {
         $result = $this->_make_xml_tree($xml);
//         echo "<pre>"; print_r($result); echo "</pre>"; exit;

         // check XML for success variable.
         if ( ! isset($result['response']))
         {
            $this->http_code = 500;
            $this->error_dev_msg = 'The Nielsen ItemFinder API did not send a response. It is likely unavailable. Please try again later.';
            $this->error_usr_msg = 'Some store data is currently not available, so the results of this search are probably not complete. Please try again later.';
            return FALSE;
         }
         else
         {
            if (isset($result['response']['errorMsg']) && $result['response']['errorMsg'] != '')
            {
               switch ($result['response']['errorMsg'])
               {
                  case 'No stores were found.':
                     break;
                  default:
                     $this->http_code = 500;
                     $this->error_dev_msg = 'The Nielsen ItemFinder API returned an error: '.$result['response']['errorMsg'];
                     $this->error_usr_msg = $result['response']['errorMsg'];
                     return FALSE;
               }
            }
         }
      }
      else
      {
         // TODO: we may want to detail out some of the more likely errors and respond more specifically.
         // TODO: we may also want to have the system send an email when these errors occur.
         $this->http_code = 500;
         $this->error_dev_msg = 'cURL reported an error and did not retrieve a response from the Nielsen ItemFinder API: '.curl_error($r);
         $this->error_usr_msg = 'The request to Nielsen did not go through. Please try again later.';
         return FALSE;
      }
      
      // if we made it this far, there were no errors detected.
      curl_close($r);
      
      // set the results variables
      $this->total_stores = 0;
      if (isset($result['response']['storesFound']))
      {
         $this->total_stores = $result['response']['storesFound'];
      }
      $perpage = 0;
      if ($perpage != 0)
      {
         $this->total_pages = floor($this->total_stores / $perpage);
         if ($this->total_stores % $perpage > 0)
         {
            $this->total_pages++;
         }
      }
      else
      {
         $this->total_pages = 0;
      }

      $stores = array();
      if ($this->total_stores > 0)
      {
         $cnt = 0;
   
         $xml_stores = $result['response']['store'];
//         echo '<pre>'; print_r($xml_stores); echo '</pre>'; exit;
         for ($i=0; $i<count($xml_stores); $i++)
         {
            $stores[$cnt]['StoreID'] = '';
            $stores[$cnt]['Name'] = $xml_stores[$i]['name'];
            $stores[$cnt]['Address1'] = $xml_stores[$i]['address'];
            $stores[$cnt]['Address2'] = $xml_stores[$i]['address2'];
            $stores[$cnt]['City'] = $xml_stores[$i]['city'];
            $stores[$cnt]['State'] = $xml_stores[$i]['state'];
            $stores[$cnt]['Zip'] = $xml_stores[$i]['zip'];
            $stores[$cnt]['Phone'] = $xml_stores[$i]['phone'];
            $stores[$cnt]['Latitude'] = $xml_stores[$i]['latitude'];
            $stores[$cnt]['Longitude'] = $xml_stores[$i]['longitude'];
            $stores[$cnt]['Website'] = '';
            $stores[$cnt]['DistanceNum'] = $xml_stores[$i]['distance'];
            $stores[$cnt]['Distance'] = $stores[$cnt]['DistanceNum'].' mi';
            $stores[$cnt]['Carries'] = 'product';
            $stores[$cnt]['Src'] = "nielsen";
            $cnt++;
         }
      }
      return $stores;
   }

   // --------------------------------------------------------------------

   /**
    * Process XML feed from Nielsen
    * 
    * Pulled from the php.net documentation comments:
    *    http://us3.php.net/manual/en/ref.xml.php
    * 
    */
   function _make_xml_tree($data)
   {
      $output = array();

      $parser = xml_parser_create();

      xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
      //xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
      //xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, 'ISO-8859-1');
      xml_parse_into_struct($parser, $data, $values, $tags);
      xml_parser_free($parser);

      $hash_stack = array();
      
//      echo "<pre>"; print_r($values); echo "</pre>"; exit;
   
      foreach ($values as $key => $val)
      {
         switch ($val['type'])
         {
            case 'open':
               array_push($hash_stack, $val['tag']);
               if (isset($val['attributes']))
               {
                  $output = $this->_compose_array($output, $hash_stack, $val['attributes']);
               }
               else
               {
                  $output = $this->_compose_array($output, $hash_stack);
               }
               break;

            case 'close':
               array_pop($hash_stack);
               break;

            case 'complete':
               $val['value'] = (isset($val['value'])) ? $val['value'] : '';
               array_push($hash_stack, $val['tag']);
               $output = $this->_compose_array($output, $hash_stack, $val['value']);
               array_pop($hash_stack);

               // handle attributes
               if (isset($val['attributes']))
               {
                  while (list($a_k,$a_v) = each($val['attributes']))
                  {
                     $hash_stack[] = $val['tag']."_attribute_".$a_k;
                     $output = $this->_compose_array($output, $hash_stack, $a_v);
                     array_pop($hash_stack);
                  }
               }
               break;
         }
      }
      return $output;
   }

   // --------------------------------------------------------------------

   /**
    * Works with make_xml_tree() and helps clean up the array that it creates
    * 
    * Pulled from the php.net documentation comments:
    *    http://us3.php.net/manual/en/ref.xml.php
    *
    */
   function &_compose_array($array, $elements, $value=array())
   {
      // get current element
      $element = array_shift($elements);
      
      // does the current element refer to a list
      if (in_array($element, $this->XML_LIST_ELEMENTS))
      {
         // more elements?
         if (sizeof($elements) > 0)
         {
            $array[$element][sizeof($array[$element])-1] = &$this->_compose_array($array[$element][sizeof($array[$element])-1], $elements, $value);
         }
         else // if (is_array($value))
         {
            $array[$element] = (isset($array[$element])) ? $array[$element] : array();
            $array[$element][sizeof($array[$element])] = $value;
         }
      }
      else
      {
         // more elements?
         if (sizeof($elements) > 0)
         {
            $array[$element] = &$this->_compose_array($array[$element], $elements, $value);
         }
         else
         {
            $array[$element] = $value;
         }
      }
      return $array;
   }

}

/* End of file nielsen_model.php */
/* Location: ./system/modules/api/models/v2/nielsen_model.php */