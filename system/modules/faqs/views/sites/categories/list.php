<body><?=$this->load->view('sites/tabs');?><div id="Wrapper">  <?php if ($admin['message'] != ''): ?><div id="flash_alert"><?=$admin['message'];?></div><?php endif; ?><?php if ($admin['error_msg'] != ''): ?><div id="flash_error"><?=$admin['error_msg'];?></div><?php endif; ?>   <div class="container">   <table class="layout">   <tr>   <td class="left">      <div class="Left">         <div class="col">            <div class="page-header">               <div class="page-header-links">               </div>   <h1>Categories</h1>            </div>            <div class="innercol"><?php if ($admin['faq_exists'] == true): ?>   <div class="listing">      <?php foreach($category_list AS $cat): ?>      <?php if ($cat['level'] > 0): ?>         <?php $category_plus = $cat['Sort'] + 1; ?>      <div style="margin-left:<?=($cat['level']-1)*2;?>em; border-top:1px solid #666; clear:both;">      <p style="float:right; text-align:right; margin:0; padding:4px 0;">      <a class="admin" href="<?=site_url('sites/categories/move/'.$site_id.'/'.$cat['ID'].'/dn/'.$last_action);?>">v</a>      <span class="pipe">|</span>      <a class="admin" href="<?=site_url('sites/categories/move/'.$site_id.'/'.$cat['ID'].'/up/'.$last_action);?>">^</a>      <span class="pipe">|</span>      <a class="admin" href="<?=site_url('sites/categories/add/'.$site_id.'/'.$cat['ParentID'].'/'.$category_plus.'/'.$last_action);?>">insert peer</a>      <span class="pipe">|</span>      <a class="admin" href="<?=site_url('sites/categories/add/'.$site_id.'/'.$cat['ID'].'/'.$cat['next_child'].'/'.$last_action);?>">add child</a></p>      <p style="margin:0; padding:4px 0;"><a style="text-decoration:none;" href="<?=site_url('sites/categories/edit/'.$site_id.'/'.$cat['ID'].'/'.$last_action);?>"><?=$cat['Name'];?></a></p>      </div>      <?php endif; ?>   <?php endforeach; ?>   <div style="border-top:1px solid #666; clear:both;"></div>   </div> <?php // listing ?><?php else: ?>   <p>There are no FAQ categories to display.</p>      <p><a class="admin" href="<?=site_url('sites/categories/add/'.$site_id.'/'.$root_id.'/1/'.$last_action);?>">Create the first FAQ category.</a></p><?php endif; ?>            </div>   <!-- innercol -->         </div>   <!-- col -->         <div class="bottom">&nbsp;</div>         <div id="Footer">   &copy; 2009 The Hain Celestial Group, Inc.        </div>   <!-- Footer -->      </div>   <!-- Left -->   </td>   <td class="right">      <div class="Right">         <div class="col">                  </div>   <?php // col ?>               </div>   <?php // Right ?>         </div>   <?php // container ?></div>   <?php // Wrapper ?></body>