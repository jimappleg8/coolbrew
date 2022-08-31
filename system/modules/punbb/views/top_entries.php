<?php foreach ($entries AS $entry): ?>

<h3 style="color:#039; margin-bottom:0;"><?=$entry['subject']?></h3>

<p style="font-size:10px; margin:2px 0 2px 0;"><?=date('F j, Y', $entry['posted']);?></p>

<p><?=$entry['message']?>

<?php endforeach; ?>