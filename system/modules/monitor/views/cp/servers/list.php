<body>

<script type="text/javascript">

$(document).ready(function() {

<?php foreach($lamps AS $key => $lamp): ?>
   $.ajax({
      url: "/admin/monitor.php/cp/servers/ajax_lamp/<?=$key;?>",
      cache: false
   }).done(function(html) {
      $("#lamp-<?=$key;?>").html(html);
   });
   
<?php endforeach; ?>

});
</script>

<?=$this->load->view('cp/tabs');?>

<?php if ($admin['message'] != ''): ?>
<div id="message">
<p><?=$admin['message'];?></p>
</div>
<?php endif; ?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

               </div>

   <h1 id="top">Servers</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<div id="content">

<h2>Linux Servers</h2>
<p>Memory use does not include buffer or cache memory used; that memory is available to applications.</p>
<table class="info">
<tr>
<td>Server</td>
<td>% Disk Used</td>
<td>% Memory Used</td>
<td>% CPU Used</td>
</tr>

<?php foreach ($lamps AS $key => $lamp): ?>
<tr id="lamp-<?=$key;?>">
<td colspan="4">loading...</td>
</tr>
<?php endforeach; ?>
</table>


<h2>Web Servers</h2>
<table class="info">
<?php foreach ($webs AS $web): ?>
<tr>
<th><?=$web['name'];?></th>
<td><?php if ($web['up'] == TRUE): ?><span style="color:green;">UP</span><?php else: ?><span style="color:red;">DOWN</span><?php endif; ?></td>
<td><a href="<?=$web['status'];?>" target="_blank">view Apache Server Status</a></td>
<td><a href="<?=$web['php-info'];?>" target="_blank">view PHP Info</a></td>
<?php if (isset($web['opcache'])): ?>
<td><a href="<?=$web['opcache'];?>" target="_blank">view OpCache</a></td>
<?php else: ?>
<td>&nbsp;</td>
<?php endif; ?>
</tr>
<?php endforeach; ?>
</table>

<!--
<div class='progress'>
        <div class='prgtext'><?php echo $dp; ?>% Disk Used</div>
        <div class='prgbar'></div>
        <div class='prginfo'>
                <span style='float: left;'><?php echo "$du of $dt used"; ?></span>
                <span style='float: right;'><?php echo "$df of $dt free"; ?></span>
                <span style='clear: both;'></span>
        </div>
</div>
-->

<h2>Database Servers</h2>
<?php foreach ($dbs AS $db): ?>
<table class="info">
<tr>
<th colspan="2"><?=$db['name'];?></th>
</tr>
<tr>
<td>Uptime</td>
<td><?=timespan(time() - $db['status']['Uptime']);?></td>
</tr>
<tr>
<td>Max used connections</td>
<td><?=$db['status']['Max_used_connections'];?></td>
</tr>
<tr>
<td>Aborted connections</td>
<td<?php if ($db['status']['Aborted_connects'] > 1): ?> style="background-color:red;"<?php endif; ?>><?=$db['status']['Aborted_connects'];?></td>
</tr>
<tr>
<td>Threads connected</td>
<td><?=$db['status']['Threads_connected'];?></td>
</tr>
<tr>
<td>Slave running</td>
<td><?=($db['status']['Slave_running'] == 'ON') ? 'Yes' : 'No';?></td>
</tr>
<tr>
<td>Slow queries</td>
<td><?=$db['status']['Slow_queries'];?></td>
</tr>
</table>
<?php endforeach; ?>

</div>

               </div> <?php /* basic-form */ ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
           
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>