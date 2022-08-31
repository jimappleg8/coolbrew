<?php

/* get disk space free (in bytes) */
$df = disk_free_space("/var/opt/httpd");
/* and get disk space total (in bytes)  */
$dt = disk_total_space("/var/opt/httpd");
/* now we calculate the disk space used (in bytes) */
$du = $dt - $df;
/* percentage of disk used - this will be used to also set the width % of the progress bar */
$dp = sprintf('%.2f',($du / $dt) * 100);

/* and we formate the size from bytes to MB, GB, etc. */
$df = formatSize($df);
$du = formatSize($du);
$dt = formatSize($dt);

function formatSize( $bytes )
{
        $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
        for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
                return( round( $bytes, 2 ) . " " . $types[$i] );
}

function get_server_memory_usage(){
 
	$free = shell_exec('free');
	$free = (string)trim($free);
	$free_arr = explode("\n", $free);
	$mem = explode(" ", $free_arr[1]);
	$mem = array_filter($mem);
	$mem = array_merge($mem);
	$bufcache = explode(" ", $free_arr[2]);
	$bufcache = array_filter($bufcache);
	$bufcache = array_merge($bufcache);
	$memory_usage = $bufcache[2]/$mem[1]*100; 
	return sprintf('%.2f', $memory_usage);
}

function get_server_cpu_usage(){
 
	$load = sys_getloadavg();
	return $load[0];
 
}
?>
Total Disk Space:<?=$dt;?>|Free Disk Space:<?=$df;?>|Used Disk Space:<?=$du;?>|Percent Used Disk Space:<?=$dp;?>%|Server Memory Usage:<?=get_server_memory_usage();?>%|Server CPU Usage:<?=get_server_cpu_usage();?>%