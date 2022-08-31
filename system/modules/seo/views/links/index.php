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

   <h1>Link Tools</h1>

            </div>

            <div class="innercol">

<h2>Site Index</h2>

<p>This script will spider your website and extract all anchor links so you can get a complete list of pages on your site. It lists internal pages, internal downloads, external pages, and external downloads. 
<br /><?php if ($index_exists): ?><a href="<?=site_url('links/show_index/'.$site_id);?>" class="admin">View the current index</a> | <?php endif; ?><a href="<?=site_url('links/index_site/'.$site_id.'/'.$last_action.'/');?>" class="admin"><?php if ($index_exists): ?>Re-index this site<?php else: ?>Index this site<?php endif; ?></a></p>

<h2>Website Grader</h2>
<p>This is a free service that will look at your website and give some recommendations about how to improve its standing in search engines. It also gives you some valuable information about the site's Google Ranking, the number of bookmarks on del.icio.us and much more. Just click on the link below, enter your site's url and go!
<br /><a href="http://website.grader.com/" class="admin">Website Grader by HubSpot</a></p>

<h2>Other SEO Tools</h2>
<p>There are many ways that folks have developed to look at your current standing and help you make improvements. The link below is one of the sources of these kinds of tools. Take a look and try them out.
<br /><a href="http://www.seochat.com/seo-tools/" class="admin">SEO Tools (from SEOChat.com)</a></p>

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
            
         </div>   <?php // col ?>
         
      </div>   <?php // Right ?>
      
   </div>   <?php // container ?>

</div>   <?php // Wrapper ?>

</body>
