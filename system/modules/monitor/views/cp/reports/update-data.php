<body>

<a name="screen-top"></a>

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

   <h1 id="top">Update Report Data</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/reports/update_data/'.$report_id);?>">

<p class="blockintro">Select the report you wish to update.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="Report">Report:</label></dt>
      <dd><?=form_dropdown('Report', $reports, $this->validation->Report);?>
      <?=$this->validation->Report_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Update data'))?> or <a class="admin" href="<?=site_url('cp/reports/index/');?>">Cancel</a>
</div>

</form>

               </div> <?php /* basic-form */ ?>

<?php if ($errors != ''): ?>

<p><?=$errors;?></p>

<?php endif; ?>


<?php if ( ! empty($notices)): ?>

<p>
   <?php foreach($notices AS $notice): ?>
   <?=$notice;?><br />
   
   <?php endforeach; ?>
</p>
<?php endif; ?>

<h3>Summary Stats</h3>

<a class="admin" href="<?=site_url('cp/reports/calculate/'.$report_id);?>">run calculations</a>

<table class="info" style="width:100%;">

<tr>
<td colspan="2"><?=date('d M Y', strtotime($report['start_date']));?> &ndash; <?=date('d M Y', strtotime($report['end_date']));?></td>
</tr>

<tr>
<td colspan="2" style="background-color:#CCC;"><strong>Report</strong></td>
</tr>

<?php if (isset($sites_in_report)): ?>
<tr>
<td>Sites in Report</td>
<td><?=$sites_in_report;?></td>
</tr>
<?php endif; ?>

<tr>
<td colspan="2" style="background-color:#CCC;"><strong>Activity</strong></td>
</tr>

<?php if (isset($site_visits['#total'])): ?>
<tr>
<td>Total Visits</td>
<td><?=number_format($site_visits['#total']['amount']);?></td>
</tr>
<?php endif; ?>

<?php if (isset($site_visits['#average_daily'])): ?>
<tr>
<td>Average Daily Visits</td>
<td><?=number_format($site_visits['#average_daily']['amount']);?></td>
</tr>
<?php endif; ?>

<?php if (isset($site_unique_visitors['#total'])): ?>
<tr>
<td>Total Unique Visitors</td>
<td><?=number_format($site_unique_visitors['#total']['amount']);?></td>
</tr>
<?php endif; ?>

<?php if (isset($site_pageviews['#total'])): ?>
<tr>
<td>Total Pageviews</td>
<td><?=number_format($site_pageviews['#total']['amount']);?></td>
</tr>
<?php endif; ?>

<?php if (isset($site_percent_new_visitors['#total'])): ?>
<tr>
<td>Ave % New Visits</td>
<td><?=number_format($site_percent_new_visitors['#total']['amount']);?></td>
</tr>
<?php endif; ?>

</table>


<h3>Stats by Site</h3>

<?php foreach ($sites_lookup AS $key => $domain): ?>
   <?php if (isset($site_visits[$key])): ?>

<a name="<?=$key;?>"></a>
<table class="info" style="width:100%;">

<tr>
<td colspan="3"><span style="font-weight:bold; font-size:1.4em;"><?=$domain;?></span></td>
</tr>

<tr>
<td colspan="3"><?=date('d M Y', strtotime($report['start_date']));?> &ndash; <?=date('d M Y', strtotime($report['end_date']));?></td>
</tr>

<tr>
<td colspan="3" style="background-color:#CCC;"><strong>Activity</strong></td>
</tr>

<?php if (isset($site_visits[$key])): ?>
<tr>
<td>Visits</td>
<td><?=number_format($site_visits[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_visits[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_unique_visitors[$key])): ?>
<tr>
<td>Unique Visitors</td>
<td><?=number_format($site_unique_visitors[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_unique_visitors[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_percent_new_visitors[$key])): ?>
<tr>
<td>% New Visitors</td>
<td><?=number_format($site_percent_new_visitors[$key]['amount'], 2);?>%</td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_percent_new_visitors[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_pageviews[$key])): ?>
<tr>
<td>PageViews</td>
<td><?=number_format($site_pageviews[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_pageviews[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<tr>
<td colspan="3" style="background-color:#CCC;"><strong>Engagement</strong></td>
</tr>

<?php if (isset($site_pageviews_per_visit[$key])): ?>
<tr>
<td>Pageviews per Visit</td>
<td><?=number_format($site_pageviews_per_visit[$key]['amount'], 2);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_pageviews_per_visit[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_average_visit_duration[$key])): ?>
<tr>
<td>Ave Visit Duration</td>
<td><?=number_format($site_average_visit_duration[$key]['amount'], 2);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_average_visit_duration[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_bounce_rate[$key])): ?>
<tr>
<td>Bounce Rate</td>
<td><?=number_format($site_bounce_rate[$key]['amount'], 2);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_bounce_rate[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_contact_us_complaint[$key])): ?>
<tr>
<td>Contact Us Complaints</td>
<td><?=number_format($site_contact_us_complaint[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_contact_us_complaint[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_contact_us_inquiry[$key])): ?>
<tr>
<td>Contact Us Inquiry</td>
<td><?=number_format($site_contact_us_inquiry[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_contact_us_inquiry[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_contact_us_praise[$key])): ?>
<tr>
<td>Contact Us Praise</td>
<td><?=number_format($site_contact_us_praise[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_contact_us_praise[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

<?php if (isset($site_contact_us_suggestion[$key])): ?>
<tr>
<td>Contact Us Suggestion</td>
<td><?=number_format($site_contact_us_suggestion[$key]['amount']);?></td>
<td><a class="admin" href="<?=site_url('cp/data/edit/'.$site_contact_us_suggestion[$key]['id'].'/'.$last_action);?>">edit</a></td>
</tr>
<?php endif; ?>

</table>

<a href="#screen-top">Jump to top</a>

   <?php endif; ?>
<?php endforeach; ?>


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
           
<h1>Jump to...</h1>

<div style="background-color:#FFF; padding-left:2em; padding-top:1em;">

<?php foreach ($sites_lookup AS $key => $domain): ?>
   <?php if (isset($site_visits[$key])): ?>

      <p style="margin:0; padding:4px 0;"><a href="#<?=$key;?>" style="text-decoration:none;"><?=$domain;?></a></p>

   <?php endif; ?>
<?php endforeach; ?>

</div>
   
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>