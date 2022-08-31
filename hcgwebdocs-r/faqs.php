<?php

require_once 'coolbrew.inc.php';

$COOLBREW['uri_is_complete'] = TRUE;

$results = get('faqs');

echo $results;

/* End of file index.php */
/* Location: hcgwebdocs/api/index.php */