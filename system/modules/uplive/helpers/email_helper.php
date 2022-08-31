<?php

/** 
 * Email Helper
 *
 */

// -------------------------------------------------------------------------

/**
 * Send message to the web team
 *
 * @access   public
 * @params   string   the server that is involved with the message
 * @params   string   the subject line
 * @params   string   the message
 * @returns  array   
 */
function send_message($server, $subject, $message)
{
   $content  = "To: Jim Applegate <jim.applegate@hain-celestial.com>\n";
   $content .= "Cc: David Basch <david.basch@hain-celestial.com>\n";
   $content .= "From: ".$server." <webmaster@hain-celestial.com>\n";
   $content .= "Subject: $subject\n";
   $content .= "\n";
   $content .= "Date Sent: ".date('Y-m-d H:i:s')."\n";
   $content .= "\n";
   $content .= $message;

   $sendmail = "/usr/sbin/sendmail -t ";

   $fd = popen($sendmail,"w");
   fputs($fd, stripslashes($content)."\n");
   pclose($fd);
}

?>