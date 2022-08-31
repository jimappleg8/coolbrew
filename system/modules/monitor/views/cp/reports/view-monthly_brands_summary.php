<?php

function activity_table($site_visits, $site_unique_visitors, $site_percent_new_visitors, $site_pageviews, $sites_lookup)
{
   $cols = array( 
      array( 'id' => '0', 'label' => 'Website', 'type' => 'string'), 
      array( 'id' => '1', 'label' => 'Visits', 'type' => 'number'), 
      array( 'id' => '2', 'label' => 'Unique Visitors', 'type' => 'number'), 
      array( 'id' => '3', 'label' => '% New Visitors', 'type' => 'number'), 
      array( 'id' => '4', 'label' => 'PageViews', 'type' => 'number'), 
   );

   $rows = array();
   
   foreach ($site_visits AS $key => $values)
   {
      if (substr($key, 0, 1) != '#')
      {
         $rows[]['c'] = array(
            array(
               'v' => $sites_lookup[$key]
            ),
            array(
               'v' => (int)$site_visits[$key]['amount'],
               'f' => number_format($site_visits[$key]["amount"])
            ),
            array(
               'v' => (int)$site_unique_visitors[$key]['amount'],
               'f' => number_format($site_unique_visitors[$key]["amount"])
            ),
            array(
               'v' => (int)$site_percent_new_visitors[$key]['amount'],
               'f' => number_format($site_percent_new_visitors[$key]["amount"]).'%'
            ),
            array(
               'v' => (int)$site_pageviews[$key]['amount'],
               'f' => number_format($site_pageviews[$key]["amount"])
            ),
         );
      }
   }
   
   return '{ "cols": '.json_encode($cols).', "rows": '.json_encode($rows).'}'; 
}

function engagement_table($site_pageviews_per_visit, $site_average_visit_duration, $site_bounce_rate, $sites_lookup)
{
   $cols = array( 
      array( 'id' => '0', 'label' => 'Website', 'type' => 'string'), 
      array( 'id' => '1', 'label' => 'Pageviews per Visit', 'type' => 'number'), 
      array( 'id' => '2', 'label' => 'Ave Visit Duration', 'type' => 'number'), 
      array( 'id' => '3', 'label' => 'Bounce Rate', 'type' => 'number'), 
   );

   $rows = array();
   
   foreach ($site_pageviews_per_visit AS $key => $values)
   {
      if (substr($key, 0, 1) != '#')
      {
         $rows[]['c'] = array(
            array(
               'v' => $sites_lookup[$key]
            ),
            array(
               'v' => (int)$site_pageviews_per_visit[$key]['amount'],
               'f' => number_format($site_pageviews_per_visit[$key]["amount"])
            ),
            array(
               'v' => (int)$site_average_visit_duration[$key]['amount'],
               'f' => number_format($site_average_visit_duration[$key]["amount"])
            ),
            array(
               'v' => (int)$site_bounce_rate[$key]['amount'],
               'f' => number_format($site_bounce_rate[$key]["amount"]).'%'
            ),
         );
      }
   }
   
   return '{ "cols": '.json_encode($cols).', "rows": '.json_encode($rows).'}'; 
}

function sites_by_region($sites_by_region)
{
   $cols = array( 
      array( 'id' => '0', 'label' => 'Task', 'type' => 'string'), 
      array( 'id' => '1', 'label' => 'Hours per Day', 'type' => 'number'), 
   );

   $rows = array();
   
   foreach ($sites_by_region AS $site)
   {
     $rows[]['c'] = array(
        array(
           'v' => $site['Region']
        ),
        array(
           'v' => (int)$site['CountSites'],
           'f' => number_format($site['CountSites'])
        ),
      );
   }
   
   return '{ "cols": '.json_encode($cols).', "rows": '.json_encode($rows).'}'; 
}

?>
<body>

<script type='text/javascript' src='https://www.google.com/jsapi'></script>

<script type='text/javascript'>

  google.load('visualization', '1', {packages:['table', 'corechart']});

  google.setOnLoadCallback(drawActivityTable);
  google.setOnLoadCallback(drawEngagementTable);
  google.setOnLoadCallback(drawSitesByRegionChart);

  function drawActivityTable()
  {
    var jsonData = '<?=activity_table($site_visits, $site_unique_visitors, $site_percent_new_visitors, $site_pageviews, $sites_lookup);?>';
    var data = new google.visualization.DataTable(jsonData);
    var table = new google.visualization.Table(document.getElementById('activity_table'));
    table.draw(data, {showRowNumber: false});
  }

  function drawEngagementTable()
  {
    var jsonData = '<?=engagement_table($site_pageviews_per_visit, $site_average_visit_duration, $site_bounce_rate, $sites_lookup);?>';
    var data = new google.visualization.DataTable(jsonData);
    var table = new google.visualization.Table(document.getElementById('engagement_table'));
    table.draw(data, {showRowNumber: false});
  }

  function drawSitesByRegionChart()
  {
    var jsonData = '<?=sites_by_region($sites_by_region);?>';
    var options = {
      title: 'Branded Sites by Region'
    };
    var data = new google.visualization.DataTable(jsonData);
    var chart = new google.visualization.PieChart(document.getElementById('sites_by_region_chart'));
    chart.draw(data, options);
  }

