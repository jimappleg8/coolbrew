<body>

<script type="text/javascript">
//<![CDATA[

<?php foreach ($sprints AS $sprint): ?>

function listHours_<?=$sprint['ID'];?>()
{
   $.ajax({
      type: 'get',
      url: "<?=site_url('cp/hours/index/'.$story_id.'/'.$sprint['ID']);?>",
      success: function(r) {
         $("#hours_list_<?=$sprint['ID'];?>").html(r);
      }
   });
}

function addHours_<?=$sprint['ID'];?>()
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('cp/hours/add/'.$story_id.'/'.$sprint['ID']);?>",
      data: $("#new_hours_item_<?=$sprint['ID'];?>").serialize(),
      success: function(r) {
         $("#hours_list_<?=$sprint['ID'];?>").html(r);
      }
   });
   $("#hours_new_item_<?=$sprint['ID'];?>").children('form').get(0).reset();
   $("#hours_new_item_<?=$sprint['ID'];?> form:not(.filter) :input:visible:enabled:first").focus();
}

function showEditHours_<?=$sprint['ID'];?>(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#hours_list_<?=$sprint['ID'];?>").html(r);
      }
   });
}

function editHours_<?=$sprint['ID'];?>(hour_id, last_action)
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('cp/hours/edit');?>"+'/'+hour_id+'/'+last_action,
      data: $("#edit_hours_item_<?=$sprint['ID'];?>").serialize(),
      success: function(r) {
         $("#hours_list_<?=$sprint['ID'];?>").html(r);
      }
   });
}

function deleteHours_<?=$sprint['ID'];?>(url)
{
  if (confirm(" Are you sure you want to delete these hours? "))
  {
     $.ajax({
        type: 'get',
        url: url,
        success: function(r) {
           $("#hours_list_<?=$sprint['ID'];?>").html(r);
        }
   });
  }
}

function hideHoursForm_<?=$sprint['ID'];?>()
{
   $("#hours_new_item_<?=$sprint['ID'];?>").hide();
   $("#link_to_add_hours_<?=$sprint['ID'];?>").show();
}

function showHoursForm_<?=$sprint['ID'];?>()
{
   $("#hours_new_item_<?=$sprint['ID'];?>").show();
   $("#link_to_add_hours_<?=$sprint['ID'];?>").hide();
   $("#hours_new_item_<?=$sprint['ID'];?>").children('form').get(0).reset();
   $("#hours_new_item_<?=$sprint['ID'];?> form:not(.filter) :input:visible:enabled:first").focus();
}

function listSprint_<?=$sprint['ID'];?>()
{
   $.ajax({
      type: 'get',
      url: "<?=site_url('cp/story_sprints/index/'.$story_id.'/'.$sprint['ID']);?>",
      success: function(r) {
         $("#sprint_list_<?=$sprint['ID'];?>").html(r);
      }
   });
}

function showEditSprint_<?=$sprint['ID'];?>(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#sprint_list_<?=$sprint['ID'];?>").html(r);
      }
   });
}

function editSprint_<?=$sprint['ID'];?>(story_id, sprint_id, last_action)
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('cp/story_sprints/edit');?>"+'/'+story_id+'/'+sprint_id+'/'+last_action,
      data: $("#edit_sprint_item_<?=$sprint['ID'];?>").serialize(),
      success: function(r) {
         $("#sprint_list_<?=$sprint['ID'];?>").html(r);
      }
   });
}

function deleteSprint_<?=$sprint['ID'];?>(url)
{
  if(confirm(" Are you sure you want to delete this sprint? "))
  {
     $.ajax({
        type: 'get',
        url: url,
        success: function(r) {
           $("#sprint_list_<?=$sprint['ID'];?>").html(r);
           $("#hours_list_<?=$sprint['ID'];?>").hide();
           $("#add_item_<?=$sprint['ID'];?>").hide();
        }
   });
  }
}

<?php endforeach; ?>

//]]>
</script>

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

   <a class="admin" href="<?=site_url('cp/stories/delete/'.$story_id.'/'.$last_action);?>">Delete this story</a> | <a class="admin" href="<?=site_url('cp/stories/index/'.$project_id);?>">Cancel</a>

               </div>

   <h1>Edit story</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<h1 style="margin-bottom:12px;"><?=$story['Description'];?></h1>

