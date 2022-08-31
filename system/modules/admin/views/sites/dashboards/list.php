<body>

<?=$this->load->view('sites/tabs');?>

<div id="Wrapper">
  
<?php if ($admin['message'] != ''): ?>
<div id="flash_alert"><?=$admin['message'];?></div>
<?php endif; ?>

   <div class="container">

   <table class="layout">
   <tr>
   <td class="left">

      <div class="Left">

         <div class="col">
                
            <div class="page-header">

               <div class="page-header-links">

               </div>   <!-- page_header_links -->
  
   <h1>Dashboard</h1>

            </div>   <!-- page_header -->

            <div class="innercol clearfix">

<div style="margin:6px 9px 0 9px;; float:left; height:9px; width:9px; background-color:#999;"></div><p style="font-size:1.2em; font-style:italic; color:#666; font-family:Georgia, Serif;"><?=$site['Description'];?></p>

<?=$thumbnail_image;?>

<?=$quick_links;?>

<div style="clear:both;"></div>

<?=$about_this_site;?>

   <?=$repository_url;?>

            </div>   <!-- innercol -->

         </div>   <!-- col -->

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy;<?=date('Y');?> The Hain Celestial Group, Inc.

        </div>   <!-- Footer -->

      </div>   <!-- Left -->

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
   
   <?=$brand_sites;?>

   <?=$site_domains;?>
   
         </div>   <!-- col -->

      </div>   <!-- Right -->

   </td>
   </tr>
   </table>
      
   </div>   <!-- class="container" -->

</div>   <!-- Wrapper -->

</body>
