<?php if ($jobs['Status'] == 1): ?>

<table cellpadding="5" cellspacing="1" border="0" bgcolor="#CCCCCC">

<tr bgcolor="#FFFFFF">
<td class="bold">Job No.</td>
<td valign="top"><?=$jobs['JobNum'];?></td>
</tr>

<tr bgcolor="#FFFFFF">
<td valign="top" class="bold">Location</td>
<td valign="top"><?=$jobs['LocationName'];?></td>
</tr>

<tr bgcolor="#FFFFFF">
<td valign="top" class="bold">Company</td>
<td valign="top"><?=$jobs['CompanyName'];?></td>
</tr>

<tr bgcolor="#FFFFFF">
<td valign="top" class="bold">Category</td>
<td valign="top"><?=$jobs['CategoryName'];?></td>
</tr>

</table>

   <h2><?=$jobs['Title'];?></h2>

   <?php if ($jobs['Summary'] != ""): ?>
      <p><b>Summary:</b> <?=$jobs['Summary'];?></p>
   <?php endif; ?>
   
   <?php if ($jobs['LocationName'] != ""): ?>
      <p><b>Location:</b> <?=$jobs['LocationName'];?></p>
   <?php endif; ?>

   <p><?=$jobs['Description'];?></p>

<table cellpadding="5" cellspacing="0" border="0">
<tr>
<td><a href="/careers/submit_resume.php/<?=$jobs['ID'];?>/"><img src="/images/careers/btn_submit_resume.gif" width="195" height="20" alt="Submit Your Resume" border="0"></a></td>
<td><a href="/careers/email.php/<?=$jobs['ID'];?>/"><img src="/images/careers/btn_email_job.gif" width="195" height="20" alt="Send job to a friend" border="0"></a></td>
<td><a href="/careers/positions.php"><img src="/images/careers/btn_back.gif" width="60" height="20" alt="Back" border="0"></a></td>
</tr>
</table>

<?php else: ?>

   <h2><?=$jobs['Title'];?></h2><br>

   <p>This job is no longer available.</p>

<?php endif; ?>