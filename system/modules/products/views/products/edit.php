<body>

<script type="text/javascript">
<!--
function dodelete()
{
   if (confirm(" Are you sure you want to delete this product? "))
   {
      document.location = "<?=site_url('products/delete/'.$site_id.'/'.$product_id.'/'.$last_action);?>";
   }
}

function insertSESFilename ()
{
   target = document.getElementById("SESFilename");
   name = document.getElementById("ProductName").value;
   var lowername = name.toLowerCase();
   var ampersands = lowername.replace(/\&/g, "and");
   var commas = ampersands.replace(/\,/g, "");
   var hyphens = commas.replace(/ /g, "-");
   target.value = hyphens;
}

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

function showSmartBenefitsList()
{
   $('#link_for_benefits_list').hide();
   $('#link_to_close_benefits_list').show();
   $('#smart_benefit_list').show();
}

function hideSmartBenefitsList()
{
   $('#link_for_benefits_list').show();
   $('#link_to_close_benefits_list').hide();
   $('#smart_benefit_list').hide();
}

function autoEnterBenefit(benefit)
{
   $('#SmartBenefits').val(benefit);
   $('#link_for_benefits_list').show();
   $('#link_to_close_benefits_list').hide();
   $('#smart_benefit_list').hide();
}

function showTechnicalDetails()
{
   $('#link_to_show_technical_details').hide();
   $('#link_to_hide_technical_details').show();
   $('#technical_details_block').show();
}

function hideTechnicalDetails()
{
   $('#link_to_show_technical_details').show();
   $('#link_to_hide_technical_details').hide();
   $('#technical_details_block').hide();
}

function showProductBenefits()
{
   $('#link_to_show_product_benefits').hide();
   $('#link_to_hide_product_benefits').show();
   $('#product_benefits_block').show();
}

function hideProductBenefits()
{
   $('#link_to_show_product_benefits').show();
   $('#link_to_hide_product_benefits').hide();
   $('#product_benefits_block').hide();
}

function showProductFeatures()
{
   $('#link_to_show_product_features').hide();
   $('#link_to_hide_product_features').show();
   $('#product_features_block').show();
}

function hideProductFeatures()
{
   $('#link_to_show_product_features').show();
   $('#link_to_hide_product_features').hide();
   $('#product_features_block').hide();
}

//-->
</script>


<?=$this->load->view('tabs');?>

<div id="Wrapper">

<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

   <?php if ($admin['group'] == 'admin'): ?><a class="admin" href="#" onclick="dodelete()">Delete Product</a> <span class="pipe">|</span><?php endif; ?><a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a>

               </div>

   <h1 id="top"><strong>Product Info</strong><span> | <a href="<?=site_url('nleas/edit/'.$site_id.'/'.$product_id.'/'.$last_action);?>">Nutrition Facts</a> | <a href="<?=site_url('categories/assign/'.$site_id.'/'.$product_id.'/'.$last_action);?>">Categories</a></span>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<h1 style="margin-bottom:12px;"><?=$product['ProductName'];?></h1>

<form id="productForm" method="post" action="<?=site_url('products/edit/'.$site_id.'/'.$product_id.'/'.$last_action);?>">

