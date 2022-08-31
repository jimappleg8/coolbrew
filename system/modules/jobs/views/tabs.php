<?php $base = "/careers/admin.php"; ?>

<div id="job-hdrMenu">
Logged in as <?=$this->session->userdata('name');?> (<a href="<?=$base;?>/admin/logout">Log-out</a>) <span class="pipe">|</span> 
<a href="<?=$base;?>/admin/edit_account/<?=$last_action;?>/index">Edit your account</a>
</div>

<h1>Jobs Administration</h1>

<div id="job-menubar">
<ul>
<?php foreach ($tabs as $tab): ?>
<?php $style = ($tab['Position'] == "right") ? ' style="float:right;"' : ''; ?>
<?php $id = ($tab['Selected'] == TRUE) ? ' id="current"' : ''; ?>
<li<?=$id;?><?=$style;?>><a href="<?=$base;?><?=$tab['Link'];?>"><?=$tab['LinkText'];?></a></li>
<?php endforeach; ?>
</ul>
</div>