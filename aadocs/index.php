<?php

require_once 'coolbrew.inc.php';

$COOLBREW['uri_is_complete'] = TRUE;

$results = get('admin.sites.dashboards');

//$results = "Hello World";

echo $results;

/* End of file index.php */