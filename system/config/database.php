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

// set the database configuration. If database configurations are different 
// for each server level, you can make these conditional using the
// SERVER_LEVEL constant.

// NOTE: I restructured this because I needed access to DB info
// for all server levels as part of the API module.


$db['production']['hostname'] = "mysql-serv-master:3307";
$db['production']['username'] = "brewuser";
$db['production']['password'] = "fr33tyr8";
$db['production']['database'] = "coolbrew_live";
$db['production']['dbdriver'] = "mysql";
$db['production']['dbprefix'] = "";
$db['production']['active_r'] = TRUE;
$db['production']['pconnect'] = TRUE;
$db['production']['db_debug'] = TRUE;
$db['production']['cache_on'] = FALSE;
$db['production']['cachedir'] = ""; 
$db['production']['char_set'] = "utf8";
$db['production']['dbcollat'] = "utf8_general_ci";

// -----------------------------------------------------------------------
// LIVE settings

$db['live-read']['hostname'] = "mysql-serv-master:3307";
$db['live-read']['username'] = "brewuser";
$db['live-read']['password'] = "fr33tyr8";
$db['live-read']['database'] = "coolbrew_live";
$db['live-read']['dbdriver'] = "mysql";
$db['live-read']['dbprefix'] = "";
$db['live-read']['active_r'] = TRUE;
$db['live-read']['pconnect'] = TRUE;
$db['live-read']['db_debug'] = TRUE;
$db['live-read']['cache_on'] = FALSE;
$db['live-read']['cachedir'] = "";
$db['live-read']['char_set'] = "utf8";
$db['live-read']['dbcollat'] = "utf8_general_ci";

$db['live-write']['hostname'] = "mysql-serv-master:3307";
$db['live-write']['username'] = "brewuser";
$db['live-write']['password'] = "fr33tyr8";
$db['live-write']['database'] = "coolbrew_live";
$db['live-write']['dbdriver'] = "mysql";
$db['live-write']['dbprefix'] = "";
$db['live-write']['active_r'] = TRUE;
$db['live-write']['pconnect'] = TRUE;
$db['live-write']['db_debug'] = TRUE;
$db['live-write']['cache_on'] = FALSE;
$db['live-write']['cachedir'] = "";
$db['live-write']['char_set'] = "utf8";
$db['live-write']['dbcollat'] = "utf8_general_ci";
   
// $db['live-hcg_read']['hostname'] = "mysql-serv-master:3307";
// $db['live-hcg_read']['username'] = "public_db";
// $db['live-hcg_read']['password'] = "jage0katt";
// $db['live-hcg_read']['database'] = "hcgpublic_live";
// $db['live-hcg_read']['dbdriver'] = "mysql";
// $db['live-hcg_read']['dbprefix'] = "";
// $db['live-hcg_read']['active_r'] = TRUE;
// $db['live-hcg_read']['pconnect'] = TRUE;
// $db['live-hcg_read']['db_debug'] = TRUE;
// $db['live-hcg_read']['cache_on'] = FALSE;
// $db['live-hcg_read']['cachedir'] = "";
// $db['live-hcg_read']['char_set'] = "utf8";
// $db['live-hcg_read']['dbcollat'] = "utf8_general_ci";
// 
// $db['live-hcg_write']['hostname'] = "mysql-serv-master:3307";
// $db['live-hcg_write']['username'] = "public_db";
// $db['live-hcg_write']['password'] = "jage0katt";
// $db['live-hcg_write']['database'] = "hcgpublic_live";
// $db['live-hcg_write']['dbdriver'] = "mysql";
// $db['live-hcg_write']['dbprefix'] = "";
// $db['live-hcg_write']['active_r'] = TRUE;
// $db['live-hcg_write']['pconnect'] = TRUE;
// $db['live-hcg_write']['db_debug'] = TRUE;
// $db['live-hcg_write']['cache_on'] = FALSE;
// $db['live-hcg_write']['cachedir'] = "";
// $db['live-hcg_write']['char_set'] = "utf8";
// $db['live-hcg_write']['dbcollat'] = "utf8_general_ci";

// -----------------------------------------------------------------------
// STAGE settings

$db['stage-read']['hostname'] = "mysql-serv-master:3307";
$db['stage-read']['username'] = "brewuser_stage";
$db['stage-read']['password'] = "fr33tyr8";
$db['stage-read']['database'] = "coolbrew_stage";
$db['stage-read']['dbdriver'] = "mysql";
$db['stage-read']['dbprefix'] = "";
$db['stage-read']['active_r'] = TRUE;
$db['stage-read']['pconnect'] = TRUE;
$db['stage-read']['db_debug'] = TRUE;
$db['stage-read']['cache_on'] = FALSE;
$db['stage-read']['cachedir'] = "";
$db['stage-read']['char_set'] = "utf8";
$db['stage-read']['dbcollat'] = "utf8_general_ci";

