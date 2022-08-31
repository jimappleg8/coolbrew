<body id="stores">

<?=$this->load->view('tabs');?>

<div id="Wrapper">
  
<?php if ($admin['error'] != ''): ?>
<div id="flash_alert" style="width:885px; margin:0 auto 12px auto; background:url(/images/admin/alertbad_icon.gif) #C00 left no-repeat; clear:both; color:#FFF; padding:5px 5px 5px 30px; font-size:14px; border:1px solid #C99;"><?=$admin['error'];?></div>
<?php endif; ?>

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

   <h1>Generate IRI Store Locator Files</h1>

            </div>

            <div class="innercol">

<p>These functions generate complete files with all the participating brands' data included. The current brands are <?=$site_list;?>.</p>

<ul>
<li><a href="<?=site_url('utilities/iri_export_upc_69');?>">Generate upc_69.csv</a></li>
<li><a href="<?=site_url('utilities/iri_export_prod_rel');?>">Generate prod_rel.csv</a></li>
<li><a href="<?=site_url('utilities/iri_export_prod');?>">Generate prod.csv</a></li>
<li><a href="<?=site_url('utilities/iri_export_report');?>">Generate Report</a></li>
</ul>

            </div>   <?php // innercol ?>

         </div>   <?php // col ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2007 The Hain Celestial Group, Inc.

        </div>   <?php // Footer ?>

      </div>   <?php // Left ?>

   </td>
   <td class="right">

      <div class="Right">

         <div class="col">
            
&nbsp;
         </div>   <?php // col ?>

      </div>   <?php // Right ?>

   </td>
   </tr>
   </table>

   </div>   <?php // class="container" ?>

</div>   <?php // Wrapper ?>

</body>
