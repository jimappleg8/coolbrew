<body>

<?=$this->load->view('cp/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

<?php if ($admin['error_msg'] != ''): ?>
<div id="flash_error"><?=$admin['error_msg'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            
               <div class="page-header-links">

   <a class="admin" href="<?=site_url('cp/stories/index/'.$project_id);?>">Cancel</a>

               </div>

   <h1>New story</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/stories/add/'.$project_id.'/'.$last_action);?>">

<h2>Write a story</h2>
<div class="block">
   <dl>
      <dt><label for="Description">Description:</label></dt>
      <dd><?=form_input(array('name'=>'Description', 'id'=>'Description', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->Description));?>
      <?=$this->validation->Description_error;?></dd>

      <dt><label for="Client">Client:</label></dt>
      <dd><?=form_input(array('name'=>'Client', 'id'=>'Client', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->Client));?>
      <?=$this->validation->Client_error;?></dd>
   </dl>
</div>

<h2>Technical Details</h2>
<div class="block">
   <dl>
      <dt><label for="HeatID">Heat ID:</label></dt>
      <dd><?=form_input(array('name'=>'HeatID', 'id'=>'HeatID', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->HeatID));?>
      <?=$this->validation->HeatID_error;?></dd>

      <dt><label for="HeatAssignment">Heat Assignment:</label></dt>
      <dd><?=form_input(array('name'=>'HeatAssignment', 'id'=>'HeatAssignment', 'maxlength'=>'6', 'size'=>'4', 'value'=>$this->validation->HeatAssignment));?>
      <?=$this->validation->HeatAssignment_error;?></dd>
   </dl>
</div>

<h2>Processing</h2>
<div class="block">
   <dl>
      <dt><label for="Points">Points:</label></dt>
      <dd><?=form_input(array('name'=>'Points', 'id'=>'Points', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->Points));?>
      <?=$this->validation->Points_error;?></dd>

      <dt><label for="Assigned">Assigned:</label></dt>
      <dd><?=form_input(array('name'=>'Assigned', 'id'=>'Assigned', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->Assigned));?>
      <?=$this->validation->Assigned_error;?></dd>

      <dt><label for="Deadline">Deadline:</label></dt>
      <dd><?=form_input(array('name'=>'Deadline', 'id'=>'Deadline', 'maxlength'=>'255', 'size'=>'20', 'value'=>$this->validation->Deadline));?>
      <?=$this->validation->Deadline_error;?></dd>

      <dt><label for="Priority">Priority:</label></dt>
      <dd><?=form_dropdown('Priority', $priorities, $this->validation->Priority);?>
      <?=$this->validation->Priority_error;?></dd>
   </dl>
</div>

<h2>Notes</h2>
<div class="block">
   <p><?=form_textarea(array('name'=>'Notes', 'id'=>'Notes', 'cols' => 70, 'rows' => 6, 'value'=>$this->validation->Notes, 'class'=>'box'));?>
    <?=$this->validation->Notes_error;?></p>
</div>

<h2>Add a sprint</h2>

<div class="block">
   <dl>
      <dt><label for="NewSprint">Sprint:</label></dt>
      <dd><?=form_dropdown('NewSprint', $sprint_list, $this->validation->NewSprint);?>
      <?=$this->validation->NewSprint_error;?></dd>

      <dt><label for="NewStatus">Status:</label></dt>
      <dd><?=form_dropdown('NewStatus', $statuses, $this->validation->NewStatus);?>
      <?=$this->validation->NewStatus_error;?></dd>

      <dt><label for="NewEstimatedHours">Estimated Hours:</label></dt>
      <dd><?=form_input(array('name'=>'NewEstimatedHours', 'id'=>'NewEstimatedHours', 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->NewEstimatedHours));?>
      <?=$this->validation->NewEstimatedHours_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save new story'))?> or <a class="admin" href="<?=site_url('cp/stories/index/'.$project_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy; 2007 The Hain Celestial Group, Inc.

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