<!-- this version is xhtml strict compatible -->

<!-- page header 0 -->

<div id="nutfacts" class="us-normal">

<?php if ($nutfacts['display_hd'] == true): ?>
<table width="246" cellpadding="1" cellspacing="0" border="0" bgcolor="#FFFFFF">
<tr>
<td><span class="productSbhd"><?=$nutfacts['ProductName'];?></span></td>
</tr>
</table>
<?php endif; ?>

<!-- end page header -->

<!-- table header -->

<table width="246" cellpadding="1" cellspacing="0" border="0" style="background-color:#000;">
<tr><td>

   <table width="244" cellpadding="1" cellspacing="0" border="0" style="background-color:#FFF;" class="Nutrition">
   
   <tr> <!-- row that establishes grid -->
   <td class="line" style="width:5px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="3" height="1" alt="" /></td>
   <td class="line" style="width:13px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="11" height="1" alt="" /></td>
   <td class="line" style="width:59px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="57" height="1" alt="" /></td>
   <td class="line" style="width:31px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="29" height="1" alt="" /></td>
   <td class="line" style="width:27px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="25" height="1" alt="" /></td>
   <td class="line" style="width:4px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="2" height="1" alt="" /></td>
   <td class="line" style="width:54px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="52" height="1" alt="" /></td>
   <td class="line" style="width:8px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="6" height="1" alt="" /></td>
   <td class="line" style="width:38px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="36" height="1" alt="" /></td>
   <td class="line" style="width:5px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="3" height="1" alt="" /></td>   
   </tr>
   
   <tr> <!-- row 1: establishes the top and side margins -->
   <td rowspan="<?=$nutfacts['total_rows'];?>" style="width:4px">&nbsp;</td>
   <td colspan="8" class="line"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="232" height="2" alt="" /></td>
   <td rowspan="<?=$nutfacts['total_rows'];?>" style="width:4px">&nbsp;</td>
   </tr>

<!-- end table header -->

<!-- section 1 -->

   <tr>
   <td colspan="8" class="NutritionHd"><b>Nutrition Facts</b></td>
   </tr>

   <tr>
   <td colspan="8" class="Nutrition">
   Serving Size: <?=set_default($nutfacts['SSIZE'], "???");?></td>
   </tr>

   <?php if ($nutfacts['MAKE'] != ""): ?>
      <tr>
      <td colspan="8" class="Nutrition">Makes: <?=$nutfacts['MAKE'];?></td>
      </tr>
   <?php endif; ?>

   <tr>
   <td colspan="8" class="Nutrition">
   Servings Per Container: <?=set_default($nutfacts['SERV'], "???");?></td>
   </tr>

<!-- end section 1 -->

<!-- section 2 -->

   <?=draw_line(array('width'=>'8', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'wideline'));?>
   
   <!-- Amount Per Serving -->
   <tr>
   <td colspan="8" class="NutritionSm"><b>Amount Per Serving</b></td>
   </tr>

   <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   
   <!-- Calories and Fat from Calories -->
   <tr>
   <td colspan="3" class="Nutrition">
   <b>Calories</b> <?=set_default($nutfacts['CAL'], "???");?></td>
   <?php if ($nutfacts['FATCAL'] != ""): ?>
      <td colspan="5" class="Nutrition right">
      Calories from Fat <?=$nutfacts['FATCAL'];?></td>
   <?php else: ?>
      <td colspan="5" class="Nutrition">&nbsp;</td>   
   <?php endif; ?>
   </tr>

   <?=draw_line(array('width'=>'4', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'wideline'));?>
   
   <tr>
   <td colspan="8" class="NutritionSm right"><b>% Daily Value<?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?></b></td>
   </tr>