<h2 id="basic_information">Basic Information</h2>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="ProductName">Product Name:</label></dt>
      <dd><?=form_input(array('name'=>'ProductName', 'id'=>'ProductName', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->ProductName));?>
      <?=$this->validation->ProductName_error;?></dd>

      <dt><label for="SESFilename">SES Filename:</label></dt>
      <dd><?=form_input(array('name'=>'SESFilename', 'id'=>'SESFilename', 'maxlength'=>'200', 'size'=>'45', 'value'=>$this->validation->SESFilename));?> <a href="#" onclick="insertSESFilename(); return false;">auto enter</a>
      <?=$this->validation->SESFilename_error;?></dd>

      <dt><label for="UPC">UPC:</label></dt>
      <dd><?=form_input(array('name'=>'UPC', 'id'=>'UPC', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->UPC));?>
      <?=$this->validation->UPC_error;?></dd>

      <dt><label for="PackageSize">Package Size:</label></dt>
      <dd><?=form_input(array('name'=>'PackageSize', 'id'=>'PackageSize', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->PackageSize));?>
      <?=$this->validation->PackageSize_error;?></dd>

      <dt><label for="AvailableIn">Available In:</label></dt>
      <dd><?=form_input(array('name'=>'AvailableIn', 'id'=>'AvailableIn', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->AvailableIn));?>
      <?=$this->validation->AvailableIn_error;?></dd>

      <dt><label for="LongDescription">Long Description:</label></dt>
      <dd><?=form_textarea(array('name'=>'LongDescription', 'id'=>'LongDescription', 'cols' => 50, 'rows' => 12, 'value'=>$this->validation->LongDescription, 'class'=>'box'));?>
      <?=$this->validation->LongDescription_error;?></dd>

      <dt><label for="Teaser">Teaser:</label></dt>
      <dd><?=form_textarea(array('name'=>'Teaser', 'id'=>'Teaser', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Teaser, 'class'=>'box'));?>
      <?=$this->validation->Teaser_error;?></dd>

      <dt><label for="Footnotes">Footnotes:</label></dt>
      <dd><?=form_textarea(array('name'=>'Footnotes', 'id'=>'Footnotes', 'cols' => 50, 'rows' => 3, 'value'=>$this->validation->Footnotes, 'class'=>'box'));?>
      <?=$this->validation->Footnotes_error;?></dd>

      <dt><label for="Ingredients">Ingredients:
      <br /><br />convert to...
      <br /><a href="#" onclick="ingredientsToLowerCase(); return false;">lower case</a>
      <br /><a href="#" onclick="ingredientsToUpperCase(); return false;">upper case</a></label></dt>
      <dd><?=form_textarea(array('name'=>'Ingredients', 'id'=>'Ingredients', 'cols' => 50, 'rows' => 12, 'value'=>$this->validation->Ingredients, 'class'=>'box'));?>
      <?=$this->validation->Ingredients_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>


<h2 id="technical_details">Technical Details <span id="link_to_show_technical_details" style="display:none;"><a href="#" onclick="showTechnicalDetails(); return false;">show</a></span><span id="link_to_hide_technical_details"><a href="#" onclick="hideTechnicalDetails(); return false;">hide</a></span></h2>
<div id="technical_details_block">
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="ProductSites">Sites:</label></dt>
      <dd><div id="product_site_list"></div></dd>

<?php
$params = '';
if (count($product_sites) > 0)
{
   foreach ($product_sites as $site)
   {
      $site_name = str_replace ("\r", '', $site);
      if (strlen($params) > 0)
      {
         $params .= ',';
      }
      $params .= "'$site_name'";
   }
}
?>

<script type="text/javascript">
<!--
var site_names = new Array (<?=$params;?>);
var site_list = new SiteList ('product_site_list', '<?=$this->validation->ProductID;?>', site_names, 'site_list', '<?=site_url('sites/add');?>', '<?=site_url('sites/remove');?>');
//-->
</script>

      <dt><label for="ProductID">Product ID:</label></dt>
      <dd><p style="font-size:12px; padding:3px;"><?=$this->validation->ProductID;?></p></dd>

      <dt><label for="Language">Language:</label></dt>
      <dd><?=form_dropdown('Language', $languages, $this->validation->Language);?>
      <?=$this->validation->Language_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="FlagAsNew" id="FlagAsNew" value="1" <?=$this->validation->set_checkbox('FlagAsNew', '1');?> />  <span style="font-size:11px;">Flag this product as new.</span>
      <?=$this->validation->FlagAsNew_error;?></dd>

      <dt><label for="ProductGroup">Product Group:</label></dt>
      <dd><?=form_input(array('name'=>'ProductGroup', 'id'=>'ProductGroup', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->ProductGroup));?>
      <?=$this->validation->ProductGroup_error;?></dd>

      <dt><label for="NutritionFacts">Nutrition Facts:</label></dt>
      <dd><?=form_input(array('name'=>'NutritionFacts', 'id'=>'NutritionFacts', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->NutritionFacts));?>
      <?=$this->validation->NutritionFacts_error;?></dd>

      <dt><label for="StoreSection">Store Category ID:</label></dt>
      <dd><?=form_input(array('name'=>'StoreSection', 'id'=>'StoreSection', 'maxlength'=>'11', 'size'=>'20', 'value'=>$this->validation->StoreSection));?>
      <?=$this->validation->StoreSection_error;?></dd>

      <dt><label for="StoreDetail">Store Product ID:</label></dt>
      <dd><?=form_input(array('name'=>'StoreDetail', 'id'=>'StoreDetail', 'maxlength'=>'11', 'size'=>'20', 'value'=>$this->validation->StoreDetail));?>
      <?=$this->validation->StoreDetail_error;?></dd>

      <dt><label for="StoreSectionPostfix">Store Link:</label></dt>
      <dd><?=form_textarea(array('name'=>'StoreSectionPostfix', 'id'=>'StoreSectionPostfix', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->StoreSectionPostfix, 'class'=>'box'));?>
      <?=$this->validation->StoreSectionPostfix_error;?></dd>

      <dt><label for="LocatorCode">Locator Code:</label></dt>
      <dd><?=form_input(array('name'=>'LocatorCode', 'id'=>'LocatorCode', 'maxlength'=>'10', 'size'=>'20', 'value'=>$this->validation->LocatorCode));?>
      <?=$this->validation->LocatorCode_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>
