<body>

<script type="text/javascript">
<!--
function dodelete()
{
   if (confirm(" Are you sure you want to permanently delete this recipe from all sites?\n\nThis cannot be undone. "))
   {
      document.location = "<?=site_url('recipes/delete/'.$site_id.'/'.$recipe_id);?>";
      
   }
}

function insertRecipeCode ()
{
   target = document.getElementById("SESFilename");
   name = document.getElementById("ProductName").value;
   var lowername = name.toLowerCase();
   var ampersands = lowername.replace(/\&/g, "and");
   var commas = ampersands.replace(/\,/g, "");
   var hyphens = commas.replace(/ /g, "-");
   target.value = hyphens;
}

function listIngredients()
{
   $.ajax({
      type: 'get',
      url: "<?=site_url('ingredients/index/'.$site_id.'/'.$recipe_id);?>",
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
}

function addIngredient()
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('ingredients/add/'.$site_id.'/'.$recipe_id);?>",
      data: $("#new_ingredient_item").serialize(),
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
   $('#ingredient_new_item').children('form').get(0).reset();
   $("#ingredient_new_item form:not(.filter) :input:visible:enabled:first").focus();
}

function showEditIngredient(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
}

function editIngredient(ingred, last_action)
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('ingredients/edit/'.$site_id);?>"+'/'+ingred+'/'+last_action,
      data: $("#edit_ingredient_item").serialize(),
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
}

function deleteIngredient(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
}

function moveIngredient(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
}

function processIngredient()
{
   $.ajax({
      type: 'post',
      url: "<?=site_url('ingredients/process/'.$site_id.'/'.$recipe_id);?>",
      data: $("#process_ingredient").serialize(),
      success: function(r) {
         $("#ingredient_list").html(r);
      }
   });
   $('#ingredient_process').hide();
   $('#link_to_add_ingredient').show();
}

function hideIngredientForm()
{
   $('#ingredient_new_item').hide();
   $('#link_to_add_ingredient').show();
}

function showIngredientForm()
{
   $('#ingredient_new_item').show();
   $('#link_to_add_ingredient').hide();
   $('#ingredient_new_item').children('form').get(0).reset();
   $('#ingredient_new_item form:not(.filter) :input:visible:enabled:first').focus();
}

function hideProcessForm()
{
   $('#ingredient_process').hide();
   $('#link_to_add_ingredient').show();
}

function showProcessForm()
{
   $('#ingredient_process').show();
   $('#link_to_add_ingredient').hide();
   $('#ingredient_process').children('form').get(0).reset();
   $('#ingredient_process form:not(.filter) :input:visible:enabled:first').focus();
}

function listProductsOne()
{
   $('#ProductOne').empty();
   mysiteid = $('select#ProductOneSiteID').val();
   $.ajax({
      type: 'post',
      url: "<?=site_url('ingredients/ajax_products/');?>"+'/'+mysiteid,
      success: function(r) {
         $("#ProductOne").html(r);
      }
   });
   if (mysiteid == '')
      $('#ProductOne').attr('disabled', 'disabled');
   else
      $('#ProductOne').removeAttr('disabled');
}

function listProductsTwo()
{
   $('#ProductTwo').empty();
   mysiteid = $('select#ProductTwoSiteID').val();
   $.ajax({
      type: 'post',
      url: "<?=site_url('ingredients/ajax_products/');?>"+'/'+mysiteid,
      success: function(r) {
         $("#ProductTwo").html(r);
      }
   });
   if (mysiteid == '')
      $('#ProductTwo').attr('disabled', 'disabled');
   else
      $('#ProductTwo').removeAttr('disabled');
}

function addSite()
{
   mysiteid = $('#Sites').val();
   $.ajax({
      type: 'post',
      url: "<?=site_url('sites/add/'.$site_id.'/'.$recipe_id);?>"+'/'+mysiteid,
      success: function(r) {
         $("#site_list").html(r);
      }
   });
}

function deleteSite(url)
{
   $.ajax({
      type: 'get',
      url: url,
      success: function(r) {
         $("#site_list").html(r);
      }
   });
}

function hideAddSiteForm()
{
   $('#site_new_item').hide();
   $('#link_to_add_site').show();
}

function showAddSiteForm()
{
   $('#site_new_item').show();
   $('#link_to_add_site').hide();
}


//-->
</script>


<?=$this->load->view('tabs');?>

<div id="Wrapper">

