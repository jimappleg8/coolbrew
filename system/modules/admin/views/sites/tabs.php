
<div id="Header">

   <?=$this->load->view('masthead');?>
   
   <h3>
   <span id="floatright">
   Logged in as <?=$this->session->userdata('name');?> (<a href="<?=site_url('cp/login/logout')?>">Log-out</a>)
   <span class="pipe">|</span> 
   <a href="/edit-account.php/<?=$last_action;?>">My info</a>
   </span>
   <a href="<?=site_url('cp/sites/index');?>">Site List</a>
   </h3>

   <h1><?=$site['Domain'];?><span> &mdash; <?=$site['BrandName'];?></span><?php if ($site['Status'] == 'inactive'): ?><span style="color:red;"> &mdash; This site is inactive.</span><?php endif; ?></h1>

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

