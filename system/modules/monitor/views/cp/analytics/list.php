<body>

<?=$this->load->view('cp/tabs');?>

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

               </div>

   <h1 id="top">Analytics</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">

<p>Welcome to the Analytics Home Page. This area of the hcgWeb Portal is under development, so don't be surprised if you see an error message or two for a while. We are trying to make it easier to access stats about the websites in a central location, so look here for summary pages listing the visitor numbers and other stats.</p>

<p>This is what is available now:</p>

<ul>
<li><a href="<?=site_url('cp/analytics/all_sites_summary');?>">All Sites Summary</a></li>
<li><a href="<?=site_url('cp/analytics/all_sites_status');?>">All Sites Status</a></li>
</ul>
               </div> <?php /* basic-form */ ?>
   
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
           
         </div>   <?php /* col */ ?>

      </div>   <?php /* Right */ ?>

   </td>
   </tr>
   </table>
      
   </div>   <?php /* container */ ?>

</div>   <?php /* Wrapper */ ?>

</body>