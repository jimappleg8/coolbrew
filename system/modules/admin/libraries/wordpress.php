<?php
/*
Class to emulate the login processes for WordPress so we can have a
single sign-on in the admin area.

These functions were pulled from and work with WordPress 2.3.2
*/

require_once '/Users/japplega/Desktop/websites/aadocs/blog/wp-includes/cache.php';

class Wordpress {

/* Method List
x	add_magic_quotes()
	apply_filters()
x	delete_usermeta()
	do_action()
	do_action_ref_array()
x	get_currentuserinfo()
x	get_userdata()
x	get_userdatabylogin()
x	is_user_logged_in()
x	maybe_serialize()
x	maybe_unserialize()
	sanitize_title()
	sanitize_user()
x	update_usermeta()
x	wp_cache_add()
x	wp_cache_delete()
x	wp_cache_get()
x	wp_clearcookie()
x	wp_get_current_user()
x	wp_insert_user()
x	wp_login()
x	wp_set_current_user()
x	wp_setcookie()
*/

/* Object List
	WP_User()
*/

/* Constants
USER_COOKIE
PASS_COOKIE
TEST_COOKIE
COOKIE_DOMAIN
COOKIEPATH
SITECOOKIEPATH
COOKIEHASH
*/
   // fill these out to match the WordPress connection info
   var $db_config = array(
         'hostname' => "localhost",
         'username' => "brewuser",
         'password' => "spike!needles",
         'database' => "coolbrew",
         'dbdriver' => "mysql",
         'dbprefix' => "",
         'active_r' => TRUE,
         'pconnect' => TRUE,
         'db_debug' => TRUE,
         'cache_on' => FALSE,
         'cachedir' => "",
       );
   var $user_table = 'adm_wp_users';
   var $usermeta_table = '';

   var $CI;     // CodeIgniter instance
   var $error;  // where error messages are kept
   
   // ------------------------------------------------------------------------
   
   function Wordpress()
   {
      $this->CI =& get_instance();

      $this->CI->load->database($this->db_config);
   }
   
   // ------------------------------------------------------------------------
   
   /**
    * Emulates the process of logging in from the WordPress login form
    *
    * WordPress checks to make sure cookies are not disabled, but we handle that
    * in the main login.
    */
   function login($user_login, $user_pass)
   {
      // make sure the user isn't already logged in according to WordPress
      if ($this->is_user_logged_in())
      {
         return TRUE;
      }

      $this->do_action_ref_array('wp_authenticate', array(&$user_login, &$user_pass));

      $user = new WP_User(0, $user_login);

      if ($this->wp_login($user_login, $user_pass))
      {
         $this->wp_setcookie($user_login, $user_pass, false, '', '', $rememberme);
         $this->do_action('wp_login', $user_login);
         return TRUE;
      }
      else
      {
         return FALSE;
      }
   }

   // ------------------------------------------------------------------------

   /**
    * Mostly this just tests the 
    */
   function wp_login($username, $password, $already_md5 = FALSE)
   {
      $username = $this->sanitize_user($username);

      if ( '' == $username )
         return FALSE;

      if ( '' == $password )
      {
         $this->error = 'The password field is empty.';
         return FALSE;
      }

      $login = $this->get_userdatabylogin($username);

      if ( ! $login)
      {
         $this->error = 'Invalid username.';
         return FALSE;
      }
      else
      {
         // If the password is already_md5, it has been double hashed.
         // Otherwise, it is plain text.
         if (($already_md5 && md5($login->user_pass) == $password) || 
             ($login->user_login == $username && $login->user_pass == md5($password)) )
         {
            return TRUE;
         }
         else
         {
            $this->error = 'Incorrect password.';
            $pwd = '';
            return FALSE;
         }
      }
   }

   // ------------------------------------------------------------------------

   function is_user_logged_in()
   {
      $user = $this->wp_get_current_user();

      if ($user->id == 0)
         return false;

      return true;
   }

   // ------------------------------------------------------------------------
   
   function logout()
   {
      $this->wp_clearcookie();
      $this->do_action('wp_logout');
   }
   
   // ------------------------------------------------------------------------
   
