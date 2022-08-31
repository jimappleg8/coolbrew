To: Consumer Relations <consumerrelations@hain-celestial.com>
From: <?=$FirstName;?> <?=$LastName;?> <<?=$Email;?>>
Subject:

Subject: webform
URL: http://<?=$URL;?>

fname: <?=$FirstName;?>

lname: <?=$LastName;?>

email: <?=$Email?>

comment: <?=$Message;?>

Product Details:

<?=$ProductName;?> (<?=$ProductID;?>)


Current Store Details:

<?=$StoreName;?> (<?=($Src == 'nielsen') ? 'Nielsen-'.$StoreID : $StoreID;?>)

<?=$Address1;?>

<?php if (isset($Address2) && $Address2 != ''): ?>
<?=$Address2;?>
<?php endif; ?>

<?=$City;?>, <?=$State;?> <?=$Zip;?>

<?=$Phone;?>


