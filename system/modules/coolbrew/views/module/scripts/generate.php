#!/usr/local/bin/php

&lt;?php

// -----------------------------------------------------------------------
// Script to autogenerate various coolbrew components
// -----------------------------------------------------------------------

$COOLBREW['command_line_interface'] = TRUE;
require_once 'coolbrew.inc.php';
get('coolbrew.generate.index', '<?=$module_name;?>');

?&gt;