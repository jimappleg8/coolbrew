<body>

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertgood_icon.gif) #E2F9E3 left no-repeat; clear:both; color:#060; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #9c9;"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

<a class="admin" href="#">Cancel</a>

               </div>
               
   <h1>Database Upload</h1>

            </div>  <?php /* page_header */ ?>

            <div class="innercol">

               <div id="basic-form">

<form method="post" action="<?=site_url('dbupload/index');?>">

<!-- <p class="blockintro">Tell us about the store.</p> -->
<div class="block">
   <dl>
      <dt><strong><label for="Source">Source:</label></strong></dt>
      <dd><?=form_dropdown('Source', $servers, $this->validation->Source);?>
      <?=$this->validation->Source_error;?></dd>

      <dt><strong><label for="Target">Target:</label></strong></dt>
      <dd><?=form_dropdown('Target', $servers, $this->validation->Target);?>
      <?=$this->validation->Target_error;?></dd>

      <dt><strong><label for="Table">Table:</label></strong></dt>
      <dd><?=form_dropdown('Table', $tables, $this->validation->Table);?>
      <?=$this->validation->Table_error;?></dd>

      <dt><strong><label for="Site">Site:</label></strong></dt>
      <dd><?=form_dropdown('Site', $sites, $this->validation->Site);?>
      <?=$this->validation->Site_error;?></dd>

      <dt><label for="Where">Where:</label></dt>
      <dd><?=form_textarea(array('name'=>'Where', 'id'=>'Where', 'cols' => 45, 'rows' => 4, 'value'=>$this->validation->Where, 'class'=>'box'));?>
      <?=$this->validation->Where_error;?></dd>

   </dl>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Upload table'))?>
</div>

</form>

               </div>  <?php /* basic-form */ ?>
   
            </div>  <?php /* innercol */ ?>

         </div>  <?php /* col */ ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2011<?= (date('Y') > '2011') ? '-'.date('Y') : ''; ?> The Hain Celestial Group, Inc.

        </div>  <?php /* Footer */ ?>

      </div>  <?php /* Left */ ?>

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">

         </div>  <?php /* col */ ?>

      </div>  <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>  <?php /* class="container" */ ?>

</div>  <?php /* Wrapper */ ?>

</body>