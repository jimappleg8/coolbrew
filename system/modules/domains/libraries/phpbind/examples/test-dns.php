<?php


//This is a debugging script.

include("phpbind/class.bind8.php");

$dns = new BindNs();
$dns->DEBUG=true;
$dns->initialize("vampire.com");
//$dns->initialize("mybozak.com");

if($dns->ERROR){
echo "<BR>$dns->ERROR<BR>";
}

$domain=$dns->DOMAIN;
$count=$dns->DOMAINCOUNT;
$ip=$dns->getIP();
$ipn=$dns->DOMAINS["vampire.com"]["A"][0];
$contact=$dns->getContact();
$serial=$dns->getSerial();


if(!$ip){

	echo "getIP() returned nada!<BR>\n";
}

if(!$contact){
	echo "getContact() returned nada!<BR>\n";
}

if(!$serial){
	echo "getSerial() returned nada!<BR>\n";
}
echo "Domain: [$domain]\n<BR>Entries: $count\n<BR>IP: $ip|$ipn\n<BR>Contact: $contact\n<BR>Serial: $serial\n<BR>";

if($dns->EXISTS){

	echo "Domain Entry EXISTS!\n<BR>Using $dns->DOMAINFILE<BR>\n";

}

if($dns->EMPTY){

        echo "Domain Entry is EMPTY!\n<BR>";

}

$dns->incSerial();

echo "<pre>\n";
echo "$dns->CONTENTS";
echo "</pre>\n";

$dns->named();


if($dns->ERROR){
echo "<BR>$dns->ERROR<BR>";
}
//exit;


?>