</div> <?php /* technical_details_block */ ?>

<h2 id="product_benefits">Product Benefits <span id="link_to_show_product_benefits"><a href="#" onclick="showProductBenefits(); return false;">show</a></span><span id="link_to_hide_product_benefits" style="display:none;"><a href="#" onclick="hideProductBenefits(); return false;">hide</a></span></h2>
<div id="product_benefits_block" style="display:none;">
<p class="blockintro">You can enter the product benefits as a simple text field, or use the Smart Benefits and Nutrition Scorecard fields.</p>
<div class="block">
   <dl>
      <dt><label for="BenefitsDisplay">Display Options:</label></dt>
      <dd><?=form_dropdown('BenefitsDisplay', $benefit_displays, $this->validation->BenefitsDisplay);?>
      <?=$this->validation->BenefitsDisplay_error;?></dd>

      <dt><label for="Benefits">Benefits:</label></dt>
      <dd><?=form_textarea(array('name'=>'Benefits', 'id'=>'Benefits', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Benefits, 'class'=>'box'));?>
      <?=$this->validation->Benefits_error;?></dd>

      <dt><label for="SmartBenefits">Smart Benefits:</label></dt>
      <dd><?=form_input(array('name'=>'SmartBenefits', 'id'=>'SmartBenefits', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->SmartBenefits));?> <span id="link_for_benefits_list"><a href="#" onclick="showSmartBenefitsList(); return false;">select from list</a></span><span id="link_to_close_benefits_list" style="display:none;"><a href="#" onclick="hideSmartBenefitsList(); return false;">hide list</a></span>
      <?=$this->validation->SmartBenefits_error;?></dd>
      
      <div id="smart_benefit_list" style="margin-left:12px; display:none;">
      <dt style="height:4em;">&nbsp;</dt>
      <dd style="border-bottom:1px solid #999; padding-bottom:9px; margin-bottom:9px;"><a href="#" onclick="autoEnterBenefit('Great in Recipes'); return false;">Great in Recipes</a>
      <br /><a href="#" onclick="autoEnterBenefit('No Added Hormones'); return false;">No Added Hormones</a>
      <br /><a href="#" onclick="autoEnterBenefit('No Need to Add Water'); return false;">No Need to Add Water</a>
      <br /><a href="#" onclick="autoEnterBenefit('No GMOs'); return false;">No GMOs</a></dd>
      </div>

      <dt>Nutrition Scorecard:</dt>
      <dd><input type="checkbox" name="NSSodium" id="NSSodium" value="1" <?=$this->validation->set_checkbox('NSSodium', '1');?> />  <span style="font-size:11px;"><strong>Sodium</strong> (<?=$nlea['SODQ'];?><span class="pipe">|</span><?=$nlea['SODP'];?>%)</span>
      <?=$this->validation->NSSodium_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NSFat" id="NSFat" value="1" <?=$this->validation->set_checkbox('NSFat', '1');?> />  <span style="font-size:11px;"><strong>Fat</strong> (<?=$nlea['TFATQ'];?><span class="pipe">|</span><?=$nlea['TFATP'];?>%)</span>
      <?=$this->validation->NSFat_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NSFiber" id="NSFiber" value="1" <?=$this->validation->set_checkbox('NSFiber', '1');?> />  <span style="font-size:11px;"><strong>Fiber</strong> (<?=$nlea['DFIBQ'];?><span class="pipe">|</span><?=$nlea['DFIBP'];?>%)</span>
      <?=$this->validation->NSFiber_error;?></dd>
      
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NSAntioxidants" id="NSAntioxidants" value="1" <?=$this->validation->set_checkbox('NSAntioxidants', '1');?> />  <span style="font-size:11px;"><strong>Antioxidants:</strong> <?=form_dropdown('NSAntioxidantChoice', $antioxidants, $this->validation->NSAntioxidantChoice);?></span>
      <?=$this->validation->NSAntioxidants_error;?>
      <?=$this->validation->NSAntioxidantChoice_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NSCalories" id="NSCalories" value="1" <?=$this->validation->set_checkbox('NSCalories', '1');?> />  <span style="font-size:11px;"><strong>Calories</strong> (<?=$nlea['CAL'];?>)</span>
      <?=$this->validation->NSCalories_error;?></dd>

      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="NSOther" id="NSOther" value="1" <?=$this->validation->set_checkbox('NSOther', '1');?> />  <span style="font-size:11px;"><strong>Other:</strong> <?=form_input(array('name'=>'NSOtherChoice', 'id'=>'NSOtherChoice', 'maxlength'=>'64', 'size'=>'25', 'value'=>$this->validation->NSOtherChoice));?></span>  <span style="font-size:11px;"><strong>Qty:</strong> <?=form_input(array('name'=>'NSOtherQuantity', 'id'=>'NSOtherQuantity', 'maxlength'=>'10', 'size'=>'6', 'value'=>$this->validation->NSOtherQuantity));?></span>
      <?=$this->validation->NSOther_error;?>
      <?=$this->validation->NSOtherChoice_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>
