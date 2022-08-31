<?php
   for ($i=0, $cnt=count($ContactEmail); $i<$cnt; $i++)
   {
      $ContactEmail[$i] = '<'.$ContactEmail[$i].'>';
   }
   $ContactEmails = implode(', ', $ContactEmail);
?>
To: <?=$ContactEmails;?> 
From: <?=$FName;?> <?=$LName;?> <<?=$Email;?>>
Subject: <?=$Subject;?> 
 

<?=$Subject;?>

Submitted: <?=$DateSent;?>


Name: <?=$FName?> <?php if ($MName != ''): ?><?=$MName?> <?php endif; ?><?=$LName?>


<?php if (isset($Address)): ?>Address: <?=$Address;?><?php endif; ?>


Home Phone: <?=$HomePhone?>

<?php if (isset($WorkPhone)): ?>Work Phone: <?=$WorkPhone?><?php endif; ?>


Email: <?=$Email?>


<?php if (isset($CoverLtr)): ?>
Cover Letter:

<?=$CoverLtr;?>
<?php endif; ?>


Resume:

<?=$Resume;?>


----
If this person submitted an EEO form, you can view it here:
http://www.hain-celestial.com/careers/view.php/admin/view_eeo/<?=$ID;?>/