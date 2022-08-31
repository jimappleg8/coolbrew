{if $profile.error == "" }

<center>
<table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%"> <tr><td> <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%"> <tr><td>

<span class="pageHd">Employee Profile</span>

<center>

<img src="{$profile.photo_path}" alt="Image of {$profile.uid}">

<table cellpadding="4" cellspacing="0" border="0">
<tr>
<td align="right"><b>Name:</b></td>
<td>{$profile.cn}</td>
</tr>
<tr>
<td align="right"><b>Job Title:</b></td>
<td>{$profile.title}</td>
</tr>
<tr>
<td align="right"><b>Email Address:</b></td>
<td><a href="mailto:{$profile.mail}">{$profile.mail}</a></td>
</tr>
<tr>
<td align="right"><b>Telephone:</b></td>
<td>{$profile.telephonenumber}</td>
</tr>
<tr>
<td align="right"><b>Fax:</b></td>
<td>{$profile.facsimiletelephonenumber}</td>
</tr>
<!-- <tr>
<td align="right"><b>Location:</b></td>
<td>{$profile.physicaldeliveryofficeName}</td>
</tr> -->
<tr>
<td align="right"><b>Location:</b></td>
<td>{$profile.l}</td>
</tr>
<tr>
<td align="right"><b>Manager's Name:</b></td>
<td><a href="/emploc/profile.php?uid={$profile.manager}">{$profile.manager_cn}</a></td>
</tr>
<!-- <tr>
<td align="right"><b>Department:</b></td>
<td>{$profile.l}</td>
</tr> -->

{section name=group loop=$profile.group_cn }

   <tr>
   {if $smarty.section.group.first}
      <td align="right"><b>E-Mail Groups:</b></td>
   {else}
      <td align="right">&nbsp;</td>
   {/if}
   <td><a href="/emploc/grpprofile.php?cn={$profile.group_cn_url[group]}">{$profile.group_cn[group]}</a></td>
   </tr>

{/section}

</table>
</center>

<p>&gt; <a href="/emploc/search.php">Return to Employee Locator page</a>

   {if $profile.access_level == 3}
      <br>&gt; <a href="/emploc/editprofile.php?mod_action=edit&uid={$profile.uid}">Edit this profile</a></p>
   { else }
      </p>
   {/if}

</td></tr></table></td></tr></table>
</center>

<center>
<p>&nbsp;</p>
<table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%"> <tr><td> <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%"> <tr><td>

<span class="pageHd">Search Again</span>

<center>
<form method="POST" name="searchForm" action="/emploc/search.php">
<span class="blockTxt">First or last name:
<input name="quick" size="20">
<input type="SUBMIT" value="Locate Employee">
&nbsp; &gt; <a href="/emploc/search.php">More Search Options</a></span>
<input type="hidden" name="searchType" value="q">
</form>
</center>

</td></tr></table></td></tr></table>
</center>


{else}

<p>There was an Error: {$profile.error}</p>


{/if}