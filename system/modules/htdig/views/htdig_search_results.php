<?php

// set display type
// $dtype = 1 - display percentage match as number
// $dtype = 2 - display images/stars

$dtype = 1;
$rating_image = '<img src="/images/search/rating.gif" width="15" height="13" alt="*">';

?>

<p>
<form method="post" action="/search/results.html">
<font size="-1">

Match: <select name="method">
<option value="and"<?php if ($options['method'] == "and"): ?> selected<?php endif; ?>>All</option>
<option value="or"<?php if ($options['method'] == "or"): ?> selected<?php endif; ?>>Any</option>
<option value="boolean"<?php if ($options['method'] == "boolean"): ?> selected<?php endif; ?>>Boolean</option>
</select>

Sort by: <select name="sort">
<?php if ($options['sort'] == "score"): ?>
<option value="score" selected>Score</option>
<?php else: ?>
<option value="score">Score</option>
<?php endif; ?>
<?php if ($options['sort'] == "time"): ?>
<option value="time" selected>Time</option>
<?php else: ?>
<option value="time">Time</option>
<?php endif; ?>
<?php if ($options['sort'] == "title"): ?>
<option value="title" selected>Title</option>
<?php else: ?>
<option value="title">Title</option>
<?php endif; ?>
<?php if ($options['sort'] == "revscore"): ?>
<option value="revscore" selected>Reverse Score</option>
<?php else: ?>
<option value="revscore">Reverse Score</option>
<?php endif; ?>
<?php if ($options['sort'] == "revtime"): ?>
<option value="revtime" selected>Reverse Time</option>
<?php else: ?>
<option value="revtime">Reverse Time</option>
<?php endif; ?>
<?php if ($options['sort'] == "revtitle"): ?>
<option value="revtitle" selected>Reverse Title</option>
<?php else: ?>
<option value="revtitle">Reverse Title</option>
<?php endif; ?>
</select>
</font>
<input type="hidden" name="config" value="htdig">
<input type="hidden" name="restrict" value="">
<input type="hidden" name="exclude" value="">
<br>
Search:
<input type="text" size="30" name="words" value="<?=$options['words'];?>">
<input type="submit" value="Search">
</form>
<br>&nbsp;</p>

<?php if ($results['MatchCount'] > 0): ?>

&nbsp;<br><hr noshade size="1">

   <p class="PageHd">Search results for '<?=$results['Words'];?> '</p>

   <hr noshade size="1">
   <b>Documents <?=$results['FirstMatch'];?> - <?=$results['LastMatch'];?> of <?=$results['MatchCount'];?> matches.  </b>
	
   <?php if ($dtype == 2): ?>
      <b>More <?=$rating_image;?>'s indicate a better match.</b>
   <?php endif; ?>

   <hr noshade size="1">

   <?php for ($i=0; $i<count($results['Matches']); $i++): ?>

   <dl><dt><strong><a href="<?=$results['Matches'][$i]['URL'];?>"><?=$results['Matches'][$i]['Title'];?></a></strong>&nbsp;&nbsp;

      <?php

      $percent = $results['Matches'][$i]['Percent'];
      $counter = ($percent/20);

      if ($dtype == 1)
      {
         echo $percent."% match";
      } 

      if ($dtype == 2)
      {
        for ( ; $counter>=1; $counter--)
        {
	       echo $rating_image;
	    }
      }

      ?>

   </dt><dd><?=$results['Matches'][$i]['Excerpt'];?><br>
   <em><a href="<?=$results['Matches'][$i]['URL'];?>"><?=$results['Matches'][$i]['URL'];?></a></em>
   <font size="-1"><?=$results['Matches'][$i]['Modified'];?>, <?=$results['Matches'][$i]['Size'];?> bytes</font>
   </dd></dl>

   <?php endfor; ?>

   <hr>
   Pages:<br>

   <?php if ($options['page'] != 1): ?>

   <a href="<?=$results['php_self'];?>?method=<?=$options['method'];?>&matchesperpage=<?=$options['matchesperpage'];?>&words=<?=$options['words'];?>&page=<?=$options['page']-1;?>"><img src="/images/search/buttonl.gif" border="0" align="middle" width="30" height="30" alt="2"></a>

   <?php endif; ?>

   <?php for ($j=0; $j<count($results['page_loop']); $j++): ?>

      <?php if ($results['page_loop'][$j] == $options['page']): ?>
   <img src="/images/search/button<?=$results['page_loop'][$j];?>.gif" border="2" align="middle" width="30" height="30" alt="<?=$results['page_loop'][$j];?>">
      <?php else: ?>
   <a href="<?=$results['php_self'];?> ?method=<?=$options['method'];?>&matchesperpage=<?=$options['matchesperpage'];?>&words=<?=$options['words'];?>&page=<?=$results['page_loop'][$j];?>"><img src="/images/search/button<?=$results['page_loop'][$j];?>.gif" border="0" align="middle" width="30" height="30" alt="<?=$results['page_loop'][$j];?>"></a>
      <?php endif; ?>

   <?php endfor; ?>

   <?php if ($options['page'] != $results['num_pages']): ?>

   <a href="<?=$results['php_self'];?>?method=<?=$options['method'];?>&matchesperpage=<?=$options['matchesperpage'];?>&words=<?=$options['words'];?>&page=<?=$options['page']+1;?>"><img src="/images/search/buttonr.gif" border="0" align="middle" width="30" height="30" alt="2"></a>

   <?php endif; ?>

<?php elseif ($results['MatchCount'] == 0 && $options['words'] != ""): ?>

&nbsp;<br><hr noshade size="1">

<p class="PageHd">Search results for '<?=$options['words'];?>'</p>

<hr noshade size="1">
<b>No results were found.</b>
<hr noshade size="1">

<?php endif; ?>
