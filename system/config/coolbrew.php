<?php

/*
|================================================
| CoolBrew Added Config Settings
|================================================
*/

/*
|--------------------------------------------------------------------------
| Proxy Settings
|--------------------------------------------------------------------------
|
| If your system requires a proxy to access outside websites, enter the
| information here. If proxy setting are different for each server level, 
| you can make these conditional using SERVER_LEVEL
|
*/
$config['proxy'] = "";
$config['proxy_port'] = "";

/*
|--------------------------------------------------------------------------
| ImageMagick Convert Path
|--------------------------------------------------------------------------
|
| Points to the location of the convert program. This will probably be made
| obsolete by CodeIgniter's Image Manipulation library.
|
*/
$config['convert_cmd'] = "/usr/bin/convert";

/*
|--------------------------------------------------------------------------
| Sendmail Path
|--------------------------------------------------------------------------
|
| Points to the location of the sendmail program. This may be made obsolete
| by CodeIgniter's Mail library.
|
*/
$config['sendmail'] = "";

/*
|--------------------------------------------------------------------------
| LDAP Connection 
|--------------------------------------------------------------------------
|
| Connection information for LDAP servers. Multiple servers can be listed
| much like the database connections.
|
*/
$config['ldap']['active_group'] = 'default';

$config['ldap']['default']['host'] = 'ldap.ctea.com';
$config['ldap']['default']['port'] = 389;
$config['ldap']['default']['base'] = 'dc=Hain-Celestial,dc=com';

$config['ldap']['activedir']['host'] = 'capitals';
$config['ldap']['activedir']['port'] = 389;
$config['ldap']['activedir']['base'] = 'DC=hvntdom, DC=hain-celestial, DC=com';

/*
|--------------------------------------------------------------------------
| CKEditor Basepath
|--------------------------------------------------------------------------
|
| The path from your site's root in which the ckeditor folder is. Note
| this is from the site's root, not the file system root. Also note the
| required slashes at start and finish.
|
|    e.g. /ckeditor/ or /system/plugins/ckeditor/  etc...
|
*/
$config['ckeditor_basepath']    = "/ckeditor/";

/*
|--------------------------------------------------------------------------
| FCKEditor Basepath
|--------------------------------------------------------------------------
|
| The path from your site's root in which the fckeditor folder is. Note
| this is from the site's root, not the file system root. Also note the
| required slashes at start and finish.
|
|    e.g. /fckeditor/ or /system/plugins/fckeditor/  etc...
|
*/
$config['fckeditor_basepath']    = "/fckeditor/";

/*
|--------------------------------------------------------------------------
| FCKEditor Toolbar Set Default
|--------------------------------------------------------------------------
|
| The default Toolbar set to be used for FCKEditor across your site. Leave
| as empty string or comment out if your happy enough with the standard
| default.
|
*/
$config['fckeditor_toolbarset_default'] = 'Default'; 

?>