</div>

<h2 id="product_features">Product Features <span id="link_to_show_product_features" style="display:none;"><a href="#" onclick="showProductFeatures(); return false;">show</a></span><span id="link_to_hide_product_features"><a href="#" onclick="hideProductFeatures(); return false;">hide</a></span></h2>
<div id="product_features_block">
<div class="block">
   <dl>
      <dt><label for="KosherSymbol">Kosher Symbol:</label></dt>
      <dd>
      <select name="KosherSymbol">
      <option value="">-- Select a Kosher symbol --</option>
  <?php foreach ($kosher_symbols AS $symbol): ?>
      <optgroup style="width:300px; background:url('<?=$symbol['SymbolFile'];?>') no-repeat center right; height:<?=$symbol['SymbolHeight']+6;?>px;">
      <option style="border-top:1px solid #999; padding:3px 3px 0 3px; margin-top:3px;" value="<?=$symbol['SymbolID'];?>"<?php if ($symbol['SymbolID'] == $this->validation->KosherSymbol): ?> selected="selected"<?php endif; ?>><?=$symbol['SymbolAlt'];?></option>
      </optgroup>
  <?php endforeach; ?>
      </select>
      <?=$this->validation->KosherSymbol_error;?></dd>

      <dt><label for="OrganicSymbol">Organic Symbol:</label></dt>
      <dd>
      <select name="OrganicSymbol">
      <option value="">-- Select an Organic symbol --</option>
  <?php foreach ($organic_symbols AS $symbol): ?>
      <optgroup style="width:300px; background:url('<?=$symbol['SymbolFile'];?>') no-repeat center right; height:<?=$symbol['SymbolHeight']+6;?>px;">
      <option style="border-top:1px solid #999; padding:3px 3px 0 3px; margin-top:3px;" value="<?=$symbol['SymbolID'];?>"<?php if ($symbol['SymbolID'] == $this->validation->OrganicSymbol): ?> selected="selected"<?php endif; ?>><?=$symbol['SymbolAlt'];?></option>
      </optgroup>
  <?php endforeach; ?>
      </select>
      <?=$this->validation->OrganicSymbol_error;?></dd>

      <dt><label for="OrganicStatement">Organic Statement:</label></dt>
      <dd><?=form_textarea(array('name'=>'OrganicStatement', 'id'=>'OrganicStatement', 'cols' => 50, 'rows' => 3, 'value'=>$this->validation->OrganicStatement, 'class'=>'box'));?>
      <?=$this->validation->OrganicStatement_error;?></dd>

      <dt><label for="AllNatural">All Natural:</label></dt>
      <dd><?=form_textarea(array('name'=>'AllNatural', 'id'=>'AllNatural', 'cols' => 50, 'rows' => 3, 'value'=>$this->validation->AllNatural, 'class'=>'box'));?>
      <?=$this->validation->AllNatural_error;?></dd>

      <dt><label for="Gluten">Gluten:</label></dt>
      <dd><?=form_input(array('name'=>'Gluten', 'id'=>'Gluten', 'maxlength'=>'128', 'size'=>'45', 'value'=>$this->validation->Gluten));?>
      <?=$this->validation->Gluten_error;?></dd>

      <dt><label for="Alergens">Alergens:</label></dt>
      <dd><?=form_textarea(array('name'=>'Alergens', 'id'=>'Alergens', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Alergens, 'class'=>'box'));?>
      <?=$this->validation->Alergens_error;?></dd>

      <dt><label for="SpiceLevel">Spice Level:</label></dt>
      <dd><?=form_input(array('name'=>'SpiceLevel', 'id'=>'SpiceLevel', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->SpiceLevel));?>
      <?=$this->validation->SpiceLevel_error;?></dd>

      <dt><label for="FlavorDescriptor">Flavor Descriptor:</label></dt>
      <dd><?=form_textarea(array('name'=>'FlavorDescriptor', 'id'=>'FlavorDescriptor', 'cols' => 50, 'rows' => 3, 'value'=>$this->validation->FlavorDescriptor, 'class'=>'box'));?>
      <?=$this->validation->FlavorDescriptor_error;?></dd>
   </dl>
