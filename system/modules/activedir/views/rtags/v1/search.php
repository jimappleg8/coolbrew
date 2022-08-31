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
   
   <?php foreach ($search['results'] AS $result): ?>
      <tr>
<!--      <td><a href="/activedir/index.php/phonebook/group_profile/<?=$result['cn_url'];?>"><?=($result['cn']) ? $result['cn'] : "&nbsp;";?></a></td> -->
      <td><?=($result['cn']) ? $result['cn'] : "&nbsp;";?></td>
      <td><?=($result['description']) ? $result['description'] : "&nbsp;";?></td>
      <td><?=($result['mail']) ? '<a href="mailto:'.$result['mail'].'">'.$result['mail'].'</a>' : "&nbsp;";?></td>
      </tr>
   <?php endforeach; ?>

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
   <td><b>Location</b></td>
   <td><b>E-mail</b></td>
   <td><b>Title</b></td>
   </tr>
   
   <?php foreach ($search['results'] AS $result): ?>
      <tr>
<!--      <td><a href="/activedir/index.php/phonebook/user_profile/<?=$result['samaccountname'];?>"><?=($result['displayname']) ? $result['displayname'] : "&nbsp;";?></a></td> -->
      <td><?=($result['displayname']) ? $result['displayname'] : "&nbsp;";?></td>
      <td><?=($result['telephonenumber']) ? $result['telephonenumber'] : "&nbsp;";?></td>
      <td><?=($result['physicaldeliveryofficename']) ? $result['physicaldeliveryofficename'] : "&nbsp;";?></td>
      <td><?=($result['mail']) ? '<a href="mailto:'.$result['mail'].'">'.$result['mail'].'</a>' : "&nbsp;";?></td>
      <td><?=($result['description']) ? $result['description'] : "&nbsp;";?></td>
      </tr>
   <?php endforeach; ?>

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

   <form method="post" action="<?=$action;?>">
   <pre>    First Name:  <?=form_input(array('name'=>'givenName', 'id'=>'givenName', 'maxlength'=>'128', 'size'=>'35', 'value'=>$this->validation->givenName));?>
      <?=$this->validation->givenName_error;?>
   <br>    Last Name:   <?=form_input(array('name'=>'sn', 'id'=>'sn', 'maxlength'=>'128', 'size'=>'35', 'value'=>$this->validation->sn));?>
      <?=$this->validation->sn_error;?>
   <br>    Telephone:   <?=form_input(array('name'=>'telephonenumber', 'id'=>'telephonenumber', 'maxlength'=>'128', 'size'=>'35', 'value'=>$this->validation->telephonenumber));?>
      <?=$this->validation->telephonenumber_error;?>
   <br>    Title:       <?=form_input(array('name'=>'title', 'id'=>'title', 'maxlength'=>'128', 'size'=>'35', 'value'=>$this->validation->title));?>
      <?=$this->validation->title_error;?></pre>
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

      <form method="post" id="myform" name="myform" action="<?=$action;?>">
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
	
       <script type="text/javascript">
       function submitform2(groupname)
       {
          form = document.getElementById('myform2');
          form.groupname.value = groupname;
          form.submit();
       }
       </script>

	  <form method="post" id="myform2" name="myform2" action="<?=$action;?>">
      <input type="hidden" name="searchType" value="g">
      <input type="hidden" id="groupname" name="groupname" value="">
      <p>Search for HCG E-mail groups by entering all or part of a group name in the field below and hitting the [Locate Group] button. Or view a <a href="" onclick="submitform2('%%ALL%%'); return false;">listing of all e-mail groups</a>.</p>
      </form>

      <form method="post" action="<?=$action;?>">
      <pre>    Group Name:  <?=form_input(array('name'=>'groupname', 'id'=>'groupname', 'maxlength'=>'128', 'size'=>'35', 'value'=>$this->validation->groupname));?>
      <?=$this->validation->groupname_error;?></pre>
      <br><input type="submit" value="Locate Group">&nbsp;
      <input type="reset" value="Reset">
      <input type="hidden" name="searchType" value="g">
      </form>

   </div>
