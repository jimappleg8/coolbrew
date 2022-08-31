<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

<a href="<?=site_url('links/export/'.$site_id);?>" class="admin">Export to CSV</a> | 
<a href="<?=site_url('links/index_site/'.$site_id.'/'.$last_action.'/');?>" class="admin">Re-index this site</a>

               </div>
               
   <h1>Site Index Results</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<table width="100%" cellspacing="0" cellpadding="3" border="0">

<tr>
<td><b>Root&nbsp;Location:&nbsp;</b></td>
<td><?=$RootURL;?></td>
</tr>

<tr>
<td><b>Date&nbsp;Indexed:&nbsp;</b></td>
<td><?=$IndexedDate;?></td>
</tr>

<?php if ($ExecutionTime != 0): ?>
   <?php
      $minutes = floor($ExecutionTime / 60);
      $seconds = $ExecutionTime - ($minutes * 60);
   ?>
<tr>
<td><b>Execution&nbsp;Time:&nbsp;</b></td>
<td><?php if ($minutes != 0): ?><?=$minutes;?> minutes, <?php endif; ?><?=number_format($seconds, 2, '.', '');?> seconds</td>
</tr>
<?php endif; ?>

<tr>
<td><b>URLs&nbsp;Extracted:&nbsp;</b></td>
<td><?=count($links);?></td>
</tr>

<tr>
<td width="110"><img src="/images/spacer.gif" width="110" height="1" alt=""></td>
<td width="100%"><img src="/images/spacer.gif" width="200" height="1" alt=""></td>
</tr>

</table>


<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Internal Webpages</h4>

<?php $link_count = 0; ?>
<?php foreach ($links AS $link): ?>
   <?php if ($link['Location'] == 'internal' && $link['Type'] == 'page'): ?>
      <?php $link_count++; ?>
<div class="link" style="padding:9px 0 0 12px; line-height:1.2em;"><?=((isset($link['Title']) && trim($link['Title']) !== '') ? $link['Title'] : "Untitled Document");?> <a class="admin" href="<?=site_url('links/detail/'.$link['ID']);?>">details</a>
<br /><span style="font-size:90%;"><a href="<?=$link['URL'];?>"><?=htmlentities($link['URL']);?></a></span>
<?php if ($link['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$link['NewURL'];?>"><?=htmlentities($link['NewURL']);?></a></span><?php endif; ?>
</div>
   <?php endif; ?>
<?php endforeach; ?>
<?php if ($link_count == 0): ?>
<div class="link" style="padding:9px 0 0 12px;">None found.</div>
<?php endif; ?>


<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Internal Downloads</h4>

<?php $link_count = 0; ?>
<?php foreach ($links AS $link): ?>
   <?php if ($link['Location'] == 'internal' && $link['Type'] == 'download'): ?>
      <?php $link_count++; ?>
<div class="link" style="padding:9px 0 0 12px; line-height:1.2em;"><?=((isset($link['Title']) && trim($link['Title']) !== '') ? $link['Title'] : "Untitled Document");?> <a class="admin" href="<?=site_url('links/detail/'.$link['ID']);?>">details</a>
<br /><span style="font-size:90%;"><a href="<?=$link['URL'];?>"><?=htmlentities($link['URL']);?></a></span>
<?php if ($link['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$link['NewURL'];?>"><?=htmlentities($link['NewURL']);?></a></span><?php endif; ?>
</div>
   <?php endif; ?>
<?php endforeach; ?>
<?php if ($link_count == 0): ?>
<div class="link" style="padding:9px 0 0 12px;">None found.</div>
<?php endif; ?>


<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">External Webpages<?php if ($ExternalTitles == 0): ?> <span style="font-weight:normal; font-size:90%;">(Titles were not resolved)</span><?php endif; ?></h4>

<?php $link_count = 0; ?>
<?php foreach ($links AS $link): ?>
   <?php if ($link['Location'] == 'external' && $link['Type'] == 'page'): ?>
      <?php $link_count++; ?>
<div class="link" style="padding:9px 0 0 12px; line-height:1.2em;"><?=((isset($link['Title']) && trim($link['Title']) !== '') ? $link['Title'] : "Untitled Document");?> <a class="admin" href="<?=site_url('links/detail/'.$link['ID']);?>">details</a>
<br /><span style="font-size:90%;"><a href="<?=$link['URL'];?>"><?=htmlentities($link['URL']);?></a></span>
<?php if ($link['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$link['NewURL'];?>"><?=htmlentities($link['NewURL']);?></a></span><?php endif; ?>
</div>
   <?php endif; ?>
<?php endforeach; ?>
<?php if ($link_count == 0): ?>
<div class="link" style="padding:9px 0 0 12px;">None found.</div>
<?php endif; ?>


<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">External Downloads</h4>

<?php $link_count = 0; ?>
<?php foreach ($links AS $link): ?>
   <?php if ($link['Location'] == 'external' && $link['Type'] == 'download'): ?>
      <?php $link_count++; ?>
<div class="link" style="padding:9px 0 0 12px; line-height:1.2em;"><?=((isset($link['Title']) && trim($link['Title']) !== '') ? $link['Title'] : "Untitled Document");?> <a class="admin" href="<?=site_url('links/detail/'.$link['ID']);?>">details</a>
<br /><span style="font-size:90%;"><a href="<?=$link['URL'];?>"><?=htmlentities($link['URL']);?></a></span>
<?php if ($link['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$link['NewURL'];?>"><?=htmlentities($link['NewURL']);?></a></span><?php endif; ?>
</div>
   <?php endif; ?>
<?php endforeach; ?>
<?php if ($link_count == 0): ?>
<div class="link" style="padding:9px 0 0 12px;">None found.</div>
<?php endif; ?>


            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy;2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
           
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>