</div>

<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="CaffeineHeight">Caffeine Amount:</label></dt>
      <dd><?=form_input(array('name'=>'CaffeineHeight', 'id'=>'CaffeineHeight', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->CaffeineHeight));?>
      <?=$this->validation->CaffeineHeight_error;?> mg</dd>

      <dt><label for="CaffeineAlt">Caffeine Statement:</label></dt>
      <dd><?=form_input(array('name'=>'CaffeineAlt', 'id'=>'CaffeineAlt', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->CaffeineAlt));?>
      <?=$this->validation->CaffeineAlt_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>
</div>

<h2 id="supplement_details">Supplement Details</h2>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="NutritionBlend">Nutrition Blend:</label></dt>
      <dd><?=form_textarea(array('name'=>'NutritionBlend', 'id'=>'NutritionBlend', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->NutritionBlend, 'class'=>'box'));?>
      <?=$this->validation->NutritionBlend_error;?></dd>

      <dt><label for="Standardization">Standardization:</label></dt>
      <dd><?=form_input(array('name'=>'Standardization', 'id'=>'Standardization', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Standardization));?>
      <?=$this->validation->Standardization_error;?></dd>
   
      <dt><label for="Directions">Directions:</label></dt>
      <dd><?=form_textarea(array('name'=>'Directions', 'id'=>'Directions', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Directions, 'class'=>'box'));?>
      <?=$this->validation->Directions_error;?></dd>

      <dt><label for="Warning">Warning:</label></dt>
      <dd><?=form_textarea(array('name'=>'Warning', 'id'=>'Warning', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Warning, 'class'=>'box'));?>
      <?=$this->validation->Warning_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