   /**
    * Insert an user into the database.
    *
    * @param   array  An array of user data.
    * @return  int    The newly created user's ID.
    */
   function wp_insert_user($user)
   {
      // Are we updating or creating?
      if ( ! empty($user['ID']))
      {
         $user_id = (int) $user['ID'];
         unset($user['ID']);
         $update = true;
      }
      else
      {
         $update = false;
         // Password is not hashed when creating new user.
         $user['user_pass'] = md5($user['user_pass']);
      }

      $user['user_login'] = $this->sanitize_user($user['user_login'], true);
      $user['user_login'] = $this->apply_filters('pre_user_login', $user['user_login']);

      if (empty($user['user_nicename']))
         $user['user_nicename'] = $this->sanitize_title($user['user_login']);
      $user['user_nicename'] = $this->apply_filters('pre_user_nicename', $user['user_nicename']);

      if (empty($user['user_url']))
         $user['user_url'] = '';
      $user['user_url'] = $this->apply_filters('pre_user_url', $user['user_url']);

      if ( empty($user['user_mail']) )
         $user['user_mail'] = '';
      $user['user_mail'] = $this->apply_filters('pre_user_email', $user['user_mail']);

      if ( empty($user['display_name']) )
         $user['display_name'] = $user['user_login'];
      $user['display_name'] = $this->apply_filters('pre_user_display_name', $user['display_name']);

      if ( empty($user['nickname']) )
         $user['nickname'] = $user['user_login'];
      $user['nickname'] = $this->apply_filters('pre_user_nickname', $user['nickname']);

      if ( empty($user['first_name']) )
         $user['first_name'] = '';
      $user['first_name'] = $this->apply_filters('pre_user_first_name', $user['first_name']);

      if ( empty($user['last_name']) )
         $user['last_name'] = '';
      $user['last_name'] = $this->apply_filters('pre_user_last_name', $user['last_name']);

      if ( empty($user['description']) )
         $user['description'] = '';
      $user['description'] = $this->apply_filters('pre_user_description', $user['description']);

      if ( empty($user['rich_editing']) )
         $user['rich_editing'] = 'true';

      if ( empty($user['user_registered']) )
         $user['user_registered'] = gmdate('Y-m-d H:i:s');

      $this->CI->load->database('read');

      if ($update)
      {
         $this->CI->db->where('ID', $user_id);
         $this->CI->db->update($this->user_table, $user);
      }
      else
      {
         $this->CI->db->insert($this->user_table, $user);
         $user_id = $this->CI->db->insert_id();
      }

      $this->update_usermeta( $user_id, 'first_name', $user['first_name']);
      $this->update_usermeta( $user_id, 'last_name', $user['last_name']);
      $this->update_usermeta( $user_id, 'nickname', $user['nickname'] );
      $this->update_usermeta( $user_id, 'description', $user['description'] );
      $this->update_usermeta( $user_id, 'jabber', $jabber );
      $this->update_usermeta( $user_id, 'aim', $aim );
      $this->update_usermeta( $user_id, 'yim', $yim );
      $this->update_usermeta( $user_id, 'rich_editing', $user['rich_editing']);

      if ($update && isset($role))
      {
         $user = new WP_User($user_id);
         $user->set_role($role);
      }

      if ( ! $update)
      {
         $user = new WP_User($user_id);
         $user->set_role(get_option('default_role'));
      }

      wp_cache_delete($user_id, 'users');
      wp_cache_delete($user['user_login'], 'userlogins');

      if ($update)
         $this->do_action('profile_update', $user_id);
      else
         $this->do_action('user_register', $user_id);

      return $user_id;
   }

   // ------------------------------------------------------------------------
   
   /**
    * Update a user in the database.
    *
    * @param   array   an array of user data.
    * @return  int     the updated user's ID.
    */
   function wp_update_user($userdata)
   {
      $ID = (int) $userdata['ID'];

      // First, get all of the original fields
      $user = $this->get_userdata($ID);

      // Escape data pulled from DB.
      $user = $this->add_magic_quotes(get_object_vars($user));

      // If password is changing, hash it now.
      if ( ! empty($userdata['user_pass']))
      {
         $plaintext_pass = $userdata['user_pass'];
         $userdata['user_pass'] = md5($userdata['user_pass']);
      }

      // Merge old and new fields with new fields overwriting old ones.
      $userdata = array_merge($user, $userdata);
      $user_id = $this->wp_insert_user($userdata);

      // Update the cookies if the password changed.
      $current_user = $this->wp_get_current_user();
      if ($current_user->id == $ID)
      {
         if (isset($plaintext_pass))
         {
            $this->wp_clearcookie();
            $this->wp_setcookie($userdata['user_login'], $plaintext_pass);
         }
      }
      return $user_id;
   }

   // ------------------------------------------------------------------------

