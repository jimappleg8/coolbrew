<?='<?xml version="1.0" encoding="UTF-8"?>';?>

<faqCategoryList generator="hcg" version="1.0">
<source><?=$source;?></source>
<?php if ( ! empty($categories)): ?>
<response>
   <FaqCategories>
   <?php foreach ($categories AS $category): ?>
      <FaqCategory>
      <?php foreach ($category AS $key => $value): ?>
         <?php if ($value != ''): ?>
   <<?=$key;?>><?=$value;?></<?=$key;?>>
         <?php else: ?>
   <<?=$key;?>/>
         <?php endif; ?>
      <?php endforeach; ?>
      </FaqCategory>
   <?php endforeach; ?>
   </FaqCategories>
</response>
<?php endif; ?>
<status><?=$status;?></status>
</faqCategoryList>
