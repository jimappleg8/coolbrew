<div class="ingredient">

<ul>
<?php foreach ($ingredients AS $ingredient): ?>

<li><a href="/modules/products/ingredient-detail.php/<?=$ingredient['IngredientCode'];?>" onclick="centeredWindow(this.href,'pop','600','600'); return false;"><?=$ingredient['Ingredient'];?> <span>&mdash; <?=$ingredient['LatinName'];?></span></a></li>

<?php endforeach; ?>
</ul>
</div>

