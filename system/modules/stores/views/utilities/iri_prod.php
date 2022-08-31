<?php if ( ! empty($errors)): ?>
Errors:
   <?php foreach ($errors AS $error): ?>
<?=$error."\n";?>
   <?php endforeach; ?>

<?php endif; ?>
<?=$line;?>
