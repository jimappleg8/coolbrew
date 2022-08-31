<?php

class Api_Tags extends Controller {

	function Api_Tags()
	{
		parent::Controller();
	}

   //-------------------------------------------------------------------------

   /**
    * Creates a form for generating API keys
    *
    */
   function generate()
   {            
      // (string) The action path
      $action = $this->tag->param(1);

      $this->load->helper(array('form','url'));
      $this->load->library('validation');
      $this->load->model('v1/Keys');

      $rules['URL'] = 'trim|required';
      $rules['ServerLevel'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['URL'] = 'URL';
      $fields['ServerLevel'] = 'Server Level';

      $this->validation->set_fields($fields);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');

      $data['action'] = $action;
      $data['server_levels'] = array(
         '' => '-- Please select a server level --',
         'local' => 'Local',
         'dev' => 'Development',
         'stage' => 'Stage',
         'live' => 'Live',
      );
      $data['display_response'] = FALSE;
      
      if ($this->validation->run() == TRUE)
      {
         $data['key'] = $this->_generate();
         $data['display_response'] = TRUE;
      }
      return $this->load->view('v1/generate', $data, TRUE);
   }

   //-------------------------------------------------------------------------

   /**
    * Processes the API key form
    *
    */
   function _generate()
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
      {
         $values[$key] = $this->input->post($key);
      }
      
      // may want to do some processing of the URL eventually
      $values['ValidURL'] = $values['URL'];
      unset($values['URL']);
      
      $values['APIKey'] = $this->Keys->generate_key($values);
      
      $this->Keys->insert_key($values);
      
      return $values;
   }

}
?>