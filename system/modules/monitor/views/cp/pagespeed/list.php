<body>

<?=$this->load->view('cp/tabs');?>

<?php if ($admin['message'] != ''): ?>
<div id="message">
<p><?=$admin['message'];?></p>
</div>
<?php endif; ?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

               </div>

   <h1 id="top">Analytics</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<p>This page lists the Google PageSpeed score for each of our websites.</p>

<?php $cnt = 0; ?>
<table class="info">
<?php foreach ($scores AS $score): ?>
<tr>
<td><a href="http://developers.google.com/speed/pagespeed/insights/?url=<?=urlencode($score['url']);?>" target="_blank"><?=$score['url'];?></a></td>
<td><?=$score['score'];?></td>
<td>
<a class="admin" href="#" onclick="$('#output-<?=$cnt;?>').show(); return false;">Learn More</a> | 
<a class="admin" href="#" onclick="$('#output-<?=$cnt;?>').hide(); return false;">Hide</a>
<div id="output-<?=$cnt;?>" style="display:none;">
<pre><?=print_r($score['output']);?></pre>
</div>
</td>
</tr>
   <?php $cnt++; ?>
<?php endforeach; ?>
</table>
               </div> <?php /* basic-form */ ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
           
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>