   function wp_setcookie($username, $password, $already_md5 = false, $home = '', $siteurl = '', $remember = false)
   {
      if ( ! $already_md5 )
         $password = md5(md5($password)); // Double hash the passwd in the cookie.

      if (empty($home))
         $cookiepath = COOKIEPATH;
      else
         $cookiepath = preg_replace('|https?://[^/]+|i', '', $home . '/' );

      if ( empty($siteurl) ) {
         $sitecookiepath = SITECOOKIEPATH;
         $cookiehash = COOKIEHASH;
      }
      else
      {
         $sitecookiepath = preg_replace('|https?://[^/]+|i', '', $siteurl . '/' );
         $cookiehash = md5($siteurl);
      }

      if ( $remember )
         $expire = time() + 31536000;
      else
         $expire = 0;

      setcookie(USER_COOKIE, $username, $expire, $cookiepath, COOKIE_DOMAIN);
      setcookie(PASS_COOKIE, $password, $expire, $cookiepath, COOKIE_DOMAIN);

      if ( $cookiepath != $sitecookiepath )
      {
         setcookie(USER_COOKIE, $username, $expire, $sitecookiepath, COOKIE_DOMAIN);
         setcookie(PASS_COOKIE, $password, $expire, $sitecookiepath, COOKIE_DOMAIN);
      }
   }

   // ------------------------------------------------------------------------

