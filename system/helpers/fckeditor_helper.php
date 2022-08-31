<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Extension to Form Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 */

include_once( BASEPATH . '/helpers/form_helper'.EXT);

// ------------------------------------------------------------------------

/**
 * FCKEditor field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_fckeditor($data = '', $value = '', $extra = '')
{
   $CI =& get_instance();

   $fckeditor_basepath = $CI->config->item('fckeditor_basepath');
   require_once DOCPATH . $fckeditor_basepath . 'fckeditor.php';
    
   $instanceName = (is_array($data) && isset($data['name'])) ? $data['name'] : $data;
   $fckeditor = new FCKeditor($instanceName);
    
   if ($fckeditor->IsCompatible())
   {
      $fckeditor->Value = html_entity_decode($value);
      $fckeditor->BasePath = $fckeditor_basepath;
      if ($fckeditor_toolbarset = $CI->config->item('fckeditor_toolbarset_default'))
         $fckeditor->ToolbarSet = $fckeditor_toolbarset;
        
      if (is_array($data))
      {
         if (isset($data['value']))
            $fckeditor->Value = html_entity_decode($data['value']);
         if (isset($data['basepath']))
            $fckeditor->BasePath = $data['basepath'];
         if (isset($data['toolbarset']))
            $fckeditor->ToolbarSet = $data['toolbarset'];
         if (isset($data['width']))
            $fckeditor->Width = $data['width'];
         if (isset($data['height']))
            $fckeditor->Height = $data['height'];
      }
      return $fckeditor->CreateHtml();
   }
   else
   {
      return form_textarea( $data, $value, $extra );
   }
}

?>