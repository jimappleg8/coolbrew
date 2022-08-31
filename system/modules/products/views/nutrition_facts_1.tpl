<!-- page header 1 -->

<center>

{if $nutfacts.display_hd == true }
<table width="246" cellpadding="1" cellspacing="0" border="0" bgcolor="#FFFFFF">
<tr>
<td><span class="productSbhd">{$nutfacts.ProductName}</span></td>
</tr>
</table>
{/if}

<!-- end page header -->

<!-- table header -->

<table width="246" cellpadding="1" cellspacing="0" border="0" bgcolor="#000000">
<tr><td>

   <table width="100%" cellpadding="1" cellspacing="0" border="0" bgcolor="#FFFFFF" class="Nutrition">
   
   <tr> <!-- row that establishes grid -->
   <td width="5"><img src="/images/dot_clear.gif" width="3" height="1" alt=""></td>
   <td width="13"><img src="/images/dot_clear.gif" width="11" height="1" alt=""></td>
   <td width="59"><img src="/images/dot_clear.gif" width="57" height="1" alt=""></td>
   <td width="31"><img src="/images/dot_clear.gif" width="29" height="1" alt=""></td>
   <td width="27"><img src="/images/dot_clear.gif" width="25" height="1" alt=""></td>
   <td width="4"><img src="/images/dot_clear.gif" width="2" height="1" alt=""></td>
   <td width="44"><img src="/images/dot_clear.gif" width="42" height="1" alt=""></td>
   <td width="10"><img src="/images/dot_clear.gif" width="8" height="1" alt=""></td>
   <td width="8"><img src="/images/dot_clear.gif" width="6" height="1" alt=""></td>
   <td width="38"><img src="/images/dot_clear.gif" width="36" height="1" alt=""></td>
   <td width="5"><img src="/images/dot_clear.gif" width="3" height="1" alt=""></td>   
   </tr>
   
   <tr> <!-- row 1: establishes the top and side margins -->
   <td rowspan="{$nutfacts.total_rows}" width="4">&nbsp;</td>
   <td colspan="9"><img src="/images/dot_clear.gif" width="232" height="2" alt=""></td>
   <td rowspan="{$nutfacts.total_rows}" width="4">&nbsp;</td>
   </tr>

<!-- end table header -->

<!-- section 1 -->

   <tr>
   <td colspan="9" class="NutritionHd"><b>Nutrition Facts</b></td>
   </tr>
   
   <tr>
   <td colspan="8" class="Nutrition">
   Serving Size: {$nutfacts.SSIZE|default:"???"}</td>
   </tr>

   {if ($nutfacts.MAKE != "") }
      <tr>
      <td colspan="8" class="Nutrition">Makes: {$nutfacts.MAKE}</td>
      </tr>
   {/if}

   <tr>
   <td colspan="8" class="Nutrition">
   Servings Per Container: {$nutfacts.SERV|default:"???"}</td>
   </tr>

<!-- end section 1 -->

<!-- section 2 -->

   {draw_line_wide width="8" indent="no"}
   
   <!-- Amount Per Serving -->
   <tr>
   <td colspan="5" class="NutritionSm" valign="bottom"><b>Amount Per Serving</b></td>
   <td class="NutritionSm" valign="bottom"><div align="right"><b>{$nutfacts.COL1HD}</b></div></td>
   <td colspan="3" class="NutritionSm" valign="bottom"><div align="right"><b>{$nutfacts.COL2HD}</b></div></td>
   </tr>

   {draw_line_wide width="1" indent="no"}
   
   <!-- Calories -->
   <tr>
   <td colspan="5" class="Nutrition"><b>Calories</b></td>
   <td class="Nutrition"><div align="right">{$nutfacts.CAL|default:"???"}</div></td>
   <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2CAL|default:"???"}</div></td>
   </tr>
   
   <!-- Fat from Calories - indented -->
   {if $nutfacts.FATCAL != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Calories from Fat</td>
      <td class="Nutrition"><div align="right">{$nutfacts.FATCAL}</div></td>
      {if $nutfacts.2FATCAL != "" }
         <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2FATCAL}</div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   {draw_line_wide width="4" indent="no"}
   
   <tr>
   <td colspan="9" class="NutritionSm"><div align="right"><b>% Daily Value{if strtoupper($nutfacts.STMT2) == "YES"}**{else}*{/if}</b></div></td>
   </tr>


<!-- end section 2 -->

