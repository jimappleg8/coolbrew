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

   <h1 id="top">Reports</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<p>These tools are designed to help the hcgWeb team assemble the statistics needed for official monthly and quarterly reports. The reports listed below may not be complete.</p>

<?php if ( ! empty($reports)): ?>
<table class="info">
   <tr>
   <th>ID</th>
   <th>Type</th>
   <th>Start Date</th>
   <th>End Date</th>
   <th>Actions</th>
   </tr>
   <?php foreach ($reports AS $report): ?>
   <tr>
   <td><?=$report['id'];?></td>
   <td><?=$report_types[$report['report_type_id']];?></td>
   <td><?=$report['start_date'];?></td>
   <td><?=$report['end_date'];?></td>
   <td><a class="admin" href="<?=site_url('cp/reports/view/'.$report['id']);?>">view</a> | <a class="admin" href="<?=site_url('cp/reports/edit/'.$report['id'].'/'.$last_action);?>">edit</a> | <a class="admin" href="<?=site_url('cp/reports/update_data/'.$report['id']);?>">update data</a></td>
   </tr>
   <?php endforeach; ?>

</table>

<?php else: ?>

<p>There are no reports to list.</p>

<?php endif; ?>

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
         
         <a href="<?=site_url('cp/reports/add/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_report.gif" width="138" height="31" alt="Add a new report" style="border:0px; margin-top:4px;" /></a>
           
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>