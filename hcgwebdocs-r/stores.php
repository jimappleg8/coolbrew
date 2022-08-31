<?php

require_once 'coolbrew.inc.php';

$COOLBREW['uri_is_complete'] = TRUE;

$results = get('stores');

echo $results;

/* End of file index.php */
/* Location: hcgwebdocs/api/index.php */