$db['stage-write']['hostname'] = "mysql-serv-master:3307";
$db['stage-write']['username'] = "brewuser_stage";
$db['stage-write']['password'] = "fr33tyr8";
$db['stage-write']['database'] = "coolbrew_stage";
$db['stage-write']['dbdriver'] = "mysql";
$db['stage-write']['dbprefix'] = "";
$db['stage-write']['active_r'] = TRUE;
$db['stage-write']['pconnect'] = TRUE;
$db['stage-write']['db_debug'] = TRUE;
$db['stage-write']['cache_on'] = FALSE;
$db['stage-write']['cachedir'] = "";
$db['stage-write']['char_set'] = "utf8";
$db['stage-write']['dbcollat'] = "utf8_general_ci";

// $db['stage-hcg_read']['hostname'] = "mysql-serv-master:3307";
// $db['stage-hcg_read']['username'] = "public_db";
// $db['stage-hcg_read']['password'] = "jage0katt";
// $db['stage-hcg_read']['database'] = "hcgpublic_stage";
// $db['stage-hcg_read']['dbdriver'] = "mysql";
// $db['stage-hcg_read']['dbprefix'] = "";
// $db['stage-hcg_read']['active_r'] = TRUE;
// $db['stage-hcg_read']['pconnect'] = TRUE;
// $db['stage-hcg_read']['db_debug'] = TRUE;
// $db['stage-hcg_read']['cache_on'] = FALSE;
// $db['stage-hcg_read']['cachedir'] = "";
// $db['stage-hcg_read']['char_set'] = "utf8";
// $db['stage-hcg_read']['dbcollat'] = "utf8_general_ci";
// 
// $db['stage-hcg_write']['hostname'] = "mysql-serv-master:3307";
// $db['stage-hcg_write']['username'] = "public_db";
// $db['stage-hcg_write']['password'] = "jage0katt";
// $db['stage-hcg_write']['database'] = "hcgpublic_stage";
// $db['stage-hcg_write']['dbdriver'] = "mysql";
// $db['stage-hcg_write']['dbprefix'] = "";
// $db['stage-hcg_write']['active_r'] = TRUE;
// $db['stage-hcg_write']['pconnect'] = TRUE;
// $db['stage-hcg_write']['db_debug'] = TRUE;
// $db['stage-hcg_write']['cache_on'] = FALSE;
// $db['stage-hcg_write']['cachedir'] = "";
// $db['stage-hcg_write']['char_set'] = "utf8";
// $db['stage-hcg_write']['dbcollat'] = "utf8_general_ci";

// -----------------------------------------------------------------------
// DEV settings

$db['dev-read']['hostname'] = "bolwebdev1:3306";
$db['dev-read']['username'] = "brewuser";
$db['dev-read']['password'] = "fr33tyr8";
$db['dev-read']['database'] = "coolbrew";
$db['dev-read']['dbdriver'] = "mysql";
$db['dev-read']['dbprefix'] = "";
$db['dev-read']['active_r'] = TRUE;
$db['dev-read']['pconnect'] = TRUE;
$db['dev-read']['db_debug'] = TRUE;
$db['dev-read']['cache_on'] = FALSE;
$db['dev-read']['cachedir'] = "";
$db['dev-read']['char_set'] = "utf8";
$db['dev-read']['dbcollat'] = "utf8_general_ci";

$db['dev-write']['hostname'] = "bolwebdev1:3306";
$db['dev-write']['username'] = "brewuser";
$db['dev-write']['password'] = "fr33tyr8";
$db['dev-write']['database'] = "coolbrew";
$db['dev-write']['dbdriver'] = "mysql";
$db['dev-write']['dbprefix'] = "";
$db['dev-write']['active_r'] = TRUE;
$db['dev-write']['pconnect'] = TRUE;
$db['dev-write']['db_debug'] = TRUE;
$db['dev-write']['cache_on'] = FALSE;
$db['dev-write']['cachedir'] = "";
$db['dev-write']['char_set'] = "utf8";
$db['dev-write']['dbcollat'] = "utf8_general_ci";

// $db['dev-hcg_read']['hostname'] = "bolwebdev1:3306";
// $db['dev-hcg_read']['username'] = "public_db";
// $db['dev-hcg_read']['password'] = "jage0katt";
// $db['dev-hcg_read']['database'] = "hcg_public";
// $db['dev-hcg_read']['dbdriver'] = "mysql";
// $db['dev-hcg_read']['dbprefix'] = "";
// $db['dev-hcg_read']['active_r'] = TRUE;
// $db['dev-hcg_read']['pconnect'] = TRUE;
// $db['dev-hcg_read']['db_debug'] = TRUE;
// $db['dev-hcg_read']['cache_on'] = FALSE;
// $db['dev-hcg_read']['cachedir'] = "";
// $db['dev-hcg_read']['char_set'] = "utf8";
// $db['dev-hcg_read']['dbcollat'] = "utf8_general_ci";
// 
// $db['dev-hcg_write']['hostname'] = "bolwebdev1:3306";
// $db['dev-hcg_write']['username'] = "public_db";
// $db['dev-hcg_write']['password'] = "jage0katt";
// $db['dev-hcg_write']['database'] = "hcg_public";
// $db['dev-hcg_write']['dbdriver'] = "mysql";
// $db['dev-hcg_write']['dbprefix'] = "";
// $db['dev-hcg_write']['active_r'] = TRUE;
// $db['dev-hcg_write']['pconnect'] = TRUE;
// $db['dev-hcg_write']['db_debug'] = TRUE;
// $db['dev-hcg_write']['cache_on'] = FALSE;
// $db['dev-hcg_write']['cachedir'] = "";
// $db['dev-hcg_write']['char_set'] = "utf8";
// $db['dev-hcg_write']['dbcollat'] = "utf8_general_ci";

