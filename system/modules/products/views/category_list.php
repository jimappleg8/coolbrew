<table width="548" cellpadding="0" cellspacing="0" border="0">

<tr>
<td colspan="8">

<h1><?=$category['CategoryName'];?></h1>

<?=$category['CategoryText'];?>

</td>
</tr>

<tr>
<?php $iteration = 0; ?>
<?php foreach($items AS $catitem): ?>

   <?php 
   if ( ! file_exists(DOCPATH.'/images/products/' . $category['CategoryCode'] . '/' . $catitem['ThumbFile']))
   {
      $thumb_file = "fpo-product-category-thumbnail-80x80.gif";
   }
   else
   {
      $thumb_file = $category['CategoryCode'].'/'.$catitem['ThumbFile'];
   }
   ?>

   <?php $iteration++; ?>
   <?php if ($iteration == 5): ?>
   </tr>
   <tr>
      <?php $iteration = 1; ?>
   <?php endif; ?>
   <td valign="top"><div style="text-align:center;"><a href="/modules/products/detail.php/<?=$category['CategoryCode'];?>/<?=$catitem['SESFilename'];?>"><img src="/images/products/<?=$thumb_file;?>" width="<?=$catitem['ThumbWidth'];?>" height="<?=$catitem['ThumbHeight'];?>" alt="<?=$catitem['ThumbFile'];?>" border="0"></a></div>
   <div class="smallnames"><a href="/modules/products/detail.php/<?=$category['CategoryCode'];?>/<?=$catitem['SESFilename'];?>"><?=$catitem['ProductName'];?></a></div></td>
   <td>&nbsp;</td>

<?php endforeach; ?>

<?php if ($iteration < 4): ?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif; ?>
<?php if ($iteration < 3): ?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif; ?>
<?php if ($iteration < 2): ?>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php endif; ?>


</tr>

<tr>
<td width="117" class="noLineHeight"><img src="/images/spacer.gif" width="117" height="1" alt="" /></td>
<td width="20" class="noLineHeight"><img src="/images/spacer.gif" width="20" height="1" alt="" /></td>
<td width="117" class="noLineHeight"><img src="/images/spacer.gif" width="117" height="1" alt="" /></td>
<td width="20" class="noLineHeight"><img src="/images/spacer.gif" width="20" height="1" alt="" /></td>
<td width="117" class="noLineHeight"><img src="/images/spacer.gif" width="117" height="1" alt="" /></td>
<td width="20" class="noLineHeight"><img src="/images/spacer.gif" width="20" height="1" alt="" /></td>
<td width="117" class="noLineHeight"><img src="/images/spacer.gif" width="117" height="1" alt="" /></td>
<td width="20" class="noLineHeight"><img src="/images/spacer.gif" width="20" height="1" alt="" /></td>
</tr>

</table>