<div class="faq-search">

<p>Search all FAQs for this site</p>

<form method="post" action="<?=$action;?>" name="faq" id="faq">

<?=form_input(array('name'=>'Words', 'id'=>'Words', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->Words));?>
<?=$this->validation->Words_error;?>

<input type="submit" name="faqSearch" id="faqSearch" value="Search">

</form>

<?=$popular_searches;?>

<div style="border-bottom:1px solid #666; margin:1.5em 0;"></div>

<?php if ( ! empty($faqs)): ?>
   
      <ul>
   <?php foreach ($faqs as $faq): ?>
      <li><a href="/modules/faqs/detail.php/<?=$faq['ID'];?>/<?=$faq['AnswerID'];?>/"><?=nl2br($faq['ShortQuestion']);?></a><?php if ($faq['FlagAsNew'] == 1): ?>&nbsp;&nbsp;<span style="color:red;">NEW!</span><?php endif; ?></li>
   <?php endforeach; ?>
   </ul>
   
<?php else: ?>

   <p>No results found.</p>
   
<?php endif; ?>

</div>