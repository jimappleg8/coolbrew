<html><head><title>Adding to DNS</title></head><body>
<?php

require ("class.bind8.php");

$domain=$HTTP_GET_VARS['domain'];
$ip=$HTTP_GET_VARS['ip'];
$type=$HTTP_GET_VARS['type'];
$mx=$HTTP_GET_VARS['mx'];
$maindomain=$HTTP_GET_VARS['maindom'];

if(!$domain || !$ip || !$type){
	echo "Usage Error!\n";
	return;
}
		$dns = new BindNs($domain);

                if($mx){
                        $dns->addMX(10,$mx);
                }

                if($dns->EXISTS){
                        $dns->incSerial();
                } else {
                        $dns->autoSerial();     //Note: Automatically generate a serial #.

                                $dns->addCNAME("www");
                        $dns->addA($ip);
                                $dns->addNS("ns1.somesite.com");
                }
                        $dns->setRefresh();     //Note: We default to 10800 (3 hours)
                        $dns->setRetry();       //Note: We default to 3600 (1 hour)
                        $dns->setExpire();      //Note: We default to 604800 (1 week)
                        $dns->setTtl();         //Note: We default to 86400 (1 day)
                        $dns->setNameserver("default.nameserver.net");
                        $dns->setContact("joe@blow.com");

                        if($dns->ERROR){
                                echo "<BR>$dns->ERROR<BR>";
                        }

                        $dns->activate();


?>
</body></html>
