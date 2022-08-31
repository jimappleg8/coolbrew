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
               
   <h1>Deploy ImagineFoods.com from STAGING to LIVE</h1>

            </div>  <?php /* page_header */ ?>

            <div class="innercol">

<div id="content">

<h2>1. Create a backup of the existing LIVE database</h2>

<p>On bolwebdb1 (log in as webadmin):</p>

<pre>
su - mysql
cd /var/opt/httpd/if-data/
mysqldump -u root -p --default-character-set=utf8 drupal_imagine_live > drupal_imagine_live_<?=$today;?>.sql
</pre>

<h2>2. Copy the database from STAGING to LIVE</h2>

<p><a href="/admin/uplive.php/imaginefoods_com/uplive_database" target="_blank">Run the uplive database script.</a></p>


<h2>3. Upload the site files</h2>

<p>On bolwebdev1:</p>

<pre>
cd /var/opt/httpd
sudo uplive -d ifdocs/
</pre>

<h2>4. Upload the product database tables</h2>

<p>To ensure that the store locator is updated, you need to move the product tables from STAGING to LIVE.</p>

</div>
   
            </div>  <?php /* innercol */ ?>

         </div>  <?php /* col */ ?>

         <div class="bottom">&nbsp;</div>

         <div id="Footer">

   &copy; 2012 The Hain Celestial Group, Inc.

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
