<!-- this version is xhtml strict compatible -->

<!-- page header 1 -->

<div id="nutfacts" clas="us-prepared">

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
   <td class="line" style="width:44px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="42" height="1" alt="" /></td>
   <td class="line" style="width:10px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="8" height="1" alt="" /></td>
   <td class="line" style="width:8px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="6" height="1" alt="" /></td>
   <td class="line" style="width:38px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="36" height="1" alt="" /></td>
   <td class="line" style="width:5px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="3" height="1" alt="" /></td>   
   </tr>
   
   <tr> <!-- row 1: establishes the top and side margins -->
   <td rowspan="<?=$nutfacts['total_rows'];?>" style="width:4px">&nbsp;</td>
   <td colspan="9" class="line"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="232" height="2" alt="" /></td>
   <td rowspan="<?=$nutfacts['total_rows'];?>" style="width:4px">&nbsp;</td>
   </tr>

<!-- end table header -->

<!-- section 1 -->

   <tr>
   <td colspan="9" class="NutritionHd"><b>Nutrition Facts</b></td>
   </tr>
   
   <tr>
   <td colspan="8" class="Nutrition">
   Serving Size: <?=set_default($nutfacts['SSIZE'], '???');?></td>
   </tr>

   <?php if ($nutfacts['MAKE'] != ""): ?>
      <tr>
      <td colspan="8" class="Nutrition">Makes: <?=$nutfacts['MAKE'];?></td>
      </tr>
   <?php endif; ?>

   <tr>
   <td colspan="8" class="Nutrition">
   Servings Per Container: <?=set_default($nutfacts['SERV'], '???');?></td>
   </tr>

<!-- end section 1 -->

