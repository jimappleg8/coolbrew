<?php if (count($faqs) > 0): ?>

<?php // This template creates a list with anchor links to sections within the same page. This is how you can list all the FAQs on a single page. ?>

   <ul>
   <?php foreach ($faqs as $faq): ?>
      <li><a href="#faqid<?=$faq['FaqID'];?>"><?=nl2br($faq['ShortQuestion']);?></a><?php if ($faq['FlagAsNew'] == 1): ?>&nbsp;&nbsp;<span style="color:red;">NEW!</span><?php endif; ?></li>
   <?php endforeach; ?>
   </ul>
   
   <div style="border-bottom:1px solid #666; margin:1.5em 0;"></div>
   
   <?php foreach ($faqs as $faq): ?>
   
   <a name="faqid<?=$faq['FaqID']?>"></a>
   <p>
   <?php if ($faq['FlagAsNew'] == 1): ?>
      <span style="color:red;">NEW!</span>&nbsp;&nbsp;
   <?php endif; ?>
   <b><?=nl2br($faq['Question']);?></b></p>
   <p><?=nl2br($faq['Answer']);?>
   <br>&nbsp;</p>


   <?php endforeach; ?>

<?php else: ?>

   <ul>
   <li>There are no FAQs at this time.</li>
   </ul>
   
<?php endif; ?>