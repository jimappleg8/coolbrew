<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CodeIgniter Contact Us Form Class
 *
 * This is to help simplify and make consistent the Contact Us tag and rTags.
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Jim Applegate
 * @link
 */

class Contact_form {

   private $form = array(
      'Subject'  => array(
         'name'  => 'Subject',
         'label' => 'Subject',
         'rules' => 'trim|required',
       ),
      'ChooseSubject'     => array(
         'label' => '-- Please select a subject --',
       ),
      'FName'     => array(
         'name' => 'First Name',
         'label' => 'First Name',
         'rules' => 'trim|required|max_length[25]',
         'start-name' => 'First Name',
         'start-rules' => 'trim|max_length[25]',
       ),
      'LName'     => array(
        'name' => 'Last Name',
        'label' => 'Last Name',
        'rules' => 'trim|required|max_length[25]',
        'start-name' => 'Last Name',
        'start-rules' => 'trim|max_length[25]',
       ),
      'Address1'  => array(
         'name' => 'Address Line 1',
         'label' => 'Address Line 1',
         'rules' => 'trim|required',
       ),
      'Address2'  => array(
         'name' => 'Address Line 2',
         'label' => 'Address Line 2',
       ),
      'City'      => array(
         'name' => 'City',
         'label' => 'City',
         'rules' => 'trim|required',
       ),
      'State'     => array(
         'name' => 'State',
         'label' => 'State',
         'rules' => 'trim|required',
       ),
      'ChooseState'     => array(
         'label' => '-- Please select a state --',
       ),
      'Country'   => array(
         'name' => 'Country',
         'label' => 'Country',
         'rules' => 'trim',
       ),
      'ChooseCountry'   => array(
         'label' => '-- Please select a country --',
       ),
      'Zip'       => array(
         'name' => 'Zip/Postal Code',
         'label' => 'Zip/Postal Code',
         'rules' => 'trim|required',
       ),
      'Phone'     => array(
         'name' => 'Daytime Phone',
         'label' => 'Daytime Phone',
       ),
      'Email'     => array(
         'name' => 'Email Address',
         'start' => TRUE,
         'label' => 'Email',
         'rules' => 'trim|required|valid_email|matches[Email2]',
         'start-name' => 'Email',
         'start-rules' => 'trim|valid_email',
       ),
      'Email2'    => array(
         'name' => 'Email Confirmation',
         'label' => 'Confirm your Email',
         'rules' => 'trim|required',
       ),
      'ProductUPC'    => array(
         'name' => 'Product Name or UPC',
         'label' => 'Product UPC',
         'rules' => 'trim',
       ),
      'ProductUPCDesc'    => array(
         'label' => 'Enter the 10 digits beneath the bar code.',
       ),
      'BestByDateLotCode' => array(
         'name' => 'Best By Date or Lot Code',
         'label' => 'Best by Date and Lot Code',
         'rules' => 'trim',
       ),
      'BestByDateLotCodeDesc' => array(
         'label' => 'Enter the use-by or best-by date along with any other codes printed or stamped on the package.  If you are unable to locate any please just put "none."',
       ),
      'Comment'   => array(
         'name' => 'Message',
         'label' => 'Message',
         'rules' => 'trim|required',
       ),
      'Marketing' => array(
         'name' => 'Marketing',
         'label' => '##default##',
         'default' => 'YES',
         'start-name' => 'Marketing',
         'start-default' => 'YES',
       ),
      'Release'   => array(
         'name' => 'Release',
         'label' => 'From time to time, we select consumer comments to post on our web site. Please check this box if you would like your comments to be considered.',
         'default' => 'YES',
         'start-name' => 'Release',
         'start-default' => 'YES',
       ),
      'form_token'   => array(
         'name' => 'CSRF Token',
         'start-name' => 'CSRF Token',
       ),
      'user_ip'   => array(
         'name' => 'User IP Address',
         'start-name' => 'User IP Address',
       ),
      'user_agent'   => array(
         'name' => 'User Agent',
         'start-name' => 'User Agent',
       ),
      'referrer'   => array(
         'name' => 'Referrer',
         'start-name' => 'Referrer',
       ),
      'spam'   => array(
         'name' => 'Spam',
         'start-name' => 'Spam',
       ),
      'SubmitText' => array(
         'label' => 'Send message',
       ),
   );
   