<!-- section 2 -->

   <?=draw_line_wide(array('width'=>'8', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'wideline'));?>
   
   <!-- Amount Per Serving -->
   <tr>
   <td colspan="5" class="NutritionSm" valign="bottom"><b>Amount Per Serving</b></td>
   <td class="NutritionSm right" valign="bottom"><b><?=$nutfacts['COL1HD'];?></b></td>
   <td colspan="3" class="NutritionSm right" valign="bottom"><b><?=$nutfacts['COL2HD'];?></b></td>
   </tr>

   <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   
   <!-- Calories -->
   <tr>
   <td colspan="5" class="Nutrition"><b>Calories</b></td>
   <td class="Nutrition right"><?=set_default($nutfacts['CAL'], "???");?></td>
   <td colspan="3" class="Nutrition"><div align="right"><?=set_default($nutfacts['CAL2'], "???");?></div></td>
   </tr>
   
   <!-- Fat from Calories - indented -->
   <?php if ($nutfacts['FATCAL'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Calories from Fat</td>
      <td class="Nutrition right"><?=$nutfacts['FATCAL'];?></td>
      <?php if ($nutfacts['FATCAL2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><?=$nutfacts['FATCAL2'];?></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <?=draw_line_wide(array('width'=>'4', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'wideline'));?>
   
   <tr>
   <td colspan="9" class="NutritionSm"><div align="right"><b>% Daily Value<?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?></b></div></td>
   </tr>


<!-- end section 2 -->

<!-- section 3 -->

   <!-- Total Fat -->
   <?php if ($nutfacts['TFATQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="5" class="Nutrition"><b>Total Fat</b> <?=$nutfacts['TFATQ'];?><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>*<?php endif; ?></td>
      <?php if ($nutfacts['TFATP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['TFATP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['TFATP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['TFATP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Saturated Fat - indented -->
   <?php if ($nutfacts['SFATQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Saturated Fat <?=$nutfacts['SFATQ'];?></td>
      <?php if ($nutfacts['SFATP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['SFATP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['SFATP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['SFATP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Trans Fat - indented -->
   <?php if ($nutfacts['HFATQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="7" class="Nutrition"><i>Trans</i> Fat <?=$nutfacts['HFATQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <!-- Polyunsaturated Fat - indented -->
   <?php if ($nutfacts['PFATQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="7" class="Nutrition">Polyunsaturated Fat <?=$nutfacts['PFATQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <!-- Monounsaturated Fat - indented -->
   <?php if ($nutfacts['MFATQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="7" class="Nutrition">Monounsaturated Fat <?=$nutfacts['MFATQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <!-- Cholesterol -->
   <?php if ($nutfacts['CHOLQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="5" class="Nutrition"><b>Cholesterol</b> <?=$nutfacts['CHOLQ'];?></td>
      <?php if ($nutfacts['CHOLP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['CHOLP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['CHOLP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['CHOLP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Sodium -->
   <?php if ($nutfacts['SODQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="5" class="Nutrition"><b>Sodium</b> <?=$nutfacts['SODQ'];?></td>
      <?php if ($nutfacts['SODP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['SODP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['SODP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['SODP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Potassium -->
   <?php if ($nutfacts['POTQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="5" class="Nutrition"><b>Potassium</b> <?=$nutfacts['POTQ'];?></td>
      <?php if ($nutfacts['POTP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['POTP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['POTP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['POTP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Total Carbs -->
   <?php if ($nutfacts['TCARBQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="5" class="Nutrition"><b>Total Carb.</b> <?=$nutfacts['TCARBQ'];?></td>
      <?php if ($nutfacts['TCARBP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['TCARBP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['TCARBP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['TCARBP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Dietary Fiber - indented -->
   <?php if ($nutfacts['DFIBQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Dietary Fiber <?=$nutfacts['DFIBQ'];?></td>
      <?php if ($nutfacts['DFIBP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['DFIBP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['DFIBP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['DFIBP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>

   <!-- Sugars - indented -->
   <?php if ($nutfacts['SUGQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Sugars <?=$nutfacts['SUGQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="3" class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <!-- Other Carbohydrate - indented -->
   <?php if ($nutfacts['OCARBQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'yes', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Other Carb. <?=$nutfacts['OCARBQ'];?></td>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="3" class="Nutrition">&nbsp;</td>
      </tr>
   <?php endif; ?>

   <!-- Protein -->
   <?php if ($nutfacts['PROTQ'] != ''): ?>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="5" class="Nutrition"><b>Protein</b> <?=$nutfacts['PROTQ'];?></td>
      <?php if ($nutfacts['PROTP'] != ''): ?>
         <td class="Nutrition right"><b><?=$nutfacts['PROTP'];?>%</b></td>
      <?php else: ?>
         <td class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      <?php if ($nutfacts['PROTP2'] != ''): ?>
         <td colspan="3" class="Nutrition"><div align="right"><b><?=$nutfacts['PROTP2'];?>%</b></div></td>
      <?php else: ?>
         <td colspan="3" class="Nutrition">&nbsp;</td>
      <?php endif; ?>
      </tr>
   <?php endif; ?>
   
   <?=draw_line_wide(array('width'=>'8', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'wideline'));?>

<!-- end section 3 -->

<!-- section 4 -->

   <!-- Vitamin A -->
   <?php if ($nutfacts['VITAP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Vitamin A</td>
      <td class="Nutrition right"><?=$nutfacts['VITAP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['VITAP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Vitamin C -->
   <?php if ($nutfacts['VITCP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Vitamin C</td>
      <td class="Nutrition right"><?=$nutfacts['VITCP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['VITCP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Calcium -->
   <?php if ($nutfacts['CALCP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Calcium</td>
      <td class="Nutrition right"><?=$nutfacts['CALCP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['CALCP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Iron -->
   <?php if ($nutfacts['IRONP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Iron</td>
      <td class="Nutrition right"><?=$nutfacts['IRONP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['IRONP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Vitamin D -->
   <?php if ($nutfacts['VITDP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Vitamin D</td>
      <td class="Nutrition right"><?=$nutfacts['VITDP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['VITDP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Vitamin E -->
   <?php if ($nutfacts['VITEP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Vitamin E</td>
      <td class="Nutrition right"><?=$nutfacts['VITEP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['VITEP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Vitamin B6 -->
   <?php if ($nutfacts['VITB6P'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Vitamin B6</td>
      <td class="Nutrition right"><?=$nutfacts['VITB6P'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['VITB6P2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Vitamin B12 -->
   <?php if ($nutfacts['VITB12P'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Vitamin B12</td>
      <td class="Nutrition right"><?=$nutfacts['VITB12P'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['VITB12P2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Thiamin -->
   <?php if ($nutfacts['THIAP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Thiamin</td>
      <td class="Nutrition right"><?=$nutfacts['THIAP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['THIAP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Riboflavin -->
   <?php if ($nutfacts['RIBOP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Riboflavin</td>
      <td class="Nutrition right"><?=$nutfacts['RIBOP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['RIBOP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Phosphorous -->
   <?php if ($nutfacts['PHOSP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Phosphorous</td>
      <td class="Nutrition right"><?=$nutfacts['PHOSP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['PHOSP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Magnesium -->
   <?php if ($nutfacts['MAGNP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Magnesium</td>
      <td class="Nutrition right"><?=$nutfacts['MAGNP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['MAGNP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <!-- Niacin -->
   <?php if ($nutfacts['NIACP'] != ''): ?>
      <tr>
      <td colspan="5" class="Nutrition">Niacin</td>
      <td class="Nutrition right"><?=$nutfacts['NIACP'];?>%</td>
      <td colspan="3" class="Nutrition right"><?=$nutfacts['NIACP2'];?>%</td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>


<!-- end section 4 -->

<!-- section 5 -->

   <?php if ($nutfacts['STMT1Q'] != ""): ?>
      <tr>
      <td colspan="9" class="Nutrition"><?=$nutfacts['STMT1Q'];?></td>
      </tr>
      <?=draw_line_wide(array('width'=>'1', 'indent'=>'no', 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>
      <tr>
      <td colspan="9" class="Nutrition">* <?=$nutfacts['STMT2Q'];?></td>
      </tr>
      <tr>
      <td colspan="9" class="Nutrition"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="2" height="1" alt="" /></td>
      </tr>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDV1']) == "YES"): ?>   
      <tr>
      <td colspan="9" class="Nutrition"><?php if (strtoupper($nutfacts.STMT2) == "YES"): ?>**<?php else: ?>*<?php endif; ?> Percent Daily Values are based on a 2,000 calorie diet.</td>
      </tr>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDV2']) == "YES"): ?>
      <tr>
      <td colspan="9" class="Nutrition"><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?> Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.</td>
      </tr>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDVT']) == "YES"): ?>

      <tr>
      <td colspan="9"><img src="http://resources.hcgweb.net/shared/dot_black.gif" width="232" height="1" alt="" /></td>
      </tr>
   
      <tr>
      <td colspan="2" class="NutritionSm">&nbsp;</td>
      <td colspan="3" class="NutritionSm">Calories:</td>
      <td colspan="2" class="NutritionSm">2,000</td>
      <td colspan="2" class="NutritionSm">2,500</td>
      </tr>
   
      <tr>
      <td colspan="9"><img src="http://resources.hcgweb.net/shared/dot_black.gif" width="232" height="1" alt="" /></td>
      </tr>
   
      <tr>
      <td colspan="2" class="NutritionSm">Total Fat</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td colspan="2" class="NutritionSm">65g</td>
      <td colspan="2" class="NutritionSm">80g</td>
      </tr>
   
      <tr>
      <td class="NutritionSm">&nbsp;</td>
      <td class="NutritionSm">Sat Fat</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td colspan="2" class="NutritionSm">20g</td>
      <td colspan="2" class="NutritionSm">25g</td>
      </tr>
   
      <tr>
      <td colspan="2" class="NutritionSm">Cholesterol</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td colspan="2" class="NutritionSm">300mg</td>
      <td colspan="2" class="NutritionSm">300mg</td>
      </tr>
   
      <tr>
      <td colspan="2" class="NutritionSm">Sodium</td>
      <td colspan="3" class="NutritionSm">Less than</td>
      <td colspan="2" class="NutritionSm">2,400mg</td>
      <td colspan="2" class="NutritionSm">2,400mg</td>
      </tr>
   
      <tr>
      <td colspan="5" class="NutritionSm">Total Carbohydrate</td>
      <td colspan="2" class="NutritionSm">300g</td>
      <td colspan="2" class="NutritionSm">375g</td>
      </tr>
   
      <tr>
      <td class="NutritionSm">&nbsp;</td>
      <td colspan="4" class="NutritionSm">Dietary Fiber</td>
      <td colspan="2" class="NutritionSm">25g</td>
      <td colspan="2" class="NutritionSm">30g</td>
      </tr>
   <?php endif; ?>
   
<!-- end section 5 -->

<!-- table footer -->

   <tr>
   <td colspan="9"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="232" height="6" alt="" /></td>
   </tr>
   
   </table>
   
</td></tr>
</table>

<p>&nbsp;</p>
The most accurate information is always on the label on the actual product. We periodically update our labels based on new nutritional analysis to verify natural variations from crop to crop and at times formula revisions. The web-site does not necessarily get updated at the same time. The values on the website are intended to be a general guide to consumers. For absolute values, the actual label on the product at hand should be relied on.

<!-- end table footer -->

</div>
