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

   <a class="admin" href="<?=site_url('cp/projects/index');?>">Cancel</a>

               </div>
               
   <h1>Add a Project</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/projects/add/'.$last_action);?>/">

<h2>Project basics</h2>
<p class="blockintro">The group and project name appear at the top of every page. The group indicates the website or web system affected.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="GroupName">Group Name:</label></dt>
      <dd><?=form_input(array('name'=>'GroupName', 'id'=>'GroupName', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->GroupName));?>
      <?=$this->validation->GroupName_error;?></dd>
   </dl>

   <dl>
      <dt class="required"><label for="ProjectName">Project Name:</label></dt>
      <dd><?=form_input(array('name'=>'ProjectName', 'id'=>'ProjectName', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->ProjectName));?>
      <?=$this->validation->ProjectName_error;?></dd>
   </dl>

   <dl>
      <dt><label for="Description">Project description:</label></dt>
      <dd><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>
   </dl>

   <dl>
      <dt><label for="RequestedDueDate">Requested Due Date:</label></dt>
      <dd><?=form_input(array('name'=>'RequestedDueDate', 'id'=>'RequestedDueDate', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->RequestedDueDate));?>
      <?=$this->validation->RequestedDueDate_error;?></dd>
   </dl>

   <dl>
      <dt><label for="DueDateNotes">Due Date Notes:</label></dt>
      <dd><?=form_textarea(array('name'=>'DueDateNotes', 'id'=>'DueDateNotes', 'cols' => 50, 'rows' => 2, 'value'=>$this->validation->DueDateNotes, 'class'=>'box'));?>
      <?=$this->validation->DueDateNotes_error;?></dd>
   </dl>
</div>

<!--
<h2><label for="ProjectTypeID">Project type</label><span style="font-size:80%; color:#F00;">*</span></h2>
<p class="blockintro">This determines what list the project is displayed in.</p>
<div class="block">
   <p><?=form_dropdown('ProjectTypeID', $project_types, $this->validation->ProjectTypeID);?>
   <?=$this->validation->ProjectTypeID_error;?></p>
</div>
-->

<?=form_hidden('ProjectTypeID', $this->validation->ProjectTypeID);?>

<!--
<h2><label for="Announcement">Overview page announcement</label></h2>
<p class="blockintro">Create an announcement that appears at the top of this project's Overview page. You can use this to describe the project, to make a special announcement, etc. Just enter the text below and check the "Yes, display this announcement" checkbox.</p>
<div class="block">
   <p><?=form_textarea(array('name'=>'Announcement', 'id'=>'Announcement', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Announcement, 'class'=>'box'));?>
   <?=$this->validation->Announcement_error;?></p>
   <p><input type="checkbox" name="ShowAnnouncement" id="ShowAnnouncement" value="1" <?=$this->validation->set_checkbox('ShowAnnouncement', '1');?> \>
   Yes, display this announcement on the overview page</p>
</div>

<h2><label for="StartPage">Start page</label></h2>
<p class="blockintro">This is the first page people see when they view this project.</p>
<div class="block">
   <p><?=form_dropdown('StartPage', $start_pages, $this->validation->StartPage);?>
   <?=$this->validation->StartPage_error;?></p>
</div>
-->

<h2>This project is...</h2>
<div class="block" id="statusoptions">
   <p><input type="radio" name="Status" id="Status" value="active" <?=$this->validation->set_radio('Status', 'active');?> \> <b>Active</b> &mdash; <span>An open project with incomplete tasks.</span></p>
   <p><input type="radio" name="Status" id="Status" value="archived" <?=$this->validation->set_radio('Status', 'archived');?> \> <b>Archived</b> &mdash; <span>A finished project with no incomplete tasks.</span></p>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this project'))?> or <a class="admin" href="/admin/projects.php/cp/projects/index">Cancel</a>
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
            
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>