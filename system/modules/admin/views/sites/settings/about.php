<body>

<?=$this->load->view('sites/tabs');?>

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

&nbsp;

               </div>   <!-- page_header_links -->

   <h1>Edit information about this site</h1>

            </div>   <!-- page_header -->

            <div class="innercol">
            
               <div id="basic-form">

<form method="post" action="<?=site_url('sites/settings/about/'.$site_id.'/'.$last_action);?>">

<div class="block">
   <?=form_ckeditor(array('name'=>'AboutThisSite', 'id'=>'AboutThisSite', 'cols' => 80, 'rows' => 20, 'value'=>$this->validation->AboutThisSite, 'class'=>'box'));?>
      <?=$this->validation->AboutThisSite_error;?>
</div>

<div class="action">
   <?=form_submit(array('name'=>'submit', 'class'=>'submit', 'value'=>'Save changes'))?> or <a class="admin" href="<?=site_url('sites/dashboards/index/'.$site_id);?>">Cancel</a>
</div>

</form>

               </div>   <!-- basic-form -->
   
            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007-<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>