<?php if ($recipes['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$recipes['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

   <a class="admin" href="#" onclick="dodelete()">Delete this recipe</a> | 
   <a class="admin" href="<?=site_url('recipes/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><strong>Recipe Info</strong><span> | <a href="<?=site_url('nleas/edit/'.$site_id.'/'.$recipe_id.'/'.$last_action);?>">Nutrition Facts</a> | <a href="<?=site_url('categories/assign/'.$site_id.'/'.$recipe_id.'/'.$last_action);?>">Categories</a></span>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<h1 style="margin-bottom:12px;"><?=$recipe['Title'];?></h1>


<?php // begin ajax area -------------------------------------------- ?>

<h2>Ingredients</h2>

<div class="ingredient_list" id="ingredient_list">
<?=$ingredients;?>
</div>

<div class="add_item" style="margin-bottom:24px;">

   <div class="widget list_widget item_wrapper" id="ingredient_new_item" style="display: none">

<form id="new_ingredient_item" onsubmit="return false;">

<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="Quantity">Quantity:</label></dt>
      <dd><?=form_input(array('name'=>'Quantity', 'id'=>'Quantity', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Quantity));?>
      <?=$this->validation->Quantity_error;?></dd>

      <dt style="height:40px;"><label for="Name">Ingredient Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Name));?>
      <br />Use {prod1} and {prod2} to place product links in the name.
      <?=$this->validation->Name_error;?></dd>

      <dt><label for="ProductOne">Product 1 {prod1}:</label></dt>
      <dd><select name="ProductOne" style="width:350px;">
      <option value="">None</option>
<?php foreach ($cats AS $cat): ?>
   <?php if ( ! empty($cat['Products'])): ?>
   <optgroup label="<?=$cat['CategoryName'];?>">
      <?php foreach ($cat['Products'] AS $product): ?>
         <?php if ($this->validation->ProductOne == $product['ProductID']): ?>
      <option value="<?=$product['ProductID'];?>" selected="selected">	<?=$product['ProductName'];?></option>
	     <?php else: ?>
      <option value="<?=$product['ProductID'];?>"><?=$product['ProductName'];?></option>
	      <?php endif; ?>
      <?php endforeach; ?>
   </optgroup>
   <?php endif; ?>
<?php endforeach; ?>
</select>
<?=$this->validation->ProductOne_error;?></dd>

      <dt><label for="ProductTwo">Product 2 {prod2}:</label></dt>
      <dd><select name="ProductTwo" style="width:350px;">
      <option value="">None</option>
<?php foreach ($cats AS $cat): ?>
   <?php if ( ! empty($cat['Products'])): ?>
   <optgroup label="<?=$cat['CategoryName'];?>">
      <?php foreach ($cat['Products'] AS $product): ?>
         <?php if ($this->validation->ProductTwo == $product['ProductID']): ?>
      <option value="<?=$product['ProductID'];?>" selected="selected">	<?=$product['ProductName'];?></option>
	     <?php else: ?>
      <option value="<?=$product['ProductID'];?>"><?=$product['ProductName'];?></option>
	      <?php endif; ?>
      <?php endforeach; ?>
   </optgroup>
   <?php endif; ?>
<?php endforeach; ?>
</select>
<?=$this->validation->ProductOne_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="IsHeading" id="IsHeading" value="1" <?=$this->validation->set_checkbox('IsHeading', '1');?> \>  Treat this ingredient as a subheading.
      <?=$this->validation->IsHeading_error;?></dd>

   </dl>
</div>

    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add ingredient', 'onclick'=>'addIngredient();'))?> or <a class="admin" href="#" onclick="hideIngredientForm(); return false;">I'm done adding items</a>

</form>

   </div>

<?php // process ingredient list ?>

   <div class="widget list_widget item_wrapper" id="ingredient_process" style="display: none">

<form id="process_ingredient" onsubmit="return false;">

<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="Ingredients">Text:</label>
      <br />
      <br />Use the "|" character 
      <br />to separate the quantity
      <br />from the ingredient name
      <br />or the entire line will be 
      <br />put into the name field.</dt>
      <dd><?=form_textarea(array('name'=>'Ingredients', 'id'=>'Ingredients', 'cols' => 50, 'rows' => 12, 'value'=>$this->validation->Ingredients, 'class'=>'box'));?>
      <?=$this->validation->Ingredients_error;?></dd>
   </dl>
</div>

    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Process ingredients', 'onclick'=>'processIngredient();'))?> or <a class="admin" href="#" onclick="hideProcessForm(); return false;">I'm done processing ingredients</a>

</form>

   </div>

   <div id="link_to_add_ingredient" class="link_to_add_child">

      <a class="admin" href="#" id="ingredient_new_item_link" onclick="showIngredientForm(); return false;">Add an ingredient</a> or 
      <a class="admin" href="#" id="ingredient_process_link" onclick="showProcessForm(); return false;">Process an ingredient list</a>
   </div>


</div>

<?php // end ajax area -------------------------------------------- ?>


<h2>Other Recipe Information</h2>

<form id="recipeForm" method="post" action="<?=site_url('recipes/edit/'.$site_id.'/'.$recipe_id.'/'.$last_action);?>">

<p class="blockintro">This code will be used to create links, so it cannot contain special characters or spaces.</p>
<div class="block">
   <dl>
      <dt><label for="RecipeCode">Recipe Code:</label></dt>
      <dd><?=form_input(array('name'=>'RecipeCode', 'id'=>'RecipeCode', 'maxlength'=>'200', 'size'=>'45', 'value'=>$this->validation->RecipeCode));?> <a href="#" onclick="insertRecipeCode(); return false;">auto enter</a>
      <?=$this->validation->RecipeCode_error;?></dd>

   </dl>
