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

   <a class="admin" href="<?=site_url('cp/links/index');?>">Cancel</a>

               </div>
               
   <h1>Add a Link</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('cp/links/add/'.$last_action);?>">

<p class="blockintro">Give some information about what the link should look like.</p>
<div class="block">
   <p><strong><label for="Title">Title:</label></strong></p>
   <p><?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'255', 'size'=>'70', 'value'=>$this->validation->Title));?>
   <?=$this->validation->Title_error;?></p>
   <p>Description:</p>
   <p><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 60, 'rows' => 10, 'value'=>$this->validation->Description, 'class'=>'box'));?>
   <?=$this->validation->Description_error;?></p>
   <p><strong><label for="URL">URL:</label></strong></p>
   <p><?=form_input(array('name'=>'URL', 'id'=>'URL', 'maxlength'=>'255', 'size'=>'70', 'value'=>$this->validation->URL));?>
   <?=$this->validation->URL_error;?></p>
   <p class="Checkbox">
   <input type="checkbox" name="Dashboard" id="Dashboard" value="1" <?=$this->validation->set_checkbox('Dashboard', '1');?> \>  Display this link on the site's dashboard
   <?=$this->validation->Dashboard_error;?>
   </p>
   <p class="Checkbox">
   <input type="checkbox" name="AdminOnly" id="AdminOnly" value="1" <?=$this->validation->set_checkbox('AdminOnly', '1');?> \>  Restrict this link to administrative users only
   <?=$this->validation->AdminOnly_error;?>
   </p>
</div>

<p class="blockintro">When this link is clicked, where would you like the resulting page to display?</p>
<div class="block">
<p class="Radio">
<input type="radio" name="OpenWhere" id="OpenWhere" value="same" <?=$this->validation->set_radio('OpenWhere', 'same');?> \>
<strong>In the same window</strong><br />
<input type="radio" name="OpenWhere" id="OpenWhere" value="new" <?=$this->validation->set_radio('OpenWhere', 'new');?> \>
<strong>In a new window</strong><br />
<input type="radio" name="OpenWhere" id="OpenWhere" value="frame" <?=$this->validation->set_radio('OpenWhere', 'frame');?> \>
<strong>In a frame</strong>
</p>
</div>


<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this link'))?> or <a class="admin" href="<?=site_url('cp/links/index');?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
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