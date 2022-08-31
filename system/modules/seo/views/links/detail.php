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

<a href="<?=site_url('links/show_index/'.$site_id);?>" class="admin">Done</a>

               </div>
               
   <h1>Page Details</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<table width="100%" cellspacing="0" cellpadding="3" border="0">

<tr>
<td><b>Page&nbsp;URL:&nbsp;</b></td>
<td><a href="<?=$link['URL'];?>"><?=htmlentities($link['URL']);?></a>
<?php if ($link['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$link['NewURL'];?>"><?=htmlentities($link['NewURL']);?></a></span><?php endif; ?></td>
</tr>

<tr>
<td><b>Page&nbsp;Title:&nbsp;</b></td>
<td><?=((isset($link['Title']) && trim($link['Title']) !== '') ? $link['Title'] : "Untitled Document");?></td>
</tr>

<tr>
<td><b>Date&nbsp;Indexed:&nbsp;</b></td>
<td><?=$link['IndexedDate'];?></td>
</tr>

<tr>
<td width="110"><img src="/images/spacer.gif" width="110" height="1" alt=""></td>
<td width="100%"><img src="/images/spacer.gif" width="200" height="1" alt=""></td>
</tr>

</table>

<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Links on this page</h4>

<?php $page_count = 0; ?>
<?php foreach ($links AS $page): ?>
   <?php $page_count++; ?>
<div class="link" style="padding:9px 0 0 12px; line-height:1.2em;"><?=((isset($page['Title']) && trim($page['Title']) !== '') ? $page['Title'] : "Untitled Document");?>
<br /><span style="font-size:90%;"><a href="<?=$page['URL'];?>"><?=htmlentities($page['URL']);?></a></span>
<?php if ($page['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$page['NewURL'];?>"><?=htmlentities($page['NewURL']);?></a></span><?php endif; ?>
</div>
<?php endforeach; ?>
<?php if ($page_count == 0): ?>
<div class="link" style="padding:9px 0 0 12px;">None found.</div>
<?php endif; ?>


<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Pages that link to this page (referrers)</h4>

<?php $page_count = 0; ?>
<?php foreach ($referrers AS $page): ?>
   <?php $page_count++; ?>
<div class="link" style="padding:9px 0 0 12px; line-height:1.2em;"><?=((isset($page['Title']) && trim($page['Title']) !== '') ? $page['Title'] : "Untitled Document");?>
<br /><span style="font-size:90%;"><a href="<?=$page['URL'];?>"><?=htmlentities($page['URL']);?></a></span>
<?php if ($page['NewURL'] != FALSE): ?><br /><span style="font-size:90%;"><span style="color:red;">--&gt; redirects to: </span><a href="<?=$page['NewURL'];?>"><?=htmlentities($page['NewURL']);?></a></span><?php endif; ?>
</div>
<?php endforeach; ?>
<?php if ($page_count == 0): ?>
<div class="link" style="padding:9px 0 0 12px;">None found.</div>
<?php endif; ?>

<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Meta Description</h4>

<p><?=($link['MetaDescription'] != '') ? $link['MetaDescription'] : "None found.";?></p>

<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Meta Keywords</h4>

<p><?=($link['MetaKeywords'] != '') ? $link['MetaKeywords'] : "None found.";?></p>

<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Meta Abstract</h4>

<p><?=($link['MetaAbstract'] != '') ? $link['MetaAbstract'] : "None found.";?></p>

<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Meta Robots</h4>

<p><?=($link['MetaRobots'] != '') ? $link['MetaRobots'] : "None found.";?></p>

<h4 style="margin-top:24px; padding-top:3px; border-top:2px dotted #999;">Raw text on the page</h4>

<p><?=($link['Text'] != '') ? $link['Text'] : "None found.";?></p>

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