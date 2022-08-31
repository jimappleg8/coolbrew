<?php

class Workflows extends Controller {

   function Workflows()
   {
      parent::Controller();   
      $this->load->library('session');
      $this->load->library('administrator', array('module_id' => 'projects'));
      $this->load->helper(array('url', 'menu'));
   }
   
   // --------------------------------------------------------------------

   /**
    * Generates the project-level workflow
    *
    */
   function index($project_id)
   {
      $admin['message'] = $this->session->userdata('projects_message');
      if ($this->session->userdata('projects_message') != '')
         $this->session->set_userdata('projects_message', '');
         
      $admin['error_msg'] = $this->session->userdata('projects_error');
      if ($this->session->userdata('projects_error') != '')
         $this->session->set_userdata('projects_error', '');
         
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');
      
      $this->load->database('write');

      // get project data
      $sql = "SELECT * FROM projects " .
             "WHERE ID = ".$project_id;
      
      $query = $this->db->query($sql);
      $project = $query->row_array();
      
      // get phase data
      $sql = 'SELECT ID, ItemName, Color, BgColor FROM projects_checklist '.
             'WHERE ItemType = \'phase\' '.
             'ORDER BY Sort';

      $query = $this->db->query($sql);
      $phase_array = $query->result_array();
      
      for ($i=0; $i<count($phase_array); $i++)
      {
         $phases[$i] = $phase_array[$i];
         $phases[$i]['has_removed'] = FALSE;
         $phases[$i]['has_tasks'] = FALSE;
      }

      // get tasks data
      $sql = 'SELECT projects_checklist.ID, projects_checklist.ParentID, '.
               'projects_checklist.ItemName, projects_checked.Status, '. 
               'projects_checked.CompletedDate, projects_checked.CompletedBy '.
             'FROM (projects_checklist, projects_type, projects_type_default) '. 
             'LEFT JOIN projects_checked '.
             'ON projects_checklist.ID = projects_checked.ChecklistID '.
             'AND projects_checked.ProjectID = '.$project_id.' '.
             'WHERE projects_checklist.ID = projects_type_default.PropertyID '. 'AND projects_type_default.Property = \'checklist\' '.
             'AND projects_type.ID = projects_type_default.ProjectTypeID '.
             'AND projects_type.ID = '.$project['ProjectTypeID'].' '.
             'ORDER BY projects_checklist.Sort';

      $query = $this->db->query($sql);
      $tasks_array = $query->result_array();

      for ($i=0; $i<count($tasks_array); $i++)
      {
         $tasks[$i] = $tasks_array[$i];
         $tasks[$i]['Tools'] = '';
      }
      
      $removed_exists = FALSE;
      for ($i=0; $i<count($phases); $i++)
      {
         for ($j=0; $j<count($tasks); $j++)
         {
            if ($tasks[$j]['ParentID'] == $phases[$i]['ID'] && $tasks[$j]['Status'] == 2)
            {
               $phases[$i]['has_removed'] = TRUE;
               $removed_exists = TRUE;
            }
            elseif ($tasks[$j]['ParentID'] == $phases[$i]['ID'])
            {
               $phases[$i]['has_tasks'] = TRUE;
            }
         }
      }
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;
      $data['tabs'] = $this->administrator->get_main_tabs('Projects');
      $data['submenu'] = get_submenu('Projects');
      $data['admin'] = $admin;
      $data['project_id'] = $project_id;
      $data['project'] = $project;
      $data['phases'] = $phases;
      $data['tasks'] = $tasks;
      $data['removed_exists'] = $removed_exists;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/workflow/list', NULL, TRUE);

   }
   
   // --------------------------------------------------------------------
   
