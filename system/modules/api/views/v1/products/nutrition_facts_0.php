<!-- this version is xhtml strict compatible -->


<style type="text/css">

#nutfacts {
  width: 246px;
  font: normal 11px/15px Arial, sans-serif;
}

#nutfacts-wrap {
  padding: 2px 5px;
  border: 1px solid #000;
  background: #fff;
}

#nutfacts-wrap table {
  margin-bottom: 0;
}

#nutfacts td div,
#nutfacts .diet td {
  padding: 2px 0;
}

#nutfacts table.vt td {
  white-space: nowrap;
}

#nutfacts .diet td.in {
  padding-left: 10px;
}

#nutfacts .b8 div {
  border-top: 8px solid #000;
}

#nutfacts .b1 div,
#nutfacts .diet td.b1 {
  border-top: 1px solid #000;
}

#nutfacts .b4 div {
  border-top: 4px solid #000;
}

#nutfacts .in div {
  margin-left: 10px;
}

#nutfacts .rt {
  text-align: right;
}

#nutfacts .ct {
  padding-left: 6px;
  padding-right: 6px;
  text-align: center;
}

#nutfacts .sm {
  font-size: 9px;
}

#nutfacts .header p {
  margin: 0;
  padding: 0;
}

#nutfacts .header h1 {
  margin: 0;
  padding: 3px 0;
  font-size: 18px;
}

#nutfacts .disclaimer {
  font-size: 10px;
  line-height: 13px;
}

#nutfacts hr {
  margin: 0;
  padding: 0;
  border: none;
  border-top: 1px solid #000;
  height: 0;
  background: none;
}

</style>


<!-- page header 0 -->

<div id="nutfacts" class="us-normal">

<?php if ($nutfacts['display_hd'] == true): ?>
<p class="productSbhd"><?=$nutfacts['ProductName'];?></p>
<?php endif; ?>

<!-- end page header -->

<div id="nutfacts-wrap">
<table cellpadding="0" cellspacing="0" border="0">
   
<!-- section 1 -->

  <tr>
  <td colspan="2" class="header"><div>
  <h1>Nutrition Facts</h1>
  <p>Serving Size: <?=set_default($nutfacts['SSIZE'], "???");?></p>
  <?php if ($nutfacts['MAKE'] != ""): ?>
    <p>Makes: <?=$nutfacts['MAKE'];?></p>
  <?php endif; ?>
  <p>Servings Per Container: <?=set_default($nutfacts['SERV'], "???");?></p>
  </div></td>
  </tr>

<!-- end section 1 -->


<!-- section 2 -->

  <!-- Amount Per Serving -->
  <tr>
  <td colspan="2" class="b8 sm"><div><b>Amount Per Serving</b></div></td>
  </tr>
                                  
  <!-- Calories and Fat from Calories -->
  <tr>
    <?php if ($nutfacts['FATCAL'] != '') $fatcal = true; ?>
    <td class="b1"><div><b>Calories</b> <?=set_default($nutfacts['CAL'], "???");?></div></td>
    <td class="b1 rt"><div><?= ($nutfacts['FATCAL'] != '') ? 'Calories from Fat '.$nutfacts['FATCAL'] : '&nbsp'; ?></div></td>
  </tr>

  <tr>
  <td colspan="2" class="b4 sm rt"><div><b>% Daily Value<?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?></b></div></td>
  </tr>

<!-- end section 2 -->

<!-- section 3 -->

<?php if ($nutfacts['TFATQ'] != ''): ?>
  <!-- Total Fat -->
  <tr>
    <td class="b1"><div><b>Total Fat</b> <?=$nutfacts['TFATQ'];?><?= (strtoupper($nutfacts['STMT2']) == 'YES') ? '*' : ''; ?></div></td>
    <td class="b1 rt"><div><b><?= ($nutfacts['TFATP'] != '') ? $nutfacts['TFATP'].'%' : '&nbsp'; ?></b></div></td>
  </tr>
<?php endif; ?>

    <?php if ($nutfacts['SFATQ'] != ''): ?>
      <!-- Saturated Fat - indented -->
      <tr>
        <td class="b1 in"><div>Saturated Fat <?=$nutfacts['SFATQ'];?></div></td>
        <td class="b1 rt"><div><b><?= ($nutfacts['SFATP'] != '') ? $nutfacts['SFATP'].'%' : '&nbsp'; ?></b></div></td>
      </tr>
    <?php endif; ?>
    
    <?php if ($nutfacts['HFATQ'] != ''): ?>
      <!-- Trans (Hydrogenated) Fat - indented -->
      <tr>
        <td colspan="2" class="b1 in"><div><i>Trans</i> Fat <?=$nutfacts['HFATQ'];?></div></td>
      </tr>
    <?php endif; ?>

    <?php if ($nutfacts['PFATQ'] != ''): ?>
      <!-- Polyunsaturated Fat - indented -->
      <tr>
        <td colspan="2" class="b1 in"><div>Polyunsaturated Fat <?=$nutfacts['PFATQ'];?></div></td>
      </tr>
    <?php endif; ?>

    <?php if ($nutfacts['MFATQ'] != ''): ?>
      <!-- Monounsaturated Fat - indented -->
      <tr>
        <td colspan="2" class="b1 in"><div>Monounsaturated Fat <?=$nutfacts['MFATQ'];?></div></td>
      </tr>
    <?php endif; ?>


