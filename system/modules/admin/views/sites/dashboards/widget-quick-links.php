<?php  // build the link menu

      $link_menu = '';
      if ($site['DevVendorURL'] != '')
      {
         $vendor = ($site['DevVendorName'] != '') ? $site['DevVendorName'] : 'vendor';
         $link_menu .= '<li><a href="'.$site['DevVendorURL'].'" title="'.$site['DevVendorURL'].'" target="_blank">View the development website at '.$vendor.'</a></li>';
      }
      if ($site['DevURL'] != '')
      {
         $link_menu .= '<li><a href="'.$site['DevURL'].'" title="'.$site['DevURL'].'" target="_blank">View the development website</a></li>';
      }
      if ($site['StageURL'] != '')
      {
         $link_menu .= '<li><a href="'.$site['StageURL'].'" title="'.$site['StageURL'].'" target="_blank">View the staging website</a></li>';
      }
      if ($site['LiveURL'] != '')
      {
         $link_menu .= '<li><a href="'.$site['LiveURL'].'" title="'.$site['LiveURL'].'" target="_blank">View the live website</a></li>';
      }
?>

<?php if (! empty($quick_links) || $link_menu != ''): ?>
<h2 style="margin:0 0 0 218px; padding-bottom:3px; border-bottom:1px solid #999;">Quick Links</h2>
   <ul style="margin-left:218px;">
   <?=$link_menu;?>
   <?php foreach($quick_links as $link): ?>
   
   <li><a href="<?php if ($link['OpenWhere'] == 'frame'): ?><?=base_url();?>/frame.php?url=<?php endif; ?><?=$link['URL'];?>"<?php if ($link['OpenWhere'] == 'new'): ?> target="_blank"<?php endif; ?>><?=$link['Title'];?></a></li>
      
   <?php endforeach; ?>
   </ul>
<?php endif; ?>
