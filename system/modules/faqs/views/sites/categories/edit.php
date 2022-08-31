<body>

<script type="text/javascript">
//<![CDATA[

function dodelete()
{
  if(confirm(" Are you sure you want to delete this FAQ category? "))
  {
    document.location = "<?=site_url('sites/categories/delete/'.$category_id.'/'.$last_action);?>";
  }
}

//]]>
</script>

<?=$this->load->view('sites/tabs');?>

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

   <a class="admin" href="#" onclick="dodelete()">Delete FAQ Category</a> <span class="pipe">|</span> 
   <a class="admin" href="<?=site_url('sites/categories/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Edit an FAQ Category</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('sites/categories/edit/'.$site_id.'/'.$category_id.'/'.$last_action);?>">

<p class="blockintro">First, choose a short string ID for the FAQ category. This string is limited to characters, numbers and underlines or dashes. Be careful about changing this as it may be referenced in site pages and it could break how some of the pages work.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="FaqCode">Category Code:</label></dt>
      <dd><?=form_input(array('name'=>'FaqCode', 'id'=>'FaqCode', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->FaqCode));?>
      <?=$this->validation->FaqCode_error;?></dd>
   </dl>
</div>

<p class="blockintro">Now, give us the basic category information:</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Name">Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->Name));?>
      <?=$this->validation->Name_error;?></dd>

      <dt>Description:</dt>
      <dd><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 50, 'rows' => 8, 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>
   </dl>
</div>

<p class="blockintro">Technical details:</p>
<div class="block">
   <dl>
      <dt><label for="ID">Category ID:</label></dt>
      <dd><p style="font-size:12px; padding:3px;"><?=$this->validation->ID;?></p></dd>

      <dt><label for="ParentID">Parent ID:</label></dt>
      <dd><?=form_dropdown('ParentID', $parents, $this->validation->ParentID);?>
      <?=$this->validation->ParentID_error;?></dd>

      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('sites/categories/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy; 2009 The Hain Celestial Group, Inc.

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