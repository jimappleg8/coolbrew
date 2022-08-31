<div class="ingredient">

<h2><?=$ingredient['Ingredient'];?>
<br /><span><?=$ingredient['LatinName'];?></span></h2>

<?php if (is_string ($ingredient ['ImageFile']) && strlen ($ingredient ['ImageFile']) > 0): ?>
	<img src="<?= base_url ().'images/ingredients/'.$ingredient ['ImageFile']; ?>" width="<?= $ingredient ['ImageWidth']; ?>" height="<?= $ingredient ['ImageHeight']; ?>"<?= (is_string ($ingredient ['ImageAlt']) && strlen ($ingredient ['ImageAlt']) > 0) ? ' alt="'.str_replace ('"', '', $ingredient ['ImageAlt']).'"' : ''; ?>" />
<?php endif; ?>

<p><?=$ingredient['Description'];?></p>

<?php if (count ($ingredient ['alternate_name']) > 0): ?>
	Commonly called:
	<?php
		for ($i = 0; $i < count ($ingredient ['alternate_name']); $i++)
		{
			if ($i > 0)
				echo ', ';
			echo $ingredient ['alternate_name'] [$i];
		}
	?>
<?php endif; ?>

</div>

