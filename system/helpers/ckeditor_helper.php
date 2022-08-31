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
 * CKEditor field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */	
function form_ckeditor($data = '', $value = '', $extra = '')
{
   $CI =& get_instance();

   $ckeditor_basepath = $CI->config->item('ckeditor_basepath');
   require_once DOCPATH . $ckeditor_basepath . 'ckeditor_php5.php';

   $ckeditor = new CKeditor();

   $ckeditor->returnOutput = TRUE;
   $ckeditor->basePath = $ckeditor_basepath;
   
   $myname = (is_array($data) && isset($data['name'])) ? $data['name'] : $data;
   $myvalue = html_entity_decode($value);
   $config = array();
   
   $config['entities'] = FALSE;
   $config['fillEmptyBlocks'] = FALSE;
   $config['autoParagraph'] = FALSE;
   $config['toolbar'] = array(
      array( 'Source','Bold','Italic','-','NumberedList','BulletedList','-','Link','Unlink','-','About' )
   );

   if (is_array($data))
   {
      if (isset($data['value']))
         $myvalue = html_entity_decode($data['value']);
      if (isset($data['basepath']))
         $ckeditor->basePath = $data['basepath'];
      if (isset($data['width']))
         $config['width'] = $data['width'];
      if (isset($data['height']))
         $config['height'] = $data['height'];
   }
   return $ckeditor->editor($myname, $myvalue, $config);
}

?>