<?php

class Uploads extends Controller {

   function Uploads()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'recipes'));
      $this->load->helper(array('form', 'text', 'url'));    
   }

   // --------------------------------------------------------------------

   /**
    * Displays form to upload recipe images
    *
    */
   function upload_image($site_id, $recipe_id, $remove = 0, $return = TRUE)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
         
      $base_path = SERVERPATH.'hcgwebdocs/resources/';
      $recipes = array();
      $old_values = array();
      $values = array();

      $this->load->model('Recipes');
      
      // get info about this recipe
      $recipe = $this->Recipes->get_recipe_record($recipe_id);
      
      // make sure the file exists
      if ($recipe['ImageFile'] != '' && $remove == 0)
      {
         if ( ! file_exists($base_path.$recipe['ImageFile']))
         {
            $remove = 1;
         }
      }
      
      if ($remove)
      {
         // save the uploaded file info
         $old_values['ImageFile'] = $recipe['ImageFile'];
         $old_values['ImageWidth'] = $recipe['ImageWidth'];
         $old_values['ImageHeight'] = $recipe['ImageHeight'];

         $values['ImageFile'] = '';
         $values['ImageWidth'] = 0;
         $values['ImageHeight'] = 0;

         $this->Recipes->update_recipe($recipe_id, $values, $old_values);

         $recipe = $this->Recipes->get_recipe_record($recipe_id);
      }
      
      $image_exists = FALSE;
      if ($recipe['ImageFile'] != '')
      {
         $image_exists = TRUE;
      }

      $config['upload_path'] = $base_path.$site_id.'/recipes/beauty';
      $config['override_name'] = url_title($recipe['Title']);
      $config['allowed_types'] = 'gif|jpg|png';
      $config['max_size'] = '2048';
      $config['max_width'] = '1024';
      $config['max_height'] = '768';

      $this->load->library('upload', $config);

      if ($this->upload->do_upload('ImageFile') == FALSE && $image_exists == FALSE)
      {
         $recipes['error'] = '';
         if ($this->input->post('ImageFile') != '')
         {
            $recipes['error'] = $this->upload->display_errors();
         }
      }
      elseif ($image_exists == FALSE)
      {
         $upload = $this->upload->data();
         
         // save the uploaded file info
         $old_values['ImageFile'] = $recipe['ImageFile'];
         $old_values['ImageWidth'] = $recipe['ImageWidth'];
         $old_values['ImageHeight'] = $recipe['ImageHeight'];

         $values['ImageFile'] = $site_id.'/recipes/beauty/'.$upload['file_name'];
         $values['ImageWidth'] = $upload['image_width'];
         $values['ImageHeight'] = $upload['image_height'];

         $this->Recipes->update_recipe($recipe_id, $values, $old_values);
         
         $data['status'] = 'added';
      }

      $data['recipes'] = $recipes; // errors and messages
      $data['config'] = $config;
      $data['status'] = (isset($data['status'])) ? $data['status'] : '';
      $data['recipe'] = $this->Recipes->get_recipe_record($recipe_id);
      $data['site_id'] = $site_id;
      $data['recipe_id'] = $recipe_id;

      $this->load->vars($data);
      
      $result = $this->load->view('uploads/image', NULL, TRUE);

      if ($return == TRUE)
      {
         return $result;
      }
      else
      {
         echo $result;
         exit;
      }
   }

   // --------------------------------------------------------------------

   /**
    * Displays form to upload recipe category images
    *
    */
   function upload_category_image($site_id, $category_id, $remove = 0, $return = TRUE)
   {
      if ( ! $this->administrator->check_acl($site_id.'-site', 'view'))
         redirect('cp/login/sorry');
         
      $base_path = SERVERPATH.'hcgwebdocs/resources/';
      $recipes = array();
      $old_values = array();
      $values = array();

      $this->load->model('Categories');
      
      // get info about this recipe
      $category = $this->Categories->get_category_data($category_id);
      
      // make sure the file exists
      if ($category['ImageFile'] != '' && $remove == 0)
      {
         if ( ! file_exists($base_path.$category['ImageFile']))
         {
            $remove = 1;
         }
      }
      
      if ($remove)
      {
         // save the uploaded file info
         $old_values['ImageFile'] = $category['ImageFile'];
         $old_values['ImageWidth'] = $category['ImageWidth'];
         $old_values['ImageHeight'] = $category['ImageHeight'];

         $values['ImageFile'] = '';
         $values['ImageWidth'] = 0;
         $values['ImageHeight'] = 0;

         $this->Categories->update_recipe_category($site_id, $category_id, $values, $old_values);

         $category = $this->Categories->get_category_data($category_id);
      }
      
      $image_exists = FALSE;
      if ($category['ImageFile'] != '')
      {
         $image_exists = TRUE;
      }

      $config['upload_path'] = $base_path.$site_id.'/recipes/categories';
      $config['override_name'] = url_title($category['CategoryName']);
      $config['allowed_types'] = 'gif|jpg|png';
      $config['max_size'] = '2048';
      $config['max_width'] = '1024';
      $config['max_height'] = '768';

      $this->load->library('upload', $config);

      if ($this->upload->do_upload('ImageFile') == FALSE && $image_exists == FALSE)
      {
         $recipes['error'] = '';
         if ($this->input->post('ImageFile') != '')
         {
            $recipes['error'] = $this->upload->display_errors();
         }
      }
      elseif ($image_exists == FALSE)
      {
         $upload = $this->upload->data();
         
         // save the uploaded file info
         $old_values['ImageFile'] = $category['ImageFile'];
         $old_values['ImageWidth'] = $category['ImageWidth'];
         $old_values['ImageHeight'] = $category['ImageHeight'];

         $values['ImageFile'] = $site_id.'/recipes/categories/'.$upload['file_name'];
         $values['ImageWidth'] = $upload['image_width'];
         $values['ImageHeight'] = $upload['image_height'];

         $this->Categories->update_recipe_category($site_id, $category_id, $values, $old_values);
         
         $data['status'] = 'added';
      }

      $data['recipes'] = $recipes; // errors and messages
      $data['config'] = $config;
      $data['status'] = (isset($data['status'])) ? $data['status'] : '';
      $data['category'] = $this->Categories->get_category_data($category_id);
      $data['site_id'] = $site_id;
      $data['category_id'] = $category_id;

      $this->load->vars($data);
      
      $result = $this->load->view('uploads/category_image', NULL, TRUE);

      if ($return == TRUE)
      {
         return $result;
      }
      else
      {
         echo $result;
         exit;
      }
   }

}

/* End of file uploads.php */
/* Location: ./system/modules/recipes/controllers/uploads.php */