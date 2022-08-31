<?php if ($search['result_type'] == "groups"): ?>

   <div align="center">
   <table width="90%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="0">
   <tr>
   <td colspan="3"><h2>HCG E-mail Group Search Results</h2></td>
   </tr>
   </table>

   <table width="90%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="1">
   <tr>
   <td><b>Group Name</b></td>
   <td><b>Description</b></td>
   <td><b>E-mail</b></td>
   </tr>
   
   <?php for ($i=0; $i<count($search['cn']); $i++): ?>
      <tr>
      <td><a href="/emploc/grpprofile.php?cn=<?=$search['cn_url'][$i];?>"><?=($search['cn'][$i]) ? $search['cn'][$i] : "&nbsp;";?></a></td>
      <td><?=($search['description'][$i]) ? $search['description'][$i] : "&nbsp;";?></td>
      <td><a href="mailto:<?=$search['mail'][$i];?>"><?=($search['mail'][$i]) ? $search['mail'][$i] : "&nbsp;";?></a></td>
      </tr>
   <?php endfor; ?>

   </table>
   <p>&nbsp;</p>
   </div>

<?php elseif ($search['result_type'] == "people"): ?>

   <div align="center">
   <table width="90%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="0">
   <tr>
   <td colspan="4"><h2>HCG Employee Search Results</h2></td>
   </tr>
   </table>
   
   <table width="90%" cellpadding="4" cellspacing="0" bgcolor="#FFFFFF" border="1">
   <tr>
   <td><b>Name</b></td>
   <td><b>Telephone</b></td>
   <td><b>E-mail</b></td>
   <td><b>Title</b></td>
   </tr>
   
   <?php for ($i=0; $i<count($search['cn']); $i++): ?>
      <tr>
      <td><a href="/emploc/profile.php?uid=<?=$search['uid'][$i];?>"><?=($search['cn'][$i]) ? $search['cn'][$i] : "&nbsp;";?></a></td>
      <td><?=($search['telephonenumber'][$i]) ? $search['telephonenumber'][$i] : "&nbsp;";?></td>
      <td><a href="mailto:<?=$search['mail'][$i];?>"><?=($search['mail'][$i]) ? $search['mail'][$i] : "&nbsp;";?></a></td>
      <td><?=($search['title'][$i]) ? $search['title'][$i] : "&nbsp;";?></td>
      </tr>
   <?php endfor; ?>

   </table>
   <p>&nbsp;</p>
   </div>

<?php elseif ($search['result_type'] == "error"): ?>

   <div align="center">
   <span class="errorNotice"><?=$search['error'];?>
   <br>&nbsp;</span>
   </div>

<?php endif; ?>


   <div id="myforms" style="width:70%; margin:0 auto; border:1px solid #999; padding:12px;">

      <h1>Employee Locator</h1>
	
      <h2>Search for Employees</h2>
	
      <p>Search for Hain Celestial Group employees by entering search criteria in the fields below and hitting the [Locate Employee] button.</p>

   <form method="post" action="/activedir/index.php/phonebook/search">
   <pre>    First Name:  <input type="text" size="35" name="givenName" value="">
   <br>    Last Name:   <input type="text" size="35" name="sn" value="">
   <br>    Telephone:   <input type="text" size="35" name="telephonenumber" value="">
   <br>    Title:       <input type="text" size="35" name="title" value=""></pre>
   <br><input type="submit" value="Locate Employee">&nbsp;<input type="reset" value="Reset">
   <input type="hidden" name="searchType" value="a">
   </form>

      <h2>Browse for Employees</h2>
	
      <p>Browse Hain Celestial Group employees by last name by clicking on the appropriate letter.
      
       <script type="text/javascript">
       function submitform(letter)
       {
          form = document.getElementById('myform');
          form.sn.value = letter;
          form.submit();
       }
       </script>

      <form method="post" id="myform" name="myform" action="/activedir/index.php/phonebook/search">
      <input type="hidden" name="searchType" value="f">
      <input type="hidden" id="sn" name="sn" value="">
      <p>
      <a href="" onclick="submitform('A'); return false;">A</a> | 
      <a href="" onclick="submitform('B'); return false;">B</a> | 
      <a href="" onclick="submitform('C'); return false;">C</a> | 
      <a href="" onclick="submitform('D'); return false;">D</a> | 
      <a href="" onclick="submitform('E'); return false;">E</a> | 
      <a href="" onclick="submitform('F'); return false;">F</a> | 
      <a href="" onclick="submitform('G'); return false;">G</a> | 
      <a href="" onclick="submitform('H'); return false;">H</a> | 
      <a href="" onclick="submitform('I'); return false;">I</a> | 
      <a href="" onclick="submitform('J'); return false;">J</a> | 
      <a href="" onclick="submitform('K'); return false;">K</a> | 
      <a href="" onclick="submitform('L'); return false;">L</a> | 
      <a href="" onclick="submitform('M'); return false;">M</a> | 
      <a href="" onclick="submitform('N'); return false;">N</a> | 
      <a href="" onclick="submitform('O'); return false;">O</a> | 
      <a href="" onclick="submitform('P'); return false;">P</a> | 
      <a href="" onclick="submitform('Q'); return false;">Q</a> | 
      <a href="" onclick="submitform('R'); return false;">R</a> | 
      <a href="" onclick="submitform('S'); return false;">S</a> | 
      <a href="" onclick="submitform('T'); return false;">T</a> | 
      <a href="" onclick="submitform('U'); return false;">U</a> | 
      <a href="" onclick="submitform('V'); return false;">V</a> | 
      <a href="" onclick="submitform('W'); return false;">W</a> | 
      <a href="" onclick="submitform('Z'); return false;">X</a> | 
      <a href="" onclick="submitform('Y'); return false;">Y</a> | 
      <a href="" onclick="submitform('Z'); return false;">Z</a>
      <br>&nbsp;</p>
      </form>
	
      <h2>Search for E-mail Groups</h2>
	
      <p>Search for HCG E-mail groups by entering all or part of a group name in the field below and hitting the [Locate Group] button. Or view a <a href="/emploc/search.php?searchType=g&groupname=%25%25ALL%25%25">listing of all e-mail groups</a>.</p>

      <form method="post" action="/activedir/index.php/phonebook/search">
      <pre>    Group Name:  <input type="text" size="35" name="groupname"></pre>
      <br><input type="submit" value="Locate Group">&nbsp;
      <input type="reset" value="Reset">
      <input type="hidden" name="searchType" value="g">
      </form>

   </div>
