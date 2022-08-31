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

<?php
	if (isset ($ingredient_message) && strlen ($ingredient_message) > 0)
		echo '<div id="message" style="text-align:center;">'.$ingredient_message.'</div>';
?>

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

<form id="ingredientForm" method="post" action="<?=site_url('ingredients/edit/'.$site_id.'/'.$ingredient_id.'/'.$last_action);?>">

<h2 id="basic_information">Basic Information</h2>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="Ingredient">Ingredient Name:</label></dt>
      <dd><?=form_input(array('name'=>'Ingredient', 'id'=>'Ingredient', 'maxlength'=>'255', 'size'=>'45', 'value'=>$this->validation->Ingredient));?>
      <?=$this->validation->Ingredient_error;?></dd>

      <dt><label for="AltName">Alternate Names:</label></dt>
      <dd><div id="alternate_names"></div></dd>

	<?php
		$params = '';
		if (count ($ingredient ['alternate_name']) > 0)
		{
			foreach ($ingredient ['alternate_name'] as $name)
			{
//				$alt_name = str_replace ("\n", '', $name);
				$alt_name = str_replace ("\r", '', $name);
				if (strlen ($params) > 0)
					$params .= ',';
				$params .= "'$alt_name'";
			}
		}
	?>
	<script type="text/javascript">
		var alt_name = new Array (<?php echo $params; ?>);
		var name_list = new AlternateNameList ('alternate_names', '<?=$ingredient_id;?>', alt_name, 'name_list', '<?=site_url ('ingredients/add_ingredient_name');?>', '<?=site_url ('ingredients/remove_ingredient_name');?>');
	</script>

      <dt><label for="LatinName">Latin Name:</label></dt>
      <dd><?=form_input(array('name'=>'LatinName', 'id'=>'LatinName', 'maxlength'=>'127', 'size'=>'30', 'value'=>$this->validation->LatinName));?>
      <?=$this->validation->LatinName_error;?></dd>

      <dt><label for="Description">Description:</label></dt>
      <dd><?=form_textarea(array('name'=>'Description', 'id'=>'Description', 'cols' => 50, 'rows' => 12, 'value'=>$this->validation->Description, 'class'=>'box'));?>
      <?=$this->validation->Description_error;?></dd>
   </dl>
</div>

<h2 id="status_information">Status Information</h2>
<div class="block">
   <dl>
      <dt><label for="Status">Status:</label></dt>
      <dd><?=form_dropdown('Status', $statuses, $this->validation->Status);?>
      <?=$this->validation->Status_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('ingredients/index/'.$site_id);?>">Cancel</a>
</div>

</form>

<?=form_open_multipart ('ingredients/upload_ingredient_image/'.$site_id.'/'.$ingredient_id.'/'.$last_action);?>

<h2 id="packaging_images">Ingredient Image</h2>
<div class="block">
   <dl>
      <dt><lable for="ImageFile">Image File:</label></dt>
      <dd>
<?php
	global $sites;
	$image_location = '';
	if (strlen ($this->validation->ImageFile) > 0)
	{
		foreach ($sites as $url => $site)
		{
			if ($site [0] == $site_id && $site [2] == 'dev')
			{
				$image_location = 'http://'.$url.'/images/ingredients/'.$this->validation->ImageFile;
				break;
			}
		}
	}
?>
		<div>
			<?php if (strlen ($image_location) > 0): ?>
				<img src="<?=$image_location;?>" width="<?= $this->validation->ImageWidth; ?>" height="<?= $this->validation->ImageHeight; ?>" <?=(strlen ($this->validation->ImageAlt) > 0 ? 'alt="'.$this->validation->ImageAlt.'"' : '');?> />
			<?php else: ?>
				There is no image for this ingredient.<br />
			<?php endif; ?>
			<input type="file" id="user_ingredient_image_file" name="userfile" size="30" /><br />
		</div>
      </dd>

      <dt><label for="ImageAlt">Image Alt:</label></dt>
      <dd><?=form_input(array('name'=>'ImageAlt', 'id'=>'ImageAlt', 'maxlength'=>'255', 'size'=>'50', 'value'=>$this->validation->ImageAlt));?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Upload image info'))?>
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
         
            <div class="indent">
  
&nbsp;            
            </div>   <?php // indent ?>

         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
