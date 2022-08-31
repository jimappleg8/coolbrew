<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CoolBrew Column Helpers
 *
 * @package		CoolBrew
 * @subpackage	Helpers
 * @category	Helpers
 * @author		Jim Applegate
 * @link		
 */

// ------------------------------------------------------------------------

/**
 * Partition
 *
 * Lets you "partition" or divide an array into a desired number of 
 * split lists -- a useful procedure for "chunking" up objects or text 
 * items into columns, or partitioning any type of data resource.
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	mixed
 * @return	mixed	depends on what the array contains
 */	
function partition($list, $p)
{
   $listlen = count($list);
   $partlen = floor($listlen / $p);
   $partrem = $listlen % $p;
   $partition = array();
   $mark = 0;
   for ($px = 0; $px < $p; $px++)
   {
      $incr = ($px < $partrem) ? $partlen + 1 : $partlen;
      $partition[$px] = array_slice($list, $mark, $incr);
      $mark += $incr;
   }
   return $partition;
}


?>