<!-- end section 2 -->

   <?php if ($nutfacts['TFATQ'] != ""): ?>
      <!-- Total Fat -->
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="7" class="Nutrition"><b>Total Fat</b> <?=$nutfacts['TFATQ'];?><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>*<?php endif; ?></td>
      <?php if ($nutfacts['TFATP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['TFATP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['SFATQ'] != ""): ?>
      <!-- Saturated Fat - indented -->
      <?=draw_line(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Saturated Fat <?=$nutfacts['SFATQ'];?></td>
      <?php if ($nutfacts['SFATP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['SFATP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['HFATQ'] != ""): ?>
      <!-- Trans (Hydrogenated) Fat - indented -->
      <?=draw_line(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition"><i>Trans</i> Fat <?=$nutfacts['HFATQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['PFATQ'] != ""): ?>
      <!-- Polyunsaturated Fat - indented -->
      <?=draw_line(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Polyunsaturated Fat <?=$nutfacts['PFATQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['MFATQ'] != ""): ?>
      <!-- Monounsaturated Fat - indented -->
      <?=draw_line(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Monounsaturated Fat <?=$nutfacts['MFATQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['CHOLQ'] != ""): ?>
      <!-- Cholesterol -->
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="7" class="Nutrition"><b>Cholesterol</b> <?=$nutfacts['CHOLQ'];?></td>
      <?php if ($nutfacts['CHOLP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['CHOLP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['SODQ'] != ""): ?>
      <!-- Sodium -->
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="7" class="Nutrition"><b>Sodium</b> <?=$nutfacts['SODQ'];?></td>
      <?php if ($nutfacts['SODP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['SODP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['POTQ'] != ""): ?>
      <!-- Potassium -->
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="7" class="Nutrition"><b>Potassium</b> <?=$nutfacts['POTQ'];?></td>
      <?php if ($nutfacts['POTP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['POTP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['TCARBQ'] != ""): ?>
      <!-- Total Carb. -->
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="7" class="Nutrition"><b>Total Carb.</b> <?=$nutfacts['TCARBQ'];?></td>
      <?php if ($nutfacts['TCARBP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['TCARBP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['DFIBQ'] != ""): ?>
      <!-- Dietary Fiber - indented -->
      <?=draw_line(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Dietary Fiber <?=$nutfacts['DFIBQ'];?></td>
      <?php if ($nutfacts['DFIBP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['DFIBP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['SUGQ'] != ""): ?>
      <!-- Sugars - indented -->
      <?=draw_line(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="6" class="Nutrition">Sugars <?=$nutfacts['SUGQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['PROTQ'] != ""): ?>
      <!-- Protein -->
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="7" class="Nutrition"><b>Protein</b> <?=$nutfacts['PROTQ'];?></td>
      <?php if ($nutfacts['PROTP'] != ""): ?>
         <td class="Nutrition right"> 
         <b><?=$nutfacts['PROTP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   
   <?=draw_line(array('width'=>'8', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'wideline'));?>

<!-- end section 3 -->

<!-- section 4 -->

   <?php $toggle = 1; ?>

   <?php if ($nutfacts['VITAP'] != ""): ?>
      <!-- Vitamin A -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Vitamin A</td>
         <td class="Nutrition right"><?=$nutfacts['VITAP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Vitamin A</td>
         <td class="Nutrition right"><?=$nutfacts['VITAP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['VITCP'] != ""): ?>
      <!-- Vitamin C -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Vitamin C</td>
         <td class="Nutrition right"><?=$nutfacts['VITCP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Vitamin C</td>
         <td class="Nutrition right"><?=$nutfacts['VITCP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['CALCP'] != ""): ?>
      <!-- Calcium -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Calcium</td>
         <td class="Nutrition right"><?=$nutfacts['CALCP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Calcium</td>
         <td class="Nutrition right"><?=$nutfacts['CALCP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['IRONP'] != ""): ?>
      <!-- Iron -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Iron</td>
         <td class="Nutrition right"><?=$nutfacts['IRONP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Iron</td>
         <td class="Nutrition right"><?=$nutfacts['IRONP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['VITDP'] != ""): ?>
      <!-- Vitamin D -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Vitamin D</td>
         <td class="Nutrition right"><?=$nutfacts['VITDP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Vitamin D</td>
         <td class="Nutrition right"><?=$nutfacts['VITDP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['VITEP'] != ""): ?>
      <!-- Vitamin E -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Vitamin E</td>
         <td class="Nutrition right"><?=$nutfacts['VITEP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Vitamin E</td>
         <td class="Nutrition right"><?=$nutfacts['VITEP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['VITB6P'] != ""): ?>
      <!-- Vitamin B6 -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Vitamin B6</td>
         <td class="Nutrition right"><?=$nutfacts['VITB6P'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Vitamin B6</td>
         <td class="Nutrition right"><?=$nutfacts['VITB6P'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['VITB12P'] != ""): ?>
      <!-- Vitamin B12 -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Vitamin B12</td>
         <td class="Nutrition right"><?=$nutfacts['VITB12P'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Vitamin B12</td>
         <td class="Nutrition right"><?=$nutfacts['VITB12P'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['THIAP'] != ""): ?>
      <!-- Thiamin -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Thiamin</td>
         <td class="Nutrition right"><?=$nutfacts['THIAP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Thiamin</td>
         <td class="Nutrition right"><?=$nutfacts['THIAP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['RIBOP'] != ""): ?>
      <!-- Riboflavin -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Riboflavin</td>
         <td class="Nutrition right"><?=$nutfacts['RIBOP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Riboflavin</td>
         <td class="Nutrition right"><?=$nutfacts['RIBOP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['PHOSP'] != ""): ?>
      <!-- Phosphorous -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Phosphorous</td>
         <td class="Nutrition right"><?=$nutfacts['PHOSP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Phosphorous</td>
         <td class="Nutrition right"><?=$nutfacts['PHOSP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['MAGNP'] != ""): ?>
      <!-- Magnesium -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Magnesium</td>
         <td class="Nutrition right"><?=$nutfacts['MAGNP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Magnesium</td>
         <td class="Nutrition right"><?=$nutfacts['MAGNP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   <?php if ($nutfacts['NIACP'] != ""): ?>
      <!-- Niacin -->
      <?php if ($toggle == 1): ?>
         <?php $toggle = 2; ?>
         <tr>
         <td colspan="2" class="Nutrition">Niacin</td>
         <td class="Nutrition right"><?=$nutfacts['NIACP'];?>%</td>
      <?php else: ?>
         <?php $toggle = 1; ?>
         <td class="Nutrition center">&bull;</td>
         <td colspan="3" class="Nutrition">Niacin</td>
         <td class="Nutrition right"><?=$nutfacts['NIACP'];?>%</td>
         </tr>
         <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <?php endif; ?>
   <?php endif; ?>

   
   <?php if ($toggle == 2): ?>
      <td colspan="5" class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

<!-- end section 4 -->

<!-- section 5 -->

   <?php if ($toggle == 2): ?>
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <?php if ($nutfacts['STMT1Q'] != ""): ?>
      <tr>
      <td colspan="8" class="Nutrition"><?=$nutfacts['STMT1Q'];?></td>
      </tr>
      <?=draw_line(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>
      <tr>
      <td colspan="8" class="Nutrition">* <?=$nutfacts['STMT2Q'];?></td>
      </tr>
      <tr>
      <td colspan="8" class="line"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="2" height="1" alt="" /></td>
      </tr>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDV1']) == "YES"): ?>
      <tr>
      <td colspan="8" class="Nutrition"><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?> Percent Daily Values are based on a 2,000 calorie diet.</td>
      </tr>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDV2']) == "YES"): ?>
      <tr>
      <td colspan="8" class="Nutrition"><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?> Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.</td>
      </tr>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDVT']) == "YES"): ?>
      <tr>
      <td colspan="8" class="line"><img src="http://resources.hcgweb.net/shared/dot_black.gif" width="232" height="1" alt="" /></td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">&nbsp;</td>
      <td colspan="3" class="NutritionSm">Calories:</td>
      <td class="NutritionSm">2,000</td>
      <td colspan="2" class="NutritionSm">2,500</td>
      </tr>

      <tr>
      <td colspan="8" class="line"><img src="http://resources.hcgweb.net/shared/dot_black.gif" width="232" height="1" alt="" /></td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">Total Fat</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">65g</td>
      <td colspan="2" class="NutritionSm">80g</td>
      </tr>

      <tr>
      <td class="NutritionSm">&nbsp;</td>
      <td class="NutritionSm">Sat Fat</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">20g</td>
      <td colspan="2" class="NutritionSm">25g</td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">Cholesterol</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">300mg</td>
      <td colspan="2" class="NutritionSm">300mg</td>
      </tr>

      <tr>
      <td colspan="2" class="NutritionSm">Sodium</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td class="NutritionSm">2,400mg</td>
      <td colspan="2" class="NutritionSm">2,400mg</td>
      </tr>

      <tr>
      <td colspan="5" class="NutritionSm">Total Carbohydrate</td>
      <td class="NutritionSm">300g</td>
      <td colspan="2" class="NutritionSm">375g</td>
      </tr>

      <tr>
      <td class="NutritionSm">&nbsp;</td>
      <td colspan="4" class="NutritionSm">Dietary Fiber</td>
      <td class="NutritionSm">25g</td>
      <td colspan="2" class="NutritionSm">30g</td>
      </tr>
   <?php endif; ?>
   
<!-- end section 5 -->

<!-- table footer -->

   <tr>
   <td colspan="8" class="line"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="232" height="6" alt="" /></td>
   </tr>
   </table>
   
</td></tr>
</table>

<!-- end table footer -->

<table width="246" cellpadding="6" cellspacing="0" border="0">
<tr>

<td class="Nutrition"><p>The most accurate information is always on the label on the actual product. We periodically update our labels based on new nutritional analysis to verify natural variations from crop to crop and at times formula revisions. The website does not necessarily get updated at the same time. The values on the website are intended to be a general guide to consumers. For absolute values, the actual label on the product at hand should be relied on.</p></td>

</tr>
</table>

</div>
