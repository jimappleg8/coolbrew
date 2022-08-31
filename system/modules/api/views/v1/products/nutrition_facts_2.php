<!-- this version is xhtml strict compatible -->

<!-- page header 2 -->

<div id="nutfacts" class="us-baby-food">

<?php if ($nutfacts['display_hd'] == true): ?>
<table width="182" cellpadding="1" cellspacing="0" border="0" bgcolor="#FFFFFF">
<tr>
<td><span class="productSbhd"><?=$nutfacts['ProductName'];?></span></td>
</tr>
</table>
<?php endif; ?>

<!-- end page header -->

<!-- table header -->

<table cellspacing="0" style="width:182px; background-color:#000;">
<tr><td>

   <table cellspacing="0" style="width:178px; background-color:#FFF;" class="Nutrition">
   
   <tr> <!-- row that establishes grid -->
   <td class="line" style="width:5px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="3" height="1" alt="" /></td>
   <td class="line" style="width:13px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="11" height="1" alt="" /></td>
   <td  class="line" style="width:119px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="117" height="1" alt="" /></td>
   <td class="line" style="width:38px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="36" height="1" alt="" /></td>
   <td class="line" style="width:5px"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="3" height="1" alt="" /></td>   
   </tr>
   
   <tr> <!-- row 1: establishes the top and side margins -->
   <td rowspan="<?=$nutfacts['total_rows'];?>" style="width:4px">&nbsp;</td>
   <td colspan="3" class="line"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="170" height="2" alt="" /></td>
   <td rowspan="<?=$nutfacts['total_rows'];?>" style="width:4px">&nbsp;</td>
   </tr>

<!-- end table header -->

<!-- section 1 -->

   <tr>
   <td colspan="3" class="NutritionHd"><b>Nutrition Facts</b></td>
   </tr>

   <tr>
   <td colspan="3" class="Nutrition">
   Serving Size: <?=set_default($nutfacts['SSIZE'], '???');?></td>
   </tr>

   <?php if ($nutfacts['MAKE'] != ""): ?>
      <tr>
      <td colspan="3" class="Nutrition">Makes: <?=$nutfacts['MAKE'];?></td>
      </tr>
   <?php endif; ?>

   <tr>
   <td colspan="3" class="Nutrition">
   Servings Per Container: <?=set_default($nutfacts['SERV'], '???');?></td>
   </tr>

<!-- end section 1 -->

<!-- section 2 -->

   <?=draw_baby_line(array('width'=>"8", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'wideline'));?>
   
   <!-- Amount Per Serving -->
   <tr>
   <td colspan="3" class="NutritionSm"><b>Amount Per Serving</b></td>
   </tr>

   <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
   
   <!-- Calories and Fat from Calories -->
   <tr>
   <td colspan="3" class="Nutrition">
   <b>Calories</b> <?=set_default($nutfacts['CAL'], "???");?></td>
   </tr>

   <?php if ($nutfacts['FATCAL'] != ""): ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="3" class="Nutrition">Calories from Fat <?=$nutfacts['FATCAL'];?></td>
      </tr>
   <?php endif; ?>

   <?=draw_baby_line(array('width'=>"4", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'wideline'));?>
   
   <tr>
   <td colspan="3" class="NutritionSm right"><b>Amount</b></td>
   </tr>

<!-- end section 2 -->