<?php if ($nutfacts['CHOLQ'] != ''): ?>
  <!-- Cholesterol -->
  <tr>
    <td class="b1"><div><b>Cholesterol</b> <?=$nutfacts['CHOLQ'];?></div></td>
    <td class="b1 rt"><div><b><?= ($nutfacts['CHOLP'] != '') ? $nutfacts['CHOLP'].'%' : '&nbsp'; ?></b></div></td>
  </tr>
<?php endif; ?>


<?php if ($nutfacts['SODQ'] != ''): ?>
  <!-- Sodium -->
  <tr>
    <td class="b1"><div><b>Sodium</b> <?=$nutfacts['SODQ'];?></div></td>
    <td class="b1 rt"><div><b><?= ($nutfacts['SODP'] != '') ? $nutfacts['SODP'].'%' : '&nbsp'; ?></b></div></td>
  </tr>
<?php endif; ?>


<?php if ($nutfacts['POTQ'] != ''): ?>
  <!-- Potassium -->
  <tr>
    <td class="b1"><div><b>Potassium</b> <?=$nutfacts['POTQ'];?></div></td>
    <td class="b1 rt"><div><b><?= ($nutfacts['POTP'] != '') ? $nutfacts['POTP'].'%' : '&nbsp'; ?></b></div></td>
  </tr>
<?php endif; ?>


<?php if ($nutfacts['TCARBQ'] != ''): ?>
  <!-- Total Carb. -->
  <tr>
    <td class="b1"><div><b>Total Carb.</b> <?=$nutfacts['TCARBQ'];?></div></td>
    <td class="b1 rt"><div><b><?= ($nutfacts['TCARBP'] != '') ? $nutfacts['TCARBP'].'%' : '&nbsp'; ?></b></div></td>
  </tr>
<?php endif; ?>

    <?php if ($nutfacts['DFIBQ'] != ''): ?>
      <!-- Dietary Fiber - indented -->
      <tr>
        <td class="b1 in"><div>Dietary Fiber <?=$nutfacts['DFIBQ'];?></div></td>
        <td class="b1 rt"><div><b><?= ($nutfacts['DFIBP'] != '') ? $nutfacts['DFIBP'].'%' : '&nbsp'; ?></b></div></td>
      </tr>
    <?php endif; ?>
    
    <?php if ($nutfacts['SUGQ'] != ''): ?>
      <!-- Sugars - indented -->
      <tr>
        <td colspan="2" class="b1 in"><div>Sugars <?=$nutfacts['SUGQ'];?></div></td>
      </tr>
    <?php endif; ?>


<?php if ($nutfacts['PROTQ'] != ''): ?>
  <!-- Protein -->
  <tr>
    <td class="b1"><div><b>Protein</b> <?=$nutfacts['PROTQ'];?></div></td>
    <td class="b1 rt"><div><b><?= ($nutfacts['PROTP'] != '') ? $nutfacts['PROTP'].'%' : '&nbsp'; ?></b></div></td>
  </tr>
<?php endif; ?>


<!-- end section 3 -->

<!-- section 4 -->

<?php

  $vitTitle = array(
    'Vitamin A',
    'Vitamin C',
    'Calcium',
    'Iron',
    'Vitamin D',
    'Vitamin E',
    'Vitamin K',
    'Thiamin',
    'Riboflavin',
    'Niacin',
    'Vitamin B6',
    'Folic Acid',
    'Folate',
    'Chloride',
    'Vitamin B12',
    'Biotin',
    'Pantothenic Acid',
    'Phosphorous',
    'Iodine',
    'Magnesium',
    'Zinc',
    'Selenium',
    'Copper',
    'Manganese',
    'Chromium',
    'Molybdenum',
  );
  
  $vitAbbrev = array(
    'VITAP',
    'VITCP',
    'CALCP',
    'IRONP',
    'VITDP',
    'VITEP',
    'VITKP',
    'THIAP',
    'RIBOP',
    'NIACP',
    'VITB6P',
    'FOLICP',
    'FOLATEP',
    'CHLORP',
    'VITB12P',
    'BIOTINP',
    'PACIDP',
    'PHOSP',
    'IODIP',
    'MAGNP',
    'ZINCP',
    'SELEP',
    'COPPP',
    'MANGP',
    'CHROMP',
    'MOLYP',
  );

