<?=$this->load->view('tabs');?><?php if ($people['message'] != ''): ?><div id="message"><p><?=$people['message'];?></p></div><?php endif; ?><div id="job-dataarea"><div class="page-header">   <div class="page-header-links">      <a class="admin" href="<?=site_url('people/add/'.$last_action);?>">Add a new person</a>   </div>   <h2>Manage People</h2></div><p>Manage the people who have access to the Jobs module.</p><?php if ($people['error_msg'] != ""): ?><div class="error">ERROR: <?=$people['error_msg'];?></div><?php endif; ?><div id="listing"><?php if ($people['people_exist'] == true): ?>   <table width="100%" cellpadding="0" cellspacing="0" border="0">   <tr>   <th>&nbsp;</th>   <th>Username</td>   <th>Full Name</td>   <th>Email</td>   <tr>   <?php foreach($people_list as $person): ?>   <tr>   <td><a class="admin" href="<?=site_url('people/edit/'.$person['Username'].'/'.$last_action);?>">Edit</a></td>   <td><?=$person['Username'];?></td>   <td><?=$person['FirstName'];?> <?=$person['LastName'];?></td>   <td><a href="mailto:<?=$person['Email'];?>"><?=$person['Email'];?></a></td>   <tr>   <?php endforeach; ?>      <tr>   <td class="hidden" width="40"><img src="/images/dot_clear.gif" width="40" height="1" alt=""></td>   <td class="hidden" width="100"><img src="/images/dot_clear.gif" width="100" height="1" alt=""></td>   <td class="hidden" width="220"><img src="/images/dot_clear.gif" width="220" height="1" alt=""></td>   <td class="hidden" width="100%"><img src="/images/dot_clear.gif" width="10" height="1" alt=""></td>   </tr>      </table></div></div> <?php // job-dataarea ?><?php else: ?>   <p>There are no people to display.</p><?php endif; ?>