   // --------------------------------------------------------------------
   
   /**
    * "Add" Page
    *
    * Shows a form representing the <?=$table_name;?> table
    * so that data can be inserted
    *
    * @access   public
    * @return   string   the HTML "add" page
    */
   function add()
   {
      $this->load->helper(array('form', 'text'));
      $this->load->library('validation');
      
<?php foreach ($fields AS $field): ?>
<?php if ($field->primary_key == 1) continue; ?>
      $rules['<?=$field->name;?>'] = 'trim';
<?php endforeach; ?>

      $this->validation->set_rules($rules);

<?php foreach ($fields AS $field): ?>
<?php if ($field->primary_key == 1) continue; ?>
      $fields['<?=$field->name;?>'] = '<?=$field->name;?>';
<?php endforeach; ?>

      $this->validation->set_fields($fields);

<?php foreach ($fields AS $field): ?>
<?php if ($field->primary_key == 1) continue; ?>
      $defaults['<?=$field->name;?>'] = '<?=$field->default;?>';
<?php endforeach; ?>

      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div style="color:red;">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $data['action'] = $this->base_uri.'/add';

         $this->load->vars($data);
         echo $this->load->view('add', NULL, TRUE);
      }
      else
      {
         $this->insert();
      }
   }
   
