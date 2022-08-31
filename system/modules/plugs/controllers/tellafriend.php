<?php

class Tellafriend extends Controller {

   function Tellafriend()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'sharing'));
      $this->load->helper(array('url','menu'));
   }

   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of all tell-a-friend records for this site
    *
    */
   function index($site_id)
   {
      $admin['error_msg'] = $this->session->userdata('plugs_error');
      if ($this->session->userdata('plugs_error') != '')
         $this->session->set_userdata('plugs_error', '');

      $this->load->model('Tell');
      
      $site = $this->administrator->get_site_data($site_id);
      
      $tell_list = $this->Tell->get_tells($site_id);

      $admin['tell_exists'] = (count($tell_list) > 0) ? TRUE : FALSE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('plugs');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Plugs');
      $data['submenu'] = get_submenu($site_id, 'Tell a Friend');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['admin'] = $admin;
      $data['tell_list'] = $tell_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('tellafriend/list', NULL, TRUE);
   }

   // --------------------------------------------------------------------

   /**
    * Generates the add contest form
    *
    */
   function add($site_id, $this_action)
   {      
      $this->load->helper(array('fckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Tell');
      
      $site = $this->administrator->get_site_data($site_id);

      $rules['TellName'] = 'trim|required';
      $rules['Language'] = 'trim|required';

      $this->validation->set_rules($rules);

      $fields['TellName'] = 'Tell Name';
      $fields['Language'] = 'Language';

      $this->validation->set_fields($fields);

      $defaults['Language'] = 'en_US';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('plugs');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Plugs');
         $data['submenu'] = get_submenu($site_id, 'Tell a Friend');
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('tellafriend/add', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->insert($site_id);
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Inserts a new contest record
    *
    */
   function insert($site_id)
   {
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);

      $values['SiteID'] = $site_id;
      
      // this is where I should add default values:
      $values['NumFriendFields'] = 4;
      $values['WrapperTemplate'] = '<div>{page_content}</div>';
      
      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $tell_id = $this->Tell->insert_tell($values);

      $last_action = $this->session->userdata('last_action') + 1;      
      redirect("tellafriend/edit/".$site_id.'/'.$tell_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Generates the edit contest form
    *
    */
   function edit($site_id, $tell_id, $this_action)
   {
      if (FALSE === $tell_id)
         return $this->index();
      
      $admin['message'] = $this->session->userdata('plugs_message');
      if ($this->session->userdata('plugs_message') != '')
         $this->session->set_userdata('plugs_message', '');

      $this->load->helper(array('fckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Tell');
      
      $site = $this->administrator->get_site_data($site_id);

      $rules['TellName'] = 'trim|required';
      $rules['Language'] = 'trim|required';
      $rules['URL'] = 'trim';
      $rules['UseSuppliedURL'] = 'trim';
      $rules['NumFriendFields'] = 'trim';
      $rules['SendSenderCopy'] = 'trim';
      $rules['WrapperTemplate'] = 'trim';
      $rules['FormTemplate'] = 'trim';
      $rules['ResultsTemplate'] = 'trim';
      $rules['EmailTemplate'] = 'trim';
      $rules['PrivacyPolicy'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaAbstract'] = 'trim';
      $rules['MetaRobots'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['TellName'] = 'Contest Name';
      $fields['Language'] = 'Language';
      $fields['URL'] = 'URL';
      $fields['UseSuppliedURL'] = 'Use Supplied URL';
      $fields['NumFriendFields'] = 'Number of Friends Fields';
      $fields['SendSenderCopy'] = 'Send a copy to the Sender?';
      $fields['WrapperTemplate'] = 'Tell a friend Wrapper Template';
      $fields['FormTemplate'] = 'Tell a friend Form Template';
      $fields['ResultsTemplate'] = 'Tell a friend Results Template';
      $fields['EmailTemplate'] = 'Tell a friend Email Template';
      $fields['PrivacyPolicy'] = 'Privacy Policy';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaAbstract'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Robots';

      $this->validation->set_fields($fields);

      $defaults = $this->Tell->get_tell_by_id($tell_id);
      
      $defaults['WrapperTemplate'] = entities_to_ascii($defaults['WrapperTemplate']);
      $defaults['FormTemplate'] = entities_to_ascii($defaults['FormTemplate']);
      $defaults['ResultsTemplate'] = entities_to_ascii($defaults['ResultsTemplate']);
      $defaults['EmailTemplate'] = entities_to_ascii($defaults['EmailTemplate']);
      $defaults['PrivacyPolicy'] = entities_to_ascii($defaults['PrivacyPolicy']);
      $defaults['MetaTitle'] = entities_to_ascii($defaults['MetaTitle']);
      $defaults['MetaDescription'] = entities_to_ascii($defaults['MetaDescription']);
      $defaults['MetaKeywords'] = entities_to_ascii($defaults['MetaKeywords']);
      $defaults['MetaAbstract'] = entities_to_ascii($defaults['MetaAbstract']);
      $defaults['MetaRobots'] = entities_to_ascii($defaults['MetaRobots']);
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('contests');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Plugs');
         $data['submenu'] = get_submenu($site_id, 'Tell a Friend');
         $data['site_id'] = $site_id;
         $data['tell_id'] = $tell_id;
         $data['site'] = $site;
         $data['admin'] = $admin;
      
         $this->load->vars($data);
   	
         return $this->load->view('tellafriend/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->update($site_id, $tell_id);
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Updates and existing tell-a-friend record
    *
    */
   function update($site_id, $tell_id)
   {
      if (FALSE === $tell_id)
         show_error('update TellAFriend requires that a Tell ID be supplied.');

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['WrapperTemplate'] = ascii_to_entities($values['WrapperTemplate']);
      $values['FormTemplate'] = ascii_to_entities($values['FormTemplate']);
      $values['ResultsTemplate'] = ascii_to_entities($values['ResultsTemplate']);
      $values['EmailTemplate'] = ascii_to_entities($values['EmailTemplate']);
      $values['PrivacyPolicy'] = ascii_to_entities($values['PrivacyPolicy']);
      $values['MetaTitle'] = ascii_to_entities($values['MetaTitle']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);
      $values['MetaAbstract'] = ascii_to_entities($values['MetaAbstract']);
      $values['MetaRobots'] = ascii_to_entities($values['MetaRobots']);

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->Tell->update_tell($tell_id, $values);
      
      $this->session->set_userdata('plugs_message', $values['TellName'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;      
      redirect("tellafriend/edit/".$site_id.'/'.$tell_id.'/'.$last_action.'/');
   }

   // --------------------------------------------------------------------
   
   /**
    * Delete Confirmation
    *
    * @access   public
    * @return   string   the HTML "delete confirm" page
    */
   function delete($id)
   {
      $this->load->model('Tell');
      
      $tell = $this->Tell->get_tell_by_id($id);

      $message = 'Are you sure you want to delete the '.$tell['TellName'].' Tell a Friend Widget?';
      
      $data['message'] = $message;
      $data['no'] = anchor(array($this->base_uri, 'index'), 'No');
      $data['yes'] = anchor(array($this->base_uri, 'do_delete', $id), 'Yes');
   
      return $this->load->view('tellafriend/delete', $data, TRUE);
   }
   
   // --------------------------------------------------------------------
   
   /**
    * Delete
    *
    * @access   public
    * @return   void   redirects to the list (index) page
    */
   function do_delete($id)
   {
      $this->load->model('Tell');
      
      $this->Tell->delete_tell($id);

      header("Refresh:0;url=".site_url(array($this->base_uri, 'index')));
      exit;
   }

}
?>