</script>
    

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
               
               <a class="admin" href="<?=site_url('cp/reports/index');?>">Cancel</a>

               </div>

   <h1 id="top">Reports</h1>

            </div>   <!-- page_header -->

            <div class="innercol">

               <div id="basic-form">


<h1 style="color:red; line-height:1em;">Monthly Websites Report
<br /><span style="color:#666; font-size:0.6em;"><?=date('d M Y', strtotime($report['start_date']));?> - <?=date('d M Y', strtotime($report['end_date']));?></span></h1>


<h1>About this report</h1>

<div id="sites_by_region_chart" style="float: right; width: 300px;"></div>

<p><span style="font-size:1.4em;">This report looks at <?=$sites_in_report;?> US branded websites.</span> It does not include European, Canadian or Non-branded websites like Intranet websites.</p>

<p>The report organizes the website data using stages of the marketing funnel: Awareness, Consideration (also called Engagement in online marketing), Conversion, Loyalty, and Advocacy. The idea is to help make it clear how the websites and other online assets are contributing to our overall marketing efforts.</p>

<?php /* -------------------------------------------------------------- */ ?>

<h1>Awareness</h1>

<p><span style="font-size:1.4em;">We averaged <?=number_format($site_visits['#average_daily']['amount']);?> daily visits</span> across all the US branded sites, for a total of <strong><?=number_format($site_visits['#total']['amount']);?></strong> visits for the month.</p>

<?php
$ave_visits_per_visitor = (int)$site_visits['#total']['amount'] / (int)$site_unique_visitors['#total']['amount'];
?>

<p><span style="font-size:1.4em;">We had a total of <?=number_format($site_unique_visitors['#total']['amount']);?> unique visitors for the month</span> for an average of <strong><?=number_format($ave_visits_per_visitor, 2);?></strong> visits per visitor.</p>

<p><span style="font-size:1.4em;">An average of <?=number_format($site_percent_new_visitors['#total']['amount']);?>% of the visitors had never visited our sites before.</span></p>

<p><span style="font-size:1.4em;">An average of XX% of visitors are bouncing off our sites.</span></p>

<p>For per-site numbers, see Table 1 at the end of this report.</p>

<?php /* -------------------------------------------------------------- */ ?>

<h1>Consideration</h1>

<?php
$ave_page_views_per_visit = (int)$site_pageviews['#total']['amount'] / (int)$site_visits['#total']['amount'];
?>

<p><span style="font-size:1.4em;">Visitors' average time on site is X:XX minutes.</span></p>

<p><span style="font-size:1.4em;">Visitors average <?=number_format($ave_page_views_per_visit, 2);?> page views per visit.</span>
<br />Visitors viewed a total of <?=number_format($site_pageviews['#total']['amount']);?> pages for the month.
<br />The averages by site range from X.XX to X.XX.</p>

<p>There is not enough data available to show the number of product pages viewed.</p>


<?php /* -------------------------------------------------------------- */ ?>

<h1>Conversion</h1>

<p>Conversions are measurable actions that visitors take that either represent an actual purchase or are a marker along the path to making a purchase.</p>

<ul>
   <li>Micro-Conversions</li>
   <ul>
      <li>Store Locator result pages</li>
      <li>Messages sent about locator results</li>
   </ul>
   <li>Macro-Conversions</li>
   <ul>
      <li>Sites with e-commerce</li>
      <li>Visits to the Online Store</li>
      <li>Purchasers</li>
   </ul>
   <li>Top 10 highest conversion websites</li>
</ul>

<?php /* -------------------------------------------------------------- */ ?>

<h1>Social Media</h1>

<ul>
   <li>Sites that have a social presence</li>
   <li>Sites that have social buttons</li>
   <li>Visits via Social Referral</li>
</ul>


<h1>Multiple Devices</h1>

<ul>
   <li>Sites that have a mobile presence</li>
   <li>Desktop visits</li>
   <li>Other Device visits</li>
   <li>Mobile visits</li>
</ul>

<?php /* -------------------------------------------------------------- */ ?>

<h1>Details</h1>

<h2>Table 1: Activity</h2>

<p>Per site visits, unique visitors, page views & % new visits</p>

<div id='activity_table'></div>

<?php /* -------------------------------------------------------------- */ ?>

<div style="clear:both;">&nbsp;</div>

<h2>Table 2: Engagement</h2>

<div id='engagement_table'></div>


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