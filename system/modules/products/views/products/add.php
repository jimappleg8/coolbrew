<body>

<script type="text/javascript">
<!--
function ingredientsToLowerCase ()
{
   target = document.getElementById("Ingredients");
   var ingred = target.value;
   var loweringred = ingred.toLowerCase();
   target.value = loweringred;
}
function ingredientsToUpperCase ()
{
   target = document.getElementById("Ingredients");
   var ingred = target.value;
   var loweringred = ingred.toUpperCase();
   target.value = loweringred;
}
//-->
</script>

<?=$this->load->view('tabs');?>

<?php if ($products['message'] != ''): ?>
<div id="message">
<p><?=$products['message'];?></p>
</div>
<?php endif; ?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top">New Product Start Page</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('products/add/'.$site_id.'/'.$last_action);?>">

<h2 id="basic_information">Get Started</h2>
<p class="blockintro">Fill in as many of these fields as you want to get started. The only required field is the product name.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="UPC">UPC:</label></dt>
      <dd><?=form_input(array('name'=>'UPC', 'id'=>'UPC', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->UPC));?>
      <?=$this->validation->UPC_error;?></dd>

      <dt><strong><label for="ProductName">Product Name:</label></strong></dt>
      <dd><?=form_input(array('name'=>'ProductName', 'id'=>'ProductName', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->ProductName));?>
      <?=$this->validation->ProductName_error;?></dd>

      <dt><label for="LongDescription">Long Description:</label></dt>
      <dd><?=form_textarea(array('name'=>'LongDescription', 'id'=>'LongDescription', 'cols' => 50, 'rows' => 12, 'value'=>$this->validation->LongDescription, 'class'=>'box'));?>
      <?=$this->validation->LongDescription_error;?></dd>

      <dt><label for="Ingredients">Ingredients:
      <br /><br />convert to...
      <br /><a href="#" onclick="ingredientsToLowerCase(); return false;">lower case</a>
      <br /><a href="#" onclick="ingredientsToUpperCase(); return false;">upper case</a></label></dt>
      <dd><?=form_textarea(array('name'=>'Ingredients', 'id'=>'Ingredients', 'cols' => 50, 'rows' => 12, 'value'=>$this->validation->Ingredients, 'class'=>'box'));?>
      <?=$this->validation->Ingredients_error;?></dd>
      
      <dt><label for="Language">Language:</label></dt>
      <dd><?=form_dropdown('Language', $languages, $this->validation->Language);?>
      <?=$this->validation->Language_error;?></dd>

      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>

   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save and continue'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a>
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