<?php if (count($faqs) > 0): ?>

<?php // This template shows how a list can be generated to link to other pages where each question and answer has it's own page. ?>

   <ul>
   <?php foreach ($faqs as $faq): ?>
      <li><a href="/modules/faqs/detail.php/<?=$faq['FaqID'];?>/<?=$faq['AnswerID'];?>/"><?=nl2br($faq['ShortQuestion']);?></a><?php if ($faq['FlagAsNew'] == 1): ?>&nbsp;&nbsp;<span style="color:red;">NEW!</span><?php endif; ?></li>
   <?php endforeach; ?>
   </ul>

<?php else: ?>

   <ul>
   <li>There are no FAQs at this time.</li>
   </ul>
   
<?php endif; ?>