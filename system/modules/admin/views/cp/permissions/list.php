<?php
   $form_style = ($form_open == TRUE) ? 'display: block;' : 'display: none;';
   $link_style = ($form_open == TRUE) ? 'display: none;' : 'display: block;';
?>

<?php if ($user['Group'] != 'admin'): ?>

<h1><?=$user['FirstName'];?> has full access to these sites:</h1>

   <div class="permissions_list_all" id="permissions_list_all">

   <?php if ($admin['sites_exist'] == TRUE): ?>

      <div class="listing">

      <?php foreach($sites AS $site): ?>
      <div class="clearfix" style="clear:both; border-bottom:1px dotted #999; margin:0 9px; padding:2px 0 3px 0; font-size:11px;" onmouseover="$('#link_to_delete_permissions_<?=$site['SiteID'];?>').show();" onmouseout="$('#link_to_delete_permissions_<?=$site['SiteID'];?>').hide();">
      <div style="float:left;"><?=$site['Domain'];?></div>
      <?php if ($admin['group'] == 'admin'): ?><div style="float:right; display:none;" id="link_to_delete_permissions_<?=$site['SiteID'];?>"><a href="<?=site_url('cp/permissions/delete/'.$username.'/'.$site['SiteID']);?>" class="admin" onclick="deletePermissions_<?=$site['SiteID'];?>(this.href); return false;">delete</a></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
      </div> <?php /* listing */ ?>

   <?php else: ?>

      <p>No sites have been assigned yet.</p>
   
   <?php endif; ?>

   </div>

   <?php if ($admin['group'] == 'admin'): ?>
   <div class="add_item" style="margin:9px 9px 18px 9px;">

      <div class="widget list_widget item_wrapper" id="permissions_new_item" style="<?=$form_style;?>">

      <form id="new_permissions_item" onsubmit="return false;">

         <p class="blockintro">Choose a website:</p>
         <div class="block">
         <?=form_dropdown('NewSite', $domains, $this->validation->NewSite, 'class="js-initial-focus"');?>
         <?=$this->validation->NewSite_error;?>
         </div>

         <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Add access to this site', 'onclick'=>'addPermissions();'))?> or <a class="admin" href="#" onclick="hidePermissionsForm(); return false;">I'm done</a>

      </form>

      </div>

      <div id="link_to_add_permissions" class="link_to_add_child" style="<?=$link_style;?>">
      <a class="admin" href="#" id="permissions_new_item_link" onclick="showPermissionsForm(); return false;">Add site access</a>
      </div>

   </div>
   <?php endif; ?>
   
<?php else: ?>

<h1><?=$user['FirstName'];?> is an <strong>administrator</strong> and has full access to all sites</h1>

<?php endif; ?>