   /**
    * Generates the project-level workflow
    *
    */
   function wf_print($project_id)
   {
      $this->collector->append_css_file('admin');
      $this->collector->append_css_file('projects_adm');
      
      $this->load->database('write');

      // get project data
      $sql = "SELECT * FROM projects " .
             "WHERE ID = ".$project_id;
      
      $query = $this->db->query($sql);
      $project = $query->row_array();
      
      $project['error_msg'] = $this->session->userdata('projects_error');
      if ($this->session->userdata('projects_error') != '')
         $this->session->set_userdata('projects_error', '');
         
      // get phase data
      $sql = 'SELECT ID, ItemName, Color, BgColor FROM projects_checklist '.
             'WHERE ItemType = \'phase\' '.
             'ORDER BY Sort';

      $query = $this->db->query($sql);
      $phase_array = $query->result_array();
      
      for ($i=0; $i<count($phase_array); $i++)
      {
         $phases[$i] = $phase_array[$i];
         $phases[$i]['has_removed'] = FALSE;
         $phases[$i]['has_tasks'] = FALSE;
      }

      // get tasks data
      $sql = 'SELECT projects_checklist.ID, projects_checklist.ParentID, '.
               'projects_checklist.ItemName, projects_checked.Status, '. 
               'projects_checked.CompletedDate, projects_checked.CompletedBy '.
             'FROM (projects_checklist, projects_type, projects_type_default) '. 
             'LEFT JOIN projects_checked '.
             'ON projects_checklist.ID = projects_checked.ChecklistID '.
             'AND projects_checked.ProjectID = '.$project_id.' '.
             'WHERE projects_checklist.ID = projects_type_default.PropertyID '. 'AND projects_type_default.Property = \'checklist\' '.
             'AND projects_type.ID = projects_type_default.ProjectTypeID '.
             'AND projects_type.ID = '.$project['ProjectTypeID'].' '.
             'ORDER BY projects_checklist.Sort';

      $query = $this->db->query($sql);
      $tasks_array = $query->result_array();

      for ($i=0; $i<count($tasks_array); $i++)
      {
         $tasks[$i] = $tasks_array[$i];
         $tasks[$i]['Tools'] = '';
      }
      
      $removed_exists = FALSE;
      for ($i=0; $i<count($phases); $i++)
      {
         for ($j=0; $j<count($tasks); $j++)
         {
            if ($tasks[$j]['ParentID'] == $phases[$i]['ID'] && $tasks[$j]['Status'] == 2)
            {
               $phases[$i]['has_removed'] = TRUE;
               $removed_exists = TRUE;
            }
            elseif ($tasks[$j]['ParentID'] == $phases[$i]['ID'])
            {
               $phases[$i]['has_tasks'] = TRUE;
            }
         }
      }
      
      $data['last_action'] = $this->session->userdata('last_action') + 1;

      // this is a project-specific screen, so add the project ID to tab links
      $data['tabs'] = $this->get_tabs('Workflow', 'project');
      for ($i=0; $i<count($data['tabs']); $i++)
      {
         $data['tabs'][$i]['Link'] = str_replace('%%%ProjectID%%%', $project['ID'], $data['tabs'][$i]['Link']);
         $data['tabs'][$i]['Link'] = str_replace('%%%last_action%%%', $data['last_action'], $data['tabs'][$i]['Link']);
      }
      
      $data['project'] = $project;
      $data['phases'] = $phases;
      $data['tasks'] = $tasks;
      $data['removed_exists'] = $removed_exists;
      
      $this->load->vars($data);
   	
      return $this->load->view('cp/workflow/print', NULL, TRUE);

   }

   // --------------------------------------------------------------------

   /**
    * Marks a workflow task as complete
    */
   function complete($project_id, $task_id)
   {
      $this->load->database('write');
      
      $values['ProjectID'] = $project_id;
      $values['ChecklistID'] = $task_id;
      $values['Status'] = 1;
      $values['CompletedDate'] = date('Y-m-d H:i:s');
      $values['CompletedBy'] = $this->session->userdata('username');
      
      $this->db->insert('projects_checked', $values);
      
      redirect('cp/workflows/index/'.$project_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Re-activates a workflow task
    */
   function uncomplete($project_id, $task_id)
   {
      $this->load->database('write');
      
      $sql = 'DELETE FROM projects_checked '.
             'WHERE ProjectID = '.$project_id.' '.
             'AND ChecklistID = '.$task_id.' '.
             'AND Status = 1';
      
      $this->db->query($sql);
      
      redirect('cp/workflows/index/'.$project_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Marks a workflow task so it is removed from the list
    */
   function remove($project_id, $task_id)
   {
      $this->load->database('write');
      
      $values['ProjectID'] = $project_id;
      $values['ChecklistID'] = $task_id;
      $values['Status'] = 2;
      
      $this->db->insert('projects_checked', $values);
      
      redirect('cp/workflows/index/'.$project_id.'/');
   }

   // --------------------------------------------------------------------

   /**
    * Re-activates a removed workflow task
    */
   function unremove($project_id, $task_id)
   {
      $this->load->database('write');
      
      $sql = 'DELETE FROM projects_checked '.
             'WHERE ProjectID = '.$project_id.' '.
             'AND ChecklistID = '.$task_id.' '.
             'AND Status = 2';
      
      $this->db->query($sql);
      
      redirect('cp/workflows/index/'.$project_id.'/');
   }
   
}
?>