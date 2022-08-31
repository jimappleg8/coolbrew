To: Jim Applegate <japplega@hain-celestial.com>, webmaster <webmaster@hain-celestial.com>
From: <?=$brand_name?> <do-not-reply@hain-celestial.com>
Subject: [Store Locator Message]

Subject: webform
URL: http://<?=$URL?>

Name: <?=$FirstName;?> <?=$LastName;?>

Email: <?=$Email;?>

Message: <?=$Message;?>


Product Details:

<?=$ProductName;?> (<?=$ProductID;?>)


Current Store Details:

<?=$StoreName;?> (<?=($StoreID == '') ? 'IRI' : $StoreID;?>)

<?=$Address1;?>

<?php if (isset($Address2) && $Address2 != ''): ?>
<?=$Address2;?>
<?php endif; ?>

<?=$City;?>, <?=$State;?> <?=$Zip;?>

<?=$Phone;?>

