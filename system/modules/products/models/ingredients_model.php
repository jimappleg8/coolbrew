<?php

class Ingredients_model extends Model {

   function Ingredients_model()
   {
      parent::Model();
      $this->load->database('read');
   }

   // --------------------------------------------------------------------

function get_name ($ingredient_id)
{
	$sql = 'select `Ingredient` from pr_ingredient where ID = "'.$ingredient_id.'";';
	$query = $this->db->query ($sql);
	$result = $query->result_array ();
	if (count ($result) == 0)
		return '';
	return $result [0] ['Ingredient'];
}

   // --------------------------------------------------------------------

function set_ingredient_code ($ingredient_id, $ingredient_code)
{
	$sql = 'update `pr_ingredient` set IngredientCode = "'.$ingredient_code.'" where ID = '.$ingredient_id;
	$query = $this->db->query ($sql);
	return $this->db->affected_rows () === 1;
}

   // --------------------------------------------------------------------

function get_alternate_names ($ingredient_id)
{
	$sql = 'select `Ingredient` from pr_ingredient_link where IngredientID = "'.$ingredient_id.'";';
	$query = $this->db->query ($sql);
	$result = $query->result_array ();

	$ret_arr = array ();
	foreach ($result as $row)
		$ret_arr [] = trim ($row ['Ingredient']);
	return $ret_arr;
}

   // --------------------------------------------------------------------

function get_alternate_names_by_code ($ingredient_code)
{
	$sql = 'select pr_ingredient_link.Ingredient as alternate_name from pr_ingredient_link inner join pr_ingredient on pr_ingredient.IngredientCode = "'.$ingredient_code.'" and pr_ingredient.ID = pr_ingredient_link.IngredientID;';
	$query = $this->db->query ($sql);
	$result = $query->result_array ();

	$ret_arr = array ();
	foreach ($result as $row)
		$ret_arr [] = trim ($row ['alternate_name']);
	return $ret_arr;
}

   // --------------------------------------------------------------------

function add_alternate_name ($ingredient_id, $name)
{
	$sql = 'insert into `pr_ingredient_link` set Ingredient = "'.$name.'", IngredientID = "'.$ingredient_id.'";';
	$query = $this->db->query ($sql);
	return $this->db->affected_rows () === 1;
}

   // --------------------------------------------------------------------

function remove_alternate_name ($ingredient_id, $name)
{
	$sql = 'delete from `pr_ingredient_link` where IngredientID = "'.$ingredient_id.'" and Ingredient = "'.$name.'";';
	$query = $this->db->query ($sql);
	return $this->db->affected_rows ();
}

   // --------------------------------------------------------------------

function set_image_file ($ingredient_id, $image_file, $image_alt, $image_width, $image_height)
{
	$sql = 'update `pr_ingredient` ';

	$sql_params = '';
	if (strlen ($image_file) > 0)
		$sql_params .= 'set ImageFile = "'.$image_file.'"';
	if (strlen ($image_alt) > 0)
	{
		if (strlen ($sql_params) > 0)
			$sql_params .= ', ';
		else
			$sql_params .= 'set ';
		$sql_params .= 'ImageAlt = "'.$image_alt.'"';
	}
	if (strlen ($sql_params) > 0)
		$sql_params .= ', ';
	$sql_params .= 'ImageWidth = "'.$image_width.'", ImageHeight = "'.$image_height.'"';

	if (strlen ($sql_params) > 0)
	{
		$sql .= $sql_params.' where ID = '.$ingredient_id.';';
		$query = $this->db->query ($sql);
		return $query !== false;
	}
	else
		return true; // nothing to write to the database, but no errors occurred
}