</div>

<p class="blockintro">Basic Information</p>
<div class="block">
   <dl>
      <dt><label for="Title">Recipe Title:</label></dt>
      <dd><?=form_input(array('name'=>'Title', 'id'=>'Title', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Title));?>
      <?=$this->validation->Title_error;?></dd>

      <dt><label for="PrepTime">Prep Time:</label></dt>
      <dd><?=form_input(array('name'=>'PrepTime', 'id'=>'PrepTime', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->PrepTime));?>
      <?=$this->validation->PrepTime_error;?></dd>

      <dt><label for="CookTime">Cook Time:</label></dt>
      <dd><?=form_input(array('name'=>'CookTime', 'id'=>'CookTime', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->CookTime));?>
      <?=$this->validation->CookTime_error;?></dd>

      <dt><label for="Yield">Yield:</label></dt>
      <dd><?=form_input(array('name'=>'Yield', 'id'=>'Yield', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->Yield));?>
      <?=$this->validation->Yield_error;?></dd>

      <dt><label for="Description">Description:</label></dt>
      <dd><?=form_ckeditor(array('name'=>'Description', 'id'=>'Description', 'width'=>'410', 'height'=>'150', 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>

      <dt><label for="Citation">Citation:</label></dt>
      <dd><?=form_ckeditor(array('name'=>'Citation', 'id'=>'Citation', 'width'=>'410', 'height'=>'100', 'value'=>$this->validation->Citation, 'class'=>'box'));?>
      <?=$this->validation->Citation_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="Featured" id="Featured" value="1" <?=$this->validation->set_checkbox('Featured', '1');?> /> <label for="Featured">Feature this product.</label></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="FlagAsNew" id="FlagAsNew" value="1" <?=$this->validation->set_checkbox('FlagAsNew', '1');?> /> <label for="FlagAsNew">Flag as new.</label></dd>
   </dl>
</div>

<p class="blockintro">Cooking Instructions</p>
<div class="block">
   <dl>
      <dt><label for="Directions">Directions:</label></dt>
      <dd><?=form_ckeditor(array('name'=>'Directions', 'id'=>'Directions', 'width'=>'410', 'height'=>'250', 'value'=>$this->validation->Directions, 'class'=>'box'));?>
      <?=$this->validation->Directions_error;?></dd>

   </dl>
</div>

<p class="blockintro">Recipe image</p>
<div class="block">
   <dl>
      <dt><label for="RecipeImage">Recipe image:</label></dt>
      <dd><iframe id="upload_target" name="upload_target" src="<?=site_url('uploads/upload_image/'.$site_id.'/'.$recipe_id);?>" style="width:400px;height:150px;border:0px solid #fff;"></iframe></dd>

   </dl>
</div>

<p class="blockintro">The content of the title, ingredients, description and directions above will be indexed for the search, but if there are additional keywords you would like to associate with this recipe, enter them here. Separate keywords with commas.</p>
<div class="block">
   <dl>
      <dt><label for="Directions">Keywords:</label></dt>
      <dd><?=form_textarea(array('name'=>'Keywords', 'id'=>'Keywords', 'cols'=>'50', 'rows'=>'6', 'value'=>$this->validation->Keywords));?>
      <?=$this->validation->Keywords_error;?></dd>

   </dl>
</div>

<p class="blockintro">Technical Details</p>
<div class="block">
   <dl>
      <div class="site_list" id="site_list">
      <?=$sites;?>
      </div>

<?php // begin ajax area -------------------------------------------- ?>

<?php // add site ?>

<div style="margin:0 30px 0 160px ; padding:3px 6px 6px 12px; background-color:#CCC;">

   <div class="widget list_widget item_wrapper" id="site_new_item" style="display: none">

<div class="block" style="margin-bottom:0; background-color:transparent;">
   <dl>
      <dt style="width:60px;"><label for="Sites">Site:</label>
      <dd><?=form_dropdown('Sites', $site_list, $this->validation->Sites, 'id="Sites"');?>
      <?=$this->validation->Sites_error;?></dd>
   </dl>
</div>

    <button value="Add site" onclick="addSite(); return false;">Add site</button> or <a class="admin" href="#" onclick="hideAddSiteForm(); return false;">I'm done adding sites</a>

   </div>

   <div id="link_to_add_site" class="link_to_add_child">

      <a class="admin" href="#" id="site_new_item_link" onclick="showAddSiteForm(); return false;">Add a site</a>
   </div>

</div>

<?php // end ajax area -------------------------------------------- ?>

      <dt><label for="ID">Recipe ID:</label></dt>
      <dd><p style="font-size:12px; padding:3px;"><?=$this->validation->ID;?></p></dd>

      <dt><label for="Language">Language:</label></dt>
      <dd><?=form_dropdown('Language', $languages, $this->validation->Language);?>
      <?=$this->validation->Language_error;?></dd>

      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('recipes/index/'.$site_id);?>">Cancel</a>
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