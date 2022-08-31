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
               
   <h1>Add a Report</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/reports/add/'.$last_action);?>">

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

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this report'))?> or <a class="admin" href="<?=site_url('cp/reports/index');?>">Cancel</a>
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

               <p>&nbsp;</p>
        
            </div>   <!-- indent -->

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>