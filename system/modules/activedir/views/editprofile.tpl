{if $profile.display_form == true && $profile.ou != "Irwindale-Replica" }

   <center>

   <table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%"> <tr><td> <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%"> <tr><td>

   <span class="pageHd">Employee Locator</span>
	
   <img src="/images/dot_clear.gif" width="100%" height="4" alt="">
   <img src="/images/dot_black.gif" width="100%" height="2" alt="">

   <p class="pageSbhd">Edit Employee Information</p>
	
   <p>Please modify fields as needed and click the "Save Changes" button. To enter the manager's UID, you can either enter their username (e.g. japplega) or their full name as it is listed in this database (e.g. "Jim Applegate", but not "Jim A", "Applegate", or "James Applegate").</p>

{if $error.general != "" }
   <p class="red">{$error.general}</p>
{/if}

   <form method="post" action="/emploc/editprofile.php">
   <center>

   <table cellpadding="4" cellspacing="0" border="0">

   <tr>
   <td>&nbsp;</td>
   <td>Record for <b class="red">{$profile.cn}</b></td>
   </tr>

   <tr>
   <td><div align="right">Telephone:&nbsp;</div></td>
   <td><input type="text" size="35" name="telephonenumber" value="{$profile.telephonenumber}"></td>
   </tr>

   <tr>
   <td><div align="right">Fax Number:&nbsp;</div></td>
   <td><input type="text" size="35" name="facsimiletelephonenumber" value="{$profile.facsimiletelephonenumber}"></td>
   </tr>

   <tr>
   <td><div align="right">Title:&nbsp;</div></td>
   <td><input type="text" size="35" name="title" value="{$profile.title}"></td>
   </tr>

   <tr>
   <td><div align="right">Location:&nbsp;</div></td>
   <td>
   {$profile.l}
   <input type="hidden" name="l" value="{$profile.l}">
<!--
      <select name="l">
      {html_options values=$profile.office_array output=$profile.office_array selected=$profile.l}
      </select>
      {if $error.location != "" }
         &nbsp; &nbsp;<span class="red">{$error.location}</span>
      {/if}
-->
   </td>
   </tr>

   <tr>
   <td><div align="right">Manager's UID:&nbsp;</div></td>
   <td>
      <input type="text" size="35" name="manager" value="{$profile.manager}">
      {if $error.manager != "" }
         <br><span class="red">{$error.manager}</span>
      {/if}

   </td>
   </tr>

   <tr>
   <td>&nbsp;</td>
   <td><input type="submit" name="submit" value="Save Changes"></td>
   </tr>

   <tr>
   <td colspan="2">&gt; <a href="/emploc/profile.php?uid={$profile.uid}">Return to Profile Page</a></td>
   </tr>

   </table>

   <input type="hidden" name="mod_action" value="save">
   <input type="hidden" name="transfer_var" value="{$profile.transfer_var}">
   <input type="hidden" name="uid" value="{$profile.uid}">
   <input type="hidden" name="cn" value="{$profile.cn}">
   </form>

   </td></tr></table></td></tr></table>
   </center>

{ elseif $profile.ou == "Irwindale-Replica" }

   <center>

   <table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%">
   <tr><td>
   <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%">
   <tr><td>

   <span class="pageHd">Employee Locator</span>
	
   <img src="/images/dot_clear.gif" width="100%" height="4" alt="">
   <img src="/images/dot_black.gif" width="100%" height="2" alt="">

   <p class="pageSbhd">Edit Employee Information</p>
	
   <p>This record cannot be edited at this time. Whatever edits were made would be lost the next evening when we pull this record from the Exchange server.</p>

   <p>&gt; <a href="/emploc/profile.php?uid={$profile.uid}">Return to Profile Page</a></p>

   </td></tr></table>
   </td></tr></table>
   </center>

{ else }

   <center>

   <table cellspacing="0" cellpadding="1" bgcolor="#666666" border="0" width="80%">
   <tr><td>
   <table cellspacing="0" cellpadding="12" bgcolor="#FFFFFF" border="0" width="100%">
   <tr><td>

   <span class="pageHd">Employee Locator</span>
	
   <img src="/images/dot_clear.gif" width="100%" height="4" alt="">
   <img src="/images/dot_black.gif" width="100%" height="2" alt="">

   <p class="pageSbhd">Edit Employee Information: Results</p>
	
   <p>{$error.results}</p>

   <p>&gt; <a href="/emploc/search.php">Return to Employee Locator</a>
   <br>&gt; <a href="/emploc/profile.php?uid={$profile.uid}">Return to Profile Page</a></p>

   </td></tr></table>
   </td></tr></table>
   </center>

{/if}
