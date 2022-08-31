To: Jim Applegate <japplega@hain-celestial.com>
From: The Hain Celestial Group <do-not-reply@hain-celestial.com>
Subject: [ Contact Us from <?=$brand_name_encoded;?> ]
Mime-Version: 1.0
Content-Type: text/plain;charset=utf-8

Subject: webform
URL: http://<?=$URL?> 
fname: <?=$FName?> 
lname: <?=$LName?> 
address1: <?=$Address1?> 
<?php if (isset($Address2)): ?> 
address2: <?=$Address2?> 
<?php endif; ?>
city: <?=$City?> 
state: <?=$State?> 
zip: <?=$Zip?> 
<?php if (isset($Phone)): ?>
phone: <?=$Phone?> 
<?php endif; ?>
email: <?=$Email?> 
comment: <?=$Comment?> 
marketing: <?=$Marketing?> 
release: <?=$Release?> 
