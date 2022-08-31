<div id="Header">

   <?=$this->load->view('../admin/masthead');?>
   
   <h3>
   <span id="floatright">
   Logged in as <?=$this->session->userdata('name');?> (<a href="/<?=index_page();?>/cp/login/logout">Log-out</a>)
   <span class="pipe">|</span> 
   <a href="/edit-account.php/<?=$last_action;?>">My info</a>
   </span>
   </h3>

   <h1>Manage Web Sites</h1>
   
   <div id="Tabs">

      <ul>
      <?php foreach ($tabs as $tab): ?>
      <?php $style = ($tab['Position'] == "right") ? ' style="float:right;"' : ''; ?>
      <?php $id = ($tab['Selected'] == TRUE) ? ' id="current"' : ''; ?>
      <li<?=$id;?><?=$style;?>><a href="<?=$tab['Link'];?>"><?=$tab['LinkText'];?></a></li>
      <?php endforeach; ?>
      </ul>

   </div>

</div>

