<?php

class Contests extends Controller {

   function Contests()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'contests'));
      $this->load->helper(array('url','menu'));
   }

   // --------------------------------------------------------------------
   
   /**
    * Generates a listing of all contests for this site
    *
    */
   function index($site_id)
   {
      $contest['error_msg'] = $this->session->userdata('contest_error');
      if ($this->session->userdata('contest_error') != '')
         $this->session->set_userdata('contest_error', '');

      $this->load->model('Contests');
      
      $site = $this->administrator->get_site_data($site_id);
      
      $contest_list = $this->Contests->get_contests($site_id);

      $contest['contest_exists'] = (count($contest_list) > 0) ? TRUE : FALSE;
      
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('contests');
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Contests');
      $data['submenu'] = get_submenu($site_id, 'Contests');
      $data['site_id'] = $site_id;
      $data['site'] = $site;
      $data['contest'] = $contest;
      $data['contest_list'] = $contest_list;
      
      $this->load->vars($data);
   	
      return $this->load->view('contests/list', NULL, TRUE);
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
      $this->load->model('Contests');
      
      $site = $this->administrator->get_site_data($site_id);

      $rules['ContestName'] = 'trim|required';
      $rules['Language'] = 'trim|required';
      $rules['ContestTitle'] = 'trim';
      $rules['StartDate'] = 'trim';
      $rules['EndDate'] = 'trim';
      $rules['EntryFrequency'] = 'trim';
      $rules['WrapperTemplate'] = 'trim';
      $rules['LandingPageTemplate'] = 'trim';
      $rules['EntryIsLandingPage'] = 'trim';
      $rules['EntryWrapperTemplate'] = 'trim';
      $rules['EntryFormTemplate'] = 'trim';
      $rules['EntrySuccessTemplate'] = 'trim';
      $rules['EntryRejectedTemplate'] = 'trim';
      $rules['EntryClosedTemplate'] = 'trim';
      $rules['EntryEmailTemplate'] = 'trim';
      $rules['TellAFriendIsEnabled'] = 'trim';
      $rules['FriendEntryAction'] = 'trim';
      $rules['MaxExtraEntries'] = 'trim';
      $rules['SendTellerNotice'] = 'trim';
      $rules['TellAFriendWrapperTemplate'] = 'trim';
      $rules['TellAFriendFormTemplate'] = 'trim';
      $rules['TellAFriendResultsTemplate'] = 'trim';
      $rules['TellAFriendEmailTemplate'] = 'trim';
      $rules['QuizIsEnabled'] = 'trim';
      $rules['QuizID'] = 'trim';
      $rules['QuizResultsTemplate'] = 'trim';
      $rules['ShortRules'] = 'trim';
      $rules['OfficialRules'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaAbstract'] = 'trim';
      $rules['MetaRobots'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ContestName'] = 'Contest Name';
      $fields['Language'] = 'Language';
      $fields['ContestTitle'] = 'Contest Title';
      $fields['StartDate'] = 'Start Date';
      $fields['EndDate'] = 'End Date';
      $fields['EntryFrequency'] = 'Entry Frequncy';
      $fields['WrapperTemplate'] = 'Wrapper Template';
      $fields['LandingPageTemplate'] = 'Landing Page Template';
      $fields['EntryIsLandingPage'] = 'Entry is the Landing Page';
      $fields['EntryWrapperTemplate'] = 'Entry Wrapper Template';
      $fields['EntryFormTemplate'] = 'Entry Form Template';
      $fields['EntrySuccessTemplate'] = 'Entry Success Template';
      $fields['EntryRejectedTemplate'] = 'Entry Rejected Template';
      $fields['EntryClosedTemplate'] = 'Entry Closed Template';
      $fields['EntryEmailTemplate'] = 'Entry Email Template';
      $fields['TellAFriendIsEnabled'] = 'Enable Tell a friend';
      $fields['FriendEntryAction'] = 'Friend Entry Action';
      $fields['MaxExtraEntries'] = 'Max Extra Entries';
      $fields['SendTellerNotice'] = 'Send Teller Notice';
      $fields['TellAFriendWrapperTemplate'] = 'Tell a friend Wrapper Template';
      $fields['TellAFriendFormTemplate'] = 'Tell a friend Form Template';
      $fields['TellAFriendResultsTemplate'] = 'Tell a friend Results Template';
      $fields['TellAFriendEmailTemplate'] = 'Tell a friend Email Template';
      $fields['QuizIsEnabled'] = 'Enable Quiz/Survey';
      $fields['QuizID'] = 'Quiz ID';
      $fields['QuizResultsTemplate'] = 'Quiz Results Template';
      $fields['ShortRules'] = 'Short Rules';
      $fields['OfficialRules'] = 'Official Rules';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaAbstract'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Robots';

      $this->validation->set_fields($fields);

      $defaults['Language'] = 'en_US';
      $defaults['StartDate'] = 'YYYY-MM-DD HH:MM:SS';
      $defaults['EndDate'] = 'YYYY-MM-DD HH:MM:SS';
      $defaults['EntryFrequency'] = 'once';
      $defaults['EntryIsLandingPage'] = 0;
      $defaults['TellAFriendIsEnabled'] = 0;
      $defaults['FriendEntryAction'] = 'none';
      $defaults['MaxExtraEntries'] = 0;
      $defaults['SendTellerNotice'] = 0;
      $defaults['QuizIsEnabled'] = 0;
      $defaults['MetaRobots'] = 'index,follow';
      
      $this->validation->set_defaults($defaults);

      $this->validation->set_error_delimiters('<div class="error">', '</div>');
      
      if ($this->validation->run() == FALSE)
      {
         $this->collector->append_css_file('admin');
         $this->collector->append_css_file('contests');

         $data['last_action'] = $this->session->userdata('last_action') + 1;
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Contests');
         $data['submenu'] = get_submenu($site_id, 'Contests');
         $data['frequencies'] = $this->Contests->get_entry_frequencies_list();
         $data['entry_actions'] = $this->Contests->get_friend_entry_action_list();
         $data['quizes'] = array();
         $data['site_id'] = $site_id;
         $data['site'] = $site;
      
         $this->load->vars($data);
   	
         return $this->load->view('contests/add', NULL, TRUE);
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
      $this->load->database('read');
      
      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['SiteID'] = $site_id;
      
      if ($values['StartDate'] == 'YYYY-MM-DD HH:MM:SS')
         unset($values['StartDate']);

      if ($values['EndDate'] == 'YYYY-MM-DD HH:MM:SS')
         unset($values['EndDate']);

      $values['WrapperTemplate'] = ascii_to_entities($values['WrapperTemplate']);
      $values['LandingPageTemplate'] = ascii_to_entities($values['LandingPageTemplate']);
      $values['EntryWrapperTemplate'] = ascii_to_entities($values['EntryWrapperTemplate']);
      $values['EntryFormTemplate'] = ascii_to_entities($values['EntryFormTemplate']);
      $values['EntrySuccessTemplate'] = ascii_to_entities($values['EntrySuccessTemplate']);
      $values['EntryRejectedTemplate'] = ascii_to_entities($values['EntryRejectedTemplate']);
      $values['EntryClosedTemplate'] = ascii_to_entities($values['EntryClosedTemplate']);
      $values['EntryEmailTemplate'] = ascii_to_entities($values['EntryEmailTemplate']);
      $values['TellAFriendWrapperTemplate'] = ascii_to_entities($values['TellAFriendWrapperTemplate']);
      $values['TellAFriendFormTemplate'] = ascii_to_entities($values['TellAFriendFormTemplate']);
      $values['TellAFriendResultsTemplate'] = ascii_to_entities($values['TellAFriendResultsTemplate']);
      $values['TellAFriendEmailTemplate'] = ascii_to_entities($values['TellAFriendEmailTemplate']);
      $values['QuizResultsTemplate'] = ascii_to_entities($values['QuizResultsTemplate']);
      $values['ShortRules'] = ascii_to_entities($values['ShortRules']);
      $values['OfficialRules'] = ascii_to_entities($values['OfficialRules']);

      $values['MetaTitle'] = ascii_to_entities($values['MetaTitle']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);
      $values['MetaAbstract'] = ascii_to_entities($values['MetaAbstract']);
      $values['MetaRobots'] = ascii_to_entities($values['MetaRobots']);

      $values['CreatedDate'] = date('Y-m-d H:i:s');
      $values['CreatedBy'] = $this->session->userdata('username');

      $this->db->insert('contests', $values);
      
      redirect("contests/index/".$site_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Generates the edit contest form
    *
    */
   function edit($site_id, $contest_id, $this_action)
   {
      if (FALSE === $contest_id)
         return $this->index();
      
      $admin['message'] = $this->session->userdata('contest_message');
      if ($this->session->userdata('contest_message') != '')
         $this->session->set_userdata('contest_message', '');

      $this->load->helper(array('fckeditor', 'text'));
      $this->load->library('validation');
      $this->load->model('Contests');
      
      $site = $this->administrator->get_site_data($site_id);

      $rules['ContestName'] = 'trim|required';
      $rules['Language'] = 'trim|required';
      $rules['ContestTitle'] = 'trim';
      $rules['StartDate'] = 'trim';
      $rules['EndDate'] = 'trim';
      $rules['EntryFrequency'] = 'trim';
      $rules['WrapperTemplate'] = 'trim';
      $rules['LandingPageTemplate'] = 'trim';
      $rules['EntryIsLandingPage'] = 'trim';
      $rules['EntryWrapperTemplate'] = 'trim';
      $rules['EntryFormTemplate'] = 'trim';
      $rules['EntrySuccessTemplate'] = 'trim';
      $rules['EntryRejectedTemplate'] = 'trim';
      $rules['EntryClosedTemplate'] = 'trim';
      $rules['EntryEmailTemplate'] = 'trim';
      $rules['TellAFriendIsEnabled'] = 'trim';
      $rules['FriendEntryAction'] = 'trim';
      $rules['MaxExtraEntries'] = 'trim';
      $rules['SendTellerNotice'] = 'trim';
      $rules['TellAFriendWrapperTemplate'] = 'trim';
      $rules['TellAFriendFormTemplate'] = 'trim';
      $rules['TellAFriendResultsTemplate'] = 'trim';
      $rules['TellAFriendEmailTemplate'] = 'trim';
      $rules['QuizIsEnabled'] = 'trim';
      $rules['QuizID'] = 'trim';
      $rules['QuizResultsTemplate'] = 'trim';
      $rules['ShortRules'] = 'trim';
      $rules['OfficialRules'] = 'trim';
      $rules['MetaTitle'] = 'trim';
      $rules['MetaDescription'] = 'trim';
      $rules['MetaKeywords'] = 'trim';
      $rules['MetaAbstract'] = 'trim';
      $rules['MetaRobots'] = 'trim';

      $this->validation->set_rules($rules);

      $fields['ContestName'] = 'Contest Name';
      $fields['Language'] = 'Language';
      $fields['ContestTitle'] = 'Contest Title';
      $fields['StartDate'] = 'Start Date';
      $fields['EndDate'] = 'End Date';
      $fields['EntryFrequency'] = 'Entry Frequncy';
      $fields['WrapperTemplate'] = 'Wrapper Template';
      $fields['LandingPageTemplate'] = 'Landing Page Template';
      $fields['EntryIsLandingPage'] = 'Entry is the Landing Page';
      $fields['EntryWrapperTemplate'] = 'Entry Form Template';
      $fields['EntryFormTemplate'] = 'Entry Form Template';
      $fields['EntrySuccessTemplate'] = 'Entry Success Template';
      $fields['EntryRejectedTemplate'] = 'Entry Rejected Template';
      $fields['EntryClosedTemplate'] = 'Entry Closed Template';
      $fields['EntryEmailTemplate'] = 'Entry Email Template';
      $fields['TellAFriendIsEnabled'] = 'Enable Tell a friend';
      $fields['FriendEntryAction'] = 'Friend Entry Action';
      $fields['MaxExtraEntries'] = 'Max Extra Entries';
      $fields['SendTellerNotice'] = 'Send Teller Notice';
      $fields['TellAFriendWrapperTemplate'] = 'Tell a friend Wrapper Template';
      $fields['TellAFriendFormTemplate'] = 'Tell a friend Form Template';
      $fields['TellAFriendResultsTemplate'] = 'Tell a friend Results Template';
      $fields['TellAFriendEmailTemplate'] = 'Tell a friend Email Template';
      $fields['QuizIsEnabled'] = 'Enable Quiz/Survey';
      $fields['QuizID'] = 'Quiz ID';
      $fields['QuizResultsTemplate'] = 'Quiz Results Template';
      $fields['ShortRules'] = 'Short Rules';
      $fields['OfficialRules'] = 'Official Rules';
      $fields['MetaTitle'] = 'Meta Title';
      $fields['MetaDescription'] = 'Meta Description';
      $fields['MetaKeywords'] = 'Meta Keywords';
      $fields['MetaAbstract'] = 'Meta Abstract';
      $fields['MetaRobots'] = 'Meta Robots';

      $this->validation->set_fields($fields);

      $defaults = $this->Contests->get_contest_by_id($contest_id);
      
      $defaults['WrapperTemplate'] = entities_to_ascii($defaults['WrapperTemplate']);
      $defaults['LandingPageTemplate'] = entities_to_ascii($defaults['LandingPageTemplate']);
      $defaults['EntryWrapperTemplate'] = entities_to_ascii($defaults['EntryWrapperTemplate']);
      $defaults['EntryFormTemplate'] = entities_to_ascii($defaults['EntryFormTemplate']);
      $defaults['EntrySuccessTemplate'] = entities_to_ascii($defaults['EntrySuccessTemplate']);
      $defaults['EntryRejectedTemplate'] = entities_to_ascii($defaults['EntryRejectedTemplate']);
      $defaults['EntryClosedTemplate'] = entities_to_ascii($defaults['EntryClosedTemplate']);
      $defaults['EntryEmailTemplate'] = entities_to_ascii($defaults['EntryEmailTemplate']);
      $defaults['TellAFriendWrapperTemplate'] = entities_to_ascii($defaults['TellAFriendWrapperTemplate']);
      $defaults['TellAFriendFormTemplate'] = entities_to_ascii($defaults['TellAFriendFormTemplate']);
      $defaults['TellAFriendResultsTemplate'] = entities_to_ascii($defaults['TellAFriendResultsTemplate']);
      $defaults['TellAFriendEmailTemplate'] = entities_to_ascii($defaults['TellAFriendEmailTemplate']);
      $defaults['QuizResultsTemplate'] = entities_to_ascii($defaults['QuizResultsTemplate']);
      $defaults['ShortRules'] = entities_to_ascii($defaults['ShortRules']);
      $defaults['OfficialRules'] = entities_to_ascii($defaults['OfficialRules']);
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
         $data['tabs'] = $this->administrator->get_site_tabs($site_id, 'Contests');
         $data['submenu'] = get_submenu($site_id, 'Contests');
         $data['frequencies'] = $this->Contests->get_entry_frequencies_list();
         $data['entry_actions'] = $this->Contests->get_friend_entry_action_list();
         $data['quizes'] = array();
         $data['site_id'] = $site_id;
         $data['contest_id'] = $contest_id;
         $data['site'] = $site;
         $data['admin'] = $admin;
      
         $this->load->vars($data);
   	
         return $this->load->view('contests/edit', NULL, TRUE);
      }
      else
      {
         // detect if the form has already been saved once
         if ($this_action > $this->session->userdata('last_action'))
         {
            $this->session->set_userdata('last_action', $this_action);
            $this->update($site_id, $contest_id);
         }
      }
   }

   // --------------------------------------------------------------------

   /**
    * Updates and existing contest record
    *
    */
   function update($site_id, $contest_id)
   {
      if (FALSE === $contest_id)
         show_error('update contest requires that a contest ID be supplied.');

      $fields = $this->validation->_fields;
      
      foreach ($fields AS $key => $value)
         $values[$key] = $this->input->post($key);
      
      $values['WrapperTemplate'] = ascii_to_entities($values['WrapperTemplate']);
      $values['LandingPageTemplate'] = ascii_to_entities($values['LandingPageTemplate']);
      $values['EntryWrapperTemplate'] = ascii_to_entities($values['EntryWrapperTemplate']);
      $values['EntryFormTemplate'] = ascii_to_entities($values['EntryFormTemplate']);
      $values['EntrySuccessTemplate'] = ascii_to_entities($values['EntrySuccessTemplate']);
      $values['EntryRejectedTemplate'] = ascii_to_entities($values['EntryRejectedTemplate']);
      $values['EntryClosedTemplate'] = ascii_to_entities($values['EntryClosedTemplate']);
      $values['EntryEmailTemplate'] = ascii_to_entities($values['EntryEmailTemplate']);
      $values['TellAFriendWrapperTemplate'] = ascii_to_entities($values['TellAFriendWrapperTemplate']);
      $values['TellAFriendFormTemplate'] = ascii_to_entities($values['TellAFriendFormTemplate']);
      $values['TellAFriendResultsTemplate'] = ascii_to_entities($values['TellAFriendResultsTemplate']);
      $values['TellAFriendEmailTemplate'] = ascii_to_entities($values['TellAFriendEmailTemplate']);
      $values['QuizResultsTemplate'] = ascii_to_entities($values['QuizResultsTemplate']);
      $values['ShortRules'] = ascii_to_entities($values['ShortRules']);
      $values['OfficialRules'] = ascii_to_entities($values['OfficialRules']);

      $values['MetaTitle'] = ascii_to_entities($values['MetaTitle']);
      $values['MetaDescription'] = ascii_to_entities($values['MetaDescription']);
      $values['MetaKeywords'] = ascii_to_entities($values['MetaKeywords']);
      $values['MetaAbstract'] = ascii_to_entities($values['MetaAbstract']);
      $values['MetaRobots'] = ascii_to_entities($values['MetaRobots']);

      $values['RevisedDate'] = date('Y-m-d H:i:s');
      $values['RevisedBy'] = $this->session->userdata('username');

      $this->db->where('ID', $contest_id);
      $this->db->update('contests', $values);
      
      $this->session->set_userdata('contest_message', $values['ContestTitle'].' has been updated.');

      $last_action = $this->session->userdata('last_action') + 1;      
      redirect("contests/edit/".$site_id.'/'.$contest_id.'/'.$last_action.'/');
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
      $message = 'Are you sure you want to delete the following row: '.$id;
      
      $data['message'] = $message;
      $data['no'] = anchor(array($this->base_uri, 'index'), 'No');
      $data['yes'] = anchor(array($this->base_uri, 'do_delete', $id), 'Yes');
   
      return $this->load->view('delete', $data, TRUE);
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
      // Now do the query
      $this->db->where('ID', $id);
      $this->db->delete('contests');

      header("Refresh:0;url=".site_url(array($this->base_uri, 'index')));
      exit;
   }

}
?>