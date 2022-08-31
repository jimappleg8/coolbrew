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

   <?php if ($products['limited']): ?><a class="admin" href="<?=site_url('products/index/'.$site_id.'/all');?>">Show all</a><?php endif; ?>
               </div>

   <h1>Discontinued products &mdash; <span><a href="<?=site_url('products/index/'.$site_id);?>">Return to active products</a></span></h1>

            </div>

            <div class="innercol">

<?php if ($products['error_msg'] != ""): ?>
<div class="error">
ERROR: <?=$products['error_msg'];?>
</div>
<?php endif; ?>

<?php if ($products['product_exists'] == true): ?>

   <?php if ( ! empty($nocat_list)): ?>

   <h2 id="nocat">No Category</h2>
   
   <div class="listing">

      <?php foreach($nocat_list AS $product): ?>
      
      <div style="border-top:1px solid #666;">
      <div style="float:right; font-size:10px; padding-top:6px;"><?=$product['ProductID'];?> <span class="pipe">|</span> <?php if ($product['ProductGroup'] == 'master'): ?>n/a<?php else: ?><?=getFullUPC($product['UPC']);?><?php endif; ?></div>
      <p style="margin:0; padding:4px 0;"><?php if ($product['ProductGroup'] != 'none' && $product['ProductGroup'] != 'master'): ?><img src="/images/indent.gif" width="13" height="10" alt="--" style="float:left; padding-right:3px;" /><?php endif; ?><a style="text-decoration:none;" href="<?=site_url('products/edit/'.$site_id.'/'.$product['ProductID'].'/'.$last_action);?>"><?=$product['ProductName'];?></a><?php if ($product['ProductGroup'] == 'master'): ?> &mdash; all sizes<?php elseif ($product['ProductGroup'] != 'none' && $product['ProductGroup'] != 'master'): ?> &mdash; <?=$product['PackageSize'];?><?php endif; ?><?php if ($product['Status'] == 'pending'): ?> <span style="color:red;">(pending)</span><?php endif; ?></p>
      </div>
      
      <?php endforeach; ?>
      
   </div>

   <?php endif; ?>
   
   <?php foreach($category_list AS $category): ?>

   <h2 id="cat<?=$category['CategoryID'];?>" style="margin-left:<?=($category['level']-1)*12;?>px;"><?=$category['CategoryName'];?></h2>
   
   <div class="listing" style="margin-left:<?=($category['level']-1)*12;?>px;">
   
      <?php foreach($product_list AS $product): ?>
      
         <?php if ($product['CategoryID'] == $category['CategoryID']): ?>

      <div style="border-top:1px solid #666;">
      <div style="float:right; font-size:10px; padding-top:6px;"><?=$product['ProductID'];?> <span class="pipe">|</span> <?php if ($product['ProductGroup'] == 'master'): ?>n/a<?php else: ?><?=getFullUPC($product['UPC']);?><?php endif; ?></div>
      <p style="margin:0; padding:4px 0;"><?php if ($product['ProductGroup'] != 'none' && $product['ProductGroup'] != 'master'): ?><img src="/images/indent.gif" width="13" height="10" alt="--" style="float:left; padding-right:3px;" /><?php endif; ?><a style="text-decoration:none;" href="<?=site_url('products/edit/'.$site_id.'/'.$product['ProductID'].'/'.$last_action);?>"><?=$product['ProductName'];?></a><?php if ($product['ProductGroup'] == 'master'): ?> &mdash; all sizes<?php elseif ($product['ProductGroup'] != 'none' && $product['ProductGroup'] != 'master'): ?> &mdash; <?=$product['PackageSize'];?><?php endif; ?><?php if ($product['Status'] == 'pending'): ?> <span style="color:red;">(pending)</span><?php endif; ?></p>
      </div>
      
         <?php endif; ?>
   
      <?php endforeach; ?>

   </div> <?php // listing ?>

   <?php endforeach; ?>

<?php else: ?>

   <p>There are no products to display.</p>
   
   <p><a class="admin" href="<?=site_url('products/add/'.$site_id.'/'.$last_action);?>">Create the first product.</a></p>
   
<?php endif; ?>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
            <a href="<?=site_url('products/add/'.$site_id.'/'.$last_action);?>" style="background-color:transparent;"><img src="/images/buttons/button_new_product.gif" width="138" height="31" alt="Add a new product" style="border:0px; margin:4px 0 24px 0;" /></a>

            <h1>Jump to...</h1>

   <?php if ( ! empty($nocat_list)): ?>
      <div style="margin-left:12px;">
      <p style="margin:0; padding:4px 0;"><a href="#nocat" style="text-decoration:none;">No Category</a></p>
      </div>
   <?php endif; ?>

   <?php foreach($category_list AS $cat): ?>

      <?php if ($cat['level'] > 0): ?>
      <div style="margin-left:<?=($cat['level']-1)*12+12;?>px;">
      <p style="margin:0; padding:4px 0;"><a href="#cat<?=$cat['CategoryID'];?>" style="text-decoration:none;"><?=$cat['CategoryName'];?></a></p>
      </div>
      <?php endif; ?>

   <?php endforeach; ?>

         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
