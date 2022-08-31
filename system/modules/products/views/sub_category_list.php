
<?php $first = TRUE; ?>

<?php foreach ($cat_list AS $cat): ?>

   <?php if ($first == TRUE): ?>
   
      <?php $first = FALSE; ?>

<h1><?=$cat['CategoryName'];?></h1>
      <?php $mainCategoryCode = $cat['CategoryCode']; ?>
      <?php if ($cat['CategoryText'] != ""): ?>
<p><?=$cat['CategoryText'];?></p>
      <?php endif; ?>

      <?php $level = "0"; ?>
      <?php for ($i=1; $i<count($cat_list); $i++): ?>
         <?php if ($cat_list[$i]['level'] > $level): ?>
            <?php $level++; ?>
         <ul>
         <?php elseif ($cat_list[$i]['level'] < $level): ?>
            <?php $level--; ?>
         </ul>
         <?php endif; ?>
         
         <?php if ($i == count($cat_list) - 1): ?>
         </ul>
         <?php endif; ?>
      <?php endfor; ?>
    <br>

   <?php else: ?>

   
<a name="cat<?=$cat['CategoryID'];?>"></a>


<h2><?=$cat['CategoryName'];?></h2>

      <?php if ($cat['CategoryText'] != ""): ?>
<p><?=$cat['CategoryText'];?></p>
      <?php endif; ?>
      <?php if ($cat['level'] == $max_level): ?>
         <?php foreach ($items AS $item): ?>
      
            <?php if ($item['CatID'] == $cat['CategoryID']): ?>

               <?php if (file_exists(DOCPATH. '/images/products/thumb/'.$item['ThumbFile']) == FALSE): ?>
                  <?php $thumb_file = "fpo-80x80.gif"; ?>
               <?php elseif ($item['ThumbFile'] == ""): ?>
                  <?php $thumb_file = "fpo-80x80.gif"; ?>
               <?php else: ?>
                  <?php $thumb_file = $items['ThumbFile']; ?>
               <?php endif; ?>

<table style="padding:0; margin:0; width:100%; border:0px none;">

   <tr>
   <!-- left column of main area -->
   <td valign="top" align="left"><a href="/products/detail.html/<?=$mainCategoryCode;?>/<?=$item['SESFilename'];?>"><img src="/images/products/thumb/<?=$thumb_file;?>" width="<?=$item['ThumbWidth'];?>" height="<?=$item['ThumbHeight'];?>" alt="<?=$item['ThumbAlt'];?>" border="0"></a></td>
   <!-- Gutter -->
   <td valign="top">&nbsp;</td>
   <!-- right column of main area -->
   <td valign="top" align="left"><p><b><a href="/products/detail.html/<?=$mainCategoryCode;?>/<?=$item['SESFilename'];?>"><?=$item['ProductName'];?></a></b>
   <br><?=$item['LongDescription'];?></p>
</td>
</tr>

            <?php endif; ?>
</table>
         <?php endforeach; ?>

      <?php endif; ?>

   <?php endif; ?>

<?php endforeach; ?>