<h2 id="packaging_images">Packaging Images</h2>
<div class="block">
   <dl>
      <dt><label for="ThumbFile">Thumbnail File:</label></dt>
      <dd><?=form_input(array('name'=>'ThumbFile', 'id'=>'ThumbFile', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->ThumbFile));?>
      <?=$this->validation->ThumbFile_error;?></dd>

      <dt><label for="ThumbWidth">Thumbnail Width:</label></dt>
      <dd><?=form_input(array('name'=>'ThumbWidth', 'id'=>'ThumbWidth', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->ThumbWidth));?>
      <?=$this->validation->ThumbWidth_error;?></dd>

      <dt><label for="ThumbHeight">Thumbnail Height:</label></dt>
      <dd><?=form_input(array('name'=>'ThumbHeight', 'id'=>'ThumbHeight', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->ThumbHeight));?>
      <?=$this->validation->ThumbHeight_error;?></dd>

      <dt><label for="ThumbAlt">Thumbnail Alt:</label></dt>
      <dd><?=form_input(array('name'=>'ThumbAlt', 'id'=>'ThumbAlt', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->ThumbAlt));?>
      <?=$this->validation->ThumbAlt_error;?></dd>
   </dl>
</div>

<div class="block">
   <dl>
      <dt><label for="SmallFile">Small File:</label></dt>
      <dd><?=form_input(array('name'=>'SmallFile', 'id'=>'SmallFile', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->SmallFile));?>
      <?=$this->validation->SmallFile_error;?></dd>

      <dt><label for="SmallWidth">Small Width:</label></dt>
      <dd><?=form_input(array('name'=>'SmallWidth', 'id'=>'SmallWidth', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->SmallWidth));?>
      <?=$this->validation->SmallWidth_error;?></dd>

      <dt><label for="SmallHeight">Small Height:</label></dt>
      <dd><?=form_input(array('name'=>'SmallHeight', 'id'=>'SmallHeight', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->SmallHeight));?>
      <?=$this->validation->SmallHeight_error;?></dd>

      <dt><label for="SmallAlt">Small Alt:</label></dt>
      <dd><?=form_input(array('name'=>'SmallAlt', 'id'=>'SmallAlt', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->SmallAlt));?>
      <?=$this->validation->SmallAlt_error;?></dd>
   </dl>
   <img src="http://resources.hcgweb.net/<?=$site_id;?>/products/small/<?=$this->validation->SmallFile;?>" />
</div>

<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="LargeFile">Large File:</label></dt>
      <dd><?=form_input(array('name'=>'LargeFile', 'id'=>'LargeFile', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->LargeFile));?>
      <?=$this->validation->LargeFile_error;?></dd>

      <dt><label for="LargeWidth">Large Width:</label></dt>
      <dd><?=form_input(array('name'=>'LargeWidth', 'id'=>'LargeWidth', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->LargeWidth));?>
      <?=$this->validation->LargeWidth_error;?></dd>

      <dt><label for="LargeHeight">Large Height:</label></dt>
      <dd><?=form_input(array('name'=>'LargeHeight', 'id'=>'LargeHeight', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->LargeHeight));?>
      <?=$this->validation->LargeHeight_error;?></dd>

      <dt><label for="LargeAlt">Large Alt:</label></dt>
      <dd><?=form_input(array('name'=>'LargeAlt', 'id'=>'LargeAlt', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->LargeAlt));?>
      <?=$this->validation->LargeAlt_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

<h2 id="featured_product">Featured Product</h2>
<p class="blockintro">This information is used sometimes to display a featured product on a site. Usually a select number of products are featured and the feature may be such that a special image is needed.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt>&nbsp;</dt>
      <dd><input type="checkbox" name="Featured" id="Featured" value="1" <?=$this->validation->set_checkbox('Featured', '1');?> /> <span style="font-size:11px;">Feature this product.</span>
      <?=$this->validation->Featured_error;?></dd>

      <dt><label for="FeatureFile">Feature Image File:</label></dt>
      <dd><?=form_input(array('name'=>'FeatureFile', 'id'=>'FeatureFile', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->FeatureFile));?>
      <?=$this->validation->FeatureFile_error;?></dd>

      <dt><label for="FeatureWidth">Feature Image Width:</label></dt>
      <dd><?=form_input(array('name'=>'FeatureWidth', 'id'=>'FeatureWidth', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->FeatureWidth));?>
      <?=$this->validation->FeatureWidth_error;?></dd>

      <dt><label for="FeatureHeight">Feature Image Height:</label></dt>
      <dd><?=form_input(array('name'=>'FeatureHeight', 'id'=>'FeatureHeight', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->FeatureHeight));?>
      <?=$this->validation->FeatureHeight_error;?></dd>

      <dt><label for="FeatureAlt">Feature Image Alt:</label></dt>
      <dd><?=form_input(array('name'=>'FeatureAlt', 'id'=>'FeatureAlt', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->FeatureAlt));?>
      <?=$this->validation->FeatureAlt_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

