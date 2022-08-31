<?php if (count($category_list) > 0): ?>

   <?php foreach($category_list AS $category): ?>
      <h2><?=$category['Name'];?></h2>
      <ul>
      <?php foreach($faq_list AS $faq): ?>
         <?php if ($faq['CategoryID'] == $category['ID']): ?>
      <li><a href="#faqid<?=$faq['ID'];?>"><?=nl2br($faq['ShortQuestion']);?></a><?php if ($faq['FlagAsNew'] == 1): ?>&nbsp;&nbsp;<span style="color:red;">NEW!</span><?php endif; ?></li>
         <?php endif; ?>
      <?php endforeach; ?>
      </ul>
   <?php endforeach; ?>

   <?php foreach($category_list AS $category): ?>
      <h2><?=$category['Name'];?></h2>
      <?php foreach($faq_list AS $faq): ?>
         <?php if ($faq['CategoryID'] == $category['ID']): ?>
   <a name="faqid<?=$faq['ID']?>"></a>
   <p>
   <?php if ($faq['FlagAsNew'] == 1): ?>
      <span style="color:red;">NEW!</span>&nbsp;&nbsp;
   <?php endif; ?>
   <b><?=$faq['Question'];?></b></p>
   <?=$faq['Answer'];?>
         <?php endif; ?>
      <?php endforeach; ?>
   <?php endforeach; ?>


<?php else: ?>

   <ul>
   <li>There are no FAQs at this time.</li>
   </ul>
   
<?php endif; ?>
