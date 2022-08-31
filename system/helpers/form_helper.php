<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @author		Jim Applegate - Cool Brew changes and additions
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/form_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Form Declaration
 *
 * Creates the opening portion of the form.
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */	
if (! function_exists('form_open'))
{
	function form_open($action = '', $attributes = array(), $hidden = array())
	{
		$CI =& get_instance();

		$action = ( strpos($action, '://') === FALSE) ? $CI->config->site_url($action) : $action;

		$form = '<form action="'.$action.'"';
	
		if ( ! isset($attributes['method']))
		{
			$form .= ' method="post"';
		}
	
		if (is_array($attributes) AND count($attributes) > 0)
		{
			foreach ($attributes as $key => $val)
			{
				$form .= ' '.$key.'="'.$val.'"';
			}
		}
	
		$form .= '>';

		if (is_array($hidden) AND count($hidden > 0))
		{
			$form .= form_hidden($hidden);
		}
	
		return $form;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Form Declaration - Multipart type
 *
 * Creates the opening portion of the form, but with "multipart/form-data".
 *
 * @access	public
 * @param	string	the URI segments of the form destination
 * @param	array	a key/value pair of attributes
 * @param	array	a key/value pair hidden data
 * @return	string
 */	
if (! function_exists('form_open_multipart'))
{
	function form_open_multipart($action, $attributes = array(), $hidden = array())
	{
		$attributes['enctype'] = 'multipart/form-data';
		return form_open($action, $attributes, $hidden);
	}
}
	
// ------------------------------------------------------------------------

/**
 * Hidden Input Field
 *
 * Generates hidden fields.  You can pass a simple key/value string or an associative
 * array with multiple values.
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @return	string
 */	
if (! function_exists('form_hidden'))
{
	function form_hidden($name, $value = '')
	{
		if ( ! is_array($name))
		{
			return '<input type="hidden" name="'.$name.'" value="'.form_prep($value).'" />';
		}

		$form = '';
		foreach ($name as $name => $value)
		{
			$form .= '<input type="hidden" name="'.$name.'" value="'.form_prep($value).'" />';
		}
	
		return $form;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Text Input Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
if (! function_exists('form_input'))
{
	function form_input($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'text', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value, 'maxlength' => '500', 'size' => '50');

		return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
	}
}
	
// ------------------------------------------------------------------------

/**
 * Password Field
 *
 * Identical to the input function but adds the "password" type
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
if (! function_exists('form_password'))
{
	function form_password($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'password';
		return form_input($data, $value, $extra);
	}
}
	
// ------------------------------------------------------------------------

/**
 * Upload Field
 *
 * Identical to the input function but adds the "file" type
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
if (! function_exists('form_upload'))
{
	function form_upload($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'file';
		return form_input($data, $value, $extra);
	}
}
	
// ------------------------------------------------------------------------

/**
 * Textarea field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
if (! function_exists('form_textarea'))
{
	function form_textarea($data = '', $value = '', $extra = '')
	{
		$defaults = array('name' => (( ! is_array($data)) ? $data : ''), 'cols' => '90', 'rows' => '12');
	
	    if ( ! is_array($data) OR ! isset($data['value']))
		{
			$val = $value;
		}
	    else
		{
			$val = $data['value']; 
			unset($data['value']); // textareas don't use the value attribute
		}
		
		return "<textarea ".parse_form_attributes($data, $defaults).$extra.">".$val."</textarea>\n";
	}
}
	
// --------------------------------------------------------------------

/**
 * Drop-down Menu
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_dropdown'))
{
	function form_dropdown($name = '', $options = array(), $selected = array(), $extra = '')
	{
		if ( ! is_array($selected))
		{
			$selected = array($selected);
		}

		// If no selected state was submitted we will attempt to set it automatically
		if (count($selected) === 0)
		{
			// If the form name appears in the $_POST array we have a winner!
			if (isset($_POST[$name]))
			{
				$selected = array($_POST[$name]);
			}
		}

		if ($extra != '') $extra = ' '.$extra;

		$multiple = (count($selected) > 1 && strpos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

		$form = '<select name="'.$name.'"'.$extra.$multiple.">\n";

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val) && ! empty($val))
			{
				$form .= '<optgroup label="'.$key.'">'."\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = (in_array($optgroup_key, $selected)) ? ' selected="selected"' : '';

					$form .= '<option value="'.$optgroup_key.'"'.$sel.'>'.(string) $optgroup_val."</option>\n";
				}

				$form .= '</optgroup>'."\n";
			}
			else
			{
				$sel = (in_array($key, $selected)) ? ' selected="selected"' : '';

				$form .= '<option value="'.$key.'"'.$sel.'>'.(string) $val."</option>\n";
			}
		}

		$form .= '</select>';

		return $form;
	}
}

// ------------------------------------------------------------------------

/**
 * Checkbox Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	bool
 * @param	string
 * @return	string
 */	
if (! function_exists('form_checkbox'))
{
	function form_checkbox($data = '', $value = '', $checked = TRUE, $extra = '')
	{
		$defaults = array('type' => 'checkbox', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);
	
		if (is_array($data) AND array_key_exists('checked', $data))
		{
			$checked = $data['checked'];
		
			if ($checked == FALSE)
			{
				unset($data['checked']);
			}
			else
			{
				$data['checked'] = 'checked';
			}
		}
	
		if ($checked == TRUE)
			$defaults['checked'] = 'checked';
		else
			unset($defaults['checked']);

		return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
	}
}
	
// ------------------------------------------------------------------------

/**
 * Radio Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	bool
 * @param	string
 * @return	string
 */	
if (! function_exists('form_radio'))
{
	function form_radio($data = '', $value = '', $checked = TRUE, $extra = '')
	{
		if ( ! is_array($data))
		{	
			$data = array('name' => $data);
		}

		$data['type'] = 'radio';
		return form_checkbox($data, $value, $checked, $extra);
	}
}

// ------------------------------------------------------------------------

/**
 * Select Date Dropdowns
 *
 * Based on the Smarty html_select_date plugin, version 1.3.2
 * @author   Andrei Zmievski
 *
 * @access	public
 * @param array
 * @return string
 */
function form_select_date($data = '')
{
	/* Default values. */
	$prefix          = "Date_";
	/* Should the select boxes be part of an array when returned from PHP?
	   e.g. setting it to "birthday", would create "birthday[Day]",
	   "birthday[Month]" & "birthday[Year]". Can be combined with prefix */
	$field_array     = null;

	$start_year      = strftime("%Y");
	$end_year        = $start_year;

	$display_days    = true;
	$display_months  = true;
	$display_years   = true;

	$month_format    = "%B";
	/* Write months as numbers by default  GL */
	$month_value_format = "%m";
	$day_format      = "%02d";
	/* Write day values using this format MB */
	$day_value_format = "%d";
	$year_as_text    = false;
	/* Display years in reverse order? Ie. 2000,1999,.... */
	$reverse_years   = false;
	/* <select size>'s of the different <select> tags.
	   If not set, uses default dropdown. */
	$day_size        = null;
	$month_size      = null;
	$year_size       = null;
	$locale          = 'en_US.utf8';

	/* Unparsed attributes common to *ALL* the <select>/<input> tags.
	   An example might be in the template: all_extra ='class ="foo"'. */
	$all_extra       = null;
	/* Separate attributes for the tags. */
	$day_extra       = null;
	$month_extra     = null;
	$year_extra      = null;
	/* Order in which to display the fields.
	   "D" -> day, "M" -> month, "Y" -> year. */

	$field_order     = 'MDY';
	/* String printed between the different fields. */
	$field_separator = "\n";
	$time = '';

	$empty_option    = FALSE;
	$all_empty       = '';
	$day_empty       = null;
	$month_empty     = null;
	$year_empty      = null;

	foreach ($data as $_key => $_value)
	{
		switch ($_key)
		{
			case 'prefix':
			case 'time':
			case 'start_year':
			case 'end_year':
			case 'month_format':
			case 'day_format':
			case 'day_value_format':
			case 'field_array':
			case 'day_size':
			case 'month_size':
			case 'year_size':
			case 'locale':
			case 'all_extra':
			case 'day_extra':
			case 'month_extra':
			case 'year_extra':
			case 'field_order':
			case 'field_separator':
			case 'month_value_format':
			case 'empty_option':
			case 'month_empty':
			case 'day_empty':
			case 'year_empty':
				$$_key = (string)$_value;
				break;

			case 'all_empty':
				$$_key = (string)$_value;
				$day_empty = $month_empty = $year_empty = $all_empty;
				break;

			case 'display_days':
			case 'display_months':
			case 'display_years':
			case 'year_as_text':
			case 'reverse_years':
				$$_key = (bool)$_value;
				break;

			default:
				// may want to throw an error, but ignore for now
		}
	}
	
	setlocale(LC_TIME, $locale);
	
	if ($empty_option == TRUE)
	{
		$day_empty = (isset($day_empty)) ? $day_empty : $all_empty;
		$month_empty = (isset($month_empty)) ? $month_empty : $all_empty;
		$year_empty = (isset($year_empty)) ? $year_empty : $all_empty;
	}
	
	if ( ! empty($time) || (empty($time) && $empty_option == FALSE))
	{
		if (empty($time))
			$time = time();

		// if negative timestamp, use date()
		if (preg_match('!^-\d+$!', $time))
		{
			$time = date('Y-m-d', $time);
		}

		// If $time is not in format yyyy-mm-dd, convert to yyy-mm-dd
		if ( ! preg_match('/^\d{0,4}-\d{0,2}-\d{0,2}$/', $time))
		{
			$time = strftime('%Y-%m-%d', make_timestamp($time));
		}

		// Now split this in pieces, which later can be used to set the select
		$time = explode("-", $time);
	}
	else
	{
		$time = array($year_empty, $month_empty, $day_empty);
	}
	
	// make syntax "+N" or "-N" work with start_year and end_year
	if (preg_match('!^(\+|\-)\s*(\d+)$!', $end_year, $match))
	{
		if ($match[1] == '+')
		{
			$end_year = strftime('%Y') + $match[2];
		}
		else
		{
			$end_year = strftime('%Y') - $match[2];
		}
	}
	if (preg_match('!^(\+|\-)\s*(\d+)$!', $start_year, $match))
	{
		if ($match[1] == '+')
		{
			$start_year = strftime('%Y') + $match[2];
		}
		else
		{
			$start_year = strftime('%Y') - $match[2];
		}
	}
	if (strlen($time[0]) > 0)
	{ 
		// force start year to include given date if not explicitly set
		if ($start_year > $time[0] && !isset($params['start_year']))
		{
			$start_year = $time[0];
		}
		// force end year to include given date if not explicitly set
		if ($end_year < $time[0] && ! isset($params['end_year']))
		{
			$end_year = $time[0];
		}
	}

	$field_order = strtoupper($field_order);

	$form_result = $month_result = $day_result = $year_result = "";

	if ($display_months)
	{
		$month_name = (null !== $field_array) ? $field_array . '[' . $prefix . 'Month]' : $prefix . 'Month';

		$months = array();
		if ($empty_option == TRUE)
		{
			$months[$month_empty] = $month_empty;
		}
		for ($i = 1; $i <= 12; $i++)
		{
			$_tmp_key = strftime($month_value_format, mktime(0, 0, 0, $i, 1, 2000));
			$_tmp_val = strftime($month_format, mktime(0, 0, 0, $i, 1, 2000));
			$months[$_tmp_key] = $_tmp_val;
		}

		$month_selected = $time[1];

		$month_extras = '';
		if (null !== $month_size)
		{
			$month_extras .= ' size="' . $month_size . '"';
		}
		if (null !== $month_extra)
		{
			$month_extras .= ' ' . $month_extra;
		}
		if (null !== $all_extra)
		{
			$month_extras .= ' ' . $all_extra;
		}
			
		$month_result = form_dropdown($month_name, $months, $month_selected, $month_extras);
	}

	if ($display_days)
	{
		$day_name = (null !== $field_array) ? $field_array . '[' . $prefix . 'Day]' : $prefix . 'Day';

		$days = array();
		if ($empty_option == TRUE)
		{
			$days[$day_empty] = $day_empty;
		}
		for ($i = 1; $i <= 31; $i++)
		{
			$days[sprintf($day_format, $i)] = sprintf($day_value_format, $i);
		}

		$day_selected = $time[2];

		$day_extras = '';
		if (null !== $day_size)
		{
			$day_extras .= ' size="' . $day_size . '"';
		}
		if (null !== $all_extra)
		{
			$day_extras .= ' ' . $all_extra;
		}
		if (null !== $day_extra)
		{
			$day_extras .= ' ' . $day_extra;
		}

		$day_result = form_dropdown($day_name, $days, $day_selected, $day_extras);
	}

	if ($display_years) 
	{
		$year_name = (null !== $field_array) ? $field_array . '[' . $prefix . 'Year]' : $prefix . 'Year';

		$year_selected = $time[0];

		$year_extras = '';
		if (null !== $all_extra)
		{
			$year_extras .= ' ' . $all_extra;
		}
		if (null !== $year_extra)
		{
			$year_extras .= ' ' . $year_extra;
		}

		if ($year_as_text)
		{
			$year_result = form_input($year_name, $year_selected, $year_extras);
		}
		else  // display as dropdown
		{
			$years = array();
			if ($empty_option == TRUE)
			{
				$years[$year_empty] = $year_empty;
			}
			$year_range = ($reverse_years) ? range((int)$end_year, (int)$start_year) : range((int)$start_year, (int)$end_year);
			foreach ($year_range AS $_tmp_key)
			{
				$years[$_tmp_key] = $_tmp_key;
			}

			if (null !== $year_size)
			{
				$year_extras .= ' size="' . $year_size . '"';
			}

			$year_result = form_dropdown($year_name, $years, $year_selected, $year_extras);
		}
	}

	// Loop thru the field_order field
	for ($i = 0; $i <= 2; $i++)
	{
		$c = substr($field_order, $i, 1);
		switch ($c)
		{
			case 'D':
				$form_result .= $day_result;
				break;

			case 'M':
				$form_result .= $month_result;
				break;

			case 'Y':
				$form_result .= $year_result;
				break;
		}
		// Add the field separator
		if ($i != 2)
		{
			$form_result .= $field_separator;
		}
	}

	return $form_result;
}

// ------------------------------------------------------------------------

/**
 * Submit Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if (! function_exists('form_submit'))
{	
	function form_submit($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'submit', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
	}
}

// ------------------------------------------------------------------------

/**
 * Reset Button
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
if (! function_exists('form_reset'))
{
	function form_reset($data = '', $value = '', $extra = '')
	{
		$defaults = array('type' => 'reset', 'name' => (( ! is_array($data)) ? $data : ''), 'value' => $value);

		return "<input ".parse_form_attributes($data, $defaults).$extra." />\n";
	}
}

// ------------------------------------------------------------------------

/**
 * Form Label Tag
 *
 * @access	public
 * @param	string	The text to appear onscreen
 * @param	string	The id the label applies to
 * @param	string	Additional attributes
 * @return	string
 */	
if (! function_exists('form_label'))
{
	function form_label($label_text = '', $id = '', $attributes = array())
	{

		$label = '<label';
	
		if ($id != '')
		{
			 $label .= " for=\"$id\"";
		}
		
		if (is_array($attributes) AND count($attributes) > 0)
		{
			foreach ($attributes as $key => $val)
			{
				$label .= ' '.$key.'="'.$val.'"';
			}
		}

		$label .= ">$label_text</label>";

		return $label;
	}
}

// ------------------------------------------------------------------------
/**
 * Fieldset Tag
 *
 * Used to produce <fieldset><legend>text</legend>.  To close fieldset
 * use form_fieldset_close()
 *
 * @access	public
 * @param	string	The legend text
 * @param	string	Additional attributes
 * @return	string
 */	
if (! function_exists('form_fieldset'))
{
	function form_fieldset($legend_text = '', $attributes = array())
	{

		$fieldset = "<fieldset";

		if (is_array($attributes) AND count($attributes) > 0)
		{
			foreach ($attributes as $key => $val)
			{
				$fieldset .= ' '.$key.'="'.$val.'"';
			}
		}
	
		$fieldset .= ">\n";
	
		if ($legend_text != '')
		{
			$fieldset .= "<legend>$legend_text</legend>\n";
		}
		


		return $fieldset;
	}
}

// ------------------------------------------------------------------------

/**
 * Fieldset Close Tag
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if (! function_exists('form_fieldset_close'))
{
	function form_fieldset_close($extra = '')
	{
		return "</fieldset>\n".$extra;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Form Close Tag
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if (! function_exists('form_close'))
{
	function form_close($extra = '')
	{
		return "</form>\n".$extra;
	}
}
	
// ------------------------------------------------------------------------

/**
 * Form Prep
 *
 * Formats text so that it can be safely placed in a form field in the event it has HTML tags.
 *
 * @access	public
 * @param	string
 * @return	string
 */	
if (! function_exists('form_prep'))
{
	function form_prep($str = '')
	{
		if ($str === '')
		{
			return '';
		}

		$temp = '__TEMP_AMPERSANDS__';
	
		// Replace entities to temporary markers so that 
		// htmlspecialchars won't mess them up
		$str = preg_replace("/&#(\d+);/", "$temp\\1;", $str);
		$str = preg_replace("/&(\w+);/",  "$temp\\1;", $str);

		$str = htmlspecialchars($str);

		// In case htmlspecialchars misses these.
		$str = str_replace(array("'", '"'), array("&#39;", "&quot;"), $str);	
	
		// Decode the temp markers back to entities
		$str = preg_replace("/$temp(\d+);/","&#\\1;",$str);
		$str = preg_replace("/$temp(\w+);/","&\\1;",$str);	
	
		return $str;	
	}
}
	
// ------------------------------------------------------------------------

/**
 * Parse the form attributes
 *
 * Helper function used by some of the form helpers
 *
 * @access	private
 * @param	array
 * @param	array
 * @return	string
 */	
if (! function_exists('parse_form_attributes'))
{
	function parse_form_attributes($attributes, $default)
	{
		if (is_array($attributes))
		{
			foreach ($default as $key => $val)
			{
				if (isset($attributes[$key]))
				{
					$default[$key] = $attributes[$key];
					unset($attributes[$key]);
				}
			}
		
			if (count($attributes) > 0)
			{	
				$default = array_merge($default, $attributes);
			}
		}
	
		$att = '';
		foreach ($default as $key => $val)
		{
			if ($key == 'value')
			{
				$val = form_prep($val);
			}
	
			$att .= $key . '="' . $val . '" ';
		}

		return $att;
	}
}

// ------------------------------------------------------------------------

/**
 * Used by form_select_date to make a timestamp from a string.
 *
 * @param string
 * @return string
 */
function make_timestamp($string)
{
    if (empty($string))
    {
        $string = "now";
    }

    $time = strtotime($string);

    if (is_numeric($time) && $time != -1)
        return $time;

    // is mysql timestamp format of YYYYMMDDHHMMSS?
    if (preg_match('/^\d{14}$/', $string))
    {
        $time = mktime(substr($string,8,2),substr($string,10,2),substr($string,12,2),
               substr($string,4,2),substr($string,6,2),substr($string,0,4));

        return $time;
    }

    // couldn't recognize it, try to return a time
    $time = (int) $string;
    if ($time > 0)
        return $time;
    else
        return time();
}


?>