   // --------------------------------------------------------------------

function get_default_ingredient ($site_id)
{
	return array (
		'ID' => '',
		'SiteID' => $site_id,
		'IngredientCode' => '',
		'Ingredient' => '',
		'LatinName' => '',
		'ImageFile' => '',
		'ImageWidth' => '',
		'ImageHeight' => '',
		'ImageAlt' => '',
		'Status' => 'active',
		'Description' => '',
		'CreatedDate' => '',
		'CreatedBy' => '',
		'RevisedDate' => '',
		'RevisedBy' => '',
		'alternate_name' => array ()
	);
}

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified ingredient ID
    *
    * @access   public
    * @return   array
    */
   function get_ingredient_data($site_id, $ingredient_id)
   {
      $sql = 'SELECT * FROM pr_ingredient '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND ID = "'.$ingredient_id.'"';
      $query = $this->db->query($sql);
      $ingredient = $query->row_array();
	$alternate_name = $this->get_alternate_names ($ingredient_id);
	$ingredient ['alternate_name'] = $alternate_name;

      return $ingredient;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified ingredient code
    *
    * @access   public
    * @return   array
    */
   function get_ingredient_data_by_code($site_id, $ingredient_code)
   {
      $sql = 'SELECT * FROM pr_ingredient '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND IngredientCode = "'.$ingredient_code.'"';
      $query = $this->db->query($sql);
      $ingredient = $query->row_array();
	$alternate_name = $this->get_alternate_names_by_code ($ingredient_code);
	$ingredient ['alternate_name'] = $alternate_name;
      
      return $ingredient;
   }

   // --------------------------------------------------------------------

   /**
    * Returns list of all active ingredients for site ID
    *
    * @access   public
    * @return   array
    */
   function get_ingredient_list($site_id)
   {
      $sql = 'SELECT * FROM pr_ingredient '.
             'WHERE SiteID = "'.$site_id.'" '.
             'AND Status = "active" '.
             'ORDER BY Ingredient';
      $query = $this->db->query($sql);
      $ingredients = $query->result_array();
      
      return $ingredients;
   }

   // --------------------------------------------------------------------

function get_product_ingredient_data ($site_id, $ingredient_list)
{
	// strip out the formatting characters to retain the original list
	$original_list = str_replace ('|', '', $ingredient_list);
	$original_list = str_replace ('!', '', $original_list);

	$ingredient_info = array ();
	$ingredients = $this->parse_ingredients ($ingredient_list);
	foreach ($ingredients as $ingredient)
	{
		// words that are never ingredient names
		if ($ingredient == 'and')
		{
			$is_ingredient = false;
		}
		else
		{
			$is_ingredient = true;
			$pos = 0;
			while ($pos < strlen ($ingredient))
			{
				// skip html entities
				if (ord ($ingredient [$pos]) == ord ('&'))
				{
					$next_space = strpos ($ingredient, ' ', $pos);
					$next_semicolon = strpos ($ingredient, ';', $pos);
					$in_html_entity = $next_semicolon !== false && ($next_space === false || $next_semicolon < $next_space);
					$pos = $next_semicolon + 1;
				}
				else
					$in_html_entity = false;

				if (!$in_html_entity)
				{
					// don't include any element that contains anything other than lower case letters and spaces
					if ((ord ($ingredient [$pos]) < ord ('a') || ord ($ingredient [$pos]) > ord ('z')) &&
						ord ($ingredient [$pos]) != ord (' '))
					{
						$is_ingredient = false;
						break;
					}
				}

				++$pos;
			}
		}
		if ($is_ingredient)
		{
			$sql = 'select * from pr_ingredient inner join pr_ingredient_link on pr_ingredient.ID = pr_ingredient_link.IngredientID and pr_ingredient.SiteID = "'.$site_id.'" and (instr(pr_ingredient_link.Ingredient, "'.$ingredient.'") != 0 or instr(pr_ingredient.IngredientCode, "'.$ingredient.'") != 0);';
			$query = $this->db->query ($sql);
			if ($query->num_rows () > 0)
			{
				$ingredient_info [] ['info'] = $query->row_array ();
			}
			else
			{
				// this ingredient does not exist in the database
				$ingredient_info [] ['info'] = null;
			}
		}
		else
		{
			// this is not an ingredient, but we still need the punctuation
			$ingredient_info [] ['info'] = null;
		}
		$cur_index = count ($ingredient_info) - 1;
		$ingredient_info [$cur_index] ['display_text'] = trim ($ingredient);
		$ingredient_info [$cur_index] ['original_list'] = $original_list;
	}
	return $ingredient_info;
}


   // --------------------------------------------------------------------

   /**
    * Returns the ingredient list without the special characters
    *
    * @access   public
    * @return   array
    */
   function clean_ingredients($ingredients)
   {
      $ingredients = str_replace(',!', ',', $ingredients);
      $ingredients = str_replace('|', '', $ingredients);
      
      return $ingredients;
   }
   
   // --------------------------------------------------------------------

   /**
    * Returns an array with the ingredient list broken into pieces
    *
    * @access   public
    * @return   array
    */
   function parse_ingredients($ingredients)
   {
      $this->load->helper ('text');

      $ingredients = entities_to_ascii (strtolower ($ingredients));

      $ingredients = str_replace(',!', '@@@', $ingredients);

      // add an implied | character after the following words and expressions
      $ingredients = str_replace ('organic ', 'organic| ', $ingredients);
//      $ingredients = str_replace ('natural ', 'natural| ', $ingredients);

      // this is the first split, according to commas
      $strings = explode(',', $ingredients);
      for ($i=0; $i<count($strings); $i++)
      {
         // the second split, looking for forced breaks
         $fibers = explode('|', $strings[$i]);
         foreach ($fibers AS $fiber)
         {
            $fiber = trim(str_replace('@@@', ',', $fiber));

		// before we split on colons, we will preserve colons that are enclosed within tags
		$b_close = '^^^/b^^^';
		$fiber = str_replace (':</b>', $b_close, $fiber);

            // look for non-word characters and mark them
            $fiber = trim(preg_replace('/([!;:\[\]\(\)\.])/', '@@@$1@@@', $fiber), '@@@');
		$fiber = ascii_to_entities ($fiber);

            // the third split, looking for other characters
            $cells = explode('@@@', $fiber);
            foreach ($cells AS $cell)
            {
		if (strpos ($cell, $b_close) !== false)
		{
			$sub_cell = explode ($b_close, $cell);
			for ($sub = 0; $sub < count ($sub_cell); $sub++)
			{
				if ($sub < count ($sub_cell) - 1)
					$new_string [] = trim ($sub_cell [$sub]).': </b>';
				else
					$new_string [] = trim ($sub_cell [$sub]);
			}
		}
		else
			$new_string[] = trim($cell);
            }
         }
         if ($i < count($strings) - 1)
         {
            $new_string[] = ',';
         }
      }
//      echo "<pre>"; print_r($new_string); echo "</pre>"; exit;

      return $new_string;
   }

   // --------------------------------------------------------------------

   /**
    * Returns data for the specified ingredient ID
    *
    * @access   public
    * @return   array
    */
   function process_ingredients($ingredients)
   {
      $new_string = $this->parse_ingredients($ingredients);
      
      for ($i=0; $i<count($new_string); $i++)
      {
         if ($new_string[$i] != ',')
         {
            $new_string[$i] = $this->make_ingredient_link($new_string[$i]);
         }
      }
      $new_ingredients = implode(' ', $new_string);
      $new_ingredients = preg_replace('/ (\W)/', "\1", $new_ingredients);

      return $new_ingredients;
   }

}

/* End of file ingredients_model.php */
/* Location: ./system/modules/products/models/ingredients_model.php */