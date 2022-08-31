
<div id="quote">
   <div id="quoteText"><p><?=$comment['Letter'];?></p></div>
   <div id="closingQuote"><img src="/images/global/quote_close.gif" width="30" height="18" alt="" /></div>
   <div id="quoteAuthor">
      <?=$comment['Author'];?>
      <br /><?php if ($comment['AuthorCity'] != ''): ?><?=$comment['AuthorCity'];?>, <?php endif; ?><?=$comment['AuthorState'];?>
   </div>
</div>
