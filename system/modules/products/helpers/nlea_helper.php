<?php

/** 
 * NLEA Helper
 *
 */

// ------------------------------------------------------------------------
//  build_stmt1()
//   used by Nutrition Facts. Turns the coded string of characters into a 
//   sentence. It builds the string in the same order as the characters 
//   are entered; if we need to enforce a particular order, we can add the
//   ability to sort the string elements.
//
// ------------------------------------------------------------------------

function build_stmt1($stmt_one)
{
   $stmt1_data = array(
      'a' => 'saturated fat',
      'b' => 'cholesterol',
      'c' => 'dietary fiber',
      'd' => 'sugars',
      'e' => 'vitamin A',
      'f' => 'vitamin C',
      'g' => 'calcium',
      'h' => 'iron',
      'i' => 'protein',
      'j' => 'calories from fat',
      'k' => '<i>trans</i> fat',
   );
   
   $statement = "Not a significant source of ";
   
   if (strlen($stmt_one) > 1)
   {
      for ($i=0, $j=strlen($stmt_one); $i<$j-1; $i++)
      {
         $statement .= $stmt1_data[$stmt_one[$i]] . ", ";
      }
      $statement .= "or " . $stmt1_data[$stmt_one[$i]] . ".";
   }
   else
   {
      $statement .= $stmt1_data[$stmt_one] . ".";
   }

   return $statement;
}


// ------------------------------------------------------------------------
//  draw_line()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not. 
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_line($params)
{
   extract($params);

   $width = (isset($width)) ? $width : '';
   $indented = (isset($indented)) ? $indented : 'NO';
   $class = (isset($class)) ? $class : '';
   $xhtml = (isset($xhtml)) ? $xhtml : 'NO';

   if (strtoupper($indent) == "YES")
   {
      $html_data = '<tr>' . "\n";
      if ($class != "")
      {
         $html_data .= '<td class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "")
      {
         $html_data .= '<td colspan="7" class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td colspan="7">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="219" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   else
   {
      $html_data = '<tr>' . "\n";
      if ($class != "")
      {
         $html_data .= '<td colspan="8" class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td colspan="8">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="232" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   return $html_data;
}


// ------------------------------------------------------------------------
//  draw_line_wide()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not.
//
//   The wide in the name indicates that it spans 9 columns rather 
//   than the 8 the other does.
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_line_wide($params)
{
   extract($params);

   $width = (isset($width)) ? $width : '';
   $indented = (isset($indented)) ? $indented : 'NO';
   $class = (isset($class)) ? $class : '';
   $xhtml = (isset($xhtml)) ? $xhtml : 'NO';

   if (strtoupper($indent) == "YES")
   {
      $html_data = '<tr>' . "\n";
      if ($class != "")
      {
         $html_data .= '<td class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "")
      {
         $html_data .= '<td colspan="8" class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td colspan="8">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="219" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   else
   {
      $html_data = '<tr>' . "\n";
      if ($class != "")
      {
         $html_data .= '<td colspan="9" class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td colspan="9">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="232" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   return $html_data;
}

// ------------------------------------------------------------------------
//  draw_baby_line()
//   used by Nutrition Facts. This is a function used in the default 
//   templates that draws a line of a specific width, indented or not. 
//   It's made available to the template using the "register_function" 
//   method in Smarty. The baby in the name indicates that it spans 3 
//   columns rather than the 8 the other does.
//
//   Parameters:
//      width="number"      the thickness of the line
//      indented="yes|no"   whether the line is indented
//      class="classname"   assigns class="classname" to the td tag
//      xhtml="yes|no"      adds a closing slash to the image tag
//
// ------------------------------------------------------------------------

function draw_baby_line($params)
{
   extract($params);

   if (strtoupper($indent) == "YES")
   {
      $html_data = '<tr>' . "\n";
      if ($class != "")
      {
         $html_data .= '<td class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td>';
      }
      $html_data .= '<img src="/images/dot_clear.gif" width="11" height="1" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      if ($class != "")
      {
         $html_data .= '<td colspan="2" class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td colspan="2">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="157" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   else
   {
      $html_data = '<tr>' . "\n";
      if ($class != "")
      {
         $html_data .= '<td colspan="3" class="'.$class.'">';
      }
      else
      {
         $html_data .= '<td colspan="3">';
      }
      $html_data .= '<img src="/images/dot_black.gif" width="170" height="'.$width.'" alt=""';
      if (strtoupper($xhtml) == "YES")
      {
         $html_data .= ' /></td>' . "\n";
      }
      else
      {
         $html_data .= '></td>' . "\n";
      }
      $html_data .= '</tr>' . "\n\n";
   }
   return $html_data;
}

/**
 * returns a refault value if the supplied variable is empty
 *
 */
function set_default($value, $return = '')
{
   if ($value == '')
   {
      return $return;
   }
   return $value;
}

?>