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
               
               <?php if ($is_authorized): ?>
               <a class="admin" href="<?=site_url('cp/analytics/all_sites_summary/revoke');?>">Revoke access</a>
               <?php else: ?>
               <a class="admin" href="<?=site_url('cp/analytics/all_sites_summary/auth');?>">Authorize access</a>
               <?php endif; ?>

               </div>

   <h1 id="top">All Sites Summary</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/analytics/all_sites_summary');?>">

<p class="blockintro">Enter a date range on which to report.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="StartDate">Start Date:</label></dt>
      <dd><?=form_input(array('name'=>'StartDate', 'id'=>'StartDate', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->StartDate));?>
      <?=$this->validation->StartDate_error;?></dd>

      <dt><label for="EndDate">End Date:</label></dt>
      <dd><?=form_input(array('name'=>'EndDate', 'id'=>'EndDate', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->EndDate));?>
      <?=$this->validation->EndDate_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Get data'))?> or <a class="admin" href="<?=site_url('cp/analytics/index/');?>">Cancel</a>
</div>

</form>

               </div> <?php /* basic-form */ ?>

<?php if ($errors != ''): ?>

<p><?=$errors;?></p>

<?php endif; ?>


<?php if ( ! empty($summary)): ?>

<style>
table.info { border-collapse:collapse; margin:2em 0 1em 0; }
table.info th { padding:6px 12px; border:1px solid #000; 
  vertical-align:top; font-weight:bold; text-align:left; }
table.info td { padding:6px 9px; border:1px solid #000; 
  vertical-align:top; }
</style>

<table class="info">

<tr>
<th style="padding: 6px 35px;">Brand&nbsp;Website</th>
<th>Total Sessions</th>
<th>Unique Visitors</th>
<th>Pageviews</th>
<th>Pages / Session</th>
<th>Ave. Session Duration</th>
<th>Bounce Rate</th>
<th>% New Visits</th>
</tr>

<?php foreach ($summary AS $item): ?>
<tr>
<td><?=$item['profile_label'];?></td>
<td><?=number_format($item['total_visits']);?></td>
<td><?=number_format($item['unique_visitors']);?></td>
<td><?=number_format($item['pageviews']);?></td>
<td><?=number_format($item['pages_per_visit'], 2);?></td>
<?php
   $seconds = (int)$item['avg_visit_duration'];
   $hours = floor($seconds / 3600);
   $mins = floor(($seconds - ($hours*3600)) / 60);
   $secs = floor($seconds - ($hours*3600) - ($mins*60));
   // I'm not using hours below, we're not even close to that long.
?>
<td><?=sprintf("%02d", $mins);?>:<?=sprintf("%02d", $secs);?></td>
<td><?=number_format($item['bounce_rate'], 2);?>%</td>
<td><?=number_format($item['percent_new_visits'], 2);?>%</td>
</tr>
<?php endforeach; ?>

</table>

<?php endif; ?>
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