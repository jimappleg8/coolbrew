<?php

class Template extends Controller {

   /**
    * TAG: include_tpl
    *
    */
   function include_tpl()
   {
      $view_file = $this->tag->param(1);
      $data_array = $this->tag->param(2);
      $data_name = $this->tag->param(3);
      
      if ( ! $view_file)
      {
         show_error('The template to view was not specified.');
      }
      
      $this->load->helper(array('url','date'));

      if ($data_array)
      {
         if ( ! $data_name) 
         {
            if (strpos($view_file, "."))
            {
               list($data_name, $extension) = explode(".", $view_file);
            }
            else
            {
               $data_name = $view_file;
            }
         }
         $data[$data_name] = $data_array;
         $this->load->view($view_file, $data);
      }
      else
      {
         $this->load->view($view_file);      
      }
      
      return;
   }
   // END include_tpl()
   
   // ---------------------------------------------------------------------
   
   /*
    * TAG: include_tpl_array
    *   allows for multiple assign statements. The data array is an array in
    *   the form 'label' => data
    * 
    */
   function include_tpl_array()
   {
      $view_file = $this->tag->param(1);
      $data_array = $this->tag->param(2);

      if ( ! $view_file)
      {
         show_error('The template to view was not specified.');
      }
      
      if ( ! $data_array)
      {
         show_error('The data array was not supplied.');
      }
      
//      $this->load->helper('url');

      $this->load->view($view_file, $data_array);
      
      return;
   }
   // END include_tpl_array()


} // END Template class

?>