// -----------------------------------------------------------------------
// LOCAL settings

$db['local-read']['hostname'] = "localhost:3306";
$db['local-read']['username'] = "brewuser";
$db['local-read']['password'] = "vfw_ufy3HFN!dpx8dev";
$db['local-read']['database'] = "coolbrew";
$db['local-read']['dbdriver'] = "mysql";
$db['local-read']['dbprefix'] = "";
$db['local-read']['active_r'] = TRUE;
$db['local-read']['pconnect'] = TRUE;
$db['local-read']['db_debug'] = TRUE;
$db['local-read']['cache_on'] = FALSE;
$db['local-read']['cachedir'] = "";
$db['local-read']['char_set'] = "utf8";
$db['local-read']['dbcollat'] = "utf8_general_ci";

$db['local-write']['hostname'] = "localhost:3306";
$db['local-write']['username'] = "brewuser";
$db['local-write']['password'] = "vfw_ufy3HFN!dpx8dev";
$db['local-write']['database'] = "coolbrew";
$db['local-write']['dbdriver'] = "mysql";
$db['local-write']['dbprefix'] = "";
$db['local-write']['active_r'] = TRUE;
$db['local-write']['pconnect'] = TRUE;
$db['local-write']['db_debug'] = TRUE;
$db['local-write']['cache_on'] = FALSE;
$db['local-write']['cachedir'] = "";
$db['local-write']['char_set'] = "utf8";
$db['local-write']['dbcollat'] = "utf8_general_ci";

// $db['local-hcg_read']['hostname'] = "localhost:3306";
// $db['local-hcg_read']['username'] = "brewuser";
// $db['local-hcg_read']['password'] = "vfw_ufy3HFN!dpx8dev";
// $db['local-hcg_read']['database'] = "hcgpublic";
// $db['local-hcg_read']['dbdriver'] = "mysql";
// $db['local-hcg_read']['dbprefix'] = "";
// $db['local-hcg_read']['active_r'] = TRUE;
// $db['local-hcg_read']['pconnect'] = TRUE;
// $db['local-hcg_read']['db_debug'] = TRUE;
// $db['local-hcg_read']['cache_on'] = FALSE;
// $db['local-hcg_read']['cachedir'] = "";
// $db['local-hcg_read']['char_set'] = "utf8";
// $db['local-hcg_read']['dbcollat'] = "utf8_general_ci";
// 
// $db['local-hcg_write']['hostname'] = "localhost:3306";
// $db['local-hcg_write']['username'] = "brewuser";
// $db['local-hcg_write']['password'] = "vfw_ufy3HFN!dpx8dev";
// $db['local-hcg_write']['database'] = "hcgpublic";
// $db['local-hcg_write']['dbdriver'] = "mysql";
// $db['local-hcg_write']['dbprefix'] = "";
// $db['local-hcg_write']['active_r'] = TRUE;
// $db['local-hcg_write']['pconnect'] = TRUE;
// $db['local-hcg_write']['db_debug'] = TRUE;
// $db['local-hcg_write']['cache_on'] = FALSE;
// $db['local-hcg_write']['cachedir'] = "";
// $db['local-hcg_write']['char_set'] = "utf8";
// $db['local-hcg_write']['dbcollat'] = "utf8_general_ci";


// -----------------------------------------------------------------------
// Assign values to the official names based on DB_SERVER_LEVEL

if (DB_SERVER_LEVEL == "live") 
{
   $active_group = "write";
   $db['read'] = $db['live-read'];
   $db['write'] = $db['live-write'];
   // $db['hcg_read'] = $db['live-hcg_read'];
   // $db['hcg_write'] = $db['live-hcg_write'];
}
elseif (DB_SERVER_LEVEL == "stage")
{
   $active_group = "write";
   $db['read'] = $db['stage-read'];
   $db['write'] = $db['stage-write'];
   // $db['hcg_read'] = $db['stage-hcg_read'];
   // $db['hcg_write'] = $db['stage-hcg_write'];
}
elseif (DB_SERVER_LEVEL == "dev")
{  
   $active_group = "read";
   $db['read'] = $db['dev-read'];
   $db['write'] = $db['dev-write'];
   // $db['hcg_read'] = $db['dev-hcg_read'];
   // $db['hcg_write'] = $db['dev-hcg_write'];
}
else  // server_level == "local"
{  
   $active_group = "read";
   $db['read'] = $db['local-read'];
   $db['write'] = $db['local-write'];
   // $db['hcg_read'] = $db['local-hcg_read'];
   // $db['hcg_write'] = $db['local-hcg_write'];
}

?>