<!-- section 3 -->

   <?php if ($nutfacts['TFATQ'] != ""): ?>
      <!-- Total Fat -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition"><b>Total Fat</b></td>
      <td class="Nutrition right"><?=$nutfacts['TFATQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['SFATQ'] != ""): ?>
      <!-- Saturated Fat - indented -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"yes", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td class="Nutrition">Saturated Fat</td>
      <td class="Nutrition right"><?=$nutfacts['SFATQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['HFATQ'] != ""): ?>
      <!-- Trans (Hydrogenated) Fat - indented -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"yes", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td class="Nutrition"><i>Trans</i> Fat</td>
      <td class="Nutrition right"><?=$nutfacts['HFATQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['PFATQ'] != ""): ?>
      <!-- Polyunsaturated Fat - indented -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"yes", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td class="Nutrition">Polyunsaturated Fat</td>
      <td class="Nutrition right"><?=$nutfacts['PFATQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['MFATQ'] != ""): ?>
      <!-- Monounsaturated Fat - indented -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"yes", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td class="Nutrition">Monounsaturated Fat</td>
      <td class="Nutrition right"><?=$nutfacts['MFATQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['CHOLQ'] != ""): ?>
      <!-- Cholesterol -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition"><b>Cholesterol</b></td>
      <td class="Nutrition right"><?=$nutfacts['CHOLQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['SODQ'] != ""): ?>
      <!-- Sodium -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition"><b>Sodium</b></td>
      <td class="Nutrition right"><?=$nutfacts['SODQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['POTQ'] != ""): ?>
      <!-- Potassium -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition"><b>Potassium</b></td>
      <td class="Nutrition right"><?=$nutfacts['POTQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['TCARBQ'] != ""): ?>
      <!-- Total Carb. -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition"><b>Total Carb.</b></td>
      <td class="Nutrition right"><?=$nutfacts['TCARBQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['DFIBQ'] != ""): ?>
      <!-- Dietary Fiber - indented -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"yes", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td class="Nutrition">Dietary Fiber</td>
      <td class="Nutrition right"><?=$nutfacts['DFIBQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['SUGQ'] != ""): ?>
      <!-- Sugars - indented -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"yes", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td class="Nutrition"><b>Sugars</b></td>
      <td class="Nutrition right"><?=$nutfacts['SUGQ'];?></td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['PROTQ'] != ""): ?>
      <!-- Protein -->
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition"><b>Protein</b></td>
      <td class="Nutrition right"><?=$nutfacts['PROTQ'];?></td>
      </tr>
   <?php endif; ?>

   <?=draw_baby_line(array('width'=>"8", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'wideline'));?>

<!-- end section 3 -->

<!-- section 4 -->

   <tr>
   <td colspan="3" class="NutritionSm"><div align="right"><b>% Daily Value</b></div></td>
   </tr>
   
   <?php $section4 = FALSE; ?>

   <?php if ($nutfacts['PROTP'] != ""): ?>
   <!-- Protein -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Protein</td>
      <td class="Nutrition right"><?=$nutfacts['PROTP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['VITAP'] != ""): ?>
   <!-- Vitamin A -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Vitamin A</td>
      <td class="Nutrition right"><?=$nutfacts['VITAP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['VITCP'] != ""): ?>
   <!-- Vitamin C -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Vitamin C</td>
      <td class="Nutrition right"><?=$nutfacts['VITCP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['CALCP'] != ""): ?>
   <!-- Calcium -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Calcium</td>
      <td class="Nutrition right"><?=$nutfacts['CALCP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['IRONP'] != ""): ?>
   <!-- Iron -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Iron</td>
      <td class="Nutrition right"><?=$nutfacts['IRONP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['VITDP'] != ""): ?>
   <!-- Vitamin D -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Vitamin D</td>
      <td class="Nutrition right"><?=$nutfacts['VITDP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['VITEP'] != ""): ?>
   <!-- Vitamin E -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Vitamin E</td>
      <td class="Nutrition right"><?=$nutfacts['VITEP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['THIAP'] != ""): ?>
   <!-- Thiamin -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Thiamin</td>
      <td class="Nutrition right"><?=$nutfacts['THIAP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['RIBOP'] != ""): ?>
   <!-- Riboflavin -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Riboflavin</td>
      <td class="Nutrition right"><?=$nutfacts['RIBOP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['NIACP'] != ""): ?>
   <!-- Niacin -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Niacin</td>
      <td class="Nutrition right"><?=$nutfacts['NIACP'];?>%</td></tr>
   <?php endif; ?>   

   <?php if ($nutfacts['VITB6P'] != ""): ?>
   <!-- Vitamin B6 -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Vitamin B6</td>
      <td class="Nutrition right"><?=$nutfacts['VITB6P'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['FOLICP'] != ""): ?>
   <!-- Folic Acid -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Folic Acid</td>
      <td class="Nutrition right"><?=$nutfacts['FOLICP'];?>%</td></tr>
   <?php endif; ?>

   <?php if ($nutfacts['VITB12P'] != ""): ?>
   <!-- Vitamin B12 -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Vitamin B12</td>
      <td class="Nutrition right"><?=$nutfacts['VITB12P'];?>%</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['PHOSP'] != ""): ?>
   <!-- Phosphorous -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Phosphorous</td>
      <td class="Nutrition right"><?=$nutfacts['PHOSP'];?>%</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['MAGNP'] != ""): ?>
   <!-- Magnesium -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Magnesium</td>
      <td class="Nutrition right"><?=$nutfacts['MAGNP'];?>%</td>
      </tr>
   <?php endif; ?>

   <?php if ($nutfacts['ZINCP'] != ""): ?>
   <!-- Zinc -->
      <?php $section4 = TRUE; ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
      <tr>
      <td colspan="2" class="Nutrition">Zinc</td>
      <td class="Nutrition right"><?=$nutfacts['ZINCP'];?>%</td>
      </tr>
   <?php endif; ?>

<!-- end section 4 -->

<!-- section 5 -->

   <?php if ($section4 == TRUE): ?>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

   <?php if ($nutfacts['STMT1Q'] != ""): ?>
      <tr>
      <td colspan="3" class="Nutrition"><?=$nutfacts['STMT1Q'];?></td>
      </tr>
      <?=draw_baby_line(array('width'=>"1", 'indent'=>"no", 'xhtml'=>'yes', 'class'=>'line'));?>
   <?php endif; ?>

<!-- end section 5 -->

<!-- table footer -->

   <tr>
   <td colspan="3" class="line"><img src="http://resources.hcgweb.net/shared/dot_clear.gif" width="174" height="6" alt="" /></td>
   </tr>
   </table>
   
</td></tr>
</table>

<!-- end table footer -->

<table width="180" cellpadding="6" cellspacing="0" border="0">
<tr>
<td class="Nutrition"><p>The most accurate information is always on the label on the actual product. We periodically update our labels based on new nutritional analysis to verify natural variations from crop to crop and at times formula revisions. The website does not necessarily get updated at the same time. The values on the website are intended to be a general guide to consumers. For absolute values, the actual label on the product at hand should be relied on.</p></td>
</tr>
</table>

</div>
