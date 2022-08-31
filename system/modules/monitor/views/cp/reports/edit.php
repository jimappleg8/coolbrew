<body>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('cp/reports/index');?>">Cancel</a>

               </div>
               
   <h1>Edit a Report</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/reports/edit/'.$report_id.'/'.$last_action);?>">

<p class="blockintro">Report basics.</p>
<div class="block">
   <p><strong><label for="type">Type:</label></strong></p>
   <p><?=form_dropdown('report_type_id', $report_types, $this->validation->report_type_id);?>
   <?=$this->validation->report_type_id_error;?></p>
   <p><strong><label for="start_date">Start Date:</label></strong></p>
   <p><?=form_input(array('name'=>'start_date', 'id'=>'start_date', 'maxlength'=>'255', 'size'=>'70', 'value'=>$this->validation->start_date));?>
   <?=$this->validation->start_date_error;?></p>
   <p><strong><label for="end_date">End Date:</label></strong></p>
   <p><?=form_input(array('name'=>'end_date', 'id'=>'end_date', 'maxlength'=>'255', 'size'=>'70', 'value'=>$this->validation->end_date));?>
   <?=$this->validation->end_date_error;?></p>
</div>

<h2>Sites</h2>
<p class="blockintro">Sites to include in the report</p>
<div class="block">
   <div class="listing">
   
   <?php foreach($site_list AS $site): ?>

      <?php $fieldname = 'site'.$site['ID']; ?>

      <div style="margin-left:0; border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$site['Domain'];?><?php if ($site['SourceID'] != NULL): ?> <span style="color:red;">(Google Analytics available)</span><?php endif; ?></label></p>
      </div>

   <?php endforeach; ?>

   </div> <?php /* listing */ ?>
</div>

<h2>Data Points</h2>
<p class="blockintro">Data points to include in the report</p>
<div class="block">
   <div class="listing">
   
   <?php foreach($data_point_list AS $data): ?>

      <?php $fieldname = 'data'.$data['id']; ?>

      <div style="margin-left:0; border-top:1px solid #666; clear:both;">
      <p style="margin:0; padding:4px 0;"><input type="checkbox" name="<?=$fieldname;?>" id="<?=$fieldname;?>" value="1" <?=$this->validation->set_checkbox($fieldname, '1');?> /> <label for="<?=$fieldname;?>"><?=$data['name'];?></label></p>
      </div>

   <?php endforeach; ?>

   </div> <?php /* listing */ ?>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('cp/reports/index');?>">Cancel</a>
</div>

</form>

               </div> <?php /* basic-form */ ?>
   
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
            
            <p>&nbsp;</p>
  
            <div class="indent">

               <a href="<?=site_url('cp/reports/duplicate/'.$report_id);?>" style="background-color:transparent;"><img src="/images/buttons/button_copy_report.gif" width="138" height="31" alt="Copy this report" style="border:0px; margin-top:4px;" /></a>
        
            </div>   <!-- indent -->

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>