   private $support_email   = 'Customer Care <hain2@cybercrs.net>';
   private $support_phone   = '1-800-434-4246';
   private $support_address = 'Customer Care<br />The Hain Celestial Group, Inc.<br />4600 Sleepytime Dr.<br />Boulder, CO 80301 USA';
   private $support_hours   = 'Monday-Friday, 9:00am-7:00pm Eastern Time';
   private $locator_link    = '';


   // --------------------------------------------------------------------

	function __construct()
	{
	}

   // --------------------------------------------------------------------

   /**
    * Returns the array of rules as used by the Validation class
    * This method is specific to the contact_us_start tag.
    *
    * @access	public
    * @return   array
    */
   public function get_start_rules()
   {
      $rules = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['start-rules']))
         {
            $rules[$key] = $value['start-rules'];
         }
      }
      return $rules;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the array of fields as used by the Validation class
    * This method is specific to the contact_us_start tag.
    *
    * @access	public
    * @return   array
    */
   public function get_start_fields()
   {
      $fields = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['start-name']))
         {
            $fields[$key] = $value['start-name'];
         }
      }
      return $fields;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the array of default values as used by the Validation class
    * This method is specific to the contact_us_start tag.
    *
    * @access	public
    * @return   array
    */
   public function get_start_defaults()
   {
      $defaults = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['start-default']))
         {
            $defaults[$key] = $value['start-default'];
         }
      }
      return $defaults;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the array of rules as used by the Validation class
    *
    * @access	public
    * @return   array
    */
   public function get_rules()
   {
      $rules = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['rules']))
         {
            $rules[$key] = $value['rules'];
         }
      }
      return $rules;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the array of fields as used by the Validation class
    *
    * @access	public
    * @return   array
    */
   public function get_fields()
   {
      $fields = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['name']))
         {
            $fields[$key] = $value['name'];
         }
      }
      return $fields;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the array of default values as used by the Validation class
    *
    * @access	public
    * @return   array
    */
   public function get_defaults()
   {
      $defaults = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['default']))
         {
            $defaults[$key] = $value['default'];
         }
      }
      return $defaults;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the labels used in the form.
    *
    * @access	public
    * @return   array
    */
   public function get_labels()
   {
      $labels = array();
      foreach ($this->form AS $key => $value)
      {
         if (isset($value['label']))
         {
            $labels[$key] = $value['label'];
         }
      }
      return $labels;
   }

   // --------------------------------------------------------------------

   /**
    * Sets the labels used in the form.
    *
    * @access	public
    * @param    string/array   Labels provided by website - can be either an 
    *                          array or a serialized string
    * @return   array
    */
   public function set_labels($value)
   {
      // if there is nothing to set, an empty string is sent
      if ($value != '')
      {
         // check for a serialized array sent from rTag
         $labels = (is_string($value)) ? unserialize($value) : $value;
         
         foreach ($labels AS $key => $value)
         {
            if (isset($this->form[$key]))
            {
               $this->form[$key] = $value;
            }
         }
      }
      return $this->get_labels();
   }

   // --------------------------------------------------------------------

   /**
    * Returns the support email.
    *
    * @access	public
    * @return   string
    */
   public function get_support_email()
   {
      return $this->support_email;
   }

   // --------------------------------------------------------------------

   /**
    * Sets the support email.
    *
    * @access	public
    * @param    string   the supplied value
    * @return   string
    */
   public function set_support_email($value)
   {
      if ($value != '')
      {
         $this->support_email = $value;
      }
      return $this->support_email;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the support phone.
    *
    * @access	public
    * @return   string
    */
   public function get_support_phone()
   {
      return $this->support_phone;
   }

   // --------------------------------------------------------------------

   /**
    * Sets the support phone.
    *
    * @access	public
    * @param    string   the supplied value
    * @return   string
    */
   public function set_support_phone($value)
   {
      if ($value != '')
      {
         $this->support_phone = $value;
      }
      return $this->support_phone;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the support address.
    *
    * @access	public
    * @return   string
    */
   public function get_support_address()
   {
      return $this->support_address;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the support hours.
    *
    * @access	public
    * @return   string
    */
   public function get_support_hours()
   {
      return $this->support_hours;
   }

   // --------------------------------------------------------------------

   /**
    * Returns the locator link.
    *
    * @access	public
    * @return   string
    */
   public function get_locator_link()
   {
      return $this->locator_link;
   }

   // --------------------------------------------------------------------

   /**
    * Sets the locator link.
    *
    * @access	public
    * @param    string   the supplied value
    * @return   string
    */
   public function set_locator_link($value)
   {
      if ($value != '')
      {
         $this->locator_link = $value;
      }
      return $this->locator_link;
   }


}