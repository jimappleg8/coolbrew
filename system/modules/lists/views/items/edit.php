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

   <a class="admin" href="<?=site_url('items/delete/'.$item_id.'/'.$last_action);?>">Delete list item</a> <span class="pipe">|</span> 
   <a class="admin" href="<?=site_url('items/index/'.$site_id.'/'.$list_id);?>">Cancel</a>

               </div>
               
   <h1>Edit a List Item</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('items/edit/'.$site_id.'/'.$item_id.'/'.$last_action);?>">

<p class="blockintro">The Sort Key is a string that can be used to sort the list when it is output. The string can also be used as part of the output.</p>
<div class="block">
   <dl>
      <dt class="required"><label for="SortKey">Sort Key:</label></dt>
      <dd><?=form_input(array('name'=>'SortKey', 'id'=>'SortKey', 'maxlength'=>'155', 'size'=>'30', 'value'=>$this->validation->SortKey));?>
      <?=$this->validation->SortKey_error;?></dd>
   </dl>
</div>

<h2>Content</h2>
<div class="block">
      <?=form_textarea(array('name'=>'Content', 'id'=>'Content', 'cols'=>'80', 'rows'=>'16', 'value'=>$this->validation->Content));?>
      <?=$this->validation->Content_error;?></dd>
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="IsHTML" id="IsHTML" value="1" <?=$this->validation->set_checkbox('IsHTML', '1');?> \><label for="IsHTML">  Contents are HTML.</label>
      <?=$this->validation->IsHTML_error;?></dd>
   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('items/index/'.$site_id.'/'.$list_id);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           Some footer text

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