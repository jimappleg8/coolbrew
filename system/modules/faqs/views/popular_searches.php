<?php if (count($searches) > 0): ?>

<div id="popular-searches">

   <?php $count = 1; ?>
   <?php foreach ($searches as $search): ?>
      <form name="search-<?=$count;?>" method="POST" action="<?=$action;?>">
      <input type="hidden" name="Words" value="<?=$search['Keyword'];?>" />
      </form>
      <?php $count++; ?>
   <?php endforeach; ?>

   <h3>Popular searches:</h3>
   <ul>
   <?php $count = 1; ?>
   <?php foreach ($searches as $search): ?>
      <li><a href="#" onclick="document.forms['search-<?=$count;?>'].submit(); return false;"><?=$search['Keyword'];?></a></li>
      <?php $count++; ?>
   <?php endforeach; ?>
   </ul>
</div>

<?php endif; ?>