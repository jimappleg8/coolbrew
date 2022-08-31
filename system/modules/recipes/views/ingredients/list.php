<div id="ingredients">

<div class="block" style="margin-bottom:0;">
<?php if ($recipes['ingredient_exists'] == true): ?>

   <div class="listing">

<table style="background-color:transparent;">
   <?php foreach($ingredients AS $ingred): ?>
<tr>
   <td style="width:130px;"><a href="<?=site_url('ingredients/move/'.$site_id.'/'.$ingred['ID'].'/up');?>" class="admin" onclick="moveIngredient(this.href); return false;">^</a> | <a href="<?=site_url('ingredients/move/'.$site_id.'/'.$ingred['ID'].'/dn');?>" class="admin" onclick="moveIngredient(this.href); return false;">v</a> | <a href="<?=site_url('ingredients/edit/'.$site_id.'/'.$ingred['ID'].'/'.$last_action);?>" class="admin" onclick="showEditIngredient(this.href); return false;">edit</a> | <a href="<?=site_url('ingredients/delete/'.$site_id.'/'.$ingred['ID']);?>" class="admin" onclick="deleteIngredient(this.href); return false;">delete</a></td>
   <td style="width:451px;"> 
   <?php if ($ingred['IsHeading'] == 1): ?><span class="heading"><?php endif; ?><?=$ingred['Quantity'];?> <?=$ingred['Name'];?><?php if ($ingred['IsHeading'] == 1): ?></span><?php endif; ?>
   </td>
</tr>
   <?php endforeach; ?>
</table>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There are no ingredients to display.</p>
   
<?php endif; ?>
   
</div>

</div>