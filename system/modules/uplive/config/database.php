<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['active_r'] TRUE/FALSE - Whether to load the active record class
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
*/


$active_group = "dev";

$db['dev']['hostname'] = "bolwebdev1:3306";
$db['dev']['username'] = "root";
$db['dev']['password'] = "tr33Cr0w";
$db['dev']['database'] = "coolbrew";
$db['dev']['dbdriver'] = "mysql";
$db['dev']['dbprefix'] = "";
$db['dev']['active_r'] = TRUE;
$db['dev']['pconnect'] = TRUE;
$db['dev']['db_debug'] = TRUE;
$db['dev']['cache_on'] = FALSE;
$db['dev']['cachedir'] = ""; 
$db['dev']['char_set'] = "utf8";
$db['dev']['dbcollat'] = "utf8_general_ci";

$db['stage']['hostname'] = "mysql-master:3306";
$db['stage']['username'] = "root";
$db['stage']['password'] = "tr33Cr0w";
$db['stage']['database'] = "coolbrew_stage";
$db['stage']['dbdriver'] = "mysql";
$db['stage']['dbprefix'] = "";
$db['stage']['active_r'] = TRUE;
$db['stage']['pconnect'] = TRUE;
$db['stage']['db_debug'] = TRUE;
$db['stage']['cache_on'] = FALSE;
$db['stage']['cachedir'] = ""; 
$db['stage']['char_set'] = "utf8";
$db['stage']['dbcollat'] = "utf8_general_ci";

$db['live']['hostname'] = "mysql-master:3306";
$db['live']['username'] = "root";
$db['live']['password'] = "tr33Cr0w";
$db['live']['database'] = "coolbrew";
$db['live']['dbdriver'] = "mysql";
$db['live']['dbprefix'] = "";
$db['live']['active_r'] = TRUE;
$db['live']['pconnect'] = TRUE;
$db['live']['db_debug'] = TRUE;
$db['live']['cache_on'] = FALSE;
$db['live']['cachedir'] = ""; 
$db['live']['char_set'] = "utf8";
$db['live']['dbcollat'] = "utf8_general_ci";

?>