   function wp_clearcookie()
   {
      setcookie(USER_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
      setcookie(PASS_COOKIE, ' ', time() - 31536000, COOKIEPATH, COOKIE_DOMAIN);
      setcookie(USER_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
      setcookie(PASS_COOKIE, ' ', time() - 31536000, SITECOOKIEPATH, COOKIE_DOMAIN);
   }
   
   // ------------------------------------------------------------------------

function wp_set_current_user($id, $name = '')
{
	global $current_user;

	if ( isset($current_user) && ($id == $current_user->ID) )
		return $current_user;

	$current_user = new WP_User($id, $name);

	setup_userdata($current_user->ID);

	do_action('set_current_user');

	return $current_user;
}

   // ------------------------------------------------------------------------

function wp_get_current_user()
{
	global $current_user;

	get_currentuserinfo();

	return $current_user;
}

   // ------------------------------------------------------------------------

function get_currentuserinfo()
{
	global $current_user;

	if ( defined('XMLRPC_REQUEST') && XMLRPC_REQUEST )
		return false;

	if ( ! empty($current_user) )
		return;

	if ( empty($_COOKIE[USER_COOKIE]) || empty($_COOKIE[PASS_COOKIE]) ||
		!wp_login($_COOKIE[USER_COOKIE], $_COOKIE[PASS_COOKIE], true) ) {
		wp_set_current_user(0);
		return false;
	}

	$user_login = $_COOKIE[USER_COOKIE];
	wp_set_current_user(0, $user_login);
}

   // ------------------------------------------------------------------------

   function get_userdata($user_id)
   {
      $user_id = (int) $user_id;
      if ($user_id == 0)
         return false;

      $user = wp_cache_get($user_id, 'users');
   
      if ($user)
         return $user;

      $sql = 'SELECT * FROM '.$this->user_table.' '.
             'WHERE ID = "'.$user_id.'" LIMIT 1';
      $query = $this->CI->db->query($sql);
      $user = $query->row();
      
      if ($query->num_rows() < 1)
         return false;
   
      $sql = 'SELECT meta_key, meta_value '.
             'FROM '.$this->usermeta_table.' '.
             'WHERE user_id = "'.$user_id.'"';
      $query = $this->CI->db->query($sql);
      $metavalues = $query->result();
   
      if ($metavalues)
      {
         foreach ($metavalues as $meta)
         {
            $value = $this->maybe_unserialize($meta->meta_value);
            $user->{$meta->meta_key} = $value;
   
            // We need to set user_level from meta, not row
            if ($wpdb->prefix . 'user_level' == $meta->meta_key)
               $user->user_level = $meta->meta_value;
         }
      }
   
      wp_cache_add($user_id, $user, 'users');
      wp_cache_add($user->user_login, $user_id, 'userlogins');
      return $user;
   }
   
   // ------------------------------------------------------------------------

   function get_userdatabylogin($user_login)
   {
      $user_login = $this->sanitize_user($user_login);
   
      if (empty($user_login))
         return false;
   
      $user_id = wp_cache_get($user_login, 'userlogins');
      $userdata = wp_cache_get($user_id, 'users');
   
      if ($userdata)
         return $userdata;
   
      $user_login = $this->CI->db->escape($user_login);
      
      $sql = 'SELECT ID FROM '.$this->user_table.' '.
             'WHERE user_login = "'.$user_login.'"';
      $query = $this->CI->db->query($sql);
      $result = $query->result();

      if ($query->num_rows() < 1)
         return false;
   
      $user = $this->get_userdata($result->ID);

      return $user;
   }

   // ------------------------------------------------------------------------

   function delete_usermeta($user_id, $meta_key, $meta_value = '')
   {
      if ( ! is_numeric($user_id))
         return false;

      $meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
   
      if (is_array($meta_value) || is_object($meta_value))
         $meta_value = serialize($meta_value);
      $meta_value = trim($meta_value);
   
      if ( ! empty($meta_value))
      {
         $sql = 'DELETE FROM '.$this->usermeta_table.' '.
                'WHERE user_id = "'.$user_id.'" '.
                'AND meta_key = "'.$meta_key.'" '.
                'AND meta_value = "'.$meta_value.'"';
         $this->CI->db->query($sql);
      }
      else
      {
         $sql = 'DELETE FROM '.$this->usermeta_table.' '.
                'WHERE user_id = "'.$user_id.'" '.
                'AND meta_key = "'.$meta_key.'"';
         $this->CI->db->query($sql);
      }
   
      $user = $this->get_userdata($user_id);
      wp_cache_delete($user_id, 'users');
      wp_cache_delete($user->user_login, 'userlogins');
   
      return true;
   }
   
   // ------------------------------------------------------------------------

   function update_usermeta($user_id, $meta_key, $meta_value)
   {
      if ( ! is_numeric($user_id))
         return false;

      $meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
   
      // FIXME: usermeta data is assumed to be already escaped
      if (is_string($meta_value))
         $meta_value = stripslashes($meta_value);
      $meta_value = $this->maybe_serialize($meta_value);
      $meta_value = $this->CI->db->escape($meta_value);
   
      if (empty($meta_value))
      {
         return $this->delete_usermeta($user_id, $meta_key);
      }
   
      $sql = 'SELECT * FROM '.$this->usermeta_table.' '.
             'WHERE user_id = "'.$user_id.'" '.
             'AND meta_key = "'.$meta_key.'"';
      $query = $this->CI->db->query($sql);
      $cur = $query->row();

      if ($query->num_rows() < 1)
      {
         $meta['user_id'] = $user_id;
         $meta['meta_key'] = $meta_key;
         $meta['meta_value'] = $meta_value;
         $this->CI->db->insert($this->usermeta_table, $meta);
      }
      else if ($cur->meta_value != $meta_value)
      {
         $meta['meta_value'] = $meta_value;
         $this->CI->db->where('user_id', $user_id);
         $this->CI->db->where('meta_key', $meta_key);
         $this->CI->db->update($this->usermeta_table, $meta);
      }
      else
      {
         return false;
      }
   
      $user = $this->get_userdata($user_id);
      wp_cache_delete($user_id, 'users');
      wp_cache_delete($user->user_login, 'userlogins');
   
      return true;
   }

   // ------------------------------------------------------------------------

   function maybe_serialize($data)
   {
      if (is_string($data))
      {
         $data = trim($data);
      }
      elseif ( is_array($data) || is_object($data) )
      {
         return serialize($data);
      }

      if ($this->is_serialized($data))
      {
         return serialize($data);
      }

      return $data;
   }
   
   // ------------------------------------------------------------------------
   
   function maybe_unserialize($original)
   {
      // don't attempt to unserialize data that wasn't serialized going in
      if ($this->is_serialized($original))
      {
         if (false !== $gm = @ unserialize($original))
         {
            return $gm;
         }
      }
      return $original;
   }
   
   // ------------------------------------------------------------------------
   
   /**
    * Determines whether the given data is serialized.
    *
    * @param   mixed    the data in question
    * @return  boolean
    */
   function is_serialized($data)
   {
      // if it isn't a string, it isn't serialized
      if ( ! is_string($data))
         return FALSE;

      $data = trim($data);

      if ( 'N;' == $data )
         return TRUE;

      if ( !preg_match('/^([adObis]):/', $data, $badions) )
         return FALSE;

      switch ($badions[1])
      {
         case 'a' :
         case 'O' :
         case 's' :
            if ( preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data) )
               return TRUE;
            break;
         case 'b' :
         case 'i' :
         case 'd' :
            if ( preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data) )
               return TRUE;
            break;
      }
      return FALSE;
   }
   
   // ------------------------------------------------------------------------
   
   function add_magic_quotes($array)
   {
      foreach ($array as $k => $v)
      {
         if (is_array($v))
         {
            $array[$k] = $this->add_magic_quotes($v);
         }
         else
         {
            $array[$k] = $this-CI-db->escape($v);
         }
      }
      return $array;
   }
   
}
?>