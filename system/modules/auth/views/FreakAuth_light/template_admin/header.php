<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>FreakAuth Administration Console &raquo <?=$heading;?></title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv='expires' content='-1' />
<meta http-equiv= 'pragma' content='no-cache' />
<meta name='robots' content='all' />
<meta name='author' content='Daniel Vecchiato 4webby.com' />
<meta name='description' content='Administration console' />
<style type='text/css' media='all'>@import url('<?=base_url();?>public/css/admin_console.css');</style>
<link rel='stylesheet' type='text/css' media='all' href='<?=base_url();?>public/css/adminconsole.css' />
<script src="<?=base_url();?>public/js/jquery.js" type="text/javascript"></script>
<script src="<?=base_url();?>public/js/flash.js" type="text/javascript"></script>
</head>
<body>

<!-- START NAVIGATION -->
<div id="header">
	<div class="back_website">
	 [ <?=anchor('', 'back to website')?> ]
	</div>
	<div class="login">	  
		<a name="top"></a>
	  	<?=loginAnchorAdmin();?>  
	</div>
	
</div>

<div id="masthead">
<table cellpadding="0" cellspacing="0" border="0" style="width:100%">
<tr>
<td><h1>FreakAuth Administration Console</h1></td>
</tr>
</table>
</div>