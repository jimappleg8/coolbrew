<?php

require_once 'coolbrew.inc.php';

$COOLBREW['uri_is_complete'] = TRUE;

$results = get('recipes');

echo $results;

/* End of file index.php */
/* Location: hcgwebdocs/api/index.php */