<form method="post" action="<?=site_url('cp/stories/edit/'.$story_id.'/'.$last_action);?>">

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

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('cp/stories/index/'.$project_id);?>">Cancel</a>
</div>

</form>


<h2>Sprints</h2>
<?php foreach ($sprints AS $sprint): ?>

<?php // begin ajax area -------------------------------------------- ?>

<?php $sprint_var = 'sprint_'.$sprint['ID']; ?>

<div class="sprint_list_<?=$sprint['ID'];?>" id="sprint_list_<?=$sprint['ID'];?>">
<?=$$sprint_var;?>
</div>

<?php $hours_var = 'hours_'.$sprint['ID']; ?>

<div class="hours_list_<?=$sprint['ID'];?>" id="hours_list_<?=$sprint['ID'];?>">
<?=$$hours_var;?>
</div>

   <?php $dateSpent_label = 'DateSpent'.$sprint['ID']; ?>
   <?php $dateSpent_error = 'DateSpent'.$sprint['ID'].'_error'; ?>
   <?php $hoursSpent_label = 'HoursSpent'.$sprint['ID']; ?>
   <?php $hoursSpent_error = 'HoursSpent'.$sprint['ID'].'_error'; ?>
   <?php $username_label = 'Username'.$sprint['ID']; ?>
   <?php $username_error = 'Username'.$sprint['ID'].'_error'; ?>
   <?php $capital_label = 'IsCapitalExpense'.$sprint['ID']; ?>
   <?php $capital_error = 'IsCapitalExpense'.$sprint['ID'].'_error'; ?>

<div class="add_item" id="add_item_<?=$sprint['ID'];?>" style="margin-bottom:18px;">

   <div class="widget list_widget item_wrapper" id="hours_new_item_<?=$sprint['ID'];?>" style="display: none">

<form id="new_hours_item_<?=$sprint['ID'];?>" onsubmit="return false;">

<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="<?=$dateSpent_label;?>">Date Spent:</label></dt>
      <dd><?=form_input(array('name'=>$dateSpent_label, 'id'=>$dateSpent_label, 'maxlength'=>'20', 'size'=>'45', 'value'=>$this->validation->$dateSpent_label));?>
      <?=$this->validation->$dateSpent_error;?></dd>

      <dt><label for="<?=$hoursSpent_label;?>">Hours Spent:</label></dt>
      <dd><?=form_input(array('name'=>$hoursSpent_label, 'id'=>$hoursSpent_label, 'maxlength'=>'20', 'size'=>'10', 'value'=>$this->validation->$hoursSpent_label));?>
      <?=$this->validation->$hoursSpent_error;?></dd>

      <dt><label for="<?=$username_label;?>">User:</label></dt>
      <dd><?=form_input(array('name'=>$username_label, 'id'=>$username_label, 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->$username_label));?>
      <?=$this->validation->$username_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="<?=$capital_label;?>" id="<?=$capital_label;?>" value="1" <?=$this->validation->set_checkbox($capital_label, '1');?> \>  These hours can be capitalized.
      <?=$this->validation->$capital_error;?></dd>
   </dl>
</div>

    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add hours', 'onclick'=>'addHours_'.$sprint['ID'].'();'))?> or <a class="admin" href="#" onclick="hideHoursForm_<?=$sprint['ID'];?>(); return false;">I'm done adding hours</a>

</form>

   </div>

   <div id="link_to_add_hours_<?=$sprint['ID'];?>" class="link_to_add_child">
      <a class="admin" href="#" id="hours_new_item_link_<?=$sprint['ID'];?>" onclick="showHoursForm_<?=$sprint['ID'];?>(); return false;">Add hours</a>
   </div>

</div>

<?php // end ajax area -------------------------------------------- ?>

<?php endforeach; ?>


<h2>Add a new sprint</h2>

<form method="post" action="<?=site_url('cp/story_sprints/add/'.$story_id.'/'.$last_action);?>">

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
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add sprint'))?>
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