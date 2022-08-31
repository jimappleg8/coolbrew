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

<?php if (isset($submenu)): ?>
   <div id="submenu" class="clearfix">

      <?php $last = count($submenu) - 1; ?>
      <ul>
      <?php for ($i=0; $i<count($submenu); $i++): ?>
         <?php 
            $style = ($submenu[$i]['Position'] == "right") ? ' style="float:right;"' : '';
            $class = '';
            $is_current = $submenu[$i]['Selected'];
            $is_last = (($submenu[$i]['Position'] == "left" && (isset($submenu[$i+1]) && $submenu[$i+1]['Position'] == "right")) || $i == $last) ? TRUE : FALSE;
            if ($is_current || $is_last)
            { 
               $class = ' class="';
               $class .= ($is_current) ? 'current' : '';
               $class .= ($is_last) ? ' last' : '';
               $class .= '"';
            }
         ?>
      <li<?=$style;?><?=$class;?>><a href="<?=site_url($submenu[$i]['Link']);?>"><?=$submenu[$i]['LinkText'];?></a></li>
      <?php endfor; ?>
      </ul>

   </div>
<?php endif; ?>

</div>

