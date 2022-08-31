<div class="block" style="margin-bottom:0;">

   <div class="listing">
   <dl>
   <?php foreach($ingredients AS $ingred): ?>
      <?php if ($ingred['ID'] == $ingredient_id): ?>
<form id="edit_ingredient_item" onsubmit="return false;">

   <div style="margin:6px 0; border-top:1 px solid #000; border-bottom:1 px solid #000;">
      <dt><label for="Quantity">Quantity (e.g. 1 cup):</label></dt>
      <dd><?=form_input(array('name'=>'Quantity', 'id'=>'Quantity', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Quantity));?>
      <?=$this->validation->Quantity_error;?></dd>

      <dt style="height:40px;"><label for="Name">Ingredient Name:</label></dt>
      <dd><?=form_input(array('name'=>'Name', 'id'=>'Name', 'maxlength'=>'200', 'size'=>'45', 'value'=>$this->validation->Name));?>
      <br />Use {prod1} and {prod2} to place product links in the name.
      <br />You can also use the form {prod1}Link Text{/prod1}.
      <?=$this->validation->Name_error;?></dd>

      <dt><label for="ProductOneSiteID">Product 1 {prod1}:</label></dt>
      <dd><?=form_dropdown('ProductOneSiteID', $site_list, $this->validation->ProductOneSiteID, 'id="ProductOneSiteID" onchange="listProductsOne();"');?>
      <?=$this->validation->ProductOneSiteID_error;?></dd>

      <dt><label for="ProductOne">&nbsp;</label></dt>
      <dd><select id="ProductOne" name="ProductOne" <?php if ($this->validation->ProductOneSiteID == ''): ?>disabled="disabled" <?php endif; ?>style="width:350px;">
      <?=$product_one;?>
      </select>
      <?=$this->validation->ProductOne_error;?></dd>

      <dt><label for="ProductTwoSiteID">Product 2 {prod2}:</label></dt>
      <dd><?=form_dropdown('ProductTwoSiteID', $site_list, $this->validation->ProductTwoSiteID, 'id="ProductTwoSiteID" onchange="listProductsTwo();"');?>
      <?=$this->validation->ProductTwoSiteID_error;?></dd>

      <dt><label for="ProductTwo">&nbsp;</label></dt>
      <dd><select id="ProductTwo" name="ProductTwo" <?php if ($this->validation->ProductTwoSiteID == ''): ?>disabled="disabled" <?php endif; ?>style="width:350px;">
      <?=$product_two;?>
      </select>
      <?=$this->validation->ProductTwo_error;?></dd>


      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="IsHeading" id="IsHeading" value="1" <?=$this->validation->set_checkbox('IsHeading', '1');?> \>  Treat this ingredient as a subheading.
      <?=$this->validation->IsHeading_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes', 'onclick'=>"editIngredient('$ingredient_id', '$last_action');"))?> or <a class="admin" href="#" onclick="listIngredients(); return false;">Cancel</a></dd>
   </div>
   
</form>
      <?php else: ?>
   <?php if ($ingred['IsHeading'] == 1): ?><dt style="margin:11px 14px 5px 0; font-size:11px;"><?php else: ?><dt style="margin:0 14px 5px 0; font-size:11px;"><?php endif; ?><a href="<?=site_url('ingredients/move/'.$site_id.'/'.$ingred['ID'].'/up');?>" class="admin" onclick="moveIngredient(this.href); return false;">^</a> | <a href="<?=site_url('ingredients/move/'.$site_id.'/'.$ingred['ID'].'/dn');?>" class="admin" onclick="moveIngredient(this.href); return false;">v</a> | <a href="<?=site_url('ingredients/edit/'.$site_id.'/'.$ingred['ID'].'/'.$last_action);?>" class="admin" onclick="showEditIngredient(this.href); return false;">edit</a> | <a href="<?=site_url('ingredients/delete/'.$site_id.'/'.$ingred['ID']);?>" class="admin" onclick="deleteIngredient(this.href); return false;">delete</a></dt>
   <?php if ($ingred['IsHeading'] == 1): ?><dd style="font-size:14px; margin-top:11px; font-weight:bold;"><?php else: ?><dd style="font-size:11px;"><?php endif; ?><?=$ingred['Quantity'];?> 
   <?=$ingred['Name'];?>
   </dd>
      <?php endif; ?>
   <?php endforeach; ?>
   </dl>
   </div> <?php // listing ?>

</div>
