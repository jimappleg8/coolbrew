#!/usr/local/bin/php -q
<?php

// -----------------------------------------------------------------------
// Script to index websites via a cron job
// -----------------------------------------------------------------------

$COOLBREW['command_line_interface'] = TRUE;

require_once 'coolbrew.inc.php';

get('seo.utilities.auto_index');

?>