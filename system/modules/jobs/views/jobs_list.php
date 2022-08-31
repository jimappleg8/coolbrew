<?php if ($num_jobs > 0): ?>

   <table cellpadding="5" cellspacing="1" border="0" bgcolor="#CCCCCC">

   <tr bgcolor="#FFFFFF">
   <th>Job No.</th>
   <?php if ($orderby == "CategoryName"): ?>
   <th bgcolor="#CCCCCC"><a href="/careers/positions.php/CategoryName/asc" style="color:#000; font-weight:bold; background:transparent;">Category</a></th>
   <?php else: ?>
   <th><a href="/careers/positions.php/CategoryName/asc" style="color:#000; font-weight:bold; background:transparent;">Category</a></th>
   <?php endif; ?>
   <?php if ($orderby == "Title"): ?>
   <th bgcolor="#CCCCCC"><a href="/careers/positions.php/Title/asc" style="color:#000; font-weight:bold; background:transparent;">Title/Position</a></th>
   <?php else: ?>
   <th><a href="/careers/positions.php/Title/asc" style="color:#000; font-weight:bold; background:transparent;">Title/Position</a></th>
   <?php endif; ?>
   <?php if ($orderby == "CompanyName"): ?>
   <th bgcolor="#CCCCCC"><a href="/careers/positions.php/CompanyName/asc" style="color:#000; font-weight:bold; background:transparent;">Company</a></th>
   <?php else: ?>
   <th><a href="/careers/positions.php/CompanyName/asc" style="color:#000; font-weight:bold; background:transparent;">Company</a></th>
   <?php endif; ?>
   <?php if ($orderby == "LocationName"): ?>
   <th bgcolor="#CCCCCC"><a href="/careers/positions.php/LocationName/asc" style="color:#000; font-weight:bold; background:transparent;">Location</a></th>
   <?php else: ?>
   <th><a href="/careers/positions.php/LocationName/asc" style="color:#000; font-weight:bold; background:transparent;">Location</a></th>
   <?php endif; ?>
   <tr>
   
   <?php foreach($jobs as $job): ?>

   <tr bgcolor="#FFFFFF">
   <td><?=$job['JobNum'];?></td>
   <td><?=$job['CategoryName'];?></td>
   <td><a href="/careers/detail.php/<?=$job['ID'];?>/"><?=$job['Title'];?></a></td>
   <td><?=$job['CompanyName'];?></td>
   <td><?=$job['LocationName'];?></td>
   <tr>

   <?php endforeach; ?>

   </table>

<?php else: ?>

   <ul class="soft">
   <li>We do not currently have any open positions.</li>
   </ul>
   
<?php endif; ?>