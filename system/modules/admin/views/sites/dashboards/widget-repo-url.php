<?php /* show the repository URL */ ?>

<script type="text/javascript">

$( document ).ready(function() {

   // highlight the repo URL
   $( "#repo-select-on-click" ).click(function() {
      $(this).select();
   });

});

</script>

<style type="text/css">
.repo-info {
	background-color:white;
	padding:12px;
	margin-top:2em;
	border:1px solid #000;
}
.repo-info h2 {
	font-size:1em;
	padding-left:2px;
	margin:0;
}

.repo-url {
	position:relative;
	margin:.25em 0 0;
	border:1px solid #dddddd;
	-webkit-border-radius:3px;
	-moz-border-radius:3px;
	border-radius:3px;
	padding:.2em .4em;
	-webkit-box-shadow:0 1px 2px rgba(0,0,0,0.05);
	-moz-box-shadow:0 1px 2px rgba(0,0,0,0.05);
	box-shadow:0 1px 2px rgba(0,0,0,0.05);
}
.repo-url input {
	-webkit-box-sizing:border-box;
	-moz-box-sizing:border-box;
	box-sizing:border-box;
	width:100%;
	margin:0;
	border:0;
	padding:0;
	background-color:transparent;
	font:normal 0.85em/1.5 Monaco, Consolas, "Lucida Console", "Courier New", Courier, monospace;
	text-shadow:0 1px 0 #FFF;
	outline:none;
	cursor: pointer;
}
.no-border {
	border:0;
	-webkit-box-shadow:none;
	-moz-box-shadow:none;
	box-shadow:none;
}
.no-border input {
	text-shadow:0;
}
</style>

<div class="repo-info">
   <h2>Repository URL</h2>
<?php if ($site['RepositoryURL'] == 'none'): ?>
   <div class="repo-url no-border">
      We do not have access to a repository for this site.
   </div>
<?php else: ?>
   <div class="repo-url">
      <input type="text" value="<?=$site['RepositoryURL'];?>" id="repo-select-on-click" readonly="readonly" />
   </div>
<?php endif; ?>
</div>
