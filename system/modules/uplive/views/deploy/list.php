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

               </div>
               
   <h1>Deploy</h1>

            </div>  <?php /* page_header */ ?>

            <div class="innercol">

<h2>ImagineFoods.com</h2>
<ul>
<li><a href="/admin/uplive.php/imaginefoods_com/upstage">Deploy from DEV to STAGING</a></li>
<li><a href="/admin/uplive.php/imaginefoods_com/uplive">Deploy from STAGING to LIVE</a></li>
</ul>
   
            </div>  <?php /* innercol */ ?>

         </div>  <?php /* col */ ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2012<?= (date('Y') > '2012') ? '-'.date('Y') : ''; ?>The Hain Celestial Group, Inc.

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