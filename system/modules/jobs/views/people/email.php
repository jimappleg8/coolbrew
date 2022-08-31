To: <?=$FirstName;?> <?=$LastName;?> <<?=$Email;?>>
From: HCG Careers <do-not-reply@hain-celestial.com>
Subject: A careers admin account has been created for you

We have created an account for you so you can use the careers admin area to create and edit job listings on the HCG corporate website. Your account info is:

User name: <?=$Username;?>

Password : <?=$Password;?>


This password should be used for the first log-in only. Once you are logged-in, please click on the "Edit your account" link at the top of the screen, enter a new password, and click "Save changes".

<?php if ($PersonalNote != ''): ?>
<?=$PersonalNote;?>
<?php endif; ?>


To login to your careers admin area, visit:
http://www.hain-celestial.com/careers/admin.php

