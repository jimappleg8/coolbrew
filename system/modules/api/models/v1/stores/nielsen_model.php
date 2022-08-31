<?php

class Nielsen_model extends Model {

   var $XML_LIST_ELEMENTS = array();
   
//   var $url = 'http://www.itemlocator.net/scripts/cgiip.exe/WService=ils3/webspeed/locatorxml.w?customer=hain';
   var $url = 'http://www2.itemlocator.net/ils/locatorXML/?customer=hain';
   
   var $total_stores;
   var $total_pages;

   // --------------------------------------------------------------------

   function Nielsen_model()
   {
      parent::Model();
   }

   // --------------------------------------------------------------------

   /**
    * Connects to the Nielsen Product Locator and returns the results.
    *
    *  supplied values:
    *     item
    *     zip
    *     radius
    *     count
    *     site-id
    *     sort
    *     product-num
    *
    */
   function get_store_list($search)
   {
      $brand = $search['site-id'];
      $sort = $search['sort'];
      $search['item'] = $search['product-num'];
      unset($search['site-id']);
      unset($search['sort']);
      unset($search['product-num']);

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
            return "Some store data is currently not available, so the results of this search are probably not complete. Please try again later.";
         }
         else
         {
            if (isset($result['response']['errorMsg']))
            {
               if ($result['response']['errorMsg'] != '')
               {
                  return $result['response']['errorMsg'];
               }
            }
         }
      }
      else
      {
         return "The request to Nielsen did not go through. Please try again later.<br />Error Message: ".curl_error($r);
      }
      
      // if we made it this far, there were no errors detected.
      curl_close($r);
      
      // set the results variables
      $this->total_stores = 0;
      if (isset($result['response']['storesFound']))
      {
         $this->total_stores = $result['response']['storesFound'];
      }
//      $perpage = $result['RESULTS']['QUERY']['STORESPERPAGE'];
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
            $stores[$cnt]['Website'] = '';
            $stores[$cnt]['Distance'] = $xml_stores[$i]['distance'];
            $stores[$cnt]['DistanceNum'] = '';
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
/* Location: ./system/modules/api/models/v1/stores/nielsen_model.php */