<!DOCTYPE>
<html>
  <head>
    <title>Google Analytics API v3 Sample Application</title>
  </head>
  <body>
  
  <div style="margin:0 36px;">
    <h1>Google Analytics API v3 Sample Application</h1>
    <p>This is a sample PHP application that demonstrates how to use the
       Google Analytics API. This sample application contains various
       demonstrations using the Google Analytics
       <a href="http://code.google.com/apis/analytics/docs/mgmt/v3/mgmtGettingStarted.html">
           Management API</a> and
       <a href="http://code.google.com/apis/analytics/docs/gdata/v3/gdataGettingStarted.html">
           Core Reporting API</a>.</p>

    <p>To begin, you must first grant this application access to your
       Google Analytics data.</p>
    <hr>

<?php
  // Print out authorization URL.
  if ($authorized) {
    print "<p><a href='$revoke_url'>Revoke access</a></p>";
  } else {
    print "<p><a href='$auth_url'>Grant access to Google Analytics data</a></p>";
  }
?>
    <hr>
    <p>Next click which demo you'd like to run.</p>
    <ul>
      <li><a href="<?=$hello_url?>">Hello Analytics API</a> &ndash;
          Traverse through the
          <a href="http://code.google.com/apis/analytics/docs/mgmt/v3/mgmtGettingStarted.html">
             Management API</a> to get the first profile ID.
          The use this ID with the
          <a href="http://code.google.com/apis/analytics/docs/gdata/v3/gdataGettingStarted.html">
             Core Reporting API</a> to print the top 25
          organic search terms.</li>

      <li><a href="<?=$mgmt_url?>">Management API Reference</a> &ndash;
          Traverse through the
          <a href="http://code.google.com/apis/analytics/docs/mgmt/v3/mgmtGettingStarted.html">
             Management API</a> and print all the important
          information returned from the API for each of the first entities.</li>

      <li><a href="<?=$report_url?>">Core Reporting API Reference</a> &ndash;
          Query the <a href="http://code.google.com/apis/analytics/docs/gdata/v3/gdataGettingStarted.html">
             Core Reporting API</a> and print out all the important information
          returned from the API.</li>
    </ul>
    <hr>
<?php
  // Print out errors or results.
  if ($errors) {
    print "<div>There was an error: <br> $errors</div>";
  } else if ($authorized) {
    print "<div>$html_output</div>";
  } 
?>
   </div>
  </body>
</html>