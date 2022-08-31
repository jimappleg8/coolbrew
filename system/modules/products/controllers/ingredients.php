<?php

class Ingredients extends Controller {

   var $cb_db;  // database object for coolbrew tables
   var $hcg_db;  // database object for hcg_public tables

   function Ingredients()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'products'));
      $this->load->helper(array('url', 'menu'));

      // this module is set up to write to the product tables in both 
      // the coolbrew and hcg_public databases. Reads are made from the
      // coolbrew tables only.
      $this->cb_db = $this->load->database('read', TRUE);
      $this->hcg_db = $this->load->database('hcg_read', TRUE);
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates a listing of this site's ingredients
    *
    */
   function index($site_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
/*
      $ingredients['error_msg'] = $this->session->userdata('products_error');
      if ($this->session->userdata('products_error') != '')
         $this->session->set_userdata('products_error', '');
*/
      $this->load->model('Sites');
      $this->load->model('Ingredients');

      $site = $this->Sites->get_site_data($site_id);

      $ingredient_list = $this->Ingredients->get_ingredient_list($site_id);
      $ingredients['ingredient_exists'] = (count($ingredient_list) == 0 && count($nocat_list) == 0) ? FALSE : TRUE;

      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('ingredients');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Ingredients');
      $data['submenu'] = get_submenu($site_id, 'Ingredients');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['ingredients'] = $ingredients;
      $data['ingredient_list'] = $ingredient_list;
      
      $this->load->vars($data);
      
      return $this->load->view('ingredients/list', NULL, TRUE);
   }
   
   // --------------------------------------------------------------------
   
function add($site_id, $this_action)
{
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

/*
   $ingredients ['message'] = $this->session->userdata ('ingredient_message');
   if ($this->session->userdata('ingredient_message') != '')
      $this->session->set_userdata('product_message', '');
*/
   $this->load->helper(array('form', 'text'));
   $this->load->model('Sites');
   $this->load->model('Ingredients');
   $this->load->library(array('validation', 'auditor'));

   $site = $this->Sites->get_site_data($site_id);

   $rules['Ingredient'] = 'trim'; //|required';
   $rules['LatinName'] = 'trim'; //|required';
   $rules['Description'] = 'trim'; //|required';
   $rules['ImageFile'] = 'trim'; //|required';
   $rules['ImageWidth'] = 'trim'; //|required';
   $rules['ImageHeight'] = 'trim'; //|required';
   $rules['ImageAlt'] = 'trim'; //|required';
   $rules['Status'] = 'trim'; //|required';

   $this->validation->set_rules($rules);

   $fields['Ingredient'] = 'Ingredient';
   $fields['LatinName'] = 'Latin Name';
   $fields['Description'] = 'Description';
   $fields['ImageFile'] = 'Image File';
   $fields['ImageWidth'] = 'Image Width';
   $fields['ImageHeight'] = 'Image Height';
   $fields['ImageAlt'] = 'Image Alt';
   $fields['Status'] = 'Status';

   $this->validation->set_fields($fields);

   $defaults['Language'] = 'en_US';

   $this->validation->set_defaults($defaults);

   $this->validation->set_error_delimiters('<div class="error">', '</div>');
   if ($this->validation->run() == false)
   {
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('ingredients');
//      $this->collector->append_js_file('alternate_name');

      $data ['statuses'] = array ('active' => 'active', 'inactive' => 'inactive');
      $data ['languages'] = array ('en_US' => 'en_US', 'en_CA' => 'en_CA', 'fr_CA' => 'fr_CA');
      $data ['last_action'] = $this->session->userdata('last_action') + 1;
      $data ['tabs'] = $this->administrator->get_site_tabs($site_id, 'Ingredients');
      $data ['submenu'] = get_submenu($site_id, 'Ingredients');
      $data ['site_id'] = $site_id;
      $data ['site'] = $site;
      $data ['ingredient'] = $this->Ingredients->get_default_ingredient($site_id);

      $this->load->vars($data);

      return $this->load->view('ingredients/add', null, true);
   }
   else
   {
      if ($this_action > $this->session->userdata('last_action'))
      {
         $this->session->set_userdata('last_action', $this_action);
         $this->_add($site_id);
      }
   }
}

   // --------------------------------------------------------------------
   
