<center>
<table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="90%">
<tr><td>
<table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%">
<tr><td>

<span class="pageHd">E-mail Group Profile</span>


<center>

<table cellpadding="4" cellspacing="0" border="0">
<tr>
<td align="right"><b>Name:</b></td>
<td>{$profile.cn}</td>
</tr>
<tr>
<td align="right"><b>Description:</b></td>
<td>{$profile.description}</td>
</tr>
<tr>
<td align="right"><b>Email Address:</b></td>
<td><a href="mailto:{$profile.mail}">{$profile.mail}</a></td>
</tr>
</table>


<table width="100%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="1">
<tr>
{if $profile.access_level == 3 }
   <td colspan="5"><span class="pageSbhd">Group Members</span></td>
{else}
   <td colspan="4"><span class="pageSbhd">Group Members</span></td>
{/if}
</tr>
<tr>
<td><b>Name</b></td>
<td><b>Telephone</b></td>
<td><b>E-mail</b></td>
<td><b>Title</b></td>
{if $profile.access_level == 3 }
   <td>&nbsp;</td>
{/if}
</tr>
   
{section name=people loop=$profile.p_uid}
   <tr>
   <td><a href="/emploc/profile.php?uid={$profile.p_uid[people]}">{$profile.p_cn[people]|default:"&nbsp;"}</a></td>
   <td>{$profile.p_telephonenumber[people]|default:"&nbsp;"}</td>
   <td><a href="mailto:{$profile.p_mail[people]}">{$profile.p_mail[people]|default:"&nbsp;"}</a></td>
   <td>{$profile.p_title[people]|default:"&nbsp;"}</td>
   {if $profile.access_level == 3 }
      <td><a href="/emploc/editprofile.php?mod_action=edit&uid={$profile.p_uid[people]}">[Edit]</a></td>
   {/if}
   </tr>
{/section}

</table>

</center>

<p>&gt; <a href="/emploc/search.php">Return to Employee Locator page</a></p>

</td></tr></table></td></tr></table>
</center>

<center>
<p>&nbsp;</p>
<table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%">
<tr><td>
<table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%">
<tr><td>

<span class="pageHd">Search Groups Again</span>

<center>
<form method="POST" name="searchForm" action="/emploc/search.php">
<span class="blockTxt">Group Name:
<input name="groupname" size="20">
<input type="SUBMIT" value="Locate Group">
&nbsp; &gt; <a href="/emploc/search.php">More Search Options</a></span>
<input type="hidden" name="searchType" value="g">
</form>
</center>

</td></tr></table></td></tr></table>
</center>
