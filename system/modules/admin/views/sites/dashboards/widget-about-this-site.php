<?php
if ($site['AboutThisSite'] == '')
{
   $site['AboutThisSite'] = '';
}
?>
<div id="about-this-site">
<h2 style="padding-bottom:3px; border-bottom:1px solid #999;">About This Site<?php if($admin['group'] == 'admin'): ?> <a class="admin" href="<?=site_url('sites/settings/about/'.$site_id.'/'.$last_action);?>">Edit</a><?php endif;?></h2>
<?php if ($site['AboutThisSite'] == ''): ?>
   <div id="about-text" style="height:72px; text-align:center; padding-top:32px; background-color:#EEE;">
   No information has been entered.</br />
   <a href="<?=site_url('sites/settings/about/'.$site_id.'/'.$last_action);?>">Enter some information about this site.</a>
   </div>
<?php else: ?>
   <div id="about-text">
   <?=$site['AboutThisSite'];?>
   </div>
<?php endif; ?>
</div>
