<?php

/** 
 * UPC Helper
 *
 */

/**
 * Converts an 11-digit UPC to a full 12-digit code.
 *
 * @access   public
 * @returns  string   in the form 0-00000-00000-0
 *
 */
function getFullUPC($upc_eleven) 
{
   $upc_eleven_str = (string)$upc_eleven;
   
   if ($upc_eleven_str == '')
   {
      return '';
   }
   
   $full_upc = substr($upc_eleven_str, 0, 1) . "-" .
            substr($upc_eleven_str, 1, 5) . "-" .
            substr($upc_eleven_str, 6, 5) . "-" .
            calculateCheckDigit($upc_eleven);

   return $full_upc;
}

// ----------------------------------------------------------------------

/**
 * Takes an 11-digit UPC and calulates the check digit.
 *
 */

function calculateCheckDigit($upc_eleven)
{
   $upc = $upc_eleven;
   
   // 1) add digits 1, 3, 5, 7, 9, 11
   $step1 = $upc[0] + $upc[2] + $upc[4] + $upc[6] + $upc[8] + $upc[10];

   // 2) multiply result by 3
   $step2 = $step1 * 3;

   // 3) add digits 2, 4, 6, 8, 10
   $step3 = $upc[1] + $upc[3] + $upc[5] + $upc[7] + $upc[9];

   // 4) add result to previous result
   $step4 = $step2 + $step3;

   // The Check Digit is the smallest number needed to round the result 
   // of Step 4 up to a multiple of 10.
   
   $check_digit = (10 - ($step4 % 10));
   if ($check_digit == 10) {
      $check_digit = 0;
   }
   
   return $check_digit;
}

?>