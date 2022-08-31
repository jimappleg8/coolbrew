<?php
if (SERVER_LEVEL == 'local')
{
   $base_url = 'http://resources-hcgweb:8888/';
}
else
{
   $base_url = 'http://resources.hcgweb.hcgweb.net/';
}
?>
<body>

<?php if ($recipe['ImageFile'] == ''): ?>

<?php if ($recipes['error'] != ''): ?><p>This is an error: <?=$recipes['error'];?></p><?php endif; ?>

<?=form_open_multipart('uploads/upload_image/'.$site_id.'/'.$recipe_id);?>

<input type="file" name="ImageFile" size="20" style="margin-top:3px;" />
<input type="submit" value="Upload" style="margin-top:4px;" />
<br /><span style="color:#999; font-size:0.95em; font-family:sans-serif;">Maximum filesize: <?=$config['max_size'];?>K
<br />Allowed file types: <i><?=str_replace('|', ' ', $config['allowed_types']);?></i>
<br />Maximum width: <?=$config['max_width'];?> pixels
<br />Maximum height: <?=$config['max_height'];?> pixels</span>

</form>

<?php else: ?>

<?php
if ($recipe['ImageWidth'] >= $recipe['ImageHeight'])
{
   $width_height = 'width="150"';
}
else
{
   $width_height = 'height="150"';
}
?>

<div style="float:left; padding-right:10px; margin-top:9px; border-right:1px solid #999; margin-right:10px;">
<img src="<?=$base_url;?><?=$recipe['ImageFile'];?>" <?=$width_height;?> alt="<?=$recipe['Title'];?>" />
</div>

<form>
<input type="button" onclick="document.location='<?=site_url('uploads/upload_image/'.$site_id.'/'.$recipe_id.'/1');?>'" value="Remove" style="margin-top:12px;" />
</form>


<?php endif; ?>

</body>