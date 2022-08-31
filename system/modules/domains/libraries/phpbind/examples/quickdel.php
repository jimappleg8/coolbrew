<html><head><title>Adding Alias to DNS</title></head><body>
<?php

require ("class.bind8.php");
require ("class.domainsfile.php");


$domain=$HTTP_GET_VARS['domain'];

if(!$domain){
	echo "Usage Error!\n";
	return;
}

		$dns = new BindNs($domain);

		if(!$dns->EXISTS){
			echo "Error. $domain does not exist in dns.";
			return false;
		}

                        $dns->activate("del");

?>
<META HTTP-EQUIV="Refresh" Content  = "1; URL=/system-admin/">
</body></html>