<!-- section 3 -->

   <!-- Total Fat -->
   {if $nutfacts.TFATQ != "" }
      {draw_line_wide width="1" indent="no"}
      <tr>
      <td colspan="5" class="Nutrition"><b>Total Fat</b> {$nutfacts.TFATQ}{if strtoupper($nutfacts.STMT2) == "YES"}*{/if}</td>
      {if $nutfacts.TFATP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.TFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2TFATP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2TFATP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Saturated Fat - indented -->
   {if $nutfacts.SFATQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Saturated Fat {$nutfacts.SFATQ}</td>
      {if $nutfacts.SFATP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.SFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2SFATP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2SFATP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Trans Fat - indented -->
   {if $nutfacts.HFATQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="7" class="Nutrition"><i>Trans</i> Fat {$nutfacts.HFATQ}</td>
      {if $nutfacts.HFATP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.HFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Polyunsaturated Fat - indented -->
   {if $nutfacts.PFATQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="7" class="Nutrition">Polyunsaturated Fat {$nutfacts.PFATQ}</td>
      {if $nutfacts.PFATP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.PFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Monounsaturated Fat - indented -->
   {if $nutfacts.MFATQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="7" class="Nutrition">Monounsaturated Fat {$nutfacts.MFATQ}</td>
      {if $nutfacts.MFATP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.MFATP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Cholesterol -->
   {if $nutfacts.CHOLQ != "" }
      {draw_line_wide width="1" indent="no"}
      <tr>
      <td colspan="5" class="Nutrition"><b>Cholesterol</b> {$nutfacts.CHOLQ}</td>
      {if $nutfacts.CHOLP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.CHOLP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2CHOLP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2CHOLP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Sodium -->
   {if $nutfacts.SODQ != "" }
      {draw_line_wide width="1" indent="no"}
      <tr>
      <td colspan="5" class="Nutrition"><b>Sodium</b> {$nutfacts.SODQ}</td>
      {if $nutfacts.SODP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.SODP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2SODP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2SODP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Potassium -->
   {if $nutfacts.POTQ != "" }
      {draw_line_wide width="1" indent="no"}
      <tr>
      <td colspan="5" class="Nutrition"><b>Potassium</b> {$nutfacts.POTQ}</td>
      {if $nutfacts.POTP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.POTP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2POTP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2POTP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Total Carbs -->
   {if $nutfacts.TCARBQ != "" }
      {draw_line_wide width="1" indent="no"}
      <tr>
      <td colspan="5" class="Nutrition"><b>Total Carb.</b> {$nutfacts.TCARBQ}</td>
      {if $nutfacts.TCARBP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.TCARBP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2TCARBP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2TCARBP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Dietary Fiber - indented -->
   {if $nutfacts.DFIBQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Dietary Fiber {$nutfacts.DFIBQ}</td>
      {if $nutfacts.DFIBP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.DFIBP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2DFIBP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2DFIBP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Sugars - indented -->
   {if $nutfacts.SUGQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Sugars {$nutfacts.SUGQ}</td>
      {if $nutfacts.SUGP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.SUGP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2SUGP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2SUGP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}

   <!-- Other Carbohydrate - indented -->
   {if $nutfacts.OCARBQ != "" }
      {draw_line_wide width="1" indent="yes"}
      <tr>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="4" class="Nutrition">Other Carb. {$nutfacts.OCARBQ}</td>
      <td class="Nutrition">&nbsp;</td>
      <td colspan="3" class="Nutrition">&nbsp;</td>
      </tr>
   {/if}

   <!-- Protein -->
   {if $nutfacts.PROTQ != "" }
      {draw_line_wide width="1" indent="no"}
      <tr>
      <td colspan="5" class="Nutrition"><b>Protein</b> {$nutfacts.PROTQ}</td>
      {if $nutfacts.PROTP != "" }
         <td class="Nutrition"><div align="right"><b>{$nutfacts.PROTP}%</b></div></td>
      {else}
         <td class="Nutrition">&nbsp;</td>
      {/if}
      {if $nutfacts.2PROTP != "" }
         <td colspan="3" class="Nutrition"><div align="right"><b>{$nutfacts.2PROTP}%</b></div></td>
      {else}
         <td colspan="3" class="Nutrition">&nbsp;</td>
      {/if}
      </tr>
   {/if}
   
   {draw_line_wide width="8" indent="no"}

<!-- end section 3 -->

<!-- section 4 -->

   <!-- Vitamin A -->
   {if $nutfacts.VITAP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Vitamin A</td>
      <td class="Nutrition"><div align="right">{$nutfacts.VITAP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2VITAP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Vitamin C -->
   {if $nutfacts.VITCP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Vitamin C</td>
      <td class="Nutrition"><div align="right">{$nutfacts.VITCP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2VITCP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Calcium -->
   {if $nutfacts.CALCP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Calcium</td>
      <td class="Nutrition"><div align="right">{$nutfacts.CALCP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2CALCP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Iron -->
   {if $nutfacts.IRONP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Iron</td>
      <td class="Nutrition"><div align="right">{$nutfacts.IRONP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2IRONP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Vitamin D -->
   {if $nutfacts.VITDP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Vitamin D</td>
      <td class="Nutrition"><div align="right">{$nutfacts.VITDP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2VITDP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Vitamin E -->
   {if $nutfacts.VITEP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Vitamin E</td>
      <td class="Nutrition"><div align="right">{$nutfacts.VITEP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2VITEP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Vitamin B6 -->
   {if $nutfacts.VITB6P != "" }
      <tr>
      <td colspan="5" class="Nutrition">Vitamin B6</td>
      <td class="Nutrition"><div align="right">{$nutfacts.VITB6P}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2VITB6P}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Vitamin B12 -->
   {if $nutfacts.VITB12P != "" }
      <tr>
      <td colspan="5" class="Nutrition">Vitamin B12</td>
      <td class="Nutrition"><div align="right">{$nutfacts.VITB12P}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2VITB12P}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Thiamin -->
   {if $nutfacts.THIAP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Thiamin</td>
      <td class="Nutrition"><div align="right">{$nutfacts.THIAP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2THIAP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Riboflavin -->
   {if $nutfacts.RIBOP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Riboflavin</td>
      <td class="Nutrition"><div align="right">{$nutfacts.RIBOP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2RIBOP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Phosphorous -->
   {if $nutfacts.PHOSP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Phosphorous</td>
      <td class="Nutrition"><div align="right">{$nutfacts.PHOSP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2PHOSP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Magnesium -->
   {if $nutfacts.MAGNP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Magnesium</td>
      <td class="Nutrition"><div align="right">{$nutfacts.MAGNP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2MAGNP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   <!-- Niacin -->
   {if $nutfacts.NIACP != "" }
      <tr>
      <td colspan="5" class="Nutrition">Niacin</td>
      <td class="Nutrition"><div align="right">{$nutfacts.NIACP}%</div></td>
      <td colspan="3" class="Nutrition"><div align="right">{$nutfacts.2NIACP}%</div></td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}


<!-- end section 4 -->

<!-- section 5 -->

   {if ($nutfacts.STMT1Q != "") }
      <tr>
      <td colspan="9" class="Nutrition">{$nutfacts.STMT1Q}</td>
      </tr>
      {draw_line_wide width="1" indent="no"}
   {/if}

   {if strtoupper($nutfacts.STMT2) == "YES"}
      <tr>
      <td colspan="9" class="Nutrition">* {$nutfacts.STMT2Q}</td>
      </tr>
      <tr>
      <td colspan="9" class="Nutrition"><img src="/images/dot_clear.gif" width="2" height="1" alt=""></td>
      </tr>
   {/if}

   {if (strtoupper($nutfacts.PDV1) == "YES") }   
      <tr>
      <td colspan="9" class="Nutrition">{if strtoupper($nutfacts.STMT2) == "YES"}**{else}*{/if} Percent Daily Values are based on a 2,000 calorie diet.</td>
      </tr>
   {/if}

   {if (strtoupper($nutfacts.PDV2) == "YES") }
      <tr>
      <td colspan="9" class="Nutrition">{if strtoupper($nutfacts.STMT2) == "YES"}**{else}*{/if} Percent Daily Values are based on a 2,000 calorie diet. Your daily values may be higher or lower depending on your calorie needs.</td>
      </tr>
   {/if}

   {if (strtoupper($nutfacts.PDVT) == "YES") }

      <tr>
      <td colspan="9"><img src="/images/dot_black.gif" width="232" height="1" alt=""></td>
      </tr>
   
      <tr>
      <td colspan="2" class="NutritionSm">&nbsp;</td>
      <td colspan="3" class="NutritionSm">Calories:</td>
      <td colspan="2" class="NutritionSm">2,000</td>
      <td colspan="2" class="NutritionSm">2,500</td>
      </tr>
   
      <tr>
      <td colspan="9"><img src="/images/dot_black.gif" width="232" height="1" alt=""></td>
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
   {/if}
   
<!-- end section 5 -->

<!-- table footer -->

   <tr>
   <td colspan="9"><img src="/images/dot_clear.gif" width="232" height="6" alt=""></td>
   </tr>
   
   </table>
   
</td></tr>
</table>

<p>&nbsp;</p>
The most accurate information is always on the label on the actual product. We periodically update our labels based on new nutritional analysis to verify natural variations from crop to crop and at times formula revisions. The web-site does not necessarily get updated at the same time. The values on the website are intended to be a general guide to consumers. For absolute values, the actual label on the product at hand should be relied on.

<!-- end table footer -->
