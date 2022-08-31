<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">

            <div class="page-header">

               <div class="page-header-links">

               </div>

   <h1>All pages</h1>

            </div>

            <div class="innercol">

<?php if ($page['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$page['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($page['page_exists'] == true): ?>

   <div class="listing">
   
   <?php foreach($page_list AS $page): ?>

      <?php if ($page['level'] > 0): ?>
         <?php $sort_plus = $page['Sort'] + 1; ?>
      <div style="margin-left:<?=($page['level']-1)*2;?>em; border-top:1px solid #666; clear:both;">
      <p style="float:right; text-align:right; margin:0; padding:4px 0;">
      <a href="<?=site_url('pages/move/'.$site_id.'/'.$page['ID'].'/dn/'.$last_action);?>" class="admin">v</a>
      <span class="pipe">|</span>
      <a href="<?=site_url('pages/move/'.$site_id.'/'.$page['ID'].'/up/'.$last_action);?>" class="admin">^</a>
      <span class="pipe">|</span>
      <a href="<?=site_url('pages/add/'.$site_id.'/'.$page['ParentID'].'/'.$sort_plus.'/'.$last_action);?>" class="admin">insert peer</a>
      <span class="pipe">|</span>
      <a href="<?=site_url('pages/add/'.$site_id.'/'.$page['ID'].'/'.$page['next_child'].'/'.$last_action);?>" class="admin">add child</a>
      <span class="pipe">|</span>
      <a href="<?=site_url('pages/delete/'.$site_id.'/'.$page['ID'].'/'.$last_action);?>" class="admin">delete</a></p>
      <p style="margin:0; padding:4px 0;"><a href="<?=site_url('pages/edit/'.$site_id.'/'.$page['ID'].'/'.$last_action);?>"style="text-decoration:none;"><?=$page['MenuText'];?></a><?php if ($page['ProductCategory'] == 1): ?> (product category)<?php endif; ?></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

   </div> <?php // listing ?>

<?php else: ?>

   <p>There is no page to display.</p>
   
   <p><a href="<?=site_url('pages/add/'.$site_id.'/'.$page_list[0]['ID'].'/1/'.$last_action);?>" class="admin">Create the first page.</a></p>

<?php endif; ?>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy 2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         </div>   <?php // col ?>
         
      </div>   <?php // Right ?>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