?>

<tr>  
<td colspan="2" class="b8"><div>

<?php
  
  $ctV = 0;
  $vitArray = array();
  
  // populate an array so we can see if there are any vitamins to list
  for ($i = 0; $i < count($vitTitle); $i++)
  {
    if ($nutfacts[$vitAbbrev[$i]] != '')
    {
      $vitArray[$ctV]['Title'] = $vitTitle[$i];
      $vitArray[$ctV]['Percent'] = $nutfacts[$vitAbbrev[$i]];
      $ctV++;
    }
  }

  if ( ! empty($vitArray))
  {
    echo '<table width="100%" cellpadding="0" cellspacing="0" border="0" class="vt">';
    if (count($vitArray) == 1)
    {
      echo '<tr><td>'.$vitArray[0]['Title'].'</td><td class="rt">'.$vitArray[0]['Percent'].'%</td></tr>';
    }
    else
    {
      for ($i = 0; $i < count($vitArray); $i++)
      {
        if ( ! ($i % 2))
        {
          echo '<tr><td>'.$vitArray[$i]['Title'].'</td><td class="rt">'.$vitArray[$i]['Percent'].'%</td>';
        }
        else
        {
          echo '<td class="ct">&#8226;</td><td>'.$vitArray[$i]['Title'].'</td><td class="rt">'.$vitArray[$i]['Percent'].'%</td></tr>';
        }
      }
      if ($ctV % 2)
      {
        echo '<td class="ct">&#8226;</td><td>&nbsp;</td><td class="rt">&nbsp;</td></tr>';
      }
    }
    echo '</table>';
  }
?>

</div></td>
</tr>

<!-- end section 4 -->

<!-- section 5 -->


<tr>
<td colspan="2">

   <?php if ($nutfacts['STMT1Q'] != ""): ?>
      <div class="b1"><?=$nutfacts['STMT1Q'];?></div>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>
      <div class="b1">* <?=$nutfacts['STMT2Q'];?></div>
   <?php endif; ?>

   <?php if (((strtoupper($nutfacts['PDV1']) == "YES") || (strtoupper($nutfacts['PDV2']) == "YES")) && ! (empty($vitArray))): ?>
      <hr />
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDV1']) == "YES"): ?>
      <div><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?> Percent Daily Values are based on a 2,000 calorie diet.</div>
   <?php endif; ?>

   <?php if (strtoupper($nutfacts['PDV2']) == "YES"): ?>
      <div><?php if (strtoupper($nutfacts['STMT2']) == "YES"): ?>**<?php else: ?>*<?php endif; ?> Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.</div>
   <?php endif; ?>

<!-- section 5.5 -->

   <?php if (strtoupper($nutfacts['PDVT']) == "YES"): ?>
  <hr />
  <table cellpadding="0" cellspacing="0" class="diet sm" style="border:0; width:100%;">
      <tr>
      <td>&nbsp;</td>
      <td>Calories:</td>
      <td>2,000</td>
      <td>2,500</td>
      </tr>


      <tr>
      <td class="b1">Total Fat</td>
      <td class="b1">Less than</td>
      <td class="b1">65g</td>
      <td class="b1">80g</td>
      </tr>

      <tr>
      <td class="in">Sat Fat</td>
      <td>Less than</td>
      <td>20g</td>
      <td>25g</td>
      </tr>

      <tr>
      <td>Cholesterol</td>
      <td>Less than</td>
      <td>300mg</td>
      <td>300mg</td>
      </tr>

      <tr>
      <td>Sodium</td>
      <td>Less than</td>
      <td>2,400mg</td>
      <td>2,400mg</td>
      </tr>

      <tr>
      <td colspan="2">Total Carbohydrate</td>
      <td>300g</td>
      <td>375g</td>
      </tr>

      <tr>
      <td colspan="2" class="in">Dietary Fiber</td>
      <td>25g</td>
      <td>30g</td>
      </tr>
  </table>
   <?php endif; ?>

</td>
</tr>



<!-- end section 5 -->


   
</td></tr>
</table>
</div>

<p class="disclaimer">The most accurate information is always on the label on the actual product. We periodically update our labels based on new nutritional analysis to verify natural variations from crop to crop and at times formula revisions. The website does not necessarily get updated at the same time. The values on the website are intended to be a general guide to consumers. For absolute values, the actual label on the product at hand should be relied on.</p>


</div>
