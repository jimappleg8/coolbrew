
<?=$this->load->view('tabs');?>

<div id="job-dataarea">

<div class="page-header">
   <div class="page-header-links">
      <a class="admin" href="/careers/print.php/jobs/print_jobs/" onclick="popup(this.href, 'report'); return false;">Print</a> <span class="pipe">|</span> 
      <a class="admin" href="<?=site_url('jobs/export');?>">Export</a> <span class="pipe">|</span> 
      <a class="admin" href="<?=site_url('jobs/filled');?>">List filled jobs</a> <span class="pipe">|</span> 
      <a class="admin" href="<?=site_url('jobs/onhold');?>">List on-hold jobs</a> <span class="pipe">|</span> 
      <a class="admin" href="<?=site_url('jobs/add/'.$last_action);?>">Add a new job listing</a>
   </div>
   <h2>Manage Job Listings</h2>
</div>

<?php if ($jobs['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$jobs['error_msg'];?>
</div>
<?php endif; ?>

<div id="listing">

<?php if ($jobs['job_exists'] == true): ?>

   <table width="100%" cellpadding="0" cellspacing="0" border="0">

   <tr>
   <th>&nbsp;</th>
   <th>
   <?php if ($orderby == "Title"): ?>
   <a href="<?=site_url('jobs/index/Title/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Job Title</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/Title/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Job Title</a>
   <?php endif; ?>
   <br /><span style="font-size:90%;"><span style="color:#CCC;">Reporting to</span> <?php if ($orderby == "Manager"): ?>
   <a href="<?=site_url('jobs/index/Manager/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Manager</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/Manager/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Manager</a>
   <?php endif; ?>
   <br /><span style="color:#CCC;">Job #</span> <?php if ($orderby == "JobNum"): ?>
   <a href="<?=site_url('jobs/index/JobNum/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Job No.</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/JobNum/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Job No.</a>
   <?php endif; ?>
    <span style="color:#CCC;">created on</span> <?php if ($orderby == "CreatedDate"): ?>
   <a href="<?=site_url('jobs/index/CreatedDate/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Date</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/CreatedDate/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Date</a>
   <?php endif; ?>
    <span style="color:#CCC;">by</span> <?php if ($orderby == "LastName"): ?>
   <a href="<?=site_url('jobs/index/LastName/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Creator</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/LastName/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Creator</a>
   <?php endif; ?>
   </span>
   </th>
   <th>
   <div style="font-size:90%;"><span style="color:#CCC;">In</span> <?php if ($orderby == "CategoryName"): ?>
   <a href="<?=site_url('jobs/index/CategoryName/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Category</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/CategoryName/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Category</a>
   <?php endif; ?>
   <br /><span style="color:#CCC;">with</span> <?php if ($orderby == "CompanyName"): ?>
   <a href="<?=site_url('jobs/index/CompanyName/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Company</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/CompanyName/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Company</a>
   <?php endif; ?>
   <br /><span style="color:#CCC;">located in</span> <?php if ($orderby == "LocationName"): ?>
   <a href="<?=site_url('jobs/index/LocationName/'.$direction);?>" style="color:#FF0; font-weight:bold; background:transparent;">Location</a>
   <?php else: ?>
   <a href="<?=site_url('jobs/index/LocationName/asc');?>" style="color:#FFF; font-weight:bold; background:transparent;">Location</a>
   <?php endif; ?>
   </div>
   </th>
   <tr>
   
   <tr>
   <td colspan="3"><img src="/images/dot_clear.gif" width="1" height="3" alt=""></td>
   </tr>
   
   <?php foreach($job_list as $job): ?>

   <tr>
   <?php $job_status = ($job['Status'] == 1) ? "Published" : "Not Published"; ?>
   <td valign="top"><a class="admin" href="<?=site_url('jobs/edit/'.$job['ID'].'/'.$last_action);?>">Edit</a></td>
   <td valign="top">
   <span style="font-size:110%; color:#444444; font-weight:bold;"><?=$job['Title'];?></span>
   <br /><span style="font-size:90%; color:#999;">Reporting to <span style="color:#333;"><?=$job['Manager'];?></span></span>
   <br /><span style="font-size:90%; color:#999;">Job # <span style="color:#333;"><?=$job['JobNum'];?></span> created on <?=date('d M Y', strtotime($job['CreatedDate']));?> by <?=$job['FirstName'];?> <?=$job['LastName'];?></span> &nbsp;<a class="admin" href="<?=site_url('jobs/toggle/'.$job['ID'].'/'.$last_action);?>"><?=$job_status;?></a>
   </td>
   <td>
   <span style="font-size:90%; color:#999;">In <span style="color:#333;"><?=$job['CategoryName'];?></span>
   <br />with <span style="color:#333;"><?=$job['CompanyName'];?></span>
   <br />located in <span style="color:#333;"><?=$job['LocationName'];?></span></span>
   </td>
   <tr>

   <?php endforeach; ?>

   <tr>
   <td class="hidden" width="40"><img src="/images/dot_clear.gif" width="40" height="1" alt=""></td>
   <td class="hidden" width="100%"><img src="/images/dot_clear.gif" width="10" height="1" alt=""></td>
   <td class="hidden" width="300"><img src="/images/dot_clear.gif" width="300" height="1" alt=""></td>
   </tr>

   </table>

</div>

</div> <?php // job-dataarea ?>

<?php else: ?>

   <p>There are no jobs to display.</p>

<?php endif; ?>