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

   <a class="admin" href="<?=site_url('cp/reports/update_data/'.$report_id.'#'.$datum['site_id']);?>">Cancel</a>

               </div>
               
   <h1>Edit a Datum</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">
               
               <p style="font-size:1.4em;"><?=$sites_lookup[$datum['site_id']];?>
               <br /><strong><?=$datum['data_point_id'];?></strong>
               <br /><?=date('d M Y', strtotime($report['start_date']));?> &ndash; <?=date('d M Y', strtotime($report['end_date']));?></p>

<form method="post" action="<?=site_url('cp/data/edit/'.$data_id.'/'.$last_action);?>">

<p class="blockintro">Update the amount and indicate the source for the data if relevant.</p>
<div class="block">
   <p><strong><label for="amount">Amount:</label></strong></p>
   <p><?=form_input(array('name'=>'amount', 'id'=>'amount', 'maxlength'=>'255', 'size'=>'70', 'value'=>$this->validation->amount));?>
   <?=$this->validation->amount_error;?></p>
   <p><label for="source">Source:</label></p>
   <p><?=form_input(array('name'=>'source', 'id'=>'source', 'maxlength'=>'255', 'size'=>'70', 'value'=>$this->validation->source));?>
   <?=$this->validation->source_error;?></p>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('cp/reports/update_data/'.$report_id.'#'.$datum['site_id']);?>">Cancel</a>
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
        
            </div>   <!-- indent -->

         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>