<body>

<?=$this->load->view('tabs');?>

<?php if ($admin['message'] != ''): ?>
<div id="message">
<p><?=$admin['message'];?></p>
</div>
<?php endif; ?>

<div id="Wrapper">
  
   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">
            

               <div class="page-header-links">

   <a class="admin" href="<?=site_url('nleas/edit/'.$site_id.'/'.$product_id.'/'.$last_action);?>">Cancel</a>

               </div>

   <h1 id="top">Copy nutrition facts</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

<h1 style="margin-bottom:12px;"><?=$product['ProductName'];?></h1>

               <div id="basic-form">

<form method="post" action="<?=site_url('nleas/copy/'.$site_id.'/'.$product_id.'/'.$last_action);?>">

<p class="blockintro">Specify the product from which you want to copy the nutrition facts by either selecting it from the pulldown list or enterng the product ID in the second field.</p>
<div class="block" style="margin-bottom:0;">
   <dl>
      <dt><label for="SourceProduct">Source:</label></dt>
      <dd><?=form_dropdown('SourceProduct', $products, $this->validation->SourceProduct);?>
      <?=$this->validation->SourceProduct_error;?></dd>

      <dt><label for="SourceProductID">Source ID:</label></dt>
      <dd><?=form_input(array('name'=>'SourceProductID', 'id'=>'SourceProductID', 'maxlength'=>'11', 'size'=>'15', 'value'=>$this->validation->SourceProductID));?>
      <?=$this->validation->SourceProductID_error;?></dd>
   </dl>
</div>

<div class="action">
    <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Copy nutrition facts'))?> or <a class="admin" href="<?=site_url('nleas/edit/'.$site_id.'/'.$product_id.'/'.$last_action);?>">Cancel</a>
</div>

</form>

               </div> <?php // basic-form ?>
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

           &copy; 2010 The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
           
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>