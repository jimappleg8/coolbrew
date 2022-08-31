<body>

<script type="text/javascript">
<!--
function insertSESFilename (name)
{
   target = document.getElementById("SESFilename");
   var lowername = name.toLowerCase();
   var ampersands = lowername.replace(/\&/g, "and");
   var commas = ampersands.replace(/\,/g, "");
   var hyphens = commas.replace(/ /g, "-");
   target.value = hyphens;
}
//-->
</script>

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

   <a class="admin" href="<?=site_url('ingredients/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><strong>Ingredient Info</strong></h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<h1 style="margin-bottom:12px;"><?=$ingredient['Ingredient'];?></h1>

<form id="ingredientForm" method="post" action="<?=site_url('ingredients/add/'.$site_id.'/'.$last_action);?>">

<h2 id="basic_information">Basic Information</h2>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="Ingredient">Ingredient Name:</label></dt>
	<dd>
		<?=form_input(array('name'=>'Ingredient', 'id'=>'Ingredient', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Ingredient));?>
		<?=$this->validation->Ingredient_error;?>
	</dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Continue'))?> or <a class="admin" href="<?=site_url('ingredients/index/'.$site_id);?>">Cancel</a>
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
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
