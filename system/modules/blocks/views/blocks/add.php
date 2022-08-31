<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('blocks/index/'.$site_id);?>">Cancel</a>

               </div>
               
   <h1>Add a Block</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('blocks/add/'.$site_id.'/'.$last_action);?>">

<p class="blockintro">The Name is a string that can be used to identify the block in the tag. The string can also be used as part of the output.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="Name">Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->Name));?>
      <?=$this->validation->Name_error;?></dd>

      <dt><label for="Language">Language:</label></dt>
      <dd><?=form_dropdown('Language', $languages, $this->validation->Language);?>
      <?=$this->validation->Language_error;?></dd>
   </dl>
</div>

<h2>Content</h2>
<div class="block">
   <?=form_fckeditor(array('name'=>'Block', 'id'=>'Block', 'toolbarset'=>'HCG', 'basepath'=>'/fckeditor/', 'width'=>'582', 'height'=>'240', 'value'=>$this->validation->Block));?>
   <?=$this->validation->Block_error;?>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add this block'))?> or <a class="admin" href="<?=site_url('blocks/index/'.$site_id);?>">Cancel</a>
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