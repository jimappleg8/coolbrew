<?php

$domain=$HTTP_GET_VARS['domain'];
$IP=$HTTP_GET_VARS['IP'];

if(!$domain){

?>
<form action="make-domain.php" method="GET">
Domain name: <input type="text" size="20" name="domain"><Br>
IP Address: <input type="text" size="20" name="IP"><br>
<input type=submit value="Make Domain">
</form>
<?php
exit;
}

include("phpbind/class.bind8.php");
echo "<P>We are creating $domain with IP: $IP\n<BR>";
$dns = new BindNs();
$dns->initialize($domain);
$dns->addA($IP);
$dns->addA("mail:10.1.1.51");
$dns->addMX(10,"mail.samples.com");

$dns->addCNAME("www");
$dns->addCNAME("mail");
$dns->addCNAME("junkie");

//I no longer like this ip..lemme nuke it!
$dns->delA("205.1.1.25");

        //We set some bull defaults here for stuff we always use.
echo "Setting defaults...\n<br>";
        $dns->setNameserver("crap.nameserver.com");    //Note: Ending period, I'm lazy...
        $dns->setContact("dillweed@mydomain.com");  //Note: This is in an email format that we convert.
if($dns->EXISTS){
	$dns->incSerial();
} else {
        $dns->autoSerial();     //Note: Automatically generate a serial #.
}
        $dns->setRefresh();     //Note: We default to 10800 (3 hours)
        $dns->setRetry();       //Note: We default to 3600 (1 hour)
        $dns->setExpire();      //Note: We default to 604800 (1 week)
        $dns->setTtl();         //Note: We default to 86400 (1 day)
        $dns->addNS("my.crappy.nameserver.com");


if($dns->ERROR){
echo "<BR>$dns->ERROR<BR>";
}

//$dns->named();		//If we want to see what's in named.conf now...

//now we activate the bad boy!

$dns->activate();  // That's it!

if($dns->ERROR){
echo "<BR>$dns->ERROR<BR>";
}
?>

Domain created!<BR>
<?php
exit;

/*
	//We set some bull defaults here for stuff we always use.
echo "Setting defaults...\n<br>";
	$dns->setNameserver("crap.nameserver.com.");	//Note: Ending period, I'm lazy...
	$dns->setContact("dillweedr@mydomain.com");  //Note: This is in an email format that we convert.
	$dns->autoSerial();	//Note: Automatically generate a serial #.
	$dns->setRefresh();	//Note: We default to 10800 (3 hours)
	$dns->setRetry();	//Note: We default to 3600 (1 hour)
	$dns->setExpire();	//Note: We default to 604800 (1 week)
	$dns->setTtl();		//Note: We default to 86400 (1 day)
	$dns->addNS("my.crappy.nameserver.com");
*/	
?>