<h2 id="beauty_image">Beauty Image</h2>
<p class="blockintro">This is used sometimes for an image showing the prepared product rather than the product packaging.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="BeautyFile">Beauty File:</label></dt>
      <dd><?=form_input(array('name'=>'BeautyFile', 'id'=>'BeautyFile', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->BeautyFile));?>
      <?=$this->validation->BeautyFile_error;?></dd>

      <dt><label for="BeautyWidth">Beauty Width:</label></dt>
      <dd><?=form_input(array('name'=>'BeautyWidth', 'id'=>'BeautyWidth', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->BeautyWidth));?>
      <?=$this->validation->BeautyWidth_error;?></dd>

      <dt><label for="BeautyHeight">Beauty Height:</label></dt>
      <dd><?=form_input(array('name'=>'BeautyHeight', 'id'=>'BeautyHeight', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->BeautyHeight));?>
      <?=$this->validation->BeautyHeight_error;?></dd>

      <dt><label for="BeautyAlt">Beauty Alt:</label></dt>
      <dd><?=form_input(array('name'=>'BeautyAlt', 'id'=>'BeautyAlt', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->BeautyAlt));?>
      <?=$this->validation->BeautyAlt_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

<h2 id="meta_data">Meta Data</h2>
<p class="blockintro">These fields may still be used but will probably be replaced by the menu module.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="MetaTitle">Page Title:</label></dt>
      <dd><?=form_input(array('name'=>'MetaTitle', 'id'=>'MetaTitle', 'maxlength'=>'255', 'size'=>'30', 'value'=>$this->validation->MetaTitle));?>
      <?=$this->validation->MetaTitle_error;?></dd>

      <dt><label for="MetaDescription">Meta Description:</label></dt>
      <dd><?=form_textarea(array('name'=>'MetaDescription', 'id'=>'MetaDescription', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->MetaDescription, 'class'=>'box'));?>
      <?=$this->validation->MetaDescription_error;?></dd>

      <dt><label for="MetaKeywords">Meta Keywords:</label></dt>
      <dd><?=form_textarea(array('name'=>'MetaKeywords', 'id'=>'MetaKeywords', 'cols' => 50, 'rows' => 6, 'value'=>$this->validation->MetaKeywords, 'class'=>'box'));?>
      <?=$this->validation->MetaKeywords_error;?></dd>

      <dt><label for="MetaMisc">Meta Abstract:</label></dt>
      <dd><?=form_textarea(array('name'=>'MetaMisc', 'id'=>'MetaMisc', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->MetaMisc, 'class'=>'box'));?>
      <?=$this->validation->MetaMisc_error;?></dd>

      <dt><label for="MetaMisc">Meta Robots:</label></dt>
      <dd><?=form_textarea(array('name'=>'MetaRobots', 'id'=>'MetaRobots', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->MetaRobots, 'class'=>'box'));?>
      <?=$this->validation->MetaRobots_error;?></dd>
   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

