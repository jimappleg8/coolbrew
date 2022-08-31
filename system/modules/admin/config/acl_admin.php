<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| ACCESS CONTROL LIST FOR ADMIN CONTROLLER
| -------------------------------------------------------------------
| This file contains an array of access control information for the
| admin controller. It contains information about each public method 
| in the controller and its default access level.
|
*/

$acl_admin[] = array( 'adm-sites',     'list',        'ALLOW' );
$acl_admin[] = array( 'adm-sites',     'add',         'DENY'  );
$acl_admin[] = array( 'adm-sites',     'report',      'DENY'  );
$acl_admin[] = array( 'adm-links',     'list',        'ALLOW' );
$acl_admin[] = array( 'adm-links',     'add',         'ALLOW' );
$acl_admin[] = array( 'adm-links',     'edit',        'ALLOW' );
$acl_admin[] = array( 'adm-links',     'delete',      'DENY'  );
$acl_admin[] = array( 'adm-domains',   'list',        'ALLOW' );
$acl_admin[] = array( 'adm-domains',   'add',         'DENY'  );
$acl_admin[] = array( 'adm-domains',   'edit',        'DENY'  );
$acl_admin[] = array( 'adm-domains',   'archive',     'DENY'  );
$acl_admin[] = array( 'adm-domains',   'view',        'ALLOW' );
$acl_admin[] = array( 'adm-domains',   'report',      'ALLOW' );
$acl_admin[] = array( 'adm-settings',  'edit',        'DENY'  );
$acl_admin[] = array( 'adm-modules',   'list',        'DENY'  );
$acl_admin[] = array( 'adm-modules',   'install',     'DENY'  );
$acl_admin[] = array( 'adm-modules',   'uninstall',   'DENY'  );
$acl_admin[] = array( 'adm-people',    'list',        'ALLOW' );
$acl_admin[] = array( 'adm-people',    'add',         'DENY'  );
$acl_admin[] = array( 'adm-people',    'edit',        'DENY'  );
$acl_admin[] = array( 'adm-people',    'view',        'ALLOW' );
$acl_admin[] = array( 'adm-people',    'permissions', 'DENY'  );
$acl_admin[] = array( 'adm-people',    'delete',      'DENY'  );
$acl_admin[] = array( 'adm-people',    'my-edit',     'ALLOW' );
$acl_admin[] = array( 'adm-companies', 'add',         'DENY'  );
$acl_admin[] = array( 'adm-companies', 'edit',        'DENY'  );
$acl_admin[] = array( 'adm-companies', 'delete',      'DENY'  );
?>