To: <?=$FirstName;?> <?=$LastName;?> <<?=$Email;?>>
From: hcgWeb <do-not-reply@hain-celestial.com>
Subject: <?php if ($Resend == TRUE): ?>Your hcgWeb admin account info<?php else: ?>A hcgWeb admin account has been created for you<?php endif; ?>


<?php if ($Resend == FALSE): ?>
We have created an account for you so you can use the hcgWeb admin area to make changes to and get important information about your websites. Your account info is:

Username: <?=$Username;?>

Password: <?=$Password;?>


This password should be used for the first log-in only. Once you are logged-in, please click on the "My info" link at the top of the screen, enter a new password, and click "Save changes".

<?php else: ?>
Here is the information you need to access the hcgWeb admin area, make changes to and get important information about your websites. Your account info is:

Username: <?=$Username;?>

Password: <?=$Password;?>


Your password has been reset to the one above. To change it, click on the "My info" link at the top of the screen, enter a new password, and click "Save changes".

<?php endif; ?>
<?php if ($PersonalNote != ''): ?>
Personal Note: <?=$PersonalNote;?>


<?php endif; ?>
To access the site, you will need to be connected to the Hain Celestial Group internal network. If you are an employee you are probably okay. Non-employees will need to connect to the network via VPN.

To login to the admin area, visit:
http://webadmin.hcgweb.net/admin/index.php/cp/sites/index

If you have any issues, please contact me. My information is below.

Jim Applegate
Web Producer
The Hain Celestial Group, Inc.
Jim.Applegate@hain-celestial.com
303-581-1464.