<h2 id="seldom_used_info">Seldom Used Info</h2>
<p class="blockintro">These are fields that rarely need to be filled out. Many of these may be on the way out.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="Verified">Verified:</label></dt>
      <dd><?=form_input(array('name'=>'Verified', 'id'=>'Verified', 'maxlength'=>'128', 'size'=>'25', 'value'=>$this->validation->Verified));?>
      <?=$this->validation->Verified_error;?></dd>

      <dt><label for="SortOrder">Sort Order:</label></dt>
      <dd><?=form_input(array('name'=>'SortOrder', 'id'=>'SortOrder', 'maxlength'=>'11', 'size'=>'6', 'value'=>$this->validation->SortOrder));?>
      <?=$this->validation->SortOrder_error;?></dd>

      <dt><label for="FilterID">Filter ID:</label></dt>
      <dd><?=form_input(array('name'=>'FilterID', 'id'=>'FilterID', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->FilterID));?>
      <?=$this->validation->FilterID_error;?></dd>

      <dt><label for="Components">Components:</label></dt>
      <dd><?=form_input(array('name'=>'Components', 'id'=>'Components', 'maxlength'=>'11', 'size'=>'20', 'value'=>$this->validation->Components));?>
      <?=$this->validation->Components_error;?></dd>

      <dt><label for="ProductType">Product Type:</label></dt>
      <dd><?=form_input(array('name'=>'ProductType', 'id'=>'ProductType', 'maxlength'=>'20', 'size'=>'30', 'value'=>$this->validation->ProductType));?>
      <?=$this->validation->ProductType_error;?></dd>

      <dt><label for="MenuSubsection">Menu Subsection:</label></dt>
      <dd><?=form_input(array('name'=>'MenuSubsection', 'id'=>'MenuSubsection', 'maxlength'=>'60', 'size'=>'30', 'value'=>$this->validation->MenuSubsection));?>
      <?=$this->validation->MenuSubsection_error;?></dd>

   </dl>
</div>

<div class="action" style="border-top:0; margin:0; padding:0;">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

<h2 id="status_information">Status Information</h2>
<div class="block">
   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>

      <dt><label for="DiscontinueDate">Discontinue Date:</label></dt>
      <dd><?=form_input(array('name'=>'DiscontinueDate', 'id'=>'DiscontinueDate', 'maxlength'=>'11', 'size'=>'20', 'value'=>$this->validation->DiscontinueDate));?>
      <?=$this->validation->DiscontinueDate_error;?></dd>

      <dt><label for="Replacements">Replacements:</label></dt>
      <dd><?=form_textarea(array('name'=>'Replacements', 'id'=>'Replacements', 'cols' => 50, 'rows' => 4, 'value'=>$this->validation->Replacements, 'class'=>'box'));?>
      <?=$this->validation->Replacements_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('products/index/'.$site_id);?>">Cancel</a> | <a class="admin" href="#top">Top</a>
</div>

</form>

               </div> <?php /* basic-form */ ?>
   
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
         
            <a href="<?=site_url('products/copy/'.$site_id.'/'.$product_id.'/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_copy_product.gif" width="138" height="31" alt="Copy this product" style="border:0px; margin-top:4px;" /></a>

            <div class="indent">
  
            <h2>Jump to...</h2>
            <ul style="font-size:12px; line-height:1.4em;">
            <li><a href="#basic_information">Basic Information</a></li>
            <li><a href="#technical_details">Technical Details</a></li>
            <li><a href="#product_benefits">Product Benefits</a></li>
            <li><a href="#product_features">Product Features</a></li>
            <li><a href="#supplement_details">Supplement Details</a></li>
            <li><a href="#packaging_images">Packaging Images</a></li>
            <li><a href="#featured_product">Featured Product</a></li>
            <li><a href="#beauty_image">Beauty Image</a></li>
            <li><a href="#meta_data">Meta Data</a></li>
            <li><a href="#seldom_used_info">Seldom Used Info</a></li>
            <li><a href="#status_information">Status Information</a></li>
            </ul>
            
            <h2>Parsed Ingredients</h2>
            <p>To force a break, insert a pipe character (|); to indicate a non-breaking comma, follow it with an exclamation point (,!).
            <ul style="font-size:12px; line-height:1.4em;">
            <?php foreach ($ingredient_list AS $ingredient): ?>
               <?php if (! in_array($ingredient, array(',','(',')','[',']','?','!',';',':','.','contains','and'))): ?>
            <li><?=$ingredient;?></li>
               <?php endif; ?>
            <?php endforeach; ?>
            </ul>

            </div>   <?php /* indent */ ?>

         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>


</body>