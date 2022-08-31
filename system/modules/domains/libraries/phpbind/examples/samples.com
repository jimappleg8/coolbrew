$ORIGIN com.
samples		IN	SOA	nameserver.concentric.net. alex.lightem.com. (
		20000513002 10800 3600 604800 3600 )
		IN	NS	nameserver.concentric.net.
		IN	NS	nameserver1.concentric.net.
		IN	NS	nameserver2.concentric.net.
		IN	NS	nameserver3.concentric.net.
		IN	MX	10 samples.com.
		IN	MX	50 custmail.concentric.net.
		IN	A	209.11.10.20
$ORIGIN samples.com.
www		IN	CNAME	samples.com.
