<?php

class Generate extends Controller {

//   var $base_path = '/var/opt/httpd/system';
   var $base_path = '/Users/japplega/Desktop/websites/system';
   
   var $data = array();
   
   var $module_name = '';
   var $item = '';
   var $item_name = '';
   
   var $controller_path = '';
   var $model_path = '';
   var $helper_path = '';
   var $view_path = '';
   
   // --------------------------------------------------------------------

   function Generate()
   {
      parent::Controller();   
   }
   
   // --------------------------------------------------------------------

   function index()
   {
      $this->module_name = $this->tag->param(1, '');
      
      $this->load->helper(array('file', 'text'));
      
      $this->process_arguments();
      
      switch ($this->item)
      {
         case 'controller':
            $this->create_controller();
            break;
         case 'model':
            $this->create_model();
            break;
         case 'helper':
            $this->create_helper();
            break;
         case 'scaffold':
            $this->build_scaffold();
            break;
         default:
            echo "item is not a valid type.";
            exit;
      }
      echo "\n";
   }
   
   // --------------------------------------------------------------------

   function process_arguments()
   {
      // get the arguments passed to the script
      if ($_SERVER['argc'] < 3)
      {
         $this->display_usage();
      }

      $this->item = $_SERVER['argv'][1];
      $this->item_name = $_SERVER['argv'][2];
      
      $this->data['module_name'] = $this->module_name;
      $this->data[$this->item.'_name'] = $this->item_name;
      
      $module_path = $this->base_path.'/modules/'.$this->module_name;
      $this->controller_path = $module_path.'/controllers';
      $this->model_path = $module_path.'/models';
      $this->helper_path = $module_path.'/helpers';
      $this->view_path = $module_path.'/views';

   }

   // --------------------------------------------------------------------

   function build_scaffold()
   {
      // I will probably need to make this more complicated
      $db_table = $this->item_name;
      
      $this->load->helper(array('file', 'text'));
      $this->load->library('Scaffolding', array('db_table'=>$db_table));
      
      $methods = array('add');
      $views = array('add');

      $this->data['controller_methods'] = '';
      foreach ($methods AS $method)
      {
         // get the methods
         $this->data['controller_methods'] .= $this->scaffolding->get_method($method);
      }
      $this->data['controller_name'] = $this->item_name;
      $this->create_controller();
      
      foreach ($views AS $view)
      {
         // get the views
         $content = $this->scaffolding->get_view($view);
         $content = entities_to_ascii($content);
         write_file($this->view_path.'/'.$db_table.'_'.$view.'.php', $content);
         chmod($this->view_path.'/'.$db_table.'_'.$view.'.php', 0664);
      }

      return TRUE;
   }

   // --------------------------------------------------------------------

   function create_controller()
   {
      $this->load->helper(array('file', 'text'));

      $content = $this->get_view('generate/controller', $this->data, TRUE);
      $content = entities_to_ascii($content);
      write_file($this->controller_path.'/'.$this->item_name.'.php', $content);
      chmod($this->controller_path.'/'.$this->item_name.'.php', 0664);
      echo "create ".$this->item."  ".$this->item_name."\n";

      return TRUE;
   }

   // --------------------------------------------------------------------

   function create_model()
   {
      $this->load->helper(array('file', 'text'));

      $content = $this->get_view('generate/model', $this->data, TRUE);
      $content = entities_to_ascii($content);
      write_file($this->model_path.'/'.$this->item_name.'.php', $content);
      chmod($this->model_path.'/'.$this->item_name.'.php', 0664);
      echo "create ".$this->item."  ".$this->item_name."\n";

      return TRUE;
   }

   // --------------------------------------------------------------------

   function create_helper()
   {
      $this->load->helper(array('file', 'text'));

      $content = $this->get_view('generate/helper', $this->data, TRUE);
      $content = entities_to_ascii($content);
      write_file($this->helper_path.'/'.$this->item_name.'_helper.php', $content);
      chmod($this->helper_path.'/'.$this->item_name.'_helper.php', 0664);
      echo "create ".$this->item."  ".$this->item_name."\n";

      return TRUE;
   }

   // --------------------------------------------------------------------

   function display_usage()
   {
      echo "usage: ./scripts/generate item item_name";
   }

   // --------------------------------------------------------------------

   /**
    * returns a view as a string. This is needed because the scaffold class
    * changes the path to the view files and we need to change it back to
    * the default temporarily each time we load a view.
    *
    */
   function get_view($view, $vars = array(), $return = FALSE)
   {
      $save_path = $this->load->_ci_view_path;
      $this->load->_ci_view_path = APPPATH().'views/';

      $content = $this->load->view($view, $vars, $return);

      $this->load->_ci_view_path = $save_path;

      return $content;
   }

}
?>