function _add($site_id)
{
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

   $fields = $this->validation->_fields;

   foreach ($fields as $key => $value)
      $values [$key] = $this->input->post($key);

   $values ['SiteID'] = $site_id;

   $values['Ingredient'] = ascii_to_entities($values['Ingredient']);
   $values['Description'] = ascii_to_entities($values['Description']);
   $values['LatinName'] = ascii_to_entities($values['LatinName']);

   $this->cb_db->insert('pr_ingredient', $values);
   $this->hcg_db->insert('pr_ingredient', $values);

   $ingredient_id = $this->cb_db->insert_id();

   $this->load->model('Ingredients');
   $new_ingredient_name = strtolower($values ['Ingredient']);
   $this->Ingredients->add_alternate_name($ingredient_id, $new_ingredient_name);
   $this->Ingredients->set_ingredient_code($ingredient_id, str_replace(' ', '-', $new_ingredient_name));

   $last_action = $this->session->userdata('last_action') + 1;

   redirect('ingredients/edit/'.$site_id.'/'.$ingredient_id.'/'.$last_action.'/');
}

   // --------------------------------------------------------------------
   
   /**
    * Updates an ingredient record
    *
    * Auditing: incomplete
    */
   function edit($site_id, $ingredient_id, $this_action)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      $ingredient_message = $this->session->userdata('ingredient_message');
      if ($this->session->userdata('ingredient_message') != '')
         $this->session->set_userdata('ingredient_message', '');

      $this->load->helper(array('form', 'text'));
      $this->load->model('Sites');
      $this->load->model('Ingredients');
      $this->load->library(array('validation', 'auditor'));

      $site = $this->Sites->get_site_data($site_id);

      $old_values = $this->Ingredients->get_ingredient_data($site_id, $ingredient_id);

      $rules['Ingredient'] = 'trim';
      $rules['LatinName'] = 'trim';
      $rules['Description'] = 'trim';
      $rules['Status'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['Ingredient'] = 'Ingredient';
      $fields['LatinName'] = 'Latin Name';
      $fields['Description'] = 'Description';
      $fields['Status'] = 'Status';

      $this->validation->set_fields($fields);

      $defaults = $old_values;
      $defaults['Ingredient'] = entities_to_ascii($defaults['Ingredient']);
      $defaults['LatinName'] = entities_to_ascii($defaults['LatinName']);
      $defaults['Description'] = entities_to_ascii($defaults['Description']);
      $defaults['Status'] = entities_to_ascii($defaults['Status']);

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('products');
         $this->collector->append_js_file('alternate_name');

         $data['statuses'] = array ('active' => 'active', 'inactive' => 'inactive');
         $data['languages'] = array ('en_US' => 'en_US', 'en_CA' => 'en_CA', 'fr_CA' => 'fr_CA');
         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Ingredients');
         $data['submenu'] = get_submenu($site_id, 'Ingredients');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
         $data['ingredient'] = $old_values;
         $data['ingredient_id'] = $ingredient_id;
         $data['ingredient_message'] = $ingredient_message;

         $this->load->vars($data);

         return $this->load->view('ingredients/edit', null, true);
      }
      else
      {
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->_edit($site_id, $ingredient_id);
         }
      }
   }

   // --------------------------------------------------------------------

   function _edit($site_id, $ingredient_id)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');

      if ($ingredient_id == 0)
         show_error('_edit_settings requires that an ingredient ID be supplied.');

      $fields = $this->validation->_fields;

      foreach ($fields as $key => $value)
         $values [$key] = $this->input->post($key);

      $values['Ingredient'] = ascii_to_entities($values['Ingredient']);
      $values['LatinName'] = ascii_to_entities($values['LatinName']);
      $values['Status'] = ascii_to_entities($values['Status']);
      $values['Description'] = ascii_to_entities($values['Description']);

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->cb_db->where('ID', $ingredient_id);
      $this->cb_db->update('pr_ingredient', $values);
      $this->hcg_db->where('ID', $ingredient_id);
      $this->hcg_db->update('pr_ingredient', $values);

      $this->session->set_userdata('ingredient_message', 'The Ingredient information has been updated.');
      $last_action = $this->session->userdata('last_action') + 1;

      redirect('ingredients/edit/'.'/'.$site_id.'/'.$ingredient_id. '/'.$last_action.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * AJAX adds an alternate ingredient name to the list
    *
    * Auditing: incomplete
    */
   function add_ingredient_name($ingredient_id, $name)
   {
      $this->load->model('Ingredients');
      $this->Ingredients->add_alternate_name($ingredient_id, $name);
   }

   // --------------------------------------------------------------------
   
   // ajax response
   function remove_ingredient_name($ingredient_id, $name)
   {
      $this->load->model('Ingredients');
      $this->Ingredients->remove_alternate_name($ingredient_id, $name);
   }

   // --------------------------------------------------------------------
   
   function upload_ingredient_image($site_id, $ingredient_id, $this_action)
   {
      $upload_path = null;
      global $sites;
      foreach ($sites as $site)
      {
         if ($site [0] == $site_id && $site [2] == 'dev')
         {
            $upload_path = '../../'.$site [1].'/images/ingredients';
            break;
         }
      }

      if ($upload_path !== null)
      {
         $config['upload_path'] = $upload_path;
         $config['allowed_types'] = 'jpg|png|gif';
//         $config['max_size'] = 100;
//         $config['max_width'] = 1024;
//         $config['max_height'] = 768;
         $this->load->library('upload', $config);

         $new_file_name = '';
         $this->load->model('Ingredients');

         if ($this->upload->do_upload())
         {
            $result = $this->upload->data();

            if (strlen($result['full_path']) > 0)
            {
               $ingredient_name = strtolower($this->Ingredients->get_name($ingredient_id));
               for ($index = 0; $index < strlen($ingredient_name); $index++)
               {
                  if ($ingredient_name [$index] >= 'a' && $ingredient_name [$index] <= 'z')
                  {
                     $new_file_name .= $ingredient_name [$index];
                  }
                  else if ($ingredient_name [$index] == ' ')
                  {
                     $new_file_name .= '-';
                  }
               }
               $new_file_name .= $result['file_ext'];

               // resize the image using the CodeIgniter library

               // max image width set here
               $max_width = 200;
               
               $orig_width = $result['image_width'];
               $orig_height = $result['image_height'];
               if ($orig_width > $max_width)
               {
                  $ratio = $max_width / $orig_width;
                  $new_width = $orig_width * $ratio;
                  $new_height = $orig_height * $ratio;
                  $config['image_library'] = 'gd2';
                  $config['source_image'] = $result['full_path'];
                  $config['new_image'] = $new_file_name;
                  $config['maintain_ratio'] = true;
                  $config['width'] = $new_width;
                  $config['height'] = $new_height;
                  $this->load->library('image_lib', $config);
                  $resize_success = $this->image_lib->resize();
                  if ($resize_success)
                  {
                     unlink($result['full_path']); // try to delete the original file
                  }
                  else
                  {
// failed to resize the image, directory permissions are supposed to be set to 777 (but it works with 776)
                     $result = $this->image_lib->display_errors();
                  }
               }
               else // it is already small enough, rename it to the appropriate name
               {
                  rename($result ['full_path'], $result ['file_path'].$new_file_name);
               }
            }
         }
         else
         {
         // upload failed, but the user is not required to upload a file
//            $result = $this->upload->display_errors();
         }

         $image_alt = ($this->input->post('ImageAlt') != false ? $this->input->post('ImageAlt') : '');

         if ($this->Ingredients->set_image_file($ingredient_id, $new_file_name, $image_alt, $new_width, $new_height))
         {
            $this->session->set_userdata('ingredient_message', 'The Ingredient image has been updated.');
         }
         else
         {
            $this->session->set_userdata('ingredient_message', 'The Ingredient image could not be updated.');
         }

         redirect('ingredients/edit/'.$site_id.'/'.$ingredient_id. '/'.$this_action.'/');
      }
      else
      {
         // site was not